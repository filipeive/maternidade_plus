@extends('layouts.app-tw')

@section('title', 'Exportar Resultados - Laboratório')
@section('page-title', 'Exportação de Resultados Laboratoriais')
@section('title-icon', 'fa-file-export')

@section('breadcrumbs')
    <a href="{{ route('laboratory.index') }}">Laboratório</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Exportar Resultados</span>
@endsection

@section('content')
<div class="card-tw p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
            <label class="label-tw">Data Início</label>
            <input type="date" name="start_date" class="input-tw" value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}">
        </div>
        <div>
            <label class="label-tw">Data Fim</label>
            <input type="date" name="end_date" class="input-tw" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw flex-1">
                <i class="fas fa-filter text-xs"></i>
                <span>Filtrar</span>
            </button>
            <button type="button" onclick="window.print()" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-print text-xs"></i>
                <span>Imprimir</span>
            </button>
        </div>
    </form>
</div>

<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <h6 class="font-bold text-surface-900 text-sm">Resultados de Exames Realizados ({{ count($exams) }} encontrados)</h6>
    </div>
    <div class="overflow-x-auto">
        <table class="table-tw">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data Realização</th>
                    <th>Gestante</th>
                    <th>Tipo de Exame</th>
                    <th>Resultado</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $index => $exam)
                <tr>
                    <td class="font-mono text-xs text-surface-500">{{ $index + 1 }}</td>
                    <td>{{ $exam->data_realizacao ? \Carbon\Carbon::parse($exam->data_realizacao)->format('d/m/Y') : 'N/D' }}</td>
                    <td class="font-semibold text-surface-900">{{ $exam->consultation?->patient?->nome_completo ?? 'N/D' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}</td>
                    <td class="text-xs font-mono">{{ $exam->resultado }}</td>
                    <td>
                        <span class="badge-success">Realizado</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-surface-500">
                        <i class="fas fa-inbox text-2xl text-surface-300 mb-2"></i>
                        <p class="text-sm font-semibold">Nenhum exame realizado no período selecionado.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
