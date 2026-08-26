@extends('layouts.app-tw')

@section('title', 'Relatório de Imunização')
@section('page-title', 'Relatório Executivo de Vacinas & IPTp')
@section('title-icon', 'fa-file-alt')

@section('breadcrumbs')
    <a href="{{ route('vaccines.index') }}">Vacinas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Relatório</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Relatório de Cobertura Vacinal & IPTp</h2>
        <p class="text-sm text-surface-500">
            Período: <strong>{{ $report['periodo']['inicio']->format('d/m/Y') }}</strong> a <strong>{{ $report['periodo']['fim']->format('d/m/Y') }}</strong>
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Voltar às Vacinas</span>
        </a>
    </div>
</div>

{{-- Summary Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-syringe"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $report['vacinas_por_tipo']->sum('total') }}</p>
            <p class="stat-card-label">Doses Aplicadas no Período</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $report['doses_pendentes'] }}</p>
            <p class="stat-card-label">Doses Pendentes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $report['reacoes_adversas'] }}</p>
            <p class="stat-card-label">Reações Adversas</p>
        </div>
    </div>
</div>

{{-- Cobertura Vacinal Table --}}
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <h3 class="text-base font-semibold text-surface-900 flex items-center gap-2">
            <i class="fas fa-chart-pie text-brand-500"></i> Cobertura Vacinal por Imunização (Norma MISAU)
        </h3>
    </div>

    @if(count($report['cobertura_vacinal']) > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Vacina / Antígeno</th>
                        <th class="text-center">Gestantes Vacinadas</th>
                        <th class="text-center">Total de Gestantes</th>
                        <th class="text-right">Taxa de Cobertura</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['cobertura_vacinal'] as $tipo => $dados)
                    <tr>
                        <td class="font-semibold text-surface-900">{{ $dados['nome'] }}</td>
                        <td class="text-center text-brand-700 font-bold">{{ $dados['gestantes_vacinadas'] }}</td>
                        <td class="text-center">{{ $dados['total_gestantes'] }}</td>
                        <td class="text-right">
                            <span class="{{ $dados['cobertura'] >= 80 ? 'badge-success' : ($dados['cobertura'] >= 50 ? 'badge-warning' : 'badge-danger') }}">
                                {{ $dados['cobertura'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-12 text-center text-surface-400">
            <i class="fas fa-file-excel text-3xl mb-2"></i>
            <p class="text-sm">Sem dados estatísticos de cobertura para o período selecionado.</p>
        </div>
    @endif
</div>
@endsection
