@extends('layouts.app-tw')

@section('title', 'Alertas de Imunização')
@section('page-title', 'Alertas de Vacinas Vencidas & Próximas Doses')
@section('title-icon', 'fa-bell')

@section('breadcrumbs')
    <a href="{{ route('vaccines.index') }}">Vacinas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Alertas</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Alertas do Plano de Imunização</h2>
        <p class="text-sm text-surface-500">Gestantes com doses de Tétano (VAT) ou IPTp-SP em atraso ou agendadas para os próximos 7 dias</p>
    </div>
    <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>Voltar às Vacinas</span>
    </a>
</div>

{{-- Doses Vencidas --}}
<div class="card-tw overflow-hidden mb-6 border-l-4 border-l-crimson-500">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Doses Vencidas (Atrasadas)</h3>
        </div>
        <span class="badge-danger font-medium">{{ $dosesVencidas->count() }} alertas</span>
    </div>

    @if($dosesVencidas->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Vacina / Imunização</th>
                        <th>Dose</th>
                        <th>Data Prevista</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dosesVencidas as $v)
                    <tr>
                        <td>
                            @if($v->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-crimson-100 text-crimson-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($v->patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
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
                            <span class="badge-danger text-2xs">Dose {{ $v->dose_numero ?? 1 }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-crimson-600">{{ $v->proxima_dose ? $v->proxima_dose->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('vaccines.edit', $v) }}" class="btn-primary-tw btn-sm-tw">
                                <i class="fas fa-syringe text-xs"></i>
                                <span>Aplicar Dose</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-10 text-center">
            <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-brand-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-2xl text-brand-500"></i>
            </div>
            <p class="text-sm text-surface-500">Nenhuma dose vencida no momento!</p>
        </div>
    @endif
</div>

{{-- Próximas Doses --}}
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm">
                <i class="fas fa-calendar-week"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Doses Agendadas para os Próximos 7 Dias</h3>
        </div>
        <span class="badge-warning font-medium">{{ $proximasDoses->count() }} agendadas</span>
    </div>

    @if($proximasDoses->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Vacina</th>
                        <th>Dose</th>
                        <th>Data Agendada</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proximasDoses as $v)
                    <tr>
                        <td>
                            @if($v->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gold-100 text-gold-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($v->patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
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
                            <span class="badge-warning text-2xs">Dose {{ $v->dose_numero ?? 1 }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-medium text-surface-800">{{ $v->proxima_dose ? $v->proxima_dose->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('vaccines.edit', $v) }}" class="btn-secondary-tw btn-sm-tw">
                                <i class="fas fa-syringe text-xs"></i>
                                <span>Antecipar / Aplicar</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-10 text-center text-surface-400">
            <i class="fas fa-calendar-check text-2xl mb-2"></i>
            <p class="text-sm">Nenhuma dose agendada para os próximos 7 dias.</p>
        </div>
    @endif
</div>
@endsection
