@extends('layouts.app-tw')

@section('title', 'Relatório Diário - Laboratório')
@section('page-title', 'Relatório Diário do Laboratório')
@section('title-icon', 'fa-file-invoice')

@section('breadcrumbs')
    <a href="{{ route('laboratory.index') }}">Laboratório</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Relatório Diário</span>
@endsection

@section('content')
<div class="card-tw p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row items-end gap-4">
        <div class="flex-1">
            <label class="label-tw">Data do Relatório</label>
            <input type="date" name="date" class="input-tw" value="{{ \Carbon\Carbon::parse($report['data'] ?? now())->format('Y-m-d') }}">
        </div>
        <button type="submit" class="btn-primary-tw btn-sm-tw">
            <i class="fas fa-search text-xs"></i>
            <span>Filtrar Data</span>
        </button>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card-tw p-4 text-center">
        <p class="text-2xl font-bold text-ocean-600">{{ $report['exames_solicitados'] ?? 0 }}</p>
        <p class="text-xs text-surface-500 uppercase tracking-wider mt-1">Exames Solicitados</p>
    </div>
    <div class="card-tw p-4 text-center">
        <p class="text-2xl font-bold text-brand-600">{{ $report['exames_realizados'] ?? 0 }}</p>
        <p class="text-xs text-surface-500 uppercase tracking-wider mt-1">Exames Realizados</p>
    </div>
    <div class="card-tw p-4 text-center">
        <p class="text-2xl font-bold text-gold-600">{{ $report['exames_pendentes'] ?? 0 }}</p>
        <p class="text-xs text-surface-500 uppercase tracking-wider mt-1">Exames Pendentes</p>
    </div>
</div>

<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <h6 class="font-bold text-surface-900 text-sm">
            Resultados Alterados no Dia ({{ \Carbon\Carbon::parse($report['data'] ?? now())->format('d/m/Y') }})
        </h6>
    </div>
    <div class="overflow-x-auto">
        <table class="table-tw">
            <thead>
                <tr>
                    <th>Gestante</th>
                    <th>Tipo de Exame</th>
                    <th>Resultado</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['exames_alterados'] ?? [] as $exam)
                <tr>
                    <td class="font-semibold text-surface-900">{{ $exam->consultation?->patient?->nome_completo ?? 'N/D' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}</td>
                    <td class="font-mono text-xs text-crimson-600 font-semibold">{{ $exam->resultado }}</td>
                    <td><span class="badge-danger">Alterado</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-surface-500">
                        <i class="fas fa-check-circle text-2xl text-brand-500 mb-2"></i>
                        <p class="text-sm font-semibold">Nenhum resultado alterado nesta data.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
