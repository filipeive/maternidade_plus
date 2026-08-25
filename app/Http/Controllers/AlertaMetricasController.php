<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AlertaMetricasController extends Controller
{
    /**
     * Dashboard analítico de Métricas e Indicadores de Monitoria & Avaliação (M&E).
     */
    public function index(Request $request): View
    {
        $metricas = $this->calcularMetricas($request);

        return view('alertas.metricas', $metricas);
    }

    /**
     * Exportação de Relatório Executivo em formato PDF via DomPDF.
     */
    public function exportPdf(Request $request): Response
    {
        $metricas = $this->calcularMetricas($request);

        $pdf = Pdf::loadView('alertas.pdf-metricas', $metricas)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        $filename = 'relatorio_alertas_me_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Motor de cálculo unificado de métricas, KPIs e datasets estatísticos.
     */
    private function calcularMetricas(Request $request): array
    {
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        $query = Alerta::query();

        if ($dataInicio) {
            $query->whereDate('created_at', '>=', Carbon::parse($dataInicio)->startOfDay());
        }
        if ($dataFim) {
            $query->whereDate('created_at', '<=', Carbon::parse($dataFim)->endOfDay());
        }

        $alertas = $query->get();
        $totalAlertas = $alertas->count();

        // 1. Total de gestantes acompanhadas
        $totalGestantes = Patient::where('ativo', true)->count();

        // 2. Alertas de Nível Alto Ativos no momento
        $alertasAltosAtivos = Alerta::where('nivel', Alerta::NIVEL_ALTO)
            ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
            ->count();

        // 3. Taxa de Resolução (%)
        $resolvidosCount = $alertas->where('status', Alerta::STATUS_RESOLVIDO)->count();
        $taxaResolucao = $totalAlertas > 0 ? round(($resolvidosCount / $totalAlertas) * 100, 1) : 0.0;

        // 4. Tempo Médio de Resolução (em dias)
        $alertasResolvidos = $alertas->where('status', Alerta::STATUS_RESOLVIDO)->filter(function ($alerta) {
            return $alerta->resolvido_em !== null && $alerta->created_at !== null;
        });

        $tempoMedioResolucao = 0.0;
        if ($alertasResolvidos->count() > 0) {
            $somaDias = 0;
            foreach ($alertasResolvidos as $item) {
                $diffHoras = Carbon::parse($item->created_at)->diffInHours(Carbon::parse($item->resolvido_em));
                $somaDias += ($diffHoras / 24.0);
            }
            $tempoMedioResolucao = round($somaDias / $alertasResolvidos->count(), 1);
        }

        // ===================================
        // DATASETS PARA CHART.JS
        // ===================================

        // Chart 1: Alertas por Tipo (Bar chart)
        $tipoLabelsMap = [
            'pressao_arterial_alta' => 'PA Elevada',
            'pressao_arterial_grave' => 'PA Grave',
            'bcf_anormal' => 'BCF Anormal',
            'gestante_faltosa' => 'Gestante Faltosa',
            'alto_risco_sem_seguimento' => 'Alto Risco Sem Seg.',
            'vacinas_em_atraso' => 'Vacinas em Atraso',
            'exames_criticos' => 'Exames Críticos',
            'ganho_peso_anormal' => 'Ganho/Perda Peso',
            'idade_gestacional_pos_termo' => 'Pós-Termo',
            'sangramento_reportado' => 'Sangramento',
        ];

        $porTipo = [];
        foreach ($alertas->groupBy('tipo') as $tipo => $grupo) {
            $label = $tipoLabelsMap[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
            $porTipo[$label] = $grupo->count();
        }
        arsort($porTipo);

        $chartAlertasPorTipo = [
            'labels' => array_keys($porTipo),
            'datasets' => [[
                'label' => 'Total de Alertas',
                'data' => array_values($porTipo),
                'backgroundColor' => [
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(111, 66, 193, 0.7)',
                    'rgba(253, 126, 20, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(108, 117, 125, 0.7)',
                    'rgba(32, 201, 151, 0.7)',
                    'rgba(214, 51, 132, 0.7)',
                ],
                'borderColor' => '#ffffff',
                'borderWidth' => 1,
            ]],
        ];

        // Chart 2: Alertas por Nível (Linha / Distribuição Temporal)
        $chartAlertasPorNivel = [
            'labels' => ['Alto', 'Médio', 'Baixo'],
            'datasets' => [[
                'label' => 'Distribuição por Nível',
                'data' => [
                    $alertas->where('nivel', Alerta::NIVEL_ALTO)->count(),
                    $alertas->where('nivel', Alerta::NIVEL_MEDIO)->count(),
                    $alertas->where('nivel', Alerta::NIVEL_BAIXO)->count(),
                ],
                'backgroundColor' => [
                    '#dc3545',
                    '#ffc107',
                    '#0dcaf0',
                ],
            ]],
        ];

        // Chart 3: Taxa de Resolução Mensal (Últimos 6 meses)
        $mesesLabels = [];
        $taxasResolucaoMensal = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $chaveMes = $mes->format('M/Y');
            $mesesLabels[] = $chaveMes;

            $alertasDoMes = Alerta::whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->get();
            $totMes = $alertasDoMes->count();
            $resMes = $alertasDoMes->where('status', Alerta::STATUS_RESOLVIDO)->count();
            $taxasResolucaoMensal[] = $totMes > 0 ? round(($resMes / $totMes) * 100, 1) : 0.0;
        }

        $chartTaxaResolucao = [
            'labels' => $mesesLabels,
            'datasets' => [[
                'label' => 'Taxa de Resolução (%)',
                'data' => $taxasResolucaoMensal,
                'borderColor' => '#009639',
                'backgroundColor' => 'rgba(0, 150, 57, 0.15)',
                'fill' => true,
                'tension' => 0.3,
            ]],
        ];

        // Chart 4: Distribuição do Tempo de Resposta
        $faixas = [
            '< 24 horas' => 0,
            '1 a 3 dias' => 0,
            '4 a 7 dias' => 0,
            '> 7 dias' => 0,
        ];

        foreach ($alertasResolvidos as $res) {
            $dias = Carbon::parse($res->created_at)->diffInDays(Carbon::parse($res->resolvido_em));
            if ($dias < 1) {
                $faixas['< 24 horas']++;
            } elseif ($dias <= 3) {
                $faixas['1 a 3 dias']++;
            } elseif ($dias <= 7) {
                $faixas['4 a 7 dias']++;
            } else {
                $faixas['> 7 dias']++;
            }
        }

        $chartDistribuicaoTempo = [
            'labels' => array_keys($faixas),
            'datasets' => [[
                'label' => 'Casos Resolvidos',
                'data' => array_values($faixas),
                'backgroundColor' => [
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                ],
            ]],
        ];

        // Tabelas analíticas detalhadas para o relatório
        $tabelaPorTipo = [];
        foreach ($alertas->groupBy('tipo') as $tipo => $grupo) {
            $label = $tipoLabelsMap[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
            $emitidos = $grupo->count();
            $resolvidos = $grupo->where('status', Alerta::STATUS_RESOLVIDO)->count();
            $tabelaPorTipo[] = [
                'tipo' => $label,
                'emitidos' => $emitidos,
                'resolvidos' => $resolvidos,
                'taxa' => $emitidos > 0 ? round(($resolvidos / $emitidos) * 100, 1) : 0,
            ];
        }

        $tabelaPorNivel = [];
        foreach (['alto' => 'Alto', 'medio' => 'Médio', 'baixo' => 'Baixo'] as $key => $label) {
            $grupo = $alertas->where('nivel', $key);
            $emitidos = $grupo->count();
            $resolvidos = $grupo->where('status', Alerta::STATUS_RESOLVIDO)->count();
            $tabelaPorNivel[] = [
                'nivel' => $label,
                'emitidos' => $emitidos,
                'resolvidos' => $resolvidos,
                'taxa' => $emitidos > 0 ? round(($resolvidos / $emitidos) * 100, 1) : 0,
            ];
        }

        return [
            'totalGestantes' => $totalGestantes,
            'totalAlertas' => $totalAlertas,
            'alertasAltosAtivos' => $alertasAltosAtivos,
            'taxaResolucao' => $taxaResolucao,
            'tempoMedioResolucao' => $tempoMedioResolucao,
            'chartAlertasPorTipo' => $chartAlertasPorTipo,
            'chartAlertasPorNivel' => $chartAlertasPorNivel,
            'chartTaxaResolucao' => $chartTaxaResolucao,
            'chartDistribuicaoTempo' => $chartDistribuicaoTempo,
            'tabelaPorTipo' => $tabelaPorTipo,
            'tabelaPorNivel' => $tabelaPorNivel,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
        ];
    }
}
