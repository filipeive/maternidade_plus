@extends('layouts.app-tw')

@section('title', 'Alertas Críticos de Laboratório')
@section('page-title', 'Alertas Críticos de Laboratório')
@section('title-icon', 'fa-triangle-exclamation')

@section('breadcrumbs')
    <a href="{{ route('laboratory.index') }}">Laboratório</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Alertas Críticos</span>
@endsection

@section('content')
{{-- Header Card --}}
<div class="card-tw p-5 mb-6 border-l-4 border-l-crimson-500">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-crimson-600 flex items-center gap-2 mb-1">
                <i class="fas fa-biohazard text-lg"></i>
                Exames Críticos Detectados (Últimos 7 dias)
            </h3>
            <p class="text-sm text-surface-500">
                Resultados reagentes ou alterados (HIV+, Sífilis+, Anemia Grave, Diabetes) que exigem conduta clínica prioritária.
            </p>
        </div>
        <span class="badge-danger text-sm px-3 py-1.5 shrink-0 self-start sm:self-auto">
            <i class="fas fa-bolt mr-1"></i>{{ $criticalExams->count() }} Resultados Críticos
        </span>
    </div>
</div>

{{-- Action Bar --}}
<div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-6">
    <a href="{{ route('laboratory.index') }}" class="btn-secondary-tw">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>Voltar ao Laboratório</span>
    </a>
    <a href="{{ route('alertas.index') }}" class="btn-danger-tw">
        <i class="fas fa-shield-alt text-xs"></i>
        <span>Ver Módulo de Alerta Precoce</span>
    </a>
</div>

{{-- Critical Exams Table --}}
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Resultados com Sinal Crítico</h3>
        </div>
    </div>

    @if($criticalExams->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Data Realização</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Resultado Encontrado</th>
                        <th>Prioridade</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criticalExams as $exam)
                    <tr>
                        <td>
                            <p class="font-medium text-surface-800 text-xs">{{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : $exam->updated_at->format('d/m/Y') }}</p>
                            <p class="text-2xs text-surface-400">{{ $exam->updated_at->format('H:i') }}</p>
                        </td>
                        <td>
                            @if($exam->consultation && $exam->consultation->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-crimson-100 text-crimson-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($exam->consultation->patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('patients.show', $exam->consultation->patient) }}"
                                           class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                            {{ $exam->consultation->patient->nome_completo }}
                                        </a>
                                        <p class="text-2xs text-surface-400">BI: {{ $exam->consultation->patient->documento_bi ?? 'N/D' }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-surface-400 italic">Gestante N/D</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-medium text-surface-900">{{ $exam->tipo_exame_label }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-crimson-600 text-xs bg-crimson-50 px-2.5 py-1 rounded border border-crimson-200">
                                {{ $exam->resultado }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-danger">
                                <i class="fas fa-bolt mr-1 text-2xs animate-pulse"></i>ALTO RISCO
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('exams.show', $exam) }}"
                                   class="btn-secondary-tw btn-sm-tw"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                    <span>Ver</span>
                                </a>
                                @if($exam->consultation && $exam->consultation->patient)
                                    <a href="{{ route('alertas.index', ['search' => $exam->consultation->patient->nome_completo]) }}"
                                       class="btn-danger-tw btn-sm-tw"
                                       title="Conduta Clínica">
                                        <i class="fas fa-stethoscope text-xs"></i>
                                        <span>Tratar</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center">
                <i class="fas fa-shield-check text-3xl text-brand-500"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhum exame crítico recente</h3>
            <p class="text-sm text-surface-500">Todos os exames realizados nos últimos 7 dias apresentam resultados normais.</p>
        </div>
    @endif
</div>
@endsection
