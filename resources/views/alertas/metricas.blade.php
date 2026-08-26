@extends('layouts.app-tw')

@section('title', 'Métricas de Impacto - Alertas Precoces')
@section('page-title', 'Indicadores de Monitoria & Avaliação (M&E)')
@section('title-icon', 'fa-chart-line')

@section('breadcrumbs')
    <a href="{{ route('alertas.index') }}">Alertas Precoces</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Métricas</span>
@endsection

@section('content')

{{-- Header & Actions --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Relatório Executivo de Alertas Precoces</h2>
        <p class="text-sm text-surface-500">Indicadores de desempenho clínico, assiduidade e eficácia de intervenção (FNI / MISAU)</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('alertas.metricas.pdf', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="btn-danger-tw">
            <i class="fas fa-file-pdf text-xs"></i>
            <span>Exportar PDF</span>
        </a>
        <a href="{{ route('alertas.index') }}" class="btn-secondary-tw">
            <i class="fas fa-list text-xs"></i>
            <span>Ver Alertas</span>
        </a>
    </div>
</div>

{{-- Filter Card --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('alertas.metricas') }}" class="flex flex-col sm:flex-row items-end gap-4">
        <div>
            <label class="label-tw">Data Início</label>
            <input type="date" name="data_inicio" class="input-tw" value="{{ $dataInicio }}">
        </div>

        <div>
            <label class="label-tw">Data Fim</label>
            <input type="date" name="data_fim" class="input-tw" value="{{ $dataFim }}">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-sync-alt text-xs"></i>
                <span>Atualizar Indicadores</span>
            </button>
            @if($dataInicio || $dataFim)
                <a href="{{ route('alertas.metricas') }}" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-times text-xs"></i>
                    <span>Limpar</span>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- KPI Cards Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-person-pregnant"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $totalGestantes }}</p>
            <p class="stat-card-label">Gestantes Ativas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-bell"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $totalAlertas }}</p>
            <p class="stat-card-label">Alertas Emitidos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-bolt"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $alertasAltosAtivos }}</p>
            <p class="stat-card-label">Altos Ativos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
            <i class="fas fa-check-double"></i>
        </div>
        <div>
            <p class="stat-card-value text-emerald-700">{{ $taxaResolucao }}%</p>
            <p class="stat-card-label">Taxa Resolução</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-stopwatch"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $tempoMedioResolucao }} <span class="text-xs font-normal text-surface-500">dias</span></p>
            <p class="stat-card-label">Tempo Médio</p>
        </div>
    </div>
</div>

{{-- Charts Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Chart 1 --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <i class="fas fa-chart-bar text-brand-500"></i>
                <h3 class="text-sm font-semibold text-surface-900">Alertas por Regra Clínica / Tipo</h3>
            </div>
        </div>
        <div class="card-body-tw">
            <div class="h-64 relative">
                <canvas id="chartAlertasPorTipo"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 2 --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <i class="fas fa-pie-chart text-crimson-500"></i>
                <h3 class="text-sm font-semibold text-surface-900">Distribuição por Nível de Severidade</h3>
            </div>
        </div>
        <div class="card-body-tw">
            <div class="h-64 relative">
                <canvas id="chartAlertasPorNivel"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 3 --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-500"></i>
                <h3 class="text-sm font-semibold text-surface-900">Evolução da Taxa de Resolução (Últimos 6 Meses)</h3>
            </div>
        </div>
        <div class="card-body-tw">
            <div class="h-64 relative">
                <canvas id="chartTaxaResolucao"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 4 --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <i class="fas fa-hourglass-half text-gold-500"></i>
                <h3 class="text-sm font-semibold text-surface-900">Distribuição do Tempo de Resposta</h3>
            </div>
        </div>
        <div class="card-body-tw">
            <div class="h-64 relative">
                <canvas id="chartDistribuicaoTempo"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Analytical Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card-tw overflow-hidden">
        <div class="card-header-tw">
            <h3 class="text-sm font-semibold text-surface-900 flex items-center gap-2">
                <i class="fas fa-table text-brand-500"></i> Desempenho por Tipo de Alerta
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Tipo de Alerta</th>
                        <th class="text-center">Emitidos</th>
                        <th class="text-center">Resolvidos</th>
                        <th class="text-right">Taxa de Sucesso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tabelaPorTipo as $row)
                        <tr>
                            <td class="font-semibold text-surface-900">{{ $row['tipo'] }}</td>
                            <td class="text-center">{{ $row['emitidos'] }}</td>
                            <td class="text-center text-emerald-700 font-bold">{{ $row['resolvidos'] }}</td>
                            <td class="text-right">
                                <span class="{{ $row['taxa'] >= 70 ? 'badge-success' : ($row['taxa'] >= 40 ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $row['taxa'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-surface-400 py-6">Nenhum registo no período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-tw overflow-hidden">
        <div class="card-header-tw">
            <h3 class="text-sm font-semibold text-surface-900 flex items-center gap-2">
                <i class="fas fa-layer-group text-ocean-500"></i> Resumo por Severidade
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Nível</th>
                        <th class="text-center">Emitidos</th>
                        <th class="text-center">Resolvidos</th>
                        <th class="text-right">Taxa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tabelaPorNivel as $row)
                        <tr>
                            <td>
                                <span class="{{ $row['nivel'] === 'Alto' ? 'badge-danger' : ($row['nivel'] === 'Médio' ? 'badge-warning' : 'badge-info') }}">
                                    {{ $row['nivel'] }}
                                </span>
                            </td>
                            <td class="text-center">{{ $row['emitidos'] }}</td>
                            <td class="text-center text-emerald-700 font-bold">{{ $row['resolvidos'] }}</td>
                            <td class="text-right font-bold text-surface-800">{{ $row['taxa'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxTipo = document.getElementById('chartAlertasPorTipo')?.getContext('2d');
    if (ctxTipo) {
        new Chart(ctxTipo, {
            type: 'bar',
            data: {!! json_encode($chartAlertasPorTipo) !!},
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    const ctxNivel = document.getElementById('chartAlertasPorNivel')?.getContext('2d');
    if (ctxNivel) {
        new Chart(ctxNivel, {
            type: 'doughnut',
            data: {!! json_encode($chartAlertasPorNivel) !!},
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    const ctxTaxa = document.getElementById('chartTaxaResolucao')?.getContext('2d');
    if (ctxTaxa) {
        new Chart(ctxTaxa, {
            type: 'line',
            data: {!! json_encode($chartTaxaResolucao) !!},
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } } } }
        });
    }

    const ctxTempo = document.getElementById('chartDistribuicaoTempo')?.getContext('2d');
    if (ctxTempo) {
        new Chart(ctxTempo, {
            type: 'bar',
            data: {!! json_encode($chartDistribuicaoTempo) !!},
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
});
</script>
@endpush
@endsection
