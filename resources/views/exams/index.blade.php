@extends('layouts.app-tw')

@section('title', 'Exames')
@section('page-title', 'Gestão de Exames Laboratoriais')
@section('title-icon', 'fa-microscope')

@section('breadcrumbs')
    <span class="active">Exames</span>
@endsection

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-flask"></i>
        </div>
        <div>
            <p class="stat-card-value">{{ $stats['total'] }}</p>
            <p class="stat-card-label">Total de Exames</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <p class="stat-card-value">{{ $stats['pendentes'] }}</p>
            <p class="stat-card-label">Exames Pendentes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="stat-card-value">{{ $stats['realizados'] }}</p>
            <p class="stat-card-label">Exames Realizados</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <p class="stat-card-value">{{ $stats['alertas_criticos'] ?? 0 }}</p>
            <p class="stat-card-label">Alertas Críticos</p>
        </div>
    </div>
</div>

{{-- Header Action --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Lista de Exames Laboratoriais</h2>
        <p class="text-sm text-surface-500">Acompanhe exames solicitados, pendentes e resultados</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('exams.create') }}" class="btn-primary-tw">
            <i class="fas fa-plus text-xs"></i>
            <span>Solicitar Exame</span>
        </a>
        <a href="{{ route('exams.pending-results') }}" class="btn-secondary-tw">
            <i class="fas fa-clock text-xs text-gold-600"></i>
            <span>Resultados Pendentes</span>
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label-tw">Status</label>
            <select name="status" class="input-tw">
                <option value="">Todos os status</option>
                <option value="solicitado" {{ request('status') === 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                <option value="em_andamento" {{ request('status') === 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                <option value="realizado" {{ request('status') === 'realizado' ? 'selected' : '' }}>Realizado</option>
                <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
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
                <option value="ultrassom_obstetrico" {{ request('tipo_exame') === 'ultrassom_obstetrico' ? 'selected' : '' }}>Ultrassom Obstétrico</option>
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
            <a href="{{ route('exams.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Exams Table --}}
<div class="card-tw overflow-hidden">
    @if($exams->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Exame</th>
                        <th>Data Solicitação</th>
                        <th>Status</th>
                        <th>Resultado</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    @php $patient = $exam->patient ?? $exam->consultation?->patient; @endphp
                    <tr>
                        <td>
                            @if($patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-ocean-100 text-ocean-700 font-bold text-sm flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                            {{ $patient->nome_completo }}
                                        </a>
                                        <p class="text-2xs text-surface-400">BI: {{ $patient->documento_bi }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-surface-400 italic">Gestante N/D</span>
                            @endif
                        </td>
                        <td>
                            <p class="font-medium text-surface-900">{{ $exam->tipo_exame_label }}</p>
                            @if($exam->laboratorio)
                                <p class="text-2xs text-surface-400">{{ $exam->laboratorio }}</p>
                            @endif
                        </td>
                        <td>
                            <p class="font-medium text-surface-800">{{ $exam->data_solicitacao ? $exam->data_solicitacao->format('d/m/Y') : '-' }}</p>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($exam->status) {
                                    'realizado' => 'badge-success',
                                    'em_andamento' => 'badge-info',
                                    'solicitado' => 'badge-warning',
                                    'cancelado' => 'badge-danger',
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
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('exams.show', $exam) }}"
                                   class="btn-icon-tw"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($exam->status !== 'realizado')
                                    <a href="{{ route('exams.result-form', $exam) }}"
                                       class="btn-icon-tw text-brand-600 hover:bg-brand-50"
                                       title="Registar Resultado">
                                        <i class="fas fa-file-signature text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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
            <p class="text-sm text-surface-500 mb-4">Solicite um novo exame ou ajuste os filtros.</p>
            <a href="{{ route('exams.create') }}" class="btn-primary-tw">
                <i class="fas fa-plus text-xs"></i>
                <span>Solicitar Exame</span>
            </a>
        </div>
    @endif
</div>
@endsection