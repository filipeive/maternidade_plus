@extends('layouts.app')

@section('title', 'Métricas de Impacto - Alertas Precoces')
@section('page-title', 'Indicadores de Monitoria & Avaliação (M&E)')
@section('title-icon', 'fa-chart-line')

@section('content')
<div class="container-fluid px-0">
    <!-- Header e Exportação -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-chart-pie me-2 text-primary"></i>Relatório Executivo de Alertas Precoces
            </h4>
            <p class="text-muted mb-0">Indicadores de desempenho clínico, assiduidade e eficácia de intervenção (FNI / MISAU).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('alertas.metricas.pdf', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i>Exportar Relatório PDF
            </a>
            <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i>Ver Lista de Alertas
            </a>
        </div>
    </div>

    <!-- Filtro de Período -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('alertas.metricas') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i>Período de Análise:</span>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Início</span>
                        <input type="date" name="data_inicio" class="form-control" value="{{ $dataInicio }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Fim</span>
                        <input type="date" name="data_fim" class="form-control" value="{{ $dataFim }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-sync-alt me-1"></i>Atualizar Indicadores
                    </button>
                    @if($dataInicio || $dataFim)
                        <a href="{{ route('alertas.metricas') }}" class="btn btn-sm btn-outline-secondary ms-1">
                            Limpar Filtro
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- 5 KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Total Gestantes -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Gestantes Acompanhadas</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalGestantes }}</h3>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="fas fa-female fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Total ativas no sistema</small>
                </div>
            </div>
        </div>

        <!-- KPI 2: Total Alertas Emitidos -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card shadow-sm border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Total Alertas Emitidos</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalAlertas }}</h3>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">No período selecionado</small>
                </div>
            </div>
        </div>

        <!-- KPI 3: Alertas Altos Ativos -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card shadow-sm border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Altos Ativos</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1">{{ $alertasAltosAtivos }}</h3>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-danger mt-2 d-block fw-semibold">Requerem ação imediata</small>
                </div>
            </div>
        </div>

        <!-- KPI 4: Taxa de Resolução -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card shadow-sm border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Taxa de Resolução</span>
                            <h3 class="fw-bold mb-0 text-success mt-1">{{ $taxaResolucao }}%</h3>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $taxaResolucao) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 5: Tempo Médio de Resolução -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card shadow-sm border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Tempo Médio Resposta</span>
                            <h3 class="fw-bold mb-0 text-warning mt-1">{{ $tempoMedioResolucao }} <small class="fs-6 text-muted">dias</small></h3>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                            <i class="fas fa-stopwatch fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Até resolução clínica</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Gráficos Chart.js -->
    <div class="row g-4 mb-4">
        <!-- Gráfico 1: Alertas por Tipo -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Alertas por Regra Clínica / Tipo
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="chartAlertasPorTipo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Alertas por Nível -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-pie-chart me-2 text-danger"></i>Distribuição por Nível de Severidade
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="chartAlertasPorNivel"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico 3: Taxa de Resolução Mensal -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-chart-line me-2 text-success"></i>Evolução da Taxa de Resolução (Últimos 6 Meses)
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="chartTaxaResolucao"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico 4: Distribuição do Tempo de Resposta -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-hourglass-half me-2 text-warning"></i>Distribuição do Tempo de Resposta aos Alertas
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="chartDistribuicaoTempo"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas Analíticas Resumo -->
    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-table me-2 text-primary"></i>Desempenho de Resolução por Tipo de Alerta
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo de Alerta</th>
                                    <th class="text-center">Emitidos</th>
                                    <th class="text-center">Resolvidos</th>
                                    <th class="text-end">Taxa de Sucesso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tabelaPorTipo as $row)
                                    <tr>
                                        <td class="fw-semibold">{{ $row['tipo'] }}</td>
                                        <td class="text-center">{{ $row['emitidos'] }}</td>
                                        <td class="text-center text-success fw-bold">{{ $row['resolvidos'] }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $row['taxa'] >= 70 ? 'success' : ($row['taxa'] >= 40 ? 'warning' : 'danger') }}">
                                                {{ $row['taxa'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Nenhum registo no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-layer-group me-2 text-primary"></i>Resumo por Nível de Severidade
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nível</th>
                                    <th class="text-center">Emitidos</th>
                                    <th class="text-center">Resolvidos</th>
                                    <th class="text-end">Taxa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabelaPorNivel as $row)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $row['nivel'] === 'Alto' ? 'danger' : ($row['nivel'] === 'Médio' ? 'warning' : 'info') }}">
                                                {{ $row['nivel'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $row['emitidos'] }}</td>
                                        <td class="text-center text-success fw-bold">{{ $row['resolvidos'] }}</td>
                                        <td class="text-end fw-bold">{{ $row['taxa'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart 1: Alertas por Tipo
    const ctxTipo = document.getElementById('chartAlertasPorTipo')?.getContext('2d');
    if (ctxTipo) {
        new Chart(ctxTipo, {
            type: 'bar',
            data: {!! json_encode($chartAlertasPorTipo) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // Chart 2: Alertas por Nível
    const ctxNivel = document.getElementById('chartAlertasPorNivel')?.getContext('2d');
    if (ctxNivel) {
        new Chart(ctxNivel, {
            type: 'doughnut',
            data: {!! json_encode($chartAlertasPorNivel) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Chart 3: Taxa de Resolução
    const ctxTaxa = document.getElementById('chartTaxaResolucao')?.getContext('2d');
    if (ctxTaxa) {
        new Chart(ctxTaxa, {
            type: 'line',
            data: {!! json_encode($chartTaxaResolucao) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { min: 0, max: 100, ticks: { callback: v => v + '%' } }
                }
            }
        });
    }

    // Chart 4: Tempo de Resposta
    const ctxTempo = document.getElementById('chartDistribuicaoTempo')?.getContext('2d');
    if (ctxTempo) {
        new Chart(ctxTempo, {
            type: 'bar',
            data: {!! json_encode($chartDistribuicaoTempo) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
