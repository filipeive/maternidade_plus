@extends('layouts.app-tw')

@section('title', 'Carga de Trabalho - Laboratório')
@section('page-title', 'Carga de Trabalho do Laboratório')
@section('title-icon', 'fa-chart-bar')

@section('breadcrumbs')
    <a href="{{ route('laboratory.index') }}">Laboratório</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Carga de Trabalho</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="card-tw p-4 flex items-center gap-4 flex-1">
        <div class="w-12 h-12 rounded-xl bg-ocean-100 text-ocean-700 flex items-center justify-center font-bold text-xl">
            <i class="fas fa-stopwatch"></i>
        </div>
        <div>
            <span class="text-2xs font-semibold uppercase tracking-wider text-surface-400">Tempo Médio de Processamento</span>
            <h3 class="text-2xl font-extrabold text-ocean-600">
                {{ round($avgProcessingTime ?? 0, 1) }} <span class="text-xs text-surface-500 font-normal">dias</span>
            </h3>
        </div>
    </div>
    <a href="{{ route('laboratory.index') }}" class="btn-secondary-tw self-start sm:self-auto">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>Voltar ao Laboratório</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card-tw overflow-hidden">
        <div class="card-header-tw">
            <h6 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                <i class="fas fa-calendar-day text-brand-500"></i> Solicitações por Dia da Semana (Este Mês)
            </h6>
        </div>
        <div class="card-body-tw p-0">
            @if(isset($workloadByDay) && count($workloadByDay) > 0)
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th class="text-right">Solicitações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workloadByDay as $day => $total)
                        <tr>
                            <td class="font-bold text-surface-900">{{ $day }}</td>
                            <td class="text-right">
                                <span class="badge-info font-bold">{{ $total }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-xs text-surface-400 text-center py-6">Sem dados de carga registrados.</p>
            @endif
        </div>
    </div>
</div>
@endsection
