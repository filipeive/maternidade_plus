@extends('layouts.app-tw')

@section('title', 'Fila de Exames Pendentes')
@section('page-title', 'Fila de Processamento - Laboratório')
@section('title-icon', 'fa-clock')

@section('breadcrumbs')
    <a href="{{ route('laboratory.index') }}">Laboratório</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Fila Pendente</span>
@endsection

@section('content')
{{-- Banner Header --}}
<div class="card-tw p-6 mb-6 bg-gradient-to-r from-ocean-600 to-ocean-800 text-white">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold flex items-center gap-2 mb-1">
                <i class="fas fa-flask"></i>
                Fila de Processamento Laboratorial
            </h3>
            <p class="text-sm text-white/80">
                Gerencie e processe exames pendentes por ordem de prioridade
            </p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <div class="text-center px-4 py-2 bg-white/10 rounded-xl backdrop-blur-sm">
                <p class="text-2xl font-bold text-gold-300 leading-none">{{ $examsPendentes->count() }}</p>
                <p class="text-2xs text-white/70 uppercase font-medium mt-1">Pendentes</p>
            </div>
            <a href="{{ route('laboratory.index') }}" class="btn-secondary-tw btn-sm-tw bg-white/20 border-white/30 text-white hover:bg-white/30">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>
    </div>
</div>

{{-- Main Queue Table Card --}}
<div class="card-tw overflow-hidden" x-data="{activeModal: null}">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Exames Solicitados na Fila</h3>
        </div>
        <span class="badge-warning font-medium">{{ $examsPendentes->count() }} exames</span>
    </div>

    @if($examsPendentes->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Data Solicitação</th>
                        <th>Status</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($examsPendentes as $exam)
                    <tr>
                        <td class="font-mono text-xs text-surface-500">{{ $exam->id }}</td>
                        <td>
                            @if($exam->consultation && $exam->consultation->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ocean-100 text-ocean-700 font-bold text-xs flex items-center justify-center shrink-0">
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
                            <p class="font-medium text-surface-800 text-xs">{{ $exam->data_solicitacao ? $exam->data_solicitacao->format('d/m/Y') : '-' }}</p>
                            <p class="text-2xs text-surface-400">{{ $exam->created_at->format('H:i') }}</p>
                        </td>
                        <td>
                            <span class="badge-warning">
                                <i class="fas fa-spinner fa-spin mr-1 text-2xs"></i>Pendente
                            </span>
                        </td>
                        <td class="text-right">
                            <button type="button"
                                    @click="activeModal = {{ $exam->id }}"
                                    class="btn-primary-tw btn-sm-tw">
                                <i class="fas fa-vial text-xs"></i>
                                <span>Lançar Resultado</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modais Alpine.js para Lançamento de Resultado --}}
        @foreach($examsPendentes as $exam)
        <div x-show="activeModal === {{ $exam->id }}"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-cloak>
            <div @click.outside="activeModal = null"
                 class="bg-white rounded-xl shadow-toast border border-surface-200 w-full max-w-md overflow-hidden animate-fade-in-up">

                <form method="POST" action="{{ route('laboratory.process-exam', $exam) }}">
                    @csrf
                    <div class="px-5 py-4 bg-gradient-to-r from-ocean-600 to-ocean-700 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-flask"></i>
                            <h4 class="font-semibold text-sm">Lançar Resultado Laboratorial</h4>
                        </div>
                        <button type="button" @click="activeModal = null" class="text-white/70 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="p-3 bg-surface-50 rounded-lg border border-surface-200/60 text-xs space-y-1">
                            <p>Gestante: <strong class="text-surface-900">{{ $exam->consultation->patient->nome_completo ?? 'N/D' }}</strong></p>
                            <p>Exame: <strong class="text-ocean-700">{{ $exam->tipo_exame_label }}</strong></p>
                        </div>

                        <div>
                            <label class="label-tw">Resultado Encontrado <span class="text-crimson-500">*</span></label>
                            <input type="text"
                                   name="resultado"
                                   class="input-tw"
                                   required
                                   placeholder="Ex: Negativo, Reagente, 12.5 g/dL...">
                        </div>

                        <div>
                            <label class="label-tw">Observações Laboratoriais</label>
                            <textarea name="observacoes"
                                      class="input-tw"
                                      rows="3"
                                      placeholder="Notas do técnico, lote do reativo..."></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-2">
                        <button type="button" @click="activeModal = null" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                        <button type="submit" class="btn-primary-tw btn-sm-tw">
                            <i class="fas fa-check text-xs"></i>
                            <span>Salvar Resultado</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-3xl text-brand-500"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Sem exames pendentes na fila!</h3>
            <p class="text-sm text-surface-500">Todos os exames solicitados foram processados com sucesso.</p>
        </div>
    @endif
</div>
@endsection