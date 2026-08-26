@extends('layouts.app-tw')

@section('title', 'Laboratório')
@section('page-title', 'Gestão Laboratorial Pré-Natal')
@section('title-icon', 'fa-flask-vial')

@section('breadcrumbs')
    <span class="active">Laboratório</span>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $stats['exames_pendentes'] ?? 0 }}</p>
            <p class="stat-card-label">Exames Pendentes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $stats['exames_realizados_hoje'] ?? 0 }}</p>
            <p class="stat-card-label">Realizados Hoje</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $stats['exames_atrasados'] ?? 0 }}</p>
            <p class="stat-card-label">Exames Atrasados</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $stats['total_este_mes'] ?? 0 }}</p>
            <p class="stat-card-label">Total Este Mês</p>
        </div>
    </div>
</div>

{{-- Header Action Bar --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Painel do Laboratório</h2>
        <p class="text-sm text-surface-500">Gestão de amostras, registo de resultados e alertas de reagentes</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('laboratory.pending-queue') }}" class="btn-primary-tw">
            <i class="fas fa-clock text-xs"></i>
            <span>Fila de Pendentes</span>
        </a>
        <a href="{{ route('laboratory.critical-alerts') }}" class="btn-danger-tw">
            <i class="fas fa-biohazard text-xs"></i>
            <span>Alertas Críticos</span>
        </a>
        <a href="{{ route('laboratory.export-results') }}" class="btn-secondary-tw">
            <i class="fas fa-download text-xs text-brand-600"></i>
            <span>Exportar</span>
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('laboratory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label-tw">Status do Exame</label>
            <select name="status" class="input-tw">
                <option value="">Todos os status</option>
                <option value="solicitado" {{ request('status') === 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                <option value="em_andamento" {{ request('status') === 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                <option value="realizado" {{ request('status') === 'realizado' ? 'selected' : '' }}>Realizado</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Tipo de Exame</label>
            <select name="tipo_exame" class="input-tw">
                <option value="">Todos os tipos</option>
                <option value="teste_hiv" {{ request('tipo_exame') === 'teste_hiv' ? 'selected' : '' }}>HIV (Teste Rápido)</option>
                <option value="teste_sifilis" {{ request('tipo_exame') === 'teste_sifilis' ? 'selected' : '' }}>Sífilis (VDRL)</option>
                <option value="hemograma_completo" {{ request('tipo_exame') === 'hemograma_completo' ? 'selected' : '' }}>Hemograma Completo</option>
                <option value="glicemia_jejum" {{ request('tipo_exame') === 'glicemia_jejum' ? 'selected' : '' }}>Glicemia em Jejum</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Pesquisar Gestante</label>
            <input type="text" name="search" class="input-tw" placeholder="Nome ou BI..." value="{{ request('search') }}">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw flex-1">
                <i class="fas fa-search text-xs"></i>
                <span>Filtrar</span>
            </button>
            <a href="{{ route('laboratory.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Main Table Card --}}
<div class="card-tw overflow-hidden" x-data="{activeModal: null}">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                <i class="fas fa-flask"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Processamento Laboratorial</h3>
        </div>
        <span class="badge-neutral font-medium">{{ $exams->total() }} exames</span>
    </div>

    @if($exams->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Data Solicitação</th>
                        <th>Status</th>
                        <th>Resultado</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    @php $patient = $exam->patient ?? $exam->consultation?->patient; @endphp
                    <tr>
                        <td>
                            @if($patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ocean-100 text-ocean-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                            {{ $patient->nome_completo }}
                                        </a>
                                        <p class="text-2xs text-surface-400">BI: {{ $patient->documento_bi ?? 'N/D' }}</p>
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
                        </td>
                        <td>
                            @php
                                $badgeClass = match($exam->status) {
                                    'realizado' => 'badge-success',
                                    'em_andamento' => 'badge-info',
                                    'solicitado' => 'badge-warning',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $exam->status)) }}</span>
                        </td>
                        <td>
                            @if($exam->resultado)
                                <span class="font-semibold {{ str_contains(strtolower($exam->resultado), 'positivo') ? 'text-crimson-600' : 'text-brand-700' }}">
                                    {{ $exam->resultado }}
                                </span>
                            @else
                                <span class="text-2xs text-surface-400 italic">Pendente</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($exam->status !== 'realizado')
                                <button type="button"
                                        @click="activeModal = {{ $exam->id }}"
                                        class="btn-primary-tw btn-sm-tw">
                                    <i class="fas fa-vial text-xs"></i>
                                    <span>Lançar</span>
                                </button>
                            @else
                                <a href="{{ route('exams.show', $exam) }}" class="btn-icon-tw" title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modais Alpine.js para Lançamento de Resultado --}}
        @foreach($exams as $exam)
        @if($exam->status !== 'realizado')
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
                    <div class="px-5 py-4 bg-gradient-to-r from-brand-600 to-brand-700 text-white flex items-center justify-between">
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
                            <p>Gestante: <strong class="text-surface-900">{{ $exam->patient->nome_completo ?? $exam->consultation?->patient?->nome_completo ?? 'N/D' }}</strong></p>
                            <p>Exame: <strong class="text-brand-700">{{ $exam->tipo_exame_label }}</strong></p>
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
        @endif
        @endforeach

        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $exams->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $exams->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $exams->total() }}</span> exames
            </p>
            <div>
                {{ $exams->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-flask text-3xl text-surface-400"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhum exame encontrado</h3>
            <p class="text-sm text-surface-500">Ajuste os filtros de pesquisa.</p>
        </div>
    @endif
</div>
@endsection