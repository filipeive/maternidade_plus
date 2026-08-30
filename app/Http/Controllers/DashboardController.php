<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Birth;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\HomeVisit;
use App\Models\MaternalProphylaxis;
use App\Models\Patient;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Avaliação proativa e automática de alertas precoces (throttled a cada 10 min)
        if (cache()->add('alertas_auto_avaliar_lock', true, now()->addMinutes(10))) {
            try {
                app(\App\Services\AlertaPrecoceService::class)->avaliarTodas();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Erro ao auto-avaliar alertas no dashboard: ' . $e->getMessage());
            }
        }

        // 1. Estatísticas Gerais / KPIs Principais
        $totalGestantes = Patient::where('ativo', true)->count();
        $totalGestantesARO = Patient::where('ativo', true)
            ->where(function ($subQuery) {
                $subQuery->where('numero_abortos', '>', 0)
                         ->orWhere('historico_medico', 'like', '%diabetes%')
                         ->orWhere('historico_medico', 'like', '%hipertensao%')
                         ->orWhere('alergias', '!=', null);
            })->count();

        $consultasHoje = Consultation::whereDate('data_consulta', today())->count();
        $consultasEstaSemana = Consultation::whereBetween('data_consulta', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $consultasPendentes = Consultation::where('status', 'agendada')->count();
        $examesPendentes = Exam::where('status', 'solicitado')->count();

        $partosMes = Birth::whereMonth('data_hora_parto', now()->month)
            ->whereYear('data_hora_parto', now()->year)
            ->count();

        $visitasMes = HomeVisit::whereMonth('data_visita', now()->month)
            ->whereYear('data_visita', now()->year)
            ->count();

        $totalTransferidas = Patient::where('ativo', false)
            ->whereIn('motivo_inativacao', ['transferencia_us', 'transferencia_provincia', 'mudanca_residencia'])
            ->count();

        $faltosasCount = Patient::where('ativo', true)
            ->whereHas('consultations', function ($consultaQuery) {
                $consultaQuery->where('status', 'agendada')->where('data_consulta', '<', now());
            })->count();

        // 2. Gráfico 1: Evolução de Consultas CPN & Partos (Últimos 6 Meses)
        $mesesLabels = [];
        $consultasMensais = [];
        $partosMensais = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $mesesLabels[] = ucfirst($mes->translatedFormat('M/Y'));

            $consultasMensais[] = Consultation::whereMonth('data_consulta', $mes->month)
                ->whereYear('data_consulta', $mes->year)
                ->where('status', 'realizada')
                ->count();

            $partosMensais[] = Birth::whereMonth('data_hora_parto', $mes->month)
                ->whereYear('data_hora_parto', $mes->year)
                ->count();
        }

        // 3. Gráfico 2: Distribuição por Trimestre & Status Gestacional
        $pacientesAtivas = Patient::where('ativo', true)->get();
        $trimestre1 = 0;
        $trimestre2 = 0;
        $trimestre3 = 0;
        $posParto = 0;

        foreach ($pacientesAtivas as $p) {
            if ($p->status_atual === 'pos_parto') {
                $posParto++;
            } else {
                $sem = $p->idade_gestacional;
                if ($sem !== null && $sem <= 13) {
                    $trimestre1++;
                } elseif ($sem !== null && $sem <= 27) {
                    $trimestre2++;
                } else {
                    $trimestre3++;
                }
            }
        }

        // 4. Gráfico 3: Cobertura de Profilaxias MISAU (Percentual sobre total de ativas)
        $baseTotal = max($totalGestantes, 1);
        $iptp1Dose = MaternalProphylaxis::whereNotNull('sp_1_dose')->count();
        $iptp3Doses = MaternalProphylaxis::whereNotNull('sp_3_dose')->count();
        $ferroFolato = MaternalProphylaxis::where(function ($profilaxiaSubQuery) {
            $profilaxiaSubQuery->where('sal_ferroso_folico_3doses', true)
                               ->orWhere('doses_sal_ferroso_entregues', '>=', 1);
        })->count();
        $mebendazol = MaternalProphylaxis::whereNotNull('mebendazol_administrado')->count();
        $tetano = MaternalProphylaxis::whereNotNull('vat_1_dose')->count();
        if ($tetano === 0) {
            $tetano = Vaccine::where('tipo_vacina', 'tetano')->distinct('patient_id')->count('patient_id');
        }

        $profilaxiasData = [
            'labels' => ['IPTp 1ª Dose', 'IPTp 3ª+ Doses', 'Tétano (VAT)', 'Ferro / Folato', 'Mebendazol'],
            'counts' => [$iptp1Dose, $iptp3Doses, $tetano, $ferroFolato, $mebendazol],
            'percentuais' => [
                round(($iptp1Dose / $baseTotal) * 100, 1),
                round(($iptp3Doses / $baseTotal) * 100, 1),
                round(($tetano / $baseTotal) * 100, 1),
                round(($ferroFolato / $baseTotal) * 100, 1),
                round(($mebendazol / $baseTotal) * 100, 1),
            ]
        ];

        // 5. Gráfico 4: Status das Visitas Domiciliárias
        $visitasRealizadas = HomeVisit::where('status', 'realizada')->count();
        $visitasAgendadas = HomeVisit::where('status', 'agendada')->count();
        $visitasNaoEncontrada = HomeVisit::where('status', 'nao_encontrada')->count();
        $visitasCanceladas = HomeVisit::where('status', 'cancelada')->count();

        // 6. Feeds & Listagens Operacionais
        $alertasPrecoces = Alerta::with('patient')
            ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
            ->orderByRaw("CASE nivel WHEN 'alto' THEN 1 WHEN 'medio' THEN 2 WHEN 'baixo' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $proximasConsultas = Consultation::with('patient')
            ->where('data_consulta', '>=', now()->startOfDay())
            ->where('data_consulta', '<=', now()->addDays(7)->endOfDay())
            ->orderBy('data_consulta')
            ->limit(6)
            ->get();

        $ultimosPartos = Birth::with('patient')
            ->orderBy('data_hora_parto', 'desc')
            ->limit(5)
            ->get();

        $pacientesFaltosas = Patient::where('ativo', true)
            ->whereHas('consultations', function ($consultaQuery) {
                $consultaQuery->where('status', 'agendada')->where('data_consulta', '<', now());
            })
            ->with(['consultations' => function ($consultaQuery) {
                $consultaQuery->where('status', 'agendada')->where('data_consulta', '<', now())->orderBy('data_consulta', 'desc');
            }])
            ->limit(5)
            ->get();

        $ultimasTransferencias = Patient::where('ativo', false)
            ->whereNotNull('data_transferencia')
            ->orderBy('data_transferencia', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalGestantes',
            'totalGestantesARO',
            'consultasHoje',
            'consultasEstaSemana',
            'consultasPendentes',
            'examesPendentes',
            'partosMes',
            'visitasMes',
            'totalTransferidas',
            'faltosasCount',
            'mesesLabels',
            'consultasMensais',
            'partosMensais',
            'trimestre1',
            'trimestre2',
            'trimestre3',
            'posParto',
            'profilaxiasData',
            'visitasRealizadas',
            'visitasAgendadas',
            'visitasNaoEncontrada',
            'visitasCanceladas',
            'alertasPrecoces',
            'proximasConsultas',
            'ultimosPartos',
            'pacientesFaltosas',
            'ultimasTransferencias'
        ));
    }

    public function home()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    }

    public function getStats()
    {
        return response()->json([
            'total_gestantes' => Patient::where('ativo', true)->count(),
            'consultas_hoje' => Consultation::whereDate('data_consulta', today())->count(),
            'partos_mes' => Birth::whereMonth('data_hora_parto', now()->month)->count(),
            'alertas_ativos' => Alerta::whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])->count(),
        ]);
    }
}
