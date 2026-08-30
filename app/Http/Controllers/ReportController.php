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

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = (int)$request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        $isFullYear = ($month === 'all');

        $dataInicio = $isFullYear 
            ? Carbon::createFromDate($year, 1, 1)->startOfYear()
            : Carbon::createFromDate($year, (int)$month, 1)->startOfMonth();

        $dataFim = $isFullYear
            ? Carbon::createFromDate($year, 12, 31)->endOfYear()
            : Carbon::createFromDate($year, (int)$month, 1)->endOfMonth();

        // 1. Gestantes & SMI
        $novasGestantesQuery = Patient::whereBetween('created_at', [$dataInicio, $dataFim]);
        $novasGestantesCount = (clone $novasGestantesQuery)->count();
        $totalAtivas = Patient::where('ativo', true)->count();
        
        $inscricoesPrecoces = (clone $novasGestantesQuery)
            ->get()
            ->filter(function (Patient $paciente) {
                $idadeGestacional = $paciente->getIdadeGestacionalSemanas();
                return $idadeGestacional !== null && $idadeGestacional <= 12;
            })
            ->count();

        $adolescentesSMI = (clone $novasGestantesQuery)
            ->get()
            ->filter(function (Patient $paciente) {
                $idade = $paciente->idade ?? ($paciente->data_nascimento ? Carbon::parse($paciente->data_nascimento)->age : null);
                return $idade !== null && $idade >= 10 && $idade <= 19;
            })
            ->count();

        $altoRiscoCount = Patient::where('ativo', true)
            ->where(function ($subQuery) {
                $subQuery->where('risco_gestacional', 'Alto')
                         ->orWhere('numero_abortos', '>', 0)
                         ->orWhereNotNull('historico_medico');
            })
            ->count();

        $transferenciasPeriodo = Patient::where('ativo', false)
            ->whereBetween('data_transferencia', [$dataInicio, $dataFim])
            ->count();

        // 2. Consultas Pré-Natais
        $consultasQuery = Consultation::whereBetween('data_consulta', [$dataInicio, $dataFim]);
        $consultasRealizadas = (clone $consultasQuery)->where('status', 'realizada')->count();
        $consultasPrimeira = (clone $consultasQuery)->where('numero_consulta', 1)->where('status', 'realizada')->count();
        $consultasQuarta = (clone $consultasQuery)->where('numero_consulta', '>=', 4)->where('status', 'realizada')->count();

        // 3. Maternidade & Nascimentos
        $partosQuery = Birth::whereBetween('data_hora_parto', [$dataInicio, $dataFim]);
        $totalPartos = (clone $partosQuery)->count();
        $partosNormais = (clone $partosQuery)->whereIn('tipo_parto', ['eutocico', 'normal', 'vaginal'])->count();
        $cesarianas = (clone $partosQuery)->whereIn('tipo_parto', ['cesariana', 'cesarea'])->count();
        $nadosVivos = (clone $partosQuery)->where('nado_vivo', true)->count();
        $nadosMortos = (clone $partosQuery)->where('nado_vivo', false)->count();
        $baixoPeso = (clone $partosQuery)->where('peso_gramas', '<', 2500)->where('peso_gramas', '>', 0)->count();
        $apgarBaixo = (clone $partosQuery)->where('apgar_5min', '<', 7)->whereNotNull('apgar_5min')->count();

        // 4. Profilaxias Maternas MISAU
        $profilaxiaQuery = MaternalProphylaxis::whereBetween('created_at', [$dataInicio, $dataFim]);
        $iptp1Dose = (clone $profilaxiaQuery)->whereNotNull('sp_1_dose')->count();
        $iptp2Dose = (clone $profilaxiaQuery)->whereNotNull('sp_2_dose')->count();
        $iptp3MaisDoses = (clone $profilaxiaQuery)->whereNotNull('sp_3_dose')->count();
        $remtilEntregues = (clone $profilaxiaQuery)->where('remtil_entregue', true)->count();
        $ferroFolatoEntregues = (clone $profilaxiaQuery)->where(function ($subQuery) {
            $subQuery->where('sal_ferroso_folico_3doses', true)
                     ->orWhere('doses_sal_ferroso_entregues', '>=', 1);
        })->count();
        $mebendazolEntregues = (clone $profilaxiaQuery)->whereNotNull('mebendazol_administrado')->count();
        $vatVacinas = Vaccine::whereBetween('data_administracao', [$dataInicio, $dataFim])->where('status', 'administrada')->count();
        $misoprostolEntregues = (clone $profilaxiaQuery)->where('misoprostol_entregue', true)->count();

        // 5. Triagem Laboratorial & PTV
        $hivTestadas = (clone $profilaxiaQuery)->whereNotNull('hiv_resultado_cpn')->count();
        $hivPositivas = (clone $profilaxiaQuery)->whereIn('hiv_resultado_cpn', ['Positivo', 'Reagente'])->count();
        $tarvIniciadas = (clone $profilaxiaQuery)->whereNotNull('tarv_inicio_data')->count();
        $parceiroHivTestados = (clone $profilaxiaQuery)->where('parceiro_testado_hiv', true)->count();
        
        $sifilisTestadas = (clone $profilaxiaQuery)->whereNotNull('sifilis_resultado')->count();
        $sifilisPositivas = (clone $profilaxiaQuery)->whereIn('sifilis_resultado', ['Positivo', 'Reagente'])->count();
        $sifilisTratadas = (clone $profilaxiaQuery)->where('sifilis_tratamento_mulher', true)->count();

        // 6. Saúde Comunitária (APEs)
        $visitasQuery = HomeVisit::whereBetween('data_visita', [$dataInicio, $dataFim]);
        $visitasTotal = (clone $visitasQuery)->count();
        $visitasRealizadas = (clone $visitasQuery)->where('status', 'realizada')->count();
        $visitasNaoEncontrada = (clone $visitasQuery)->where('status', 'nao_encontrada')->count();
        $visitasDispensadas = (clone $visitasQuery)->where('status', 'cancelada')->count();

        // 7. Alertas Precoces & M&E
        $alertasQuery = Alerta::whereBetween('created_at', [$dataInicio, $dataFim]);
        $alertasTotal = (clone $alertasQuery)->count();
        $alertasAltos = (clone $alertasQuery)->where('nivel', Alerta::NIVEL_ALTO)->count();
        $alertasMedios = (clone $alertasQuery)->where('nivel', Alerta::NIVEL_MEDIO)->count();
        $alertasResolvidos = (clone $alertasQuery)->where('status', Alerta::STATUS_RESOLVIDO)->count();
        $taxaResolubilidade = $alertasTotal > 0 ? round(($alertasResolvidos / $alertasTotal) * 100, 1) : 100.0;

        $stats = compact(
            'novasGestantesCount', 'totalAtivas', 'inscricoesPrecoces', 'adolescentesSMI', 'altoRiscoCount', 'transferenciasPeriodo',
            'consultasRealizadas', 'consultasPrimeira', 'consultasQuarta',
            'totalPartos', 'partosNormais', 'cesarianas', 'nadosVivos', 'nadosMortos', 'baixoPeso', 'apgarBaixo',
            'iptp1Dose', 'iptp2Dose', 'iptp3MaisDoses', 'remtilEntregues', 'ferroFolatoEntregues', 'mebendazolEntregues', 'vatVacinas', 'misoprostolEntregues',
            'hivTestadas', 'hivPositivas', 'tarvIniciadas', 'parceiroHivTestados', 'sifilisTestadas', 'sifilisPositivas', 'sifilisTratadas',
            'visitasTotal', 'visitasRealizadas', 'visitasNaoEncontrada', 'visitasDispensadas',
            'alertasTotal', 'alertasAltos', 'alertasMedios', 'alertasResolvidos', 'taxaResolubilidade'
        );

        return view('reports.index', compact('stats', 'year', 'month', 'isFullYear'));
    }
}
