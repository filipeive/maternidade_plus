@extends('layouts.app-tw')

@section('title', 'Central de Notificações & SMS')
@section('page-title', 'Central de Notificações & Comunicação SMS')
@section('title-icon', 'fa-bell')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Notificações & SMS</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6" x-data="{
    activeTab: '{{ request('tab', 'notificacoes') }}',
    openModalSingle: false,
    selectedPatient: null,
    messageText: '',
    selectedTemplate: ''
}">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card cursor-pointer hover:shadow-md transition-shadow" @click="activeTab = 'notificacoes'">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $notifStats['nao_lidas'] ?? 0 }}</p>
                <p class="stat-card-label">Notificações Não Lidas ({{ $notifStats['total'] ?? 0 }} total)</p>
            </div>
        </div>

        <div class="stat-card cursor-pointer hover:shadow-md transition-shadow" @click="activeTab = 'faltosas'">
            <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $totalFaltosas ?? 0 }}</p>
                <p class="stat-card-label">Pacientes Faltosas</p>
            </div>
        </div>

        <div class="stat-card cursor-pointer hover:shadow-md transition-shadow" @click="activeTab = 'logs'">
            <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $totalEnviadosMes ?? 0 }}</p>
                <p class="stat-card-label">SMS Enviados este Mês ({{ $taxaSucesso ?? 100 }}% sucesso)</p>
            </div>
        </div>

        <div class="stat-card cursor-pointer hover:shadow-md transition-shadow" @click="activeTab = 'notificacoes'">
            <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $notifStats['alertas'] ?? 0 }}</p>
                <p class="stat-card-label">Alertas Clínicos Ativos</p>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-surface-200 gap-3">
        <div class="flex items-center gap-2 sm:gap-4 overflow-x-auto pb-1">
            <button @click="activeTab = 'notificacoes'"
                    class="py-3 px-2 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'notificacoes' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-bell"></i>
                <span>Notificações do Sistema</span>
                @if(($notifStats['nao_lidas'] ?? 0) > 0)
                    <span class="bg-crimson-500 text-white text-2xs font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                        {{ $notifStats['nao_lidas'] }}
                    </span>
                @endif
            </button>

            <button @click="activeTab = 'faltosas'"
                    class="py-3 px-2 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'faltosas' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-user-xmark"></i>
                <span>Pacientes Faltosas ({{ $totalFaltosas ?? 0 }})</span>
            </button>

            <button @click="activeTab = 'nova_mensagem'"
                    class="py-3 px-2 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'nova_mensagem' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-paper-plane"></i>
                <span>Enviar SMS Individual</span>
            </button>

            <button @click="activeTab = 'logs'"
                    class="py-3 px-2 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'logs' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Histórico de SMS</span>
            </button>

            <button @click="activeTab = 'modelos'"
                    class="py-3 px-2 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'modelos' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-file-lines"></i>
                <span>Modelos MISAU</span>
            </button>
        </div>

        {{-- Actions based on Active Tab --}}
        <div class="flex items-center gap-2 mb-2 sm:mb-0">
            <template x-if="activeTab === 'notificacoes'">
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-check-double text-xs"></i>
                        <span>Marcar todas como lidas</span>
                    </button>
                </form>
            </template>

            <template x-if="activeTab === 'faltosas'">
                <button onclick="document.getElementById('modalBulkSms').classList.remove('hidden')" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Disparo em Massa para Faltosas</span>
                </button>
            </template>
        </div>
    </div>

    {{-- ============================================================
         TAB 1: NOTIFICAÇÕES DO SISTEMA
         ============================================================ --}}
    <div x-show="activeTab === 'notificacoes'" class="space-y-4" x-cloak>

        {{-- Search & Filters --}}
        <div class="card-tw p-4">
            <form method="GET" action="{{ route('notifications.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <input type="hidden" name="tab" value="notificacoes">

                <div class="sm:col-span-5 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Pesquisar por título, paciente ou mensagem..."
                           class="input-tw pl-9 text-xs w-full">
                </div>

                <div class="sm:col-span-3">
                    <select name="tipo" class="input-tw text-xs w-full">
                        <option value="todos" {{ request('tipo') == 'todos' ? 'selected' : '' }}>Todos os Tipos</option>
                        <option value="alerta_clinico" {{ request('tipo') == 'alerta_clinico' ? 'selected' : '' }}>Alerta Clínico</option>
                        <option value="consulta_faltosa" {{ request('tipo') == 'consulta_faltosa' ? 'selected' : '' }}>Consulta Faltosa</option>
                        <option value="exame_pronto" {{ request('tipo') == 'exame_pronto' ? 'selected' : '' }}>Exame Pronto</option>
                        <option value="vacina_atraso" {{ request('tipo') == 'vacina_atraso' ? 'selected' : '' }}>Vacina em Atraso</option>
                        <option value="sistema" {{ request('tipo') == 'sistema' ? 'selected' : '' }}>Notificação Geral</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status" class="input-tw text-xs w-full">
                        <option value="" {{ !request('status') ? 'selected' : '' }}>Todos os Status</option>
                        <option value="nao_lidos" {{ request('status') == 'nao_lidos' ? 'selected' : '' }}>Não Lidas</option>
                        <option value="lidos" {{ request('status') == 'lidos' ? 'selected' : '' }}>Lidas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="btn-primary-tw btn-sm-tw flex-1 justify-center">
                        <i class="fas fa-filter text-xs"></i>
                        <span>Filtrar</span>
                    </button>
                    @if(request()->hasAny(['search', 'tipo', 'status']))
                        <a href="{{ route('notifications.index', ['tab' => 'notificacoes']) }}" class="btn-secondary-tw btn-sm-tw px-2.5" title="Limpar Filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Notifications List --}}
        <div class="card-tw overflow-hidden">
            @if($notifications->isEmpty())
                <div class="py-12 text-center text-surface-400">
                    <i class="fas fa-bell-slash text-4xl mb-3 text-surface-300"></i>
                    <h3 class="text-base font-semibold text-surface-700">Nenhuma notificação encontrada</h3>
                    <p class="text-xs text-surface-400 mt-1">Não existem notificações pendentes com os filtros selecionados.</p>
                </div>
            @else
                <div class="divide-y divide-surface-100">
                    @foreach($notifications as $notif)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors {{ !$notif->lido ? 'bg-brand-50/30' : 'hover:bg-surface-50' }}">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                {{-- Notification Icon --}}
                                <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-base
                                    @if($notif->cor === 'danger') bg-crimson-100 text-crimson-600
                                    @elseif($notif->cor === 'warning') bg-gold-100 text-gold-700
                                    @elseif($notif->cor === 'success') bg-brand-100 text-brand-600
                                    @else bg-ocean-100 text-ocean-600 @endif">
                                    <i class="fas fa-{{ $notif->icone ?: 'bell' }}"></i>
                                </div>

                                {{-- Notification Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-semibold text-surface-900 {{ !$notif->lido ? 'font-bold' : '' }}">
                                            {{ $notif->titulo }}
                                        </h4>
                                        @if(!$notif->lido)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-2xs font-semibold bg-brand-500 text-white">
                                                Nova
                                            </span>
                                        @endif
                                        <span class="text-2xs text-surface-400">· {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                                    </div>
                                    <p class="text-xs text-surface-600 mt-1 leading-relaxed">{{ $notif->mensagem }}</p>

                                    @if($notif->patient)
                                        <p class="text-2xs text-surface-400 mt-1">
                                            <i class="fas fa-person-pregnant mr-1"></i> Paciente: <span class="font-medium text-surface-600">{{ $notif->patient->nome_completo }}</span> (BI: {{ $notif->patient->documento_bi ?? 'N/A' }})
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                @if($notif->url)
                                    <a href="{{ $notif->url }}"
                                       onclick="fetch('{{ route('notifications.mark-read', $notif->id) }}', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })"
                                       class="btn-secondary-tw btn-xs-tw text-brand-600 hover:text-brand-700">
                                        <i class="fas fa-arrow-up-right-from-square text-2xs"></i>
                                        <span>Abrir</span>
                                    </a>
                                @endif

                                @if(!$notif->lido)
                                    <form method="POST" action="{{ route('notifications.mark-read', $notif->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-ghost-tw btn-xs-tw text-surface-500 hover:text-brand-600" title="Marcar como lida">
                                            <i class="fas fa-check text-2xs"></i>
                                            <span>Lida</span>
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}" onsubmit="return confirm('Deseja eliminar esta notificação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost-tw btn-xs-tw text-surface-400 hover:text-crimson-600" title="Eliminar notificação">
                                        <i class="fas fa-trash-can text-2xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-surface-100">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         TAB 2: PACIENTES FALTOSAS & DISPARO SMS
         ============================================================ --}}
    <div x-show="activeTab === 'faltosas'" class="space-y-4" x-cloak>
        
        {{-- Search & Filter --}}
        <div class="card-tw p-4">
            <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="tab" value="faltosas">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                    <input type="text" name="search_faltosa" value="{{ request('search_faltosa') }}" placeholder="Pesquisar por nome, BI ou telefone da gestante..." class="input-tw pl-9 text-xs w-full">
                </div>
                <button type="submit" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Filtrar</span>
                </button>
            </form>
        </div>

        {{-- Faltosas Table --}}
        <div class="card-tw overflow-hidden">
            <div class="table-container">
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Gestante</th>
                            <th>Data Agendada</th>
                            <th>Atraso</th>
                            <th>Contacto</th>
                            <th class="text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faltosas as $consulta)
                            @php
                                $diasAtraso = \Carbon\Carbon::parse($consulta->data_consulta)->startOfDay()->diffInDays(\Carbon\Carbon::now()->startOfDay());
                                $patient = $consulta->patient;
                                $phone = $patient->contacto ?? $patient->contacto_emergencia ?? 'N/D';
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-surface-100 flex items-center justify-center text-brand-600 text-xs font-semibold">
                                            <i class="fas fa-person-pregnant"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-surface-900">{{ $patient->nome_completo ?? 'N/A' }}</p>
                                            <p class="text-2xs text-surface-400">BI: {{ $patient->documento_bi ?? 'N/D' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs font-medium text-surface-800">
                                        {{ \Carbon\Carbon::parse($consulta->data_consulta)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status-tw bg-crimson-50 text-crimson-700 border-crimson-200">
                                        <i class="fas fa-clock mr-1"></i> {{ $diasAtraso }} {{ $diasAtraso == 1 ? 'dia' : 'dias' }} atraso
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs font-mono text-surface-600">
                                        <i class="fas fa-phone text-2xs mr-1 text-surface-400"></i> {{ $phone }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button @click="
                                        selectedPatient = {{ json_encode($patient) }};
                                        messageText = 'Estimada {{ $patient->nome_completo ?? 'Gestante' }}, notou-se a sua ausência na consulta pré-natal agendada para {{ \Carbon\Carbon::parse($consulta->data_consulta)->format('d/m/Y') }}. Dirija-se ao Centro de Saúde de Quelimane Urbano para reagendar e manter o seu bebê seguro.';
                                        openModalSingle = true;
                                    " class="btn-primary-tw btn-xs-tw">
                                        <i class="fas fa-paper-plane text-2xs"></i>
                                        <span>Enviar SMS</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-surface-400">
                                    <i class="fas fa-check-circle text-2xl text-emerald-500 mb-2"></i>
                                    <p class="text-sm">Não há pacientes faltosas registadas no momento!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($faltosas->hasPages())
                <div class="p-4 border-t border-surface-100">
                    {{ $faltosas->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         TAB 3: ENVIAR SMS INDIVIDUAL
         ============================================================ --}}
    <div x-show="activeTab === 'nova_mensagem'" class="space-y-4" x-cloak>
        <div class="card-tw p-6 max-w-2xl mx-auto">
            <h3 class="text-base font-semibold text-surface-900 mb-1 flex items-center gap-2">
                <i class="fas fa-paper-plane text-brand-600"></i> Envio Direto de Notificação SMS
            </h3>
            <p class="text-xs text-surface-500 mb-6">Selecione uma gestante, escolha um modelo MISAU ou personalize a mensagem.</p>

            <form method="POST" action="{{ route('sms.send-single') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label-tw">Gestante Destinatária <span class="text-crimson-500">*</span></label>
                    <select name="patient_id" class="input-tw text-xs" required x-on:change="
                        let opt = $event.target.selectedOptions[0];
                        if (opt && messageText.includes('{nome}')) {
                            messageText = messageText.replace('{nome}', opt.getAttribute('data-nome') || '');
                        }
                    ">
                        <option value="">-- Selecione a Paciente --</option>
                        @foreach($allPatients as $p)
                            <option value="{{ $p->id }}" data-nome="{{ $p->nome_completo }}" data-phone="{{ $p->contacto ?? $p->contacto_emergencia }}">
                                {{ $p->nome_completo }} ({{ $p->contacto ?? $p->contacto_emergencia ?? 'Sem Telefone' }}) - BI: {{ $p->documento_bi ?? 'N/D' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label-tw">Modelo Pré-Configurado (MISAU)</label>
                    <select class="input-tw text-xs" x-model="selectedTemplate" x-on:change="
                        const tpls = {{ json_encode($templates) }};
                        if (tpls[selectedTemplate]) {
                            messageText = tpls[selectedTemplate].texto;
                        }
                    ">
                        <option value="">-- Escolha um Modelo (Opcional) --</option>
                        @foreach($templates as $key => $tpl)
                            <option value="{{ $key }}">{{ $tpl['titulo'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="form-label-tw">Conteúdo da Mensagem SMS <span class="text-crimson-500">*</span></label>
                        <span class="text-2xs text-surface-400" x-text="(480 - messageText.length) + ' caracteres restantes'"></span>
                    </div>
                    <textarea name="mensagem" rows="4" maxlength="480" x-model="messageText" required
                              placeholder="Digite a mensagem ou use o modelo selecionado acima..."
                              class="input-tw text-xs font-mono"></textarea>
                    <p class="text-2xs text-surface-400 mt-1">Variáveis disponíveis: <code class="text-brand-600">{nome}</code>, <code class="text-brand-600">{data}</code>, <code class="text-brand-600">{servico}</code></p>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" @click="messageText = ''; selectedTemplate = ''" class="btn-secondary-tw btn-sm-tw">Limpar</button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Disparar SMS</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
         TAB 4: HISTÓRICO DE LOGS DE SMS
         ============================================================ --}}
    <div x-show="activeTab === 'logs'" class="space-y-4" x-cloak>
        <div class="card-tw overflow-hidden">
            <div class="p-4 border-b border-surface-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-surface-900">Histórico de Disparos de SMS</h3>
                <span class="text-xs text-surface-500">Últimos registros de envio</span>
            </div>

            <div class="table-container">
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Destinatário</th>
                            <th>Número de Telefone</th>
                            <th>Mensagem Enviada</th>
                            <th>Status de Envio</th>
                            <th>Data & Hora</th>
                            <th class="text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($smsLogs as $log)
                            <tr>
                                <td>
                                    @if(isset($log->patient_id) && $log->patient_id)
                                        <a href="{{ route('patients.show', $log->patient_id) }}" class="font-medium text-surface-900 hover:text-brand-600">
                                            {{ $log->paciente_nome ?? 'Gestante' }}
                                        </a>
                                    @else
                                        <p class="font-medium text-surface-900">{{ $log->paciente_nome ?? 'Destinatário Externo' }}</p>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-xs text-surface-600">{{ $log->telefone ?? $log->phone_number ?? 'N/D' }}</span>
                                </td>
                                <td class="max-w-md">
                                    <p class="text-xs text-surface-600 line-clamp-2" title="{{ $log->mensagem ?? $log->message ?? '' }}">
                                        {{ $log->mensagem ?? $log->message ?? 'Sem mensagem' }}
                                    </p>
                                    @if(!empty($log->erro))
                                        <span class="text-3xs font-mono text-crimson-800 block mt-1 bg-crimson-50 p-1 rounded border border-crimson-200 truncate max-w-xs" title="{{ $log->erro }}">
                                            <i class="fas fa-terminal text-3xs"></i> {{ $log->erro }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status === 'enviado' || $log->status === 'delivered')
                                        <span class="badge-status-tw bg-emerald-50 text-emerald-700 border-emerald-200">
                                            <i class="fas fa-check-circle mr-1"></i> Enviado
                                        </span>
                                    @elseif($log->status === 'pendente')
                                        <span class="badge-status-tw bg-gold-50 text-gold-700 border-gold-200">
                                            <i class="fas fa-clock mr-1"></i> Pendente
                                        </span>
                                    @else
                                        <span class="badge-status-tw bg-crimson-50 text-crimson-700 border-crimson-200">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Falha
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-2xs text-surface-500 font-mono">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if(isset($log->patient_id) && $log->patient_id)
                                        <form method="POST" action="{{ route('sms.send-single') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="patient_id" value="{{ $log->patient_id }}">
                                            <input type="hidden" name="mensagem" value="{{ $log->mensagem }}">
                                            <button type="submit" class="btn-secondary-tw btn-xs-tw text-gold-700 hover:text-gold-800 hover:bg-gold-50" title="Reenviar SMS para esta gestante">
                                                <i class="fas fa-rotate-right text-2xs mr-1"></i>
                                                <span>Reenviar</span>
                                            </button>
                                        </form>
                                    @else
                                        <button @click="
                                            messageText = '{{ addslashes($log->mensagem ?? '') }}';
                                            activeTab = 'nova_mensagem';
                                        " class="btn-ghost-tw btn-xs-tw text-surface-500 hover:text-brand-600" title="Reutilizar Mensagem">
                                            <i class="fas fa-copy text-2xs mr-1"></i>
                                            <span>Reutilizar</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-surface-400">
                                    <i class="fas fa-clock-rotate-left text-2xl mb-2 text-surface-300"></i>
                                    <p class="text-sm">Nenhum histórico de envio registrado ainda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($smsLogs->hasPages())
                <div class="p-4 border-t border-surface-100">
                    {{ $smsLogs->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         TAB 5: MODELOS MISAU
         ============================================================ --}}
    <div x-show="activeTab === 'modelos'" class="space-y-4" x-cloak>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($templates as $key => $tpl)
                <div class="card-tw p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                            <i class="fas fa-{{ $tpl['icon'] ?? 'file-lines' }} text-brand-600 text-xs"></i>
                            <span>{{ $tpl['titulo'] }}</span>
                        </h4>
                        <button @click="
                            activeTab = 'nova_mensagem';
                            selectedTemplate = '{{ $key }}';
                            messageText = '{{ addslashes($tpl['texto']) }}';
                        " class="btn-ghost-tw btn-xs-tw text-brand-600 hover:text-brand-700">
                            <i class="fas fa-copy text-2xs mr-1"></i> Usar Modelo
                        </button>
                    </div>
                    <div class="bg-surface-50 p-3 rounded-lg border border-surface-100 text-xs font-mono text-surface-700 leading-relaxed">
                        {{ $tpl['texto'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================
         MODAL: SMS INDIVIDUAL
         ============================================================ --}}
    <div x-show="openModalSingle"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-cloak>
        
        <div @click.outside="openModalSingle = false" class="card-tw max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="text-base font-semibold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-brand-600"></i> Enviar Notificação SMS
                </h3>
                <button @click="openModalSingle = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('sms.send-single') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="patient_id" :value="selectedPatient ? selectedPatient.id : ''">

                <div>
                    <label class="form-label-tw">Destinatária</label>
                    <p class="text-sm font-semibold text-surface-900" x-text="selectedPatient ? selectedPatient.nome_completo : ''"></p>
                    <p class="text-xs text-surface-500 font-mono" x-text="selectedPatient ? 'Telefone: ' + (selectedPatient.contacto || selectedPatient.contacto_emergencia || 'Não registado') : ''"></p>
                </div>

                <div>
                    <label class="form-label-tw">Mensagem</label>
                    <textarea name="mensagem" rows="4" maxlength="480" x-model="messageText" required
                              class="input-tw text-xs font-mono"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="openModalSingle = false" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Enviar SMS Agora</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
         MODAL: DISPARO EM MASSA
         ============================================================ --}}
    <div id="modalBulkSms" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4 hidden">
        <div class="card-tw max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="text-base font-semibold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-brand-600"></i> Disparo em Massa para Faltosas
                </h3>
                <button onclick="document.getElementById('modalBulkSms').classList.add('hidden')" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('sms.send-bulk') }}" class="space-y-4">
                @csrf

                <div class="p-3 rounded-lg bg-gold-50 border border-gold-200 text-xs text-gold-800 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-base text-gold-600 shrink-0"></i>
                    <span>Esta ação enviará SMS para todas as <strong>{{ $totalFaltosas ?? 0 }} gestantes faltosas</strong> com número de contacto registado.</span>
                </div>

                <div>
                    <label class="form-label-tw">Modelo de Mensagem</label>
                    <textarea name="mensagem_template" rows="4" maxlength="480" required
                              class="input-tw text-xs font-mono">Estimada {nome}, notou-se a sua ausência na consulta pré-natal agendada para {data}. Dirija-se ao Centro de Saúde de Quelimane Urbano para reagendar e manter o seu bebê seguro.</textarea>
                    <p class="text-2xs text-surface-400 mt-1">Variáveis que serão substituídas automaticamente: <code>{nome}</code> e <code>{data}</code>.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalBulkSms').classList.add('hidden')" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Confirmar e Disparar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

