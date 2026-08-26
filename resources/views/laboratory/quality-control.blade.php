@extends('layouts.app-tw')

@section('title', 'Controle de Qualidade - Laboratório')
@section('page-title', 'Controle de Qualidade Laboratorial')
@section('title-icon', 'fa-shield-virus')

@section('breadcrumbs')
    <a href="{{ route('laboratory.index') }}">Laboratório</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Controle de Qualidade</span>
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="card-tw p-4 text-center">
        <p class="text-xs text-surface-500 uppercase tracking-wider mb-1">Tempo Médio Entrega</p>
        <h3 class="text-2xl font-extrabold text-ocean-600">{{ $qualityMetrics['tempo_medio_entrega'] ?? 0 }}d</h3>
    </div>
    <div class="card-tw p-4 text-center">
        <p class="text-xs text-surface-500 uppercase tracking-wider mb-1">Resultados Críticos</p>
        <h3 class="text-2xl font-extrabold text-crimson-600">{{ $qualityMetrics['exames_criticos'] ?? 0 }}</h3>
    </div>
    <div class="card-tw p-4 text-center">
        <p class="text-xs text-surface-500 uppercase tracking-wider mb-1">Taxa Reprocessamento</p>
        <h3 class="text-2xl font-extrabold text-gold-600">{{ $qualityMetrics['taxa_reprocessamento'] ?? 0 }}%</h3>
    </div>
    <div class="card-tw p-4 text-center">
        <p class="text-xs text-surface-500 uppercase tracking-wider mb-1">Conformidade Qualidade</p>
        <h3 class="text-2xl font-extrabold text-brand-600">{{ $qualityMetrics['satisfacao_cliente'] ?? 95 }}%</h3>
    </div>
</div>

<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <h6 class="font-bold text-surface-900 text-sm flex items-center gap-2">
            <i class="fas fa-microscope text-gold-500"></i> Resultados Alterados ou Anormais (Este Mês)
        </h6>
    </div>
    <div class="overflow-x-auto">
        <table class="table-tw">
            <thead>
                <tr>
                    <th>Data Realização</th>
                    <th>Gestante</th>
                    <th>Tipo de Exame</th>
                    <th>Resultado</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alteredExams ?? [] as $exam)
                <tr>
                    <td>{{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : 'N/D' }}</td>
                    <td class="font-semibold text-surface-900">{{ $exam->consultation?->patient?->nome_completo ?? 'N/D' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}</td>
                    <td class="font-mono text-xs text-crimson-600 font-semibold">{{ $exam->resultado }}</td>
                    <td><span class="badge-danger">Alterado</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-surface-500">
                        <i class="fas fa-shield-check text-2xl text-brand-500 mb-2"></i>
                        <p class="text-sm font-semibold">Nenhum exame com resultado alterado registrado este mês.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
