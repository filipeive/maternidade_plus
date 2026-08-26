@extends('layouts.app-tw')

@section('title', 'Dashboard')
@section('page-title', 'Painel de Controle')
@section('title-icon', 'fa-grid-2')

@section('content')

{{-- ============================================================
     STAT CARDS
     ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    {{-- Total Gestantes --}}
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-person-pregnant"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value">{{ $totalGestantes ?? 0 }}</p>
            <p class="stat-card-label">Total de Gestantes</p>
        </div>
    </div>

    {{-- Consultas Esta Semana --}}
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-calendar-week"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value">{{ $consultasEstaSemana ?? 0 }}</p>
            <p class="stat-card-label">Consultas Esta Semana</p>
        </div>
    </div>

    {{-- Consultas Pendentes --}}
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-clock"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value">{{ $consultasPendentes ?? 0 }}</p>
            <p class="stat-card-label">Consultas Pendentes</p>
        </div>
    </div>

    {{-- Exames Pendentes --}}
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-flask-vial"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value">{{ $examesPendentes ?? 0 }}</p>
            <p class="stat-card-label">Exames Pendentes</p>
        </div>
    </div>

</div>

{{-- ============================================================
     QUICK ACTIONS
     ============================================================ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <a href="{{ route('patients.create') }}"
       class="card-tw p-4 flex flex-col items-center gap-2 text-center group hover:border-brand-300 transition-all duration-200">
        <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-user-plus"></i>
        </div>
        <span class="text-xs font-medium text-surface-600 group-hover:text-brand-700">Nova Gestante</span>
    </a>

    <a href="{{ route('consultations.create') }}"
       class="card-tw p-4 flex flex-col items-center gap-2 text-center group hover:border-gold-300 transition-all duration-200">
        <div class="w-10 h-10 rounded-xl bg-gold-100 text-gold-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-calendar-plus"></i>
        </div>
        <span class="text-xs font-medium text-surface-600 group-hover:text-gold-700">Nova Consulta</span>
    </a>

    <a href="{{ route('exams.create') }}"
       class="card-tw p-4 flex flex-col items-center gap-2 text-center group hover:border-ocean-300 transition-all duration-200">
        <div class="w-10 h-10 rounded-xl bg-ocean-100 text-ocean-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-microscope"></i>
        </div>
        <span class="text-xs font-medium text-surface-600 group-hover:text-ocean-700">Novo Exame</span>
    </a>

    <a href="{{ route('alertas.index') }}"
       class="card-tw p-4 flex flex-col items-center gap-2 text-center group hover:border-crimson-300 transition-all duration-200">
        <div class="w-10 h-10 rounded-xl bg-crimson-100 text-crimson-600 flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <span class="text-xs font-medium text-surface-600 group-hover:text-crimson-700">Ver Alertas</span>
    </a>
</div>

{{-- ============================================================
     MAIN CONTENT: Consultas + Alertas
     ============================================================ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Próximas Consultas (col-span-2) --}}
    <div class="xl:col-span-2 card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-sm">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Próximas Consultas</h3>
            </div>
            <a href="{{ route('consultations.create') }}" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-plus text-xs"></i>
                <span>Nova Consulta</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            @if(isset($proximasConsultas) && $proximasConsultas->count() > 0)
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Gestante</th>
                            <th>Data/Hora</th>
                            <th>Tipo</th>
                            <th>Semanas</th>
                            <th>Status</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proximasConsultas as $consulta)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-semibold shrink-0">
                                        {{ strtoupper(substr($consulta->patient->nome_completo ?? 'N', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-surface-900 truncate">{{ $consulta->patient->nome_completo }}</p>
                                        <p class="text-2xs text-surface-400">BI: {{ $consulta->patient->documento_bi }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="font-medium">{{ $consulta->data_consulta->format('d/m/Y') }}</p>
                                <p class="text-2xs text-surface-400">{{ $consulta->data_consulta->format('H:i') }}</p>
                            </td>
                            <td>
                                <span class="badge-info">{{ $consulta->tipo_consulta_label }}</span>
                            </td>
                            <td>
                                <span class="font-medium">{{ $consulta->semanas_gestacao ?? 'N/A' }}ª</span>
                            </td>
                            <td>
                                @if($consulta->status === 'confirmada')
                                    <span class="badge-success">
                                        <i class="fas fa-check-circle mr-1 text-2xs"></i>Confirmada
                                    </span>
                                @else
                                    <span class="badge-warning">
                                        <i class="fas fa-clock mr-1 text-2xs"></i>{{ ucfirst($consulta->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('consultations.show', $consulta) }}"
                                       class="btn-icon-tw w-8 h-8" title="Ver">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('consultations.edit', $consulta) }}"
                                       class="btn-icon-tw w-8 h-8" title="Editar">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                        <i class="fas fa-calendar-xmark text-2xl text-surface-400"></i>
                    </div>
                    <p class="text-surface-500 mb-3">Nenhuma consulta agendada para os próximos dias.</p>
                    <a href="{{ route('consultations.create') }}" class="btn-primary-tw">
                        <i class="fas fa-plus text-xs"></i>
                        <span>Agendar Consulta</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Sidebar: Alertas Precoces --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-600 flex items-center justify-center text-sm">
                    <i class="fas fa-bell"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Alertas Precoces</h3>
            </div>
            <a href="{{ route('alertas.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                Ver todos →
            </a>
        </div>

        <div class="divide-y divide-surface-100 max-h-96 overflow-y-auto">
            @if(isset($alertasPrecoces) && $alertasPrecoces->count() > 0)
                @foreach($alertasPrecoces as $alerta)
                <div class="px-5 py-3.5 hover:bg-surface-50/80 transition-colors">
                    <div class="flex items-start gap-3">
                        {{-- Severity Indicator --}}
                        <div class="shrink-0 mt-0.5">
                            @if($alerta->nivel === 'alto')
                                <span class="w-2.5 h-2.5 rounded-full bg-crimson-500 inline-block animate-pulse"></span>
                            @elseif($alerta->nivel === 'medio')
                                <span class="w-2.5 h-2.5 rounded-full bg-gold-500 inline-block"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-ocean-500 inline-block"></span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                @if($alerta->nivel === 'alto')
                                    <span class="badge-danger text-2xs">Alto</span>
                                @elseif($alerta->nivel === 'medio')
                                    <span class="badge-warning text-2xs">Médio</span>
                                @else
                                    <span class="badge-info text-2xs">Baixo</span>
                                @endif
                                <span class="text-2xs text-surface-400">{{ $alerta->tipo_label }}</span>
                            </div>

                            @if($alerta->patient)
                                <a href="{{ route('patients.show', $alerta->patient) }}"
                                   class="text-sm font-medium text-surface-900 hover:text-brand-600 transition-colors">
                                    {{ $alerta->patient->nome_completo }}
                                </a>
                            @endif

                            <p class="text-xs text-surface-500 mt-0.5 line-clamp-2">{{ $alerta->mensagem }}</p>

                            <div class="flex items-center justify-between mt-2">
                                <span class="text-2xs text-surface-400">
                                    <i class="fas fa-clock mr-0.5"></i>
                                    {{ $alerta->created_at->format('d/m H:i') }}
                                </span>
                                <a href="{{ route('alertas.index', ['search' => $alerta->patient?->nome_completo]) }}"
                                   class="text-2xs font-medium text-brand-600 hover:text-brand-700">
                                    Tratar →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="py-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-shield-check text-xl text-brand-400"></i>
                    </div>
                    <p class="text-sm text-surface-500">Nenhum alerta ativo</p>
                    <p class="text-2xs text-surface-400 mt-1">Todas as gestantes estão em bom estado</p>
                </div>
            @endif
        </div>

        {{-- Quick Links --}}
        <div class="card-footer-tw flex items-center gap-2">
            <a href="{{ route('alertas.metricas') }}" class="btn-ghost-tw btn-sm-tw flex-1 justify-center">
                <i class="fas fa-chart-line text-xs"></i>
                <span>Métricas</span>
            </a>
            <a href="{{ route('alertas.index') }}" class="btn-primary-tw btn-sm-tw flex-1 justify-center">
                <i class="fas fa-list text-xs"></i>
                <span>Todos os Alertas</span>
            </a>
        </div>
    </div>

</div>

{{-- ============================================================
     FOLLOW-UP ALERTS
     ============================================================ --}}
@if(isset($alertas) && $alertas->count() > 0)
<div class="mt-6">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-600 flex items-center justify-center text-sm">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Alertas de Acompanhamento</h3>
                <span class="badge-warning ml-1">{{ $alertas->count() }}</span>
            </div>
        </div>

        <div class="divide-y divide-surface-100">
            @foreach($alertas as $alerta)
            <div class="px-5 py-3.5 flex items-center gap-4 hover:bg-gold-50/30 transition-colors">
                <i class="fas fa-exclamation-triangle text-gold-500 shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <span class="font-medium text-surface-900">{{ $alerta['gestante'] }}</span>
                    <span class="text-surface-500 mx-1">·</span>
                    <span class="text-surface-600">{{ $alerta['mensagem'] }}</span>
                </div>
                <a href="{{ $alerta['link'] }}" class="btn-secondary-tw btn-sm-tw shrink-0">
                    <i class="fas fa-eye text-xs"></i>
                    <span>Detalhes</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
