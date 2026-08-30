<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlertaController extends Controller
{
    /**
     * Lista filtrável e paginada de alertas clínicos.
     */
    public function index(Request $request): View
    {
        $query = Alerta::with(['patient', 'consultation', 'resolvidoPor', 'acoes.user']);

        // Filtro por Nível
        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        // Filtro por Tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por Pesquisa de Paciente (Nome ou BI)
        $search = $request->input('search') ?? $request->input('paciente');
        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('nome_completo', 'like', "%{$search}%")
                  ->orWhere('documento_bi', 'like', "%{$search}%");
            });
        }

        // Filtro por Intervalo de Datas
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->data_inicio)->startOfDay());
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->data_fim)->endOfDay());
        }

        // Ordenação padrão: Severidade (Alto -> Médio -> Baixo) seguida pela data mais recente
        $query->orderByRaw("CASE nivel WHEN 'alto' THEN 1 WHEN 'medio' THEN 2 WHEN 'baixo' THEN 3 ELSE 4 END")
              ->orderByDesc('created_at');

        $alertas = $query->paginate(15)->withQueryString();

        // Estatísticas rápidas para os cards do topo
        $estatisticas = [
            'total_ativos' => Alerta::whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count(),
            'altos_ativos' => Alerta::where('nivel', Alerta::NIVEL_ALTO)->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count(),
            'em_seguimento' => Alerta::where('status', Alerta::STATUS_EM_SEGUIMENTO)->count(),
            'resolvidos' => Alerta::where('status', Alerta::STATUS_RESOLVIDO)->count(),
        ];

        return view('alertas.index', compact('alertas', 'estatisticas'));
    }

    /**
     * Transita o status de um alerta (ativo -> em_seguimento / resolvido / ignorado).
     */
    public function transitar(Request $request, Alerta $alerta): RedirectResponse
    {
        $user = auth()->user();

        // Verificação de permissões Spatie (Apenas Administrador e Médico ou com permissão manage_alerts)
        if (!$user || (!$user->can('manage_alerts') && !$user->hasRole(['Administrador', 'Médico']))) {
            abort(403, 'Acesso não autorizado para resolução ou transição de alertas.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                Alerta::STATUS_EM_SEGUIMENTO,
                Alerta::STATUS_RESOLVIDO,
                Alerta::STATUS_IGNORADO,
            ])],
            'nota' => ['required', 'string', 'max:1000'],
        ], [
            'status.required' => 'O novo status é obrigatório.',
            'nota.required' => 'A nota de resolução/seguimento é obrigatória para fins de auditoria clínica.',
            'nota.max' => 'A nota não pode exceder 1000 caracteres.',
        ]);

        $alerta->transitarStatus($validated['status'], $user, $validated['nota']);

        return redirect()->back()->with('success', 'Status do alerta atualizado com sucesso.');
    }

    /**
     * Alias para transitar com status resolvido ou direto.
     */
    public function resolver(Request $request, Alerta $alerta): RedirectResponse
    {
        return $this->transitar($request, $alerta);
    }

    /**
     * Marca um alerta clínico como lido.
     */
    public function marcarLido(Request $request, Alerta $alerta)
    {
        $alerta->marcarLido();

        if ($request->wantsJson() || $request->ajax()) {
            $alertasAltosCount = Alerta::where('nivel', Alerta::NIVEL_ALTO)
                ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
                ->where('lido', false)
                ->count();

            return response()->json([
                'status' => 'ok',
                'message' => 'Alerta marcado como lido.',
                'alertasAltosCount' => $alertasAltosCount,
            ]);
        }

        return redirect()->back()->with('success', 'Alerta marcado como lido.');
    }

    /**
     * Marca todos os alertas ativos como lidos.
     */
    public function marcarTodosLidos(Request $request)
    {
        Alerta::where('lido', false)->update(['lido' => true]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Todos os alertas foram marcados como lidos.',
                'alertasAltosCount' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Todos os alertas foram marcados como lidos.');
    }

    /**
     * Avalia todas as gestantes ativas e gera alertas clínicos precoces imediatamente.
     */
    public function avaliarTodos(Request $request): RedirectResponse
    {
        try {
            $resultado = app(\App\Services\AlertaPrecoceService::class)->avaliarTodas();
            return redirect()->back()->with(
                'success',
                "Avaliação clínica concluída com sucesso: {$resultado['avaliadas']} gestantes analisadas, {$resultado['novos_alertas']} novos alertas gerados."
            );
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erro ao avaliar alertas: ' . $e->getMessage());
        }
    }

    /**
     * Painel de Auditoria e Avaliações Clínicas Precoces de todas as gestantes.
     */
    public function avaliacoes(Request $request): View
    {
        $filtro = $request->input('filtro', 'todos');
        $busca = $request->input('search');

        $gestantes = Patient::where('ativo', true)
            ->where('status_atual', Patient::STATUS_GESTANTE)
            ->with([
                'consultations' => fn($q) => $q->orderByDesc('data_consulta'),
                'exams',
                'vaccines',
                'alertas' => fn($q) => $q->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO]),
            ])
            ->get();

        $avaliacoes = $gestantes->map(function ($p) {
            $ultimaConsulta = $p->consultations->first();
            $diasSemConsulta = $ultimaConsulta 
                ? Carbon::parse($ultimaConsulta->data_consulta)->diffInDays(now())
                : Carbon::parse($p->created_at)->diffInDays(now());

            $idadeGestacional = $p->getIdadeGestacionalSemanas() ?? ($p->semanas_gestacao ?? 0);

            // Sinais Vitais
            $paStr = $ultimaConsulta->pressao_arterial ?? null;
            $paSistolica = null;
            $paDiastolica = null;
            $isPaAlta = false;
            $isPaGrave = false;

            if ($paStr && preg_match('/(\d{2,3})\s*(?:[\/xX\-:]|\s+)\s*(\d{2,3})/', $paStr, $m)) {
                $paSistolica = (int)$m[1];
                $paDiastolica = (int)$m[2];
                if ($paSistolica >= 160 || $paDiastolica >= 110) {
                    $isPaGrave = true;
                } elseif ($paSistolica >= 140 || $paDiastolica >= 90) {
                    $isPaAlta = true;
                }
            }

            $bcf = $ultimaConsulta->batimentos_fetais ?? null;
            $isBcfAnormal = $bcf && ($bcf < 110 || $bcf > 160);

            // Faltosa
            $isFaltosa = false;
            $motivoFaltosa = null;
            $agendadas = $p->consultations->where('status', 'agendada');
            foreach ($agendadas as $ag) {
                $dt = $ag->proxima_consulta ?? $ag->data_consulta;
                if ($dt && Carbon::parse($dt)->lte(now()->subDays(3))) {
                    $isFaltosa = true;
                    $motivoFaltosa = Carbon::parse($dt)->diffInDays(now()) . ' dias de atraso na consulta';
                    break;
                }
            }
            if (!$isFaltosa && $diasSemConsulta > 30) {
                $isFaltosa = true;
                $motivoFaltosa = $diasSemConsulta . ' dias sem consulta';
            }

            // Pós-termo
            $isPosTermo = ($idadeGestacional > 41);

            // Exames Críticos
            $examesCriticos = [];
            foreach ($p->exams as $ex) {
                $res = mb_strtolower($ex->resultado ?? '', 'UTF-8');
                if (($ex->tipo_exame === 'teste_hiv' || str_contains($res, 'hiv')) && preg_match('/(?:reagente|positivo)/i', $res) && !preg_match('/(?:n[aã]o\s+reagente|negativo)/i', $res)) {
                    $examesCriticos[] = 'HIV+ (' . ($ex->resultado) . ')';
                }
                if (($ex->tipo_exame === 'teste_sifilis' || str_contains($res, 'vdrl') || str_contains($res, 'sifilis')) && preg_match('/(?:reagente|positivo)/i', $res) && !preg_match('/(?:n[aã]o\s+reagente|negativo)/i', $res)) {
                    $examesCriticos[] = 'Sífilis+ (' . ($ex->resultado) . ')';
                }
                if (preg_match('/(?:hb|hemoglobina)[\s\:\=]*(\d+(?:[\.,]\d+)?)/i', $res, $mHb)) {
                    $hb = (float)str_replace(',', '.', $mHb[1]);
                    if ($hb > 0 && $hb < 7.0) {
                        $examesCriticos[] = "Hb {$hb} g/dL (Anemia Grave)";
                    }
                }
            }

            // Vacinas em atraso
            $vacinasAtrasadas = $p->vaccines->filter(fn($v) => $v->status === 'pendente' && $v->proxima_dose && Carbon::parse($v->proxima_dose)->lt(now()))->count();

            // Status Geral
            $alertasAtivos = $p->alertas;
            $temAlertaAlto = $alertasAtivos->where('nivel', 'alto')->count() > 0;
            $temAlertaMedio = $alertasAtivos->where('nivel', 'medio')->count() > 0;

            $statusClass = 'normal';
            if ($temAlertaAlto || $isPaGrave || $isPosTermo || count($examesCriticos) > 0) {
                $statusClass = 'critico';
            } elseif ($temAlertaMedio || $isPaAlta || $isFaltosa || $vacinasAtrasadas > 0 || $p->risco_gestacional === 'Alto') {
                $statusClass = 'atencao';
            }

            return (object) [
                'patient' => $p,
                'idade_gestacional' => $idadeGestacional,
                'dias_sem_consulta' => $diasSemConsulta,
                'ultima_consulta' => $ultimaConsulta,
                'pressao_arterial' => $paStr,
                'pa_sistolica' => $paSistolica,
                'pa_diastolica' => $paDiastolica,
                'is_pa_alta' => $isPaAlta,
                'is_pa_grave' => $isPaGrave,
                'bcf' => $bcf,
                'is_bcf_anormal' => $isBcfAnormal,
                'is_faltosa' => $isFaltosa,
                'motivo_faltosa' => $motivoFaltosa,
                'is_pos_termo' => $isPosTermo,
                'is_alto_risco' => ($p->risco_gestacional === 'Alto' || $p->isAltoRisco()),
                'exames_criticos' => $examesCriticos,
                'vacinas_atrasadas' => $vacinasAtrasadas,
                'alertas_ativos' => $alertasAtivos,
                'status_class' => $statusClass,
            ];
        });

        // Estatísticas para os cards
        $stats = [
            'total_avaliadas' => $avaliacoes->count(),
            'normais' => $avaliacoes->where('status_class', 'normal')->count(),
            'atencao' => $avaliacoes->where('status_class', 'atencao')->count(),
            'criticos' => $avaliacoes->where('status_class', 'critico')->count(),
            'faltosas' => $avaliacoes->where('is_faltosa', true)->count(),
            'pos_termo' => $avaliacoes->where('is_pos_termo', true)->count(),
            'alto_risco' => $avaliacoes->where('is_alto_risco', true)->count(),
        ];

        // Filtro de Busca por Texto
        if ($busca) {
            $avaliacoes = $avaliacoes->filter(function($item) use ($busca) {
                return str_contains(mb_strtolower($item->patient->nome_completo), mb_strtolower($busca))
                    || str_contains(mb_strtolower($item->patient->documento_bi ?? ''), mb_strtolower($busca))
                    || str_contains(mb_strtolower($item->patient->contacto ?? ''), mb_strtolower($busca));
            });
        }

        // Filtro por Tab
        if ($filtro === 'criticos') {
            $avaliacoes = $avaliacoes->where('status_class', 'critico');
        } elseif ($filtro === 'atencao') {
            $avaliacoes = $avaliacoes->where('status_class', 'atencao');
        } elseif ($filtro === 'normais') {
            $avaliacoes = $avaliacoes->where('status_class', 'normal');
        } elseif ($filtro === 'faltosas') {
            $avaliacoes = $avaliacoes->where('is_faltosa', true);
        } elseif ($filtro === 'pos_termo') {
            $avaliacoes = $avaliacoes->where('is_pos_termo', true);
        } elseif ($filtro === 'aro') {
            $avaliacoes = $avaliacoes->where('is_alto_risco', true);
        }

        return view('alertas.avaliacoes', compact('avaliacoes', 'stats', 'filtro', 'busca'));
    }

    /**
     * Exporta a lista de auditoria clínica em PDF formatado MISAU.
     */
    public function avaliacoesPdf(Request $request)
    {
        $gestantes = Patient::where('ativo', true)
            ->where('status_atual', Patient::STATUS_GESTANTE)
            ->with([
                'consultations' => fn($q) => $q->orderByDesc('data_consulta'),
                'exams',
                'vaccines',
                'alertas' => fn($q) => $q->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO]),
            ])
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('alertas.avaliacoes-pdf', [
            'gestantes' => $gestantes,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Auditoria_Clinica_Alertas_' . date('Ymd_His') . '.pdf');
    }
}
