@extends('layouts.app-tw')

@section('title', 'Visitas Domiciliárias')
@section('page-title', 'Gestão de Visitas Domiciliárias')
@section('title-icon', 'fa-house-medical')

@section('breadcrumbs')
    <span class="active">Visitas Domiciliárias</span>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $stats['agendadas_hoje'] ?? 0 }}</p>
            <p class="stat-card-label">Agendadas Hoje</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $stats['realizadas_semana'] ?? 0 }}</p>
            <p class="stat-card-label">Realizadas esta Semana</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $stats['atrasadas'] ?? 0 }}</p>
            <p class="stat-card-label">Visitas Atrasadas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $stats['total_mes'] ?? 0 }}</p>
            <p class="stat-card-label">Total do Mês</p>
        </div>
    </div>
</div>

{{-- Header Action --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Acompanhamento no Terreno</h2>
        <p class="text-sm text-surface-500">Agendamento e registo de visitas domiciliárias a gestantes de risco ou faltosas</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('home_visits.create') }}" class="btn-primary-tw">
            <i class="fas fa-plus text-xs"></i>
            <span>Nova Visita</span>
        </a>
        <a href="{{ route('home_visits.active-search') }}" class="btn-secondary-tw text-crimson-700 bg-crimson-50 border-crimson-200 hover:bg-crimson-100">
            <i class="fas fa-person-walking-arrow-right text-xs text-crimson-600"></i>
            <span>Busca Ativa Faltosas</span>
        </a>
        <a href="{{ route('home_visits.daily-schedule') }}" class="btn-secondary-tw">
            <i class="fas fa-calendar-day text-xs text-gold-600"></i>
            <span>Agenda de Hoje</span>
        </a>
        <a href="{{ route('home_visits.route-planning') }}" class="btn-secondary-tw">
            <i class="fas fa-route text-xs text-brand-600"></i>
            <span>Rota de Visitas</span>
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('home_visits.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label-tw">Pesquisar Gestante</label>
            <input type="text"
                   name="search"
                   class="input-tw"
                   placeholder="Nome ou BI..."
                   value="{{ request('search') }}">
        </div>

        <div>
            <label class="label-tw">Status da Visita</label>
            <select name="status" class="input-tw">
                <option value="">Todos os status</option>
                <option value="agendada" {{ request('status') === 'agendada' ? 'selected' : '' }}>Agendada</option>
                <option value="realizada" {{ request('status') === 'realizada' ? 'selected' : '' }}>Realizada</option>
                <option value="nao_encontrada" {{ request('status') === 'nao_encontrada' ? 'selected' : '' }}>Não Encontrada</option>
                <option value="reagendada" {{ request('status') === 'reagendada' ? 'selected' : '' }}>Reagendada</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Motivo da Visita</label>
            <select name="motivo" class="input-tw">
                <option value="">Todos os motivos</option>
                <option value="busca_ativa_falta" {{ request('motivo') === 'busca_ativa_falta' ? 'selected' : '' }}>Busca Ativa por Falta</option>
                <option value="seguimento_alto_risco" {{ request('motivo') === 'seguimento_alto_risco' ? 'selected' : '' }}>Seguimento de Alto Risco</option>
                <option value="pos_parto" {{ request('motivo') === 'pos_parto' ? 'selected' : '' }}>Acompanhamento Pós-parto</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw flex-1">
                <i class="fas fa-search text-xs"></i>
                <span>Filtrar</span>
            </button>
            <a href="{{ route('home_visits.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Visits Table Card --}}
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                <i class="fas fa-house-medical"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Visitas Domiciliárias Registadas</h3>
        </div>
        <span class="badge-neutral font-medium">{{ $visits->total() }} registos</span>
    </div>

    @if($visits->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Data Agendada</th>
                        <th>Gestante</th>
                        <th>Motivo</th>
                        <th>Status</th>
                        <th>Agente / Profissional</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visits as $visit)
                    <tr>
                        <td>
                            <p class="font-medium text-surface-800 text-xs">{{ $visit->data_visita ? $visit->data_visita->format('d/m/Y') : '-' }}</p>
                            @if($visit->horario_previsto)
                                <p class="text-2xs text-surface-400">{{ $visit->horario_previsto }}</p>
                            @endif
                        </td>
                        <td>
                            @if($visit->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($visit->patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                            {{ $visit->patient->nome_completo }}
                                        </a>
                                        <p class="text-2xs text-surface-400">BI: {{ $visit->patient->documento_bi ?? 'N/D' }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-surface-400 italic">Gestante N/D</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-medium text-surface-900 text-xs">{{ $visit->motivo_label ?? ucfirst(str_replace('_', ' ', $visit->motivo_visita)) }}</span>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($visit->status) {
                                    'realizada' => 'badge-success',
                                    'agendada' => 'badge-warning',
                                    'nao_encontrada' => 'badge-danger',
                                    'reagendada' => 'badge-info',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $visit->status)) }}</span>
                        </td>
                        <td>
                            <span class="text-xs text-surface-600">{{ $visit->user->name ?? 'Agente Comunitário' }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('home_visits.show', $visit) }}"
                                   class="btn-icon-tw"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($visit->status === 'agendada')
                                    <form method="POST" action="{{ route('home_visits.resolve-at-facility', $visit) }}" onsubmit="return confirm('Marcar esta visita como atendida na US (dispensa deslocação ao terreno)?');" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="motivo_resolucao" value="Paciente compareceu espontaneamente à Unidade Sanitária.">
                                        <button type="submit" class="btn-icon-tw text-brand-600 hover:text-brand-700 hover:bg-brand-50" title="Paciente Atendida na US (Dispensar Visita)">
                                            <i class="fas fa-circle-check text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('home_visits.edit', $visit) }}"
                                   class="btn-icon-tw"
                                   title="Editar / Actualizar">
                                    <i class="fas fa-pen text-xs"></i>
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
                Mostrando <span class="font-medium text-surface-800">{{ $visits->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $visits->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $visits->total() }}</span> visitas
            </p>
            <div>
                {{ $visits->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-house-chimney-medical text-3xl text-surface-400"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhuma visita domiciliária encontrada</h3>
            <p class="text-sm text-surface-500 mb-4">Agende uma nova visita para acompanhamento no terreno.</p>
            <a href="{{ route('home_visits.create') }}" class="btn-primary-tw">
                <i class="fas fa-plus text-xs"></i>
                <span>Agendar Nova Visita</span>
            </a>
        </div>
    @endif
</div>
@endsection