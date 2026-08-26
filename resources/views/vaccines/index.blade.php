@extends('layouts.app-tw')

@section('title', 'Gestão de Vacinas')
@section('page-title', 'Programa de Imunização Pré-natal & IPTp')
@section('title-icon', 'fa-syringe')

@section('breadcrumbs')
    <span class="active">Vacinas & IPTp</span>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $stats['total_administradas'] ?? 0 }}</p>
            <p class="stat-card-label">Doses Administradas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $stats['doses_pendentes'] ?? 0 }}</p>
            <p class="stat-card-label">Doses Pendentes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $stats['doses_vencidas'] ?? 0 }}</p>
            <p class="stat-card-label">Doses Vencidas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $stats['proximas_7_dias'] ?? 0 }}</p>
            <p class="stat-card-label">Próximos 7 Dias</p>
        </div>
    </div>
</div>

{{-- Header & Action Bar --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Plano de Imunização e IPTp</h2>
        <p class="text-sm text-surface-500">Monitoria de doses de Tétano (VAT) e Malária (IPTp-SP) para gestantes</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('vaccines.create') }}" class="btn-primary-tw">
            <i class="fas fa-plus text-xs"></i>
            <span>Registar Vacinação</span>
        </a>
        <a href="{{ route('vaccines.pending-alert') }}" class="btn-secondary-tw">
            <i class="fas fa-bell text-xs text-crimson-500"></i>
            <span>Alertas ({{ ($stats['doses_vencidas'] ?? 0) + ($stats['proximas_7_dias'] ?? 0) }})</span>
        </a>
        <a href="{{ route('vaccines.generate-report') }}" class="btn-secondary-tw">
            <i class="fas fa-file-alt text-xs text-brand-600"></i>
            <span>Relatório MISAU</span>
        </a>
    </div>
</div>

{{-- Filters Card --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('vaccines.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label-tw">Pesquisar Gestante</label>
            <input type="text"
                   name="patient_search"
                   class="input-tw"
                   placeholder="Nome da gestante..."
                   value="{{ request('patient_search') }}">
        </div>

        <div>
            <label class="label-tw">Tipo de Imunização</label>
            <select name="tipo_vacina" class="input-tw">
                <option value="">Todas</option>
                <option value="tetano" {{ request('tipo_vacina') === 'tetano' ? 'selected' : '' }}>Tétano (VAT)</option>
                <option value="iptp" {{ request('tipo_vacina') === 'iptp' ? 'selected' : '' }}>IPTp (Malária)</option>
                <option value="covid" {{ request('tipo_vacina') === 'covid' ? 'selected' : '' }}>COVID-19</option>
                <option value="hepatite_b" {{ request('tipo_vacina') === 'hepatite_b' ? 'selected' : '' }}>Hepatite B</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Status da Dose</label>
            <select name="status" class="input-tw">
                <option value="">Todos os status</option>
                <option value="administrada" {{ request('status') === 'administrada' ? 'selected' : '' }}>Administrada</option>
                <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="vencida" {{ request('status') === 'vencida' ? 'selected' : '' }}>Vencida</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw flex-1">
                <i class="fas fa-search text-xs"></i>
                <span>Filtrar</span>
            </button>
            <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Vaccines Table Card --}}
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                <i class="fas fa-syringe"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Registos de Imunização</h3>
        </div>
        <span class="badge-neutral font-medium">{{ $vaccines->total() }} registos</span>
    </div>

    @if($vaccines->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Vacina / Imunização</th>
                        <th>Dose</th>
                        <th>Data Prevista</th>
                        <th>Data Aplicação</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccines as $v)
                    <tr>
                        <td>
                            @if($v->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($v->patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('patients.show', $v->patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                            {{ $v->patient->nome_completo }}
                                        </a>
                                        <p class="text-2xs text-surface-400">BI: {{ $v->patient->documento_bi ?? 'N/D' }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-surface-400 italic">Gestante N/D</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-semibold text-surface-900">{{ $v->descricao ?? $v->tipo_vacina }}</span>
                        </td>
                        <td>
                            <span class="badge-info text-2xs font-semibold">Dose {{ $v->dose_numero ?? 1 }}</span>
                        </td>
                        <td>
                            <span class="text-xs text-surface-700">{{ $v->proxima_dose ? $v->proxima_dose->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td>
                            @if($v->data_administracao)
                                <span class="text-xs font-medium text-brand-700">{{ $v->data_administracao->format('d/m/Y') }}</span>
                            @else
                                <span class="text-2xs text-surface-400 italic">Pendente</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($v->status) {
                                    'administrada' => 'badge-success',
                                    'pendente' => 'badge-warning',
                                    'vencida' => 'badge-danger',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst($v->status) }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if($v->status !== 'administrada')
                                    <a href="{{ route('vaccines.edit', $v) }}"
                                       class="btn-primary-tw btn-sm-tw"
                                       title="Registar Aplicação">
                                        <i class="fas fa-syringe text-xs"></i>
                                        <span>Aplicar</span>
                                    </a>
                                @endif
                                <a href="{{ route('vaccines.show', $v) }}"
                                   class="btn-icon-tw"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $vaccines->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $vaccines->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $vaccines->total() }}</span> registos
            </p>
            <div>
                {{ $vaccines->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-syringe text-3xl text-surface-400"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhum registo de vacinação encontrado</h3>
            <p class="text-sm text-surface-500">Adicione uma nova vacinação ou ajuste os filtros.</p>
        </div>
    @endif
</div>
@endsection