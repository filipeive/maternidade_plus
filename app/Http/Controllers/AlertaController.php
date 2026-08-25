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
}
