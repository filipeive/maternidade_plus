@extends('layouts.app-tw')

@section('title', 'Detalhes do Exame')
@section('page-title', 'Exame: ' . $exam->tipo_exame_label)
@section('title-icon', 'fa-microscope')

@section('breadcrumbs')
    <a href="{{ route('exams.index') }}">Exames</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Detalhes</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Content (2/3) --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="card-tw">
            <div class="card-header-tw flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-100 text-ocean-700 flex items-center justify-center font-bold">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-surface-900">{{ $exam->tipo_exame_label }}</h3>
                        <p class="text-xs text-surface-500">Solicitado em: {{ $exam->data_solicitacao->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @if ($exam->status === 'solicitado')
                        <a href="{{ route('exams.result-form', $exam) }}" class="btn-primary-tw btn-sm-tw">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Lançar Resultado</span>
                        </a>
                    @endif
                    <a href="{{ route('exams.edit', $exam) }}" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-edit text-xs"></i>
                        <span>Editar</span>
                    </a>
                    @if ($exam->resultado)
                        <button class="btn-secondary-tw btn-sm-tw" onclick="window.print()">
                            <i class="fas fa-print text-xs"></i>
                            <span>Imprimir</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="card-body-tw space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-2 border-b sm:border-b-0 sm:border-r border-surface-100 pb-3 sm:pb-0 sm:pr-4">
                        <p class="flex justify-between">
                            <span class="text-surface-500">Tipo de Exame:</span>
                            <span class="font-semibold text-surface-900">{{ $exam->tipo_exame_label }}</span>
                        </p>
                        @if ($exam->descricao_exame)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Descrição:</span>
                                <span class="text-surface-900">{{ $exam->descricao_exame }}</span>
                            </p>
                        @endif
                        <p class="flex justify-between">
                            <span class="text-surface-500">Data Solicitação:</span>
                            <span class="font-semibold text-surface-900">{{ $exam->data_solicitacao->format('d/m/Y') }}</span>
                        </p>
                        @if ($exam->data_realizacao)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Data Realização:</span>
                                <span class="font-semibold text-brand-600">{{ $exam->data_realizacao->format('d/m/Y') }}</span>
                            </p>
                        @endif
                        <p class="flex justify-between">
                            <span class="text-surface-500">Status:</span>
                            @php
                                $statusClass = match ($exam->status) {
                                    'realizado' => 'badge-success',
                                    'solicitado' => 'badge-warning',
                                    default => 'badge-neutral',
                                };
                            @endphp
                            <span class="{{ $statusClass }}">{{ ucfirst($exam->status) }}</span>
                        </p>
                    </div>

                    <div class="space-y-2">
                        <p class="flex justify-between">
                            <span class="text-surface-500">Solicitado Por:</span>
                            <span class="font-semibold text-surface-900">{{ $exam->consultation->user->name ?? 'Profissional' }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-surface-500">Data da Consulta:</span>
                            <span class="font-semibold text-surface-900">{{ $exam->consultation->data_consulta->format('d/m/Y H:i') }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-surface-500">Tipo da Consulta:</span>
                            <span class="badge-info">{{ $exam->consultation->tipo_consulta_label }}</span>
                        </p>
                        @if ($exam->consultation->semanas_gestacao)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Semanas de Gestação:</span>
                                <span class="font-semibold text-surface-900">{{ $exam->consultation->semanas_gestacao }}ª semana</span>
                            </p>
                        @endif
                    </div>
                </div>

                @if ($exam->observacoes)
                    <div class="pt-4 border-t border-surface-100">
                        <h6 class="font-bold text-surface-800 text-xs mb-2">Observações Clínicas</h6>
                        <div class="p-3 bg-surface-50 rounded-lg text-xs text-surface-700 leading-relaxed border-l-4 border-ocean-500">
                            {{ $exam->observacoes }}
                        </div>
                    </div>
                @endif

                @if ($exam->resultado)
                    <div class="pt-4 border-t border-surface-100">
                        <h6 class="font-bold text-brand-800 text-xs mb-2 flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-brand-500"></i> Resultado do Exame
                        </h6>
                        <div class="p-4 bg-brand-50/50 border border-brand-200 rounded-xl text-xs text-surface-900 leading-relaxed font-mono whitespace-pre-wrap">
                            {!! nl2br(e($exam->resultado)) !!}
                        </div>
                        @if ($exam->data_realizacao)
                            <p class="text-2xs text-surface-400 mt-2">
                                Realizado em {{ $exam->data_realizacao->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                @else
                    <div class="p-4 bg-gold-50 border border-gold-200 rounded-xl flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-gold-900">
                            <i class="fas fa-clock text-gold-600 text-base shrink-0"></i>
                            <span>Aguardando lançamento de resultado pelo laboratório</span>
                        </div>
                        @if ($exam->status === 'solicitado')
                            <a href="{{ route('exams.result-form', $exam) }}" class="btn-primary-tw btn-sm-tw shrink-0">
                                Lançar Resultado
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar (1/3) --}}
    <div class="space-y-6">
        <div class="card-tw">
            <div class="card-header-tw">
                <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Dados da Gestante</h6>
            </div>
            <div class="card-body-tw space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($exam->consultation->patient->nome_completo ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="font-bold text-surface-900 text-sm">{{ $exam->consultation->patient->nome_completo }}</h6>
                        <p class="text-2xs text-surface-500">BI: {{ $exam->consultation->patient->documento_bi }}</p>
                    </div>
                </div>

                <div class="border-t border-surface-100 pt-3 space-y-1.5 text-xs">
                    <p class="flex justify-between">
                        <span class="text-surface-500">Idade:</span>
                        <span class="font-semibold text-surface-800">{{ $exam->consultation->patient->idade }} anos</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-surface-500">Contacto:</span>
                        <span class="font-semibold text-surface-800">{{ $exam->consultation->patient->contacto }}</span>
                    </p>
                </div>

                <div class="border-t border-surface-100 pt-3">
                    <a href="{{ route('patients.show', $exam->consultation->patient) }}" class="btn-secondary-tw btn-sm-tw w-full">
                        <i class="fas fa-user text-xs"></i>
                        <span>Ver Perfil Completo</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
