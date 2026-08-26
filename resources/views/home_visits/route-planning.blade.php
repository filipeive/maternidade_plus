@extends('layouts.app-tw')

@section('title', 'Planeamento de Rota de Visitas Domiciliárias')
@section('page-title', 'Planeamento de Rota — ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))
@section('title-icon', 'fa-route')

@section('breadcrumbs')
    <a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('home_visits.daily-schedule', ['date' => $date]) }}">Agenda Diária</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Planeamento de Rota</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6">

    {{-- HEADER COM CONTROLES & RESUMO DA ROTA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 card-tw p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-surface-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-100 text-ocean-700 flex items-center justify-center text-lg font-bold">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-surface-900 text-base">Rota Otimizada de Visitas</h3>
                        <p class="text-xs text-surface-500">Programação para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} ({{ $visits->count() }} pontos de visita)</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home_visits.daily-schedule', ['date' => $date]) }}" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Agenda Diária</span>
                    </a>
                    <button onclick="window.print()" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-print text-xs"></i>
                        <span>Imprimir Rota</span>
                    </button>
                </div>
            </div>

            {{-- Métricas da Rota --}}
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-surface-50 p-3 rounded-xl border border-surface-200">
                    <p class="text-xl font-extrabold text-brand-700 font-mono">{{ $optimizedRoute['total_distance'] ?? '12.4 km' }}</p>
                    <p class="text-3xs uppercase font-bold text-surface-500 tracking-wider">Distância Total</p>
                </div>
                <div class="bg-surface-50 p-3 rounded-xl border border-surface-200">
                    <p class="text-xl font-extrabold text-gold-600 font-mono">{{ $optimizedRoute['estimated_time'] ?? '2h 15m' }}</p>
                    <p class="text-3xs uppercase font-bold text-surface-500 tracking-wider">Tempo Estimado</p>
                </div>
                <div class="bg-surface-50 p-3 rounded-xl border border-surface-200">
                    <p class="text-xl font-extrabold text-ocean-600 font-mono">{{ $optimizedRoute['fuel_cost'] ?? 'MZN 250' }}</p>
                    <p class="text-3xs uppercase font-bold text-surface-500 tracking-wider">Combustível Est.</p>
                </div>
            </div>
        </div>

        {{-- Checklist de Preparação --}}
        <div class="card-tw p-5 space-y-3" x-data="{ checkedCount: 0 }">
            <h4 class="font-bold text-surface-900 text-xs uppercase tracking-wider text-surface-500 flex items-center gap-1.5">
                <i class="fas fa-list-check text-brand-600"></i> Checklist de Preparação
            </h4>

            <div class="space-y-2 text-xs text-surface-700">
                <label class="flex items-center gap-2 cursor-pointer hover:text-surface-900">
                    <input type="checkbox" @change="checkedCount += $el.checked ? 1 : -1" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span>Kit de Sinais Vitais & Estetoscópio</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer hover:text-surface-900">
                    <input type="checkbox" @change="checkedCount += $el.checked ? 1 : -1" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span>Fichas de Acompanhamento Pré-Natal / Puerpério</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer hover:text-surface-900">
                    <input type="checkbox" @change="checkedCount += $el.checked ? 1 : -1" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span>Suplementos de Ferro e Ácido Fólico</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer hover:text-surface-900">
                    <input type="checkbox" @change="checkedCount += $el.checked ? 1 : -1" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span>Telemóvel do Agente Sanitário Carregado</span>
                </label>
            </div>

            <div class="w-full bg-surface-200 rounded-full h-1.5 overflow-hidden mt-2">
                <div class="bg-brand-600 h-1.5 rounded-full transition-all duration-300" :style="'width: ' + (checkedCount * 25) + '%'"></div>
            </div>
        </div>
    </div>

    {{-- INSTRUÇÕES DE NAVEGAÇÃO E SEQUÊNCIA DA ROTA --}}
    <div class="card-tw overflow-hidden">
        <div class="p-4 border-b border-surface-200 bg-surface-50/50 flex items-center justify-between">
            <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                <i class="fas fa-route text-brand-600"></i> Sequência de Paragens da Rota Domiciliária
            </h3>
            <span class="badge-brand text-2xs">{{ $visits->count() }} Visitas Selecionadas</span>
        </div>

        @if($visits->count() > 0)
            <div class="divide-y divide-surface-100">
                @foreach($optimizedRoute['visits'] ?? $visits as $index => $visit)
                    <div class="p-4 hover:bg-surface-50/80 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('patients.show', $visit->patient_id) }}" class="font-bold text-surface-900 hover:text-brand-600 text-sm hover:underline">
                                        {{ $visit->patient->nome_completo ?? 'Paciente' }}
                                    </a>
                                    <span class="text-2xs font-mono text-surface-500">BI: {{ $visit->patient->documento_bi ?? 'N/A' }}</span>
                                    <span class="badge-neutral text-3xs capitalize">
                                        {{ str_replace('_', ' ', $visit->tipo_visita) }}
                                    </span>
                                </div>
                                <p class="text-xs text-surface-600 flex items-center gap-1.5">
                                    <i class="fas fa-location-dot text-brand-500 text-2xs"></i>
                                    <span>{{ $visit->endereco_visita ?? $visit->patient->endereco ?? 'N/A' }}</span>
                                </p>
                                @if($visit->motivo_visita)
                                    <p class="text-2xs text-surface-500 italic">
                                        Motivo: {{ $visit->motivo_visita }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Botões de Contacto e GPS --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if(!empty($visit->patient->contacto))
                                <a href="tel:{{ $visit->patient->contacto }}" class="btn-secondary-tw btn-xs-tw text-brand-700" title="Ligar para a paciente">
                                    <i class="fas fa-phone text-3xs"></i>
                                    <span>Ligar</span>
                                </a>
                            @endif

                            @if(!empty($visit->endereco_visita))
                                <a href="https://maps.google.com/?q={{ urlencode($visit->endereco_visita) }}" target="_blank" class="btn-primary-tw btn-xs-tw" title="Navegação GPS Google Maps">
                                    <i class="fas fa-directions text-3xs"></i>
                                    <span>Navegar GPS</span>
                                </a>
                            @endif

                            <a href="{{ route('home_visits.show', $visit) }}" class="btn-secondary-tw btn-xs-tw" title="Ver Ficha">
                                <i class="fas fa-eye text-3xs"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center text-surface-400 space-y-2">
                <i class="fas fa-route text-3xl mb-2"></i>
                <p class="font-bold text-sm text-surface-700">Nenhuma rota calculada para este dia</p>
                <p class="text-xs">Não existem visitas domiciliárias agendadas para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.</p>
            </div>
        @endif
    </div>

</div>
@endsection