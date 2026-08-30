@extends('layouts.app-tw')

@section('title', 'Consultas')
@section('page-title', 'Gestão de Consultas')
@section('title-icon', 'fa-calendar-check')

@section('breadcrumbs')
    <span class="active">Consultas</span>
@endsection

@section('content')
<div x-data="{ openModal: false, modalAction: '', patientName: '', obs: '', orient: '' }" class="space-y-6">

    {{-- Quick Action Modal para Conclusão / Agendamento da Próxima Consulta --}}
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-surface-900/60 backdrop-blur-xs transition-opacity" @click="openModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-surface-200">
                <form :action="modalAction" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-3 border-b border-surface-100 pb-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-lg font-bold">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-surface-900">Concluir Consulta & Agendar Próxima</h3>
                            <p class="text-xs text-surface-500">Paciente: <strong x-text="patientName"></strong></p>
                        </div>
                    </div>

                    <div>
                        <label class="label-tw">Notas Clínicas / Observações</label>
                        <textarea name="observacoes" x-model="obs" rows="2" class="input-tw" placeholder="Notas sobre o atendimento..."></textarea>
                    </div>

                    <div>
                        <label class="label-tw">Orientações Médicas para a Mãe</label>
                        <textarea name="orientacoes" x-model="orient" rows="2" class="input-tw" placeholder="Recomendações nutricionais, vacinação, sinais de alarme..."></textarea>
                    </div>

                    <div class="p-3 bg-brand-50/70 rounded-xl border border-brand-100 space-y-3">
                        <div>
                            <label class="label-tw text-brand-900 flex items-center gap-1.5">
                                <i class="fas fa-calendar-plus text-brand-600"></i>
                                <span>Agendar Próxima Consulta</span>
                            </label>
                            <input type="datetime-local" name="proxima_consulta" class="input-tw" value="{{ now()->addWeeks(4)->format('Y-m-d\TH:i') }}">
                        </div>

                        <div class="space-y-2 pt-1 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-surface-800">
                                <input type="checkbox" name="agendar_proxima" value="1" checked class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                                <span>Criar agendamento automático no sistema</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer font-medium text-brand-900">
                                <input type="checkbox" name="enviar_sms" value="1" checked class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                                <span>📱 Enviar SMS de lembrete com a data da próxima consulta</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                        <button type="button" @click="openModal = false" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                        <button type="submit" class="btn-primary-tw btn-sm-tw">
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Gravar & Notificar Paciente</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Top Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-surface-900">Lista de Consultas ANC & Puerpério</h2>
            <p class="text-sm text-surface-500">Gestão de consultas pré-natais, confirmações rápidas e agendamento de seguimento</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('consultations.create') }}" class="btn-primary-tw">
                <i class="fas fa-plus text-xs"></i>
                <span>Nova Consulta</span>
            </a>
            <a href="{{ route('home_visits.active-search') }}" class="btn-secondary-tw text-crimson-700 bg-crimson-50 border-crimson-200 hover:bg-crimson-100">
                <i class="fas fa-person-walking-arrow-right text-xs text-crimson-600"></i>
                <span>Busca Ativa Faltosas</span>
            </a>
            <a href="{{ route('exams.pending-results') }}" class="btn-secondary-tw">
                <i class="fas fa-flask text-xs text-gold-600"></i>
                <span>Exames Pendentes</span>
            </a>
        </div>
    </div>

    {{-- Quick Filter Pills --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="{{ route('consultations.index', ['hoje' => 1]) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all shrink-0 {{ request('hoje') ? 'bg-brand-600 text-white shadow-xs' : 'bg-white text-surface-700 hover:bg-surface-100 border border-surface-200' }}">
            <i class="fas fa-calendar-day"></i>
            <span>Consultas de Hoje</span>
        </a>

        <a href="{{ route('consultations.index', ['atrasadas' => 1]) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all shrink-0 {{ request('atrasadas') ? 'bg-crimson-600 text-white shadow-xs' : 'bg-white text-surface-700 hover:bg-surface-100 border border-surface-200' }}">
            <i class="fas fa-clock-rotate-left"></i>
            <span>Faltosas / Pendentes</span>
        </a>

        <a href="{{ route('consultations.index', ['tipo' => 'pos_parto']) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all shrink-0 {{ request('tipo') === 'pos_parto' ? 'bg-ocean-600 text-white shadow-xs' : 'bg-white text-surface-700 hover:bg-surface-100 border border-surface-200' }}">
            <i class="fas fa-baby"></i>
            <span>Consultas de Puerpério</span>
        </a>

        <a href="{{ route('consultations.index') }}"
           class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all shrink-0 {{ (!request('hoje') && !request('atrasadas') && !request('tipo') && !request('search') && !request('status')) ? 'bg-surface-800 text-white shadow-xs' : 'bg-white text-surface-700 hover:bg-surface-100 border border-surface-200' }}">
            <i class="fas fa-layer-group"></i>
            <span>Todas as Consultas</span>
        </a>
    </div>

    {{-- Filters Card --}}
    <div class="card-tw p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div class="lg:col-span-2">
                <label class="label-tw">Pesquisar Paciente</label>
                <input type="text" name="search" class="input-tw" placeholder="Nome, NIB, BI ou Telefone..." value="{{ request('search') }}">
            </div>

            <div>
                <label class="label-tw">Status</label>
                <select name="status" class="input-tw">
                    <option value="">Todos os status</option>
                    <option value="agendada" {{ request('status') === 'agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="confirmada" {{ request('status') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="realizada" {{ request('status') === 'realizada' ? 'selected' : '' }}>Realizada</option>
                    <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <div>
                <label class="label-tw">Tipo de Consulta</label>
                <select name="tipo" class="input-tw">
                    <option value="">Todos os tipos</option>
                    <option value="1_trimestre" {{ request('tipo') === '1_trimestre' ? 'selected' : '' }}>1º Trimestre</option>
                    <option value="2_trimestre" {{ request('tipo') === '2_trimestre' ? 'selected' : '' }}>2º Trimestre</option>
                    <option value="3_trimestre" {{ request('tipo') === '3_trimestre' ? 'selected' : '' }}>3º Trimestre</option>
                    <option value="pos_parto" {{ request('tipo') === 'pos_parto' ? 'selected' : '' }}>Pós-parto</option>
                    <option value="emergencia" {{ request('tipo') === 'emergencia' ? 'selected' : '' }}>Emergência</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary-tw btn-sm-tw flex-1">
                    <i class="fas fa-search text-xs"></i>
                    <span>Filtrar</span>
                </button>
                <a href="{{ route('consultations.index') }}" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-times text-xs"></i>
                    <span>Limpar</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Consultations Table --}}
    <div class="card-tw overflow-hidden">
        @if($consultations->count() > 0)
            <div class="overflow-x-auto">
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Gestante</th>
                            <th>Data / Hora</th>
                            <th>Tipo</th>
                            <th>Semanas</th>
                            <th>Status</th>
                            <th>Profissional</th>
                            <th class="text-right">Ações Rápidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                        @php
                            $patient = $consultation->patient;
                            $isAtrasada = in_array($consultation->status, ['agendada', 'confirmada', 'pendente']) && $consultation->data_consulta->isPast();
                            $temAlertaFaltosa = $patient && $patient->alertasAtivos->whereIn('tipo', ['gestante_faltosa', 'consulta_atrasada', 'faltosa_recorrente'])->count() > 0;
                            $temAlertaAlto = $patient && $patient->alertasAtivos->where('nivel', 'alto')->count() > 0;
                            
                            $rowClass = '';
                            if ($temAlertaAlto || $temAlertaFaltosa || $isAtrasada) {
                                $rowClass = $temAlertaAlto ? 'bg-crimson-50/40 hover:bg-crimson-50/70 border-l-4 border-l-crimson-500' : 'bg-gold-50/30 hover:bg-gold-50/60 border-l-4 border-l-gold-500';
                            }
                        @endphp
                        <tr class="{{ $rowClass }} transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full {{ $temAlertaAlto ? 'bg-crimson-100 text-crimson-700 border border-crimson-300' : ($temAlertaFaltosa || $isAtrasada ? 'bg-gold-100 text-gold-800 border border-gold-300' : 'bg-brand-100 text-brand-700') }} font-bold text-sm flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                                {{ $patient->nome_completo ?? 'Paciente N/D' }}
                                            </a>
                                            @if($isAtrasada || $temAlertaFaltosa)
                                                <span class="badge-danger text-3xs font-bold uppercase animate-pulse inline-flex items-center gap-1">
                                                    <i class="fas fa-clock-rotate-left"></i> Faltosa
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-2xs text-surface-400">BI: {{ $patient->documento_bi ?? 'N/D' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="font-medium text-surface-800 {{ $isAtrasada ? 'text-crimson-600 font-bold' : '' }}">{{ $consultation->data_consulta->format('d/m/Y') }}</p>
                                <p class="text-2xs text-surface-400">{{ $consultation->data_consulta->format('H:i') }}</p>
                            </td>
                            <td>
                                <span class="badge-info">{{ $consultation->tipo_consulta_label }}</span>
                            </td>
                            <td>
                                <span class="font-medium text-surface-800">{{ $consultation->semanas_gestacao ?? 'N/A' }}ª</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($consultation->status) {
                                        'realizada' => 'badge-success',
                                        'confirmada' => 'badge-info',
                                        'agendada' => $isAtrasada ? 'badge-danger' : 'badge-warning',
                                        'cancelada' => 'badge-danger',
                                        default => 'badge-neutral'
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">
                                    {{ $isAtrasada && $consultation->status === 'agendada' ? 'Atrasada / Faltosa' : ucfirst($consultation->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-xs text-surface-600">{{ $consultation->user->name ?? 'Sistema' }}</span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('consultations.show', $consultation) }}"
                                       class="btn-icon-tw"
                                       title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    @if($isAtrasada || $temAlertaFaltosa)
                                        <a href="{{ route('home_visits.active-search') }}"
                                           class="btn-tw bg-gold-400 hover:bg-gold-300 text-surface-950 btn-xs-tw font-bold"
                                           title="Encaminhar Busca Ativa APE">
                                            <i class="fas fa-person-walking text-3xs"></i>
                                            <span>APE</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('consultations.create', ['patient_id' => $consultation->patient_id]) }}"
                                       class="btn-icon-tw text-brand-600 hover:bg-brand-50"
                                       title="Agendar Nova Consulta Direta">
                                        <i class="fas fa-calendar-plus text-xs"></i>
                                    </a>

                                    @if($consultation->status !== 'realizada')
                                        <button type="button"
                                                @click="openModal = true; modalAction = '{{ route('consultations.complete', $consultation) }}'; patientName = '{{ addslashes($consultation->patient->nome_completo ?? '') }}'; obs = '{{ addslashes($consultation->observacoes ?? '') }}'; orient = '{{ addslashes($consultation->orientacoes ?? '') }}'"
                                                class="btn-primary-tw btn-xs-tw"
                                                title="Marcar como Realizada & Agendar Próxima">
                                            <i class="fas fa-check-double text-xs"></i>
                                            <span>Concluir</span>
                                        </button>
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
                    Mostrando <span class="font-medium text-surface-800">{{ $consultations->firstItem() ?? 0 }}</span> a
                    <span class="font-medium text-surface-800">{{ $consultations->lastItem() ?? 0 }}</span> de
                    <span class="font-medium text-surface-800">{{ $consultations->total() }}</span> consultas
                </p>
                <div>
                    {{ $consultations->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                    <i class="fas fa-calendar-xmark text-3xl text-surface-400"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhuma consulta encontrada</h3>
                <p class="text-sm text-surface-500 mb-4">Ajuste os filtros ou agende uma nova consulta pré-natal.</p>
                <a href="{{ route('consultations.create') }}" class="btn-primary-tw">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Agendar Consulta</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection