@extends('layouts.app-tw')

@section('title', 'Agenda Diária de Visitas Domiciliárias')
@section('page-title', 'Agenda Diária — ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))
@section('title-icon', 'fa-calendar-day')

@section('breadcrumbs')
    <a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Agenda Diária</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6" x-data="{ 
    viewMode: 'timeline',
    completeModalOpen: false,
    currentVisitId: null,
    currentPatientName: ''
}">

    {{-- HEADER DE CONTROLO DE DATA & AÇÕES --}}
    <div class="card-tw p-5">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Seletor de Data & Atalhos --}}
            <form method="GET" action="{{ route('home_visits.daily-schedule') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div class="relative min-w-[170px]">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="input-tw pl-9 text-xs font-semibold">
                </div>

                <div class="flex items-center bg-surface-100 p-1 rounded-xl border border-surface-200 text-xs">
                    <a href="{{ route('home_visits.daily-schedule', ['date' => now()->subDay()->format('Y-m-d')]) }}"
                       class="px-3 py-1.5 rounded-lg font-medium text-surface-700 hover:text-surface-900 hover:bg-white transition-all flex items-center gap-1">
                        <i class="fas fa-chevron-left text-2xs"></i> Ontem
                    </a>
                    <a href="{{ route('home_visits.daily-schedule', ['date' => now()->format('Y-m-d')]) }}"
                       class="px-3 py-1.5 rounded-lg font-bold shadow-xs transition-all flex items-center gap-1 {{ $date == now()->format('Y-m-d') ? 'bg-brand-600 text-white' : 'text-surface-700 hover:bg-white' }}">
                        <i class="fas fa-calendar-day text-2xs"></i> Hoje
                    </a>
                    <a href="{{ route('home_visits.daily-schedule', ['date' => now()->addDay()->format('Y-m-d')]) }}"
                       class="px-3 py-1.5 rounded-lg font-medium text-surface-700 hover:text-surface-900 hover:bg-white transition-all flex items-center gap-1">
                        Amanhã <i class="fas fa-chevron-right text-2xs"></i>
                    </a>
                </div>
            </form>

            {{-- Botões de Ação --}}
            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <a href="{{ route('home_visits.route-planning', ['date' => $date]) }}" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-route text-ocean-600"></i>
                    <span>Planeamento de Rota</span>
                </a>
                <a href="{{ route('home_visits.create') }}" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Nova Visita</span>
                </a>
            </div>
        </div>
    </div>

    {{-- STAT CARDS — RESUMO DO DIA --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $stats['total_agendadas'] ?? $visits->count() }}</p>
                <p class="stat-card-label">Visitas Agendadas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-house-medical-circle-check"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $stats['realizadas'] ?? $visits->where('status', 'realizada')->count() }}</p>
                <p class="stat-card-label">Visitas Realizadas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $stats['pendentes'] ?? $visits->where('status', 'agendada')->count() }}</p>
                <p class="stat-card-label">Visitas Pendentes</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
                <i class="fas fa-stopwatch"></i>
            </div>
            <div>
                <p class="stat-card-value text-base font-bold">
                    {{ floor(($stats['tempo_estimado'] ?? 120) / 60) }}h {{ ($stats['tempo_estimado'] ?? 120) % 60 }}m
                </p>
                <p class="stat-card-label">Tempo Est. de Percurso</p>
            </div>
        </div>
    </div>

    {{-- LISTA / TIMELINE DE VISITAS --}}
    <div class="card-tw overflow-hidden">
        {{-- Card Header --}}
        <div class="p-4 border-b border-surface-200 flex items-center justify-between bg-surface-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="font-bold text-surface-900 text-sm">Cronograma de Visitas</h3>
                    <p class="text-2xs text-surface-500">Programação detalhada para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Alternador de Modos de Vista --}}
            <div class="flex items-center bg-surface-200/70 p-1 rounded-xl text-xs font-semibold">
                <button @click="viewMode = 'timeline'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5"
                        :class="viewMode === 'timeline' ? 'bg-white text-brand-700 shadow-xs' : 'text-surface-600 hover:text-surface-900'">
                    <i class="fas fa-stream text-2xs"></i>
                    <span>Cronograma</span>
                </button>
                <button @click="viewMode = 'table'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5"
                        :class="viewMode === 'table' ? 'bg-white text-brand-700 shadow-xs' : 'text-surface-600 hover:text-surface-900'">
                    <i class="fas fa-list-check text-2xs"></i>
                    <span>Tabela</span>
                </button>
            </div>
        </div>

        @if($visits->count() > 0)
            {{-- VISTA 1: CRONOGRAMA / TIMELINE --}}
            <div x-show="viewMode === 'timeline'" class="p-6">
                <div class="relative border-l-2 border-surface-200 space-y-6 ml-4 pl-6">
                    @foreach($visits->sortBy('data_visita') as $visit)
                        @php
                            $isOverdue = $visit->data_visita->isPast() && $visit->status == 'agendada';
                            $isCompleted = $visit->status == 'realizada';
                        @endphp
                        <div class="relative group">
                            {{-- Timeline Node Icon --}}
                            <div class="absolute -left-[35px] top-1.5 w-6 h-6 rounded-full border-2 border-white shadow-xs flex items-center justify-center text-3xs font-bold transition-all
                                {{ $isCompleted ? 'bg-brand-600 text-white' : ($isOverdue ? 'bg-crimson-600 text-white' : 'bg-gold-500 text-white') }}">
                                @if($isCompleted)
                                    <i class="fas fa-check"></i>
                                @elseif($isOverdue)
                                    <i class="fas fa-exclamation"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                            </div>

                            {{-- Card da Visita --}}
                            <div class="bg-white rounded-xl border border-surface-200 p-4 shadow-2xs hover:shadow-md transition-all space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-surface-100 pb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm shrink-0">
                                            <i class="fas fa-person-pregnant"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('patients.show', $visit->patient_id) }}" class="font-bold text-surface-900 hover:text-brand-600 text-sm hover:underline block">
                                                {{ $visit->patient->nome_completo ?? 'Paciente Sem Nome' }}
                                            </a>
                                            <span class="text-2xs text-surface-500 font-mono">BI: {{ $visit->patient->documento_bi ?? 'N/A' }} · Contacto: {{ $visit->patient->contacto ?? $visit->patient->contacto_emergencia ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        {{-- Badge de Horário --}}
                                        <span class="px-2.5 py-1 rounded-lg text-2xs font-mono font-bold bg-surface-100 text-surface-800 flex items-center gap-1">
                                            <i class="fas fa-clock text-3xs text-surface-400"></i>
                                            {{ $visit->data_visita?->format('H:i') }}
                                        </span>

                                        {{-- Badge Tipo de Visita --}}
                                        @php
                                            $tipoBadge = match($visit->tipo_visita) {
                                                'puerperio_48h' => 'badge-success',
                                                'pos_parto' => 'badge-info',
                                                'alto_risco' => 'badge-danger',
                                                'faltosa' => 'badge-warning',
                                                'emergencia' => 'badge-danger',
                                                default => 'badge-neutral'
                                            };
                                        @endphp
                                        <span class="{{ $tipoBadge }} text-3xs capitalize">
                                            {{ str_replace('_', ' ', $visit->tipo_visita) }}
                                        </span>

                                        {{-- Badge Status --}}
                                        @php
                                            $statusBadge = match($visit->status) {
                                                'realizada' => 'badge-success',
                                                'agendada' => $isOverdue ? 'badge-danger' : 'badge-warning',
                                                'reagendada' => 'badge-info',
                                                'nao_encontrada' => 'badge-neutral',
                                                default => 'badge-neutral'
                                            };
                                        @endphp
                                        <span class="{{ $statusBadge }} text-3xs capitalize">
                                            {{ str_replace('_', ' ', $visit->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Endereço e Detalhes --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-surface-700">
                                    <div class="md:col-span-2 space-y-1">
                                        <p class="flex items-start gap-1.5 text-surface-600">
                                            <i class="fas fa-location-dot text-brand-500 mt-0.5 shrink-0"></i>
                                            <span><strong>Endereço:</strong> {{ $visit->endereco_visita ?? $visit->patient->endereco ?? 'N/A' }}</span>
                                        </p>
                                        @if($visit->motivo_visita)
                                            <p class="flex items-start gap-1.5 text-surface-600">
                                                <i class="fas fa-clipboard-list text-ocean-500 mt-0.5 shrink-0"></i>
                                                <span><strong>Motivo:</strong> {{ $visit->motivo_visita }}</span>
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Botões Rápidos da Visita --}}
                                    <div class="flex items-center justify-start md:justify-end gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-surface-100">
                                        <a href="{{ route('home_visits.show', $visit) }}" class="btn-secondary-tw btn-sm-tw py-1 px-2.5 text-3xs" title="Ver Ficha Completa">
                                            <i class="fas fa-eye text-3xs"></i>
                                            <span>Detalhes</span>
                                        </a>

                                        @if($visit->status === 'agendada')
                                            <button @click="
                                                currentVisitId = {{ $visit->id }};
                                                currentPatientName = '{{ addslashes($visit->patient->nome_completo ?? '') }}';
                                                completeModalOpen = true;
                                            " class="btn-tw bg-brand-600 hover:bg-brand-700 text-white text-3xs font-bold py-1 px-2.5 rounded-lg shadow-xs flex items-center gap-1">
                                                <i class="fas fa-check text-3xs"></i>
                                                <span>Concluir</span>
                                            </button>
                                        @endif

                                        @if(!empty($visit->endereco_visita))
                                            <a href="https://maps.google.com/?q={{ urlencode($visit->endereco_visita) }}" target="_blank" class="btn-icon-tw w-7 h-7 text-xs" title="Abrir GPS / Google Maps">
                                                <i class="fas fa-directions text-ocean-600"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- VISTA 2: TABELA DE VISITAS --}}
            <div x-show="viewMode === 'table'">
                <div class="table-container-tw">
                    <table class="table-tw">
                        <thead>
                            <tr>
                                <th>Horário</th>
                                <th>Paciente / Gestante</th>
                                <th>Tipo de Visita</th>
                                <th>Endereço / Bairro</th>
                                <th>Contacto</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits->sortBy('data_visita') as $visit)
                                @php
                                    $isOverdue = $visit->data_visita->isPast() && $visit->status == 'agendada';
                                @endphp
                                <tr>
                                    <td class="font-mono text-xs font-bold text-surface-900">
                                        {{ $visit->data_visita?->format('H:i') }}
                                        @if($isOverdue)
                                            <span class="block text-3xs text-crimson-600 font-normal">Atrasada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.show', $visit->patient_id) }}" class="font-bold text-surface-900 hover:text-brand-600 hover:underline block text-xs">
                                            {{ $visit->patient->nome_completo ?? 'Paciente' }}
                                        </a>
                                        <span class="text-3xs text-surface-400">BI: {{ $visit->patient->documento_bi ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-neutral text-3xs capitalize">
                                            {{ str_replace('_', ' ', $visit->tipo_visita) }}
                                        </span>
                                    </td>
                                    <td class="max-w-xs text-xs text-surface-700">
                                        <p class="truncate" title="{{ $visit->endereco_visita }}">{{ $visit->endereco_visita ?? 'N/A' }}</p>
                                    </td>
                                    <td class="font-mono text-xs text-surface-800">
                                        {{ $visit->patient->contacto ?? 'Sem Telefone' }}
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = match($visit->status) {
                                                'realizada' => 'badge-success',
                                                'agendada' => $isOverdue ? 'badge-danger' : 'badge-warning',
                                                'reagendada' => 'badge-info',
                                                'nao_encontrada' => 'badge-neutral',
                                                default => 'badge-neutral'
                                            };
                                        @endphp
                                        <span class="{{ $statusBadge }} text-3xs capitalize">
                                            {{ str_replace('_', ' ', $visit->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('home_visits.show', $visit) }}" class="btn-secondary-tw btn-xs-tw" title="Ver Detalhes">
                                                <i class="fas fa-eye text-3xs"></i>
                                            </a>
                                            @if($visit->status === 'agendada')
                                                <button @click="
                                                    currentVisitId = {{ $visit->id }};
                                                    currentPatientName = '{{ addslashes($visit->patient->nome_completo ?? '') }}';
                                                    completeModalOpen = true;
                                                " class="btn-tw bg-brand-600 hover:bg-brand-700 text-white text-3xs font-bold py-1 px-2 rounded-lg" title="Concluir Visita">
                                                    <i class="fas fa-check text-3xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="py-12 text-center text-surface-400 space-y-3">
                <div class="w-16 h-16 rounded-full bg-surface-100 text-surface-400 mx-auto flex items-center justify-center text-2xl">
                    <i class="fas fa-calendar-xmark"></i>
                </div>
                <h4 class="font-bold text-surface-800 text-sm">Sem visitas agendadas para esta data</h4>
                <p class="text-xs text-surface-500 max-w-sm mx-auto">
                    Não existem registos de visitas domiciliárias programadas para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.
                </p>
                <a href="{{ route('home_visits.create') }}" class="btn-primary-tw btn-sm-tw inline-flex">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Agendar Nova Visita Domiciliária</span>
                </a>
            </div>
        @endif
    </div>

    {{-- MODAL ALPINE.JS: CONCLUIR VISITA RÁPIDA --}}
    <div x-show="completeModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-2xl border border-surface-200 w-full max-w-lg p-6 space-y-4 text-left" @click.outside="completeModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-brand-600"></i> Concluir Visita Domiciliária
                </h3>
                <button @click="completeModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="'{{ url('/home_visits') }}/' + currentVisitId + '/complete'" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="p-3 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900">
                    Concluindo visita domiciliária para a paciente: <strong x-text="currentPatientName"></strong>
                </div>

                <div>
                    <label class="label-tw">Observações do Ambiente & Domicílio <span class="text-crimson-500">*</span></label>
                    <textarea name="observacoes_ambiente" rows="2" required placeholder="Descreva as condições habitacionais e higiênicas da residência..." class="input-tw text-xs"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label-tw">Condições de Higiene <span class="text-crimson-500">*</span></label>
                        <select name="condicoes_higiene" required class="input-tw text-xs">
                            <option value="bom">Bom / Adequado</option>
                            <option value="regular">Regular</option>
                            <option value="ruim">Precário / Deficiente</option>
                        </select>
                    </div>

                    <div>
                        <label class="label-tw">Apoio Familiar <span class="text-crimson-500">*</span></label>
                        <select name="apoio_familiar" required class="input-tw text-xs">
                            <option value="adequado">Adequado / Presente</option>
                            <option value="parcial">Parcial</option>
                            <option value="inadequado">Inexistente / Vulnerável</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Orientações e Recomendações Dadas <span class="text-crimson-500">*</span></label>
                    <textarea name="orientacoes_dadas" rows="2" required placeholder="Insira as principais orientações prestadas à paciente..." class="input-tw text-xs"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="necessita_referencia" name="necessita_referencia" value="1" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <label for="necessita_referencia" class="text-xs font-semibold text-surface-800">
                        Necessita de Referência ao Centro de Saúde / Hospital
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-surface-100">
                    <button type="button" @click="completeModalOpen = false" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-check text-xs"></i>
                        <span>Salvar e Registrar Conclusão</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection