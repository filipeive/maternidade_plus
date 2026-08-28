@extends('layouts.app-tw')

@section('title', 'Busca Ativa Comunitária')
@section('page-title', 'Busca Ativa de Pacientes Faltosas')
@section('title-icon', 'fa-person-walking-arrow-right')

@section('breadcrumbs')
    <a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Busca Ativa Comunitária</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ 
    selectedPatients: [],
    selectAll: false,
    batchModalOpen: false,
    referModalOpen: false,
    currentPatient: null,

    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedPatients = Array.from(document.querySelectorAll('.patient-checkbox')).map(el => el.value);
        } else {
            this.selectedPatients = [];
        }
    },
    openReferModal(patient) {
        this.currentPatient = patient;
        this.referModalOpen = true;
    }
}">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <p class="stat-card-value text-crimson-600">{{ $stats['total_faltosas'] ?? count($faltosas) }}</p>
                <p class="stat-card-label">Pacientes Faltosas Identificadas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
                <i class="fas fa-person-walking"></i>
            </div>
            <div>
                <p class="stat-card-value text-gold-700">{{ $stats['visitas_agendadas'] ?? 0 }}</p>
                <p class="stat-card-label">Visitas de Busca Ativa em Curso</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <p class="stat-card-value text-brand-700">{{ $stats['recuperadas_mes'] ?? 0 }}</p>
                <p class="stat-card-label">Gestantes Recuperadas no Mês</p>
            </div>
        </div>
    </div>

    {{-- Header de Navegação e Ações em Lote --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-surface-900 flex items-center gap-2">
                <i class="fas fa-person-walking-arrow-right text-brand-600"></i>
                <span>Gestantes e Puérperas Faltosas</span>
            </h2>
            <p class="text-sm text-surface-500">Módulo de coordenação entre Enfermeiras de SMI e Activistas Comunitárias (APEs) para recuperação ao cuidado</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" 
                    @click="batchModalOpen = true" 
                    :disabled="selectedPatients.length === 0"
                    class="btn-primary-tw disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-calendar-plus text-xs"></i>
                <span>Agendar em Lote (<span x-text="selectedPatients.length"></span>)</span>
            </button>
            <a href="{{ route('home_visits.index') }}" class="btn-secondary-tw">
                <i class="fas fa-list text-xs"></i>
                <span>Todas as Visitas</span>
            </a>
        </div>
    </div>

    {{-- Filtros e Barra de Pesquisa --}}
    <div class="card-tw p-4">
        <form method="GET" action="{{ route('home_visits.active-search') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Pesquisar por nome da gestante, BI, contacto ou bairro..." 
                       class="input-tw pl-9 text-xs">
            </div>
            <button type="submit" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-filter text-xs"></i>
                <span>Filtrar</span>
            </button>
            @if($search)
                <a href="{{ route('home_visits.active-search') }}" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-times text-xs"></i>
                    <span>Limpar</span>
                </a>
            @endif
        </form>
    </div>

    {{-- Tabela de Pacientes Faltosas --}}
    <div class="card-tw overflow-hidden">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Lista de Busca Ativa Prioritária</h3>
            </div>
            <span class="badge-neutral font-medium">{{ count($faltosas) }} pacientes faltosas</span>
        </div>

        @if(count($faltosas) > 0)
            <div class="overflow-x-auto">
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">
                                <input type="checkbox" 
                                       x-model="selectAll" 
                                       @change="toggleSelectAll()" 
                                       class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                            </th>
                            <th>Paciente / Gestante</th>
                            <th>Consulta em Falta</th>
                            <th>Atraso</th>
                            <th>Endereço & Contactos</th>
                            <th>Estado da Visita</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faltosas as $patient)
                            @php
                                $lastMissed = $patient->consultations->first();
                                $daysOverdue = $lastMissed && $lastMissed->data_consulta ? (int) $lastMissed->data_consulta->diffInDays(now()) : 0;
                                $pendingVisit = $patient->homeVisits->where('status', 'agendada')->first();
                            @endphp
                            <tr class="hover:bg-surface-50/70 transition-colors">
                                <td class="text-center">
                                    <input type="checkbox" 
                                           value="{{ $patient->id }}" 
                                           x-model="selectedPatients"
                                           class="patient-checkbox rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('patients.show', $patient) }}" class="font-bold text-surface-900 hover:text-brand-600 transition-colors text-xs">
                                                {{ $patient->nome_completo }}
                                            </a>
                                            <p class="text-2xs text-surface-500 font-mono">BI: {{ $patient->documento_bi ?? 'N/D' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($lastMissed)
                                        <p class="text-xs font-semibold text-surface-800">
                                            {{ $lastMissed->data_consulta ? $lastMissed->data_consulta->format('d/m/Y') : 'Data N/D' }}
                                        </p>
                                        <p class="text-2xs text-surface-500">{{ ucfirst($lastMissed->tipo_consulta ?? 'Consulta CPN') }}</p>
                                    @else
                                        <span class="text-surface-400 text-xs italic">Sem dados de consulta</span>
                                    @endif
                                </td>
                                <td>
                                    @if($daysOverdue > 14)
                                        <span class="badge-danger font-bold text-3xs">
                                            <i class="fas fa-exclamation-circle mr-0.5"></i> {{ $daysOverdue }} dias atrasada
                                        </span>
                                    @elseif($daysOverdue > 7)
                                        <span class="badge-warning font-semibold text-3xs">
                                            {{ $daysOverdue }} dias
                                        </span>
                                    @else
                                        <span class="badge-neutral text-3xs">
                                            {{ $daysOverdue }} dias
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <p class="text-xs text-surface-800 flex items-center gap-1">
                                        <i class="fas fa-location-dot text-brand-500 text-3xs"></i>
                                        <span class="truncate max-w-[180px]">{{ $patient->endereco ?? 'Sem endereço' }}</span>
                                    </p>
                                    <p class="text-2xs text-surface-500">
                                        <i class="fas fa-phone text-surface-400 text-3xs"></i> {{ $patient->contacto ?? 'Sem telefone' }}
                                        @if($patient->parceiro_contacto)
                                            · Parceiro: {{ $patient->parceiro_contacto }}
                                        @endif
                                    </p>
                                </td>
                                <td>
                                    @if($pendingVisit)
                                        <span class="badge-info text-3xs flex items-center gap-1 w-fit">
                                            <i class="fas fa-calendar-check"></i>
                                            <span>Visita {{ $pendingVisit->data_visita ? $pendingVisit->data_visita->format('d/m') : '' }}</span>
                                        </span>
                                        <p class="text-3xs text-surface-400 mt-0.5">Resp: {{ $pendingVisit->user->name ?? 'Activista' }}</p>
                                    @else
                                        <span class="badge-danger text-3xs">Pendente de Busca</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($pendingVisit)
                                            <a href="{{ route('home_visits.show', $pendingVisit) }}" 
                                               class="btn-secondary-tw btn-xs-tw" 
                                               title="Ver Visita Agendada">
                                                <i class="fas fa-eye text-3xs"></i>
                                                <span>Ver</span>
                                            </a>
                                            <form method="POST" action="{{ route('home_visits.resolve-at-facility', $pendingVisit) }}" onsubmit="return confirm('Confirmar que a gestante compareceu à US e a visita de campo não é mais necessária?');" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="motivo_resolucao" value="Paciente compareceu espontaneamente à Unidade Sanitária.">
                                                <button type="submit" class="btn-secondary-tw btn-xs-tw text-brand-600 hover:text-brand-700" title="Marcar como Atendida na US">
                                                    <i class="fas fa-circle-check text-3xs text-brand-500"></i>
                                                    <span>Atendida US</span>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" 
                                                    @click="openReferModal({{ json_encode($patient) }})" 
                                                    class="btn-primary-tw btn-xs-tw">
                                                <i class="fas fa-paper-plane text-3xs"></i>
                                                <span>Encaminhar</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600">
                    <i class="fas fa-shield-heart text-3xl"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhuma gestante faltosa no momento!</h3>
                <p class="text-sm text-surface-500">Todas as consultas e seguimentos clínicos estão em dia.</p>
            </div>
        @endif
    </div>

    {{-- MODAL DE ENCAMINHAMENTO INDIVIDUAL PARA ACTIVISTA --}}
    <div x-show="referModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/50 backdrop-blur-xs"
         @keydown.escape.window="referModalOpen = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 space-y-5 border border-surface-200" @click.away="referModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-person-walking"></i>
                    </div>
                    <h3 class="font-bold text-surface-900 text-sm">Encaminhar para Visita Comunitária</h3>
                </div>
                <button type="button" @click="referModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('home_visits.refer-patient') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="patient_id" :value="currentPatient ? currentPatient.id : ''">
                <input type="hidden" name="tipo_visita" value="faltosa">

                <div class="p-3 bg-surface-50 rounded-xl border border-surface-200 text-xs">
                    <p class="font-bold text-surface-900" x-text="currentPatient ? currentPatient.nome_completo : ''"></p>
                    <p class="text-surface-500" x-text="currentPatient ? 'Endereço: ' + (currentPatient.endereco || 'Sem endereço') : ''"></p>
                    <p class="text-surface-500" x-text="currentPatient ? 'Contacto: ' + (currentPatient.contacto || 'Sem telefone') : ''"></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label-tw">Data Prevista da Visita <span class="text-crimson-500">*</span></label>
                        <input type="date" name="data_visita" value="{{ now()->addDay()->format('Y-m-d') }}" required class="input-tw text-xs">
                    </div>
                    <div>
                        <label class="label-tw">Atribuir a Activista / Agente</label>
                        <select name="user_id" class="input-tw text-xs">
                            <option value="">Equipa Comunitária Geral</option>
                            @foreach($communityAgents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Motivo do Encaminhamento <span class="text-crimson-500">*</span></label>
                    <input type="text" name="motivo_visita" value="Busca ativa de gestante faltosa às consultas de rotina MISAU" required class="input-tw text-xs">
                </div>

                <div>
                    <label class="label-tw">Recomendações e Instruções para a Activista</label>
                    <textarea name="observacoes_gerais" rows="3" placeholder="Ex: Alertar para sinais de perigo, orientar cumprimento do calendário CPN e trazer o parceiro..." class="input-tw text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                    <button type="button" @click="referModalOpen = false" class="btn-secondary-tw btn-sm-tw">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Confirmar Encaminhamento</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DE AGENDAMENTO EM LOTE --}}
    <div x-show="batchModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/50 backdrop-blur-xs"
         @keydown.escape.window="batchModalOpen = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 space-y-5 border border-surface-200" @click.away="batchModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3 class="font-bold text-surface-900 text-sm">Agendamento em Lote de Busca Ativa</h3>
                </div>
                <button type="button" @click="batchModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('home_visits.schedule-active-search') }}" class="space-y-4">
                @csrf
                <template x-for="id in selectedPatients" :key="id">
                    <input type="hidden" name="patient_ids[]" :value="id">
                </template>

                <div class="p-3 bg-gold-50 border border-gold-200 rounded-xl text-xs text-gold-900">
                    <p class="font-bold"><i class="fas fa-info-circle mr-1"></i> <span x-text="selectedPatients.length"></span> pacientes selecionadas para busca ativa comunitária.</p>
                </div>

                <div>
                    <label class="label-tw">Data Programada para as Visitas <span class="text-crimson-500">*</span></label>
                    <input type="date" name="data_visita" value="{{ now()->addDay()->format('Y-m-d') }}" required class="input-tw text-xs">
                </div>

                <div>
                    <label class="label-tw">Atribuir a Activista / Agente Responsável</label>
                    <select name="user_id" class="input-tw text-xs">
                        <option value="">Atribuir a mim ({{ auth()->user()->name }})</option>
                        @foreach($communityAgents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                    <button type="button" @click="batchModalOpen = false" class="btn-secondary-tw btn-sm-tw">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-calendar-check text-xs"></i>
                        <span>Gerar Visitas Domiciliárias</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
