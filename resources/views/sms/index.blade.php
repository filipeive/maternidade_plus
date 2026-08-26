@extends('layouts.app-tw')

@section('title', 'Central de Notificações SMS')
@section('page-title', 'Central de Notificações SMS & Recuperação de Faltosas')
@section('title-icon', 'fa-comment-sms')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Central de SMS</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6" x-data="{ activeTab: 'faltosas', openModalSingle: false, selectedPatient: null, messageText: '' }">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $totalFaltosas }}</p>
                <p class="stat-card-label">Pacientes Faltosas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $totalEnviadosMes }}</p>
                <p class="stat-card-label">SMS Enviados este Mês</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $taxaSucesso }}%</p>
                <p class="stat-card-label">Taxa de Entrega SMS</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
                <i class="fas fa-sim-card"></i>
            </div>
            <div>
                <p class="stat-card-value text-xs font-bold font-mono">httpSMS API</p>
                <p class="stat-card-label">+258 86 213 4230</p>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center justify-between border-b border-surface-200">
        <div class="flex items-center gap-4">
            <button @click="activeTab = 'faltosas'"
                    class="py-3 px-1 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                    :class="activeTab === 'faltosas' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-user-xmark"></i>
                <span>Pacientes Faltosas ({{ $totalFaltosas }})</span>
            </button>
            <button @click="activeTab = 'nova_mensagem'"
                    class="py-3 px-1 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                    :class="activeTab === 'nova_mensagem' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-paper-plane"></i>
                <span>Enviar SMS Individual</span>
            </button>
            <button @click="activeTab = 'logs'"
                    class="py-3 px-1 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                    :class="activeTab === 'logs' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Histórico de Logs de Envio</span>
            </button>
        </div>

        <div x-show="activeTab === 'faltosas'">
            <button onclick="document.getElementById('modalBulkSms').classList.remove('hidden')" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-paper-plane text-xs"></i>
                <span>Disparar SMS em Massa para Faltosas</span>
            </button>
        </div>
    </div>

    {{-- TAB 1: PACIENTES FALTOSAS --}}
    <div x-show="activeTab === 'faltosas'" class="space-y-4">
        
        {{-- Search & Filter --}}
        <div class="card-tw p-4">
            <form method="GET" action="{{ route('sms.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome, BI ou telefone da gestante..." class="input-tw pl-9 text-xs">
                </div>
                <button type="submit" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Filtrar</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('sms.index') }}" class="btn-secondary-tw btn-sm-tw text-crimson-600">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="card-tw overflow-hidden">
            <div class="table-container-tw">
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Gestante / Paciente</th>
                            <th>Contacto / Telefone</th>
                            <th>Consulta Atrasada</th>
                            <th>Dias de Atraso</th>
                            <th>Ações de Notificação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faltosas as $c)
                            @php
                                $p = $c->patient;
                                $atrasoDias = now()->diffInDays($c->data_consulta);
                            @endphp
                            <tr>
                                <td>
                                    @if($p)
                                        <a href="{{ route('patients.show', $p) }}" class="font-bold text-brand-700 hover:underline block">
                                            {{ $p->nome_completo }}
                                        </a>
                                        <span class="text-2xs text-surface-400">BI: {{ $p->documento_bi ?? 'N/A' }} · IG: {{ $p->idade_gestacional_detalhada ?? ($p->semanas_gestacao ? $p->semanas_gestacao . 'ª sem' : 'N/A') }}</span>
                                    @else
                                        <span class="text-surface-400 italic">Paciente Desconhecida</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-xs text-surface-800 font-medium">
                                        {{ $p->contacto ?? $p->contacto_emergencia ?? 'Sem Telefone' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-semibold text-surface-800">{{ $c->data_consulta?->format('d/m/Y') }}</span>
                                    <span class="text-2xs text-surface-400 block capitalize">{{ str_replace('_', ' ', $c->tipo_consulta) }}</span>
                                </td>
                                <td>
                                    <span class="badge-danger font-bold">
                                        {{ $atrasoDias }} {{ $atrasoDias == 1 ? 'dia' : 'dias' }} de atraso
                                    </span>
                                </td>
                                <td>
                                    <button @click="
                                        selectedPatient = { id: {{ $p->id ?? 0 }}, nome: '{{ addslashes($p->nome_completo ?? '') }}', fone: '{{ $p->contacto ?? '' }}', data: '{{ $c->data_consulta?->format('d/m/Y') }}' };
                                        messageText = 'Estimada ' + selectedPatient.nome + ', notou-se a sua ausência na consulta pré-natal agendada para ' + selectedPatient.data + '. Dirija-se ao Centro de Saúde de Quelimane Urbano para reagendar e manter o seu bebê seguro.';
                                        openModalSingle = true;
                                    " class="btn-primary-tw btn-sm-tw py-1 px-2.5 text-xs" {{ empty($p->contacto) ? 'disabled' : '' }}>
                                        <i class="fas fa-paper-plane text-2xs"></i>
                                        <span>Enviar SMS</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-surface-400">
                                    <i class="fas fa-check-circle text-3xl text-brand-500 mb-2"></i>
                                    <p class="text-sm font-semibold">Excelente! Não existem pacientes faltosas pendentes no momento.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($faltosas->hasPages())
    {{-- TAB 3: ENVIAR SMS INDIVIDUAL / SERVIÇOS / RESULTADOS --}}
    <div x-show="activeTab === 'nova_mensagem'" class="card-tw p-6 space-y-6" x-data="{
        selectedPatientId: '',
        patientName: '',
        patientPhone: '',
        templateType: 'exames',
        servicoNome: 'Hemograma Completo',
        customText: '',
        updateMsg() {
            let pName = this.patientName || '{nome}';
            let serv = this.servicoNome || 'Serviço Clínico';
            if (this.templateType === 'exames') {
                this.customText = 'Estimada ' + pName + ', informamos que o resultado do seu exame clínico de ' + serv + ' já se encontra disponível no Centro de Saúde de Quelimane Urbano. Compareça para levantamento.';
            } else if (this.templateType === 'lembrete') {
                this.customText = 'Estimada ' + pName + ', lembramos que a sua consulta de acompanhamento pré-natal no Centro de Saúde está agendada para breve. Cuide de si e do seu bebê.';
            } else if (this.templateType === 'vacinacao') {
                this.customText = 'Estimada ' + pName + ', a sua dose de vacina/prevenção contra malária (IPTp) está pronta no Centro de Saúde de Quelimane Urbano. Compareça para proteção.';
            } else if (this.templateType === 'geral') {
                this.customText = 'Estimada ' + pName + ', solicitamos a sua comparência no Centro de Saúde de Quelimane Urbano para o serviço de ' + serv + '.';
            }
        }
    }" x-init="updateMsg()">
        <div class="border-b border-surface-100 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                <i class="fas fa-paper-plane text-brand-600"></i> Envio Individual de Notificação SMS
            </h3>
            <span class="badge-neutral text-2xs uppercase">httpSMS Provider</span>
        </div>

        <form method="POST" action="{{ route('sms.send-single') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                {{-- Selecionar Paciente --}}
                <div>
                    <label class="label-tw">Selecione a Paciente / Gestante <span class="text-crimson-500">*</span></label>
                    <select name="patient_id" x-model="selectedPatientId" @change="
                        let opt = $el.options[$el.selectedIndex];
                        patientName = opt.getAttribute('data-nome') || '';
                        patientPhone = opt.getAttribute('data-fone') || '';
                        updateMsg();
                    " required class="input-tw text-xs">
                        <option value="">Escolha a paciente na lista...</option>
                        @foreach($allPatients as $patientItem)
                            <option value="{{ $patientItem->id }}" 
                                    data-nome="{{ $patientItem->nome_completo }}"
                                    data-fone="{{ $patientItem->contacto ?? $patientItem->contacto_emergencia }}">
                                {{ $patientItem->nome_completo }} — {{ $patientItem->contacto ?? 'Sem Telefone' }} (NID: {{ $patientItem->documento_bi ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de Notificação / Serviço --}}
                <div>
                    <label class="label-tw">Tipo de Notificação / Serviço</label>
                    <select x-model="templateType" @change="updateMsg()" class="input-tw text-xs">
                        <option value="exames">🔬 Resultado de Exame Pronto</option>
                        <option value="lembrete">📅 Lembrete de Consulta ANC</option>
                        <option value="vacinacao">💉 Aviso de Vacinação & IPTp-SP</option>
                        <option value="geral">💬 Notificação Geral de Serviço</option>
                    </select>
                </div>

                {{-- Nome do Exame / Serviço Especifico --}}
                <div x-show="templateType === 'exames' || templateType === 'geral'" class="md:col-span-2">
                    <label class="label-tw">Nome do Exame / Serviço Específico</label>
                    <input type="text" x-model="servicoNome" @input="updateMsg()" placeholder="Ex: Hemograma Completo, Ecografia Obstétrica, Tétano" class="input-tw text-xs">
                </div>

            </div>

            {{-- Caixa da Mensagem --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="label-tw mb-0">Texto da Mensagem SMS <span class="text-crimson-500">*</span></label>
                    <span class="text-3xs font-mono text-surface-400" x-text="customText.length + ' / 480 caracteres'"></span>
                </div>
                <textarea name="mensagem" x-model="customText" rows="4" required class="input-tw text-xs font-sans leading-relaxed"></textarea>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="btn-primary-tw font-bold text-xs py-2.5 px-6">
                    <i class="fas fa-paper-plane text-xs mr-1"></i>
                    <span>Enviar Notificação SMS</span>
                </button>
            </div>
        </form>
    </div>

    {{-- TAB 2: HISTÓRICO DE LOGS DE SMS --}}
    <div x-show="activeTab === 'logs'" class="card-tw overflow-hidden">
        <div class="table-container-tw">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Data & Hora</th>
                        <th>Destinatário / Paciente</th>
                        <th>Número de Telefone</th>
                        <th>Mensagem Enviada</th>
                        <th>Status da Entrega</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($smsLogs as $log)
                        <tr>
                            <td class="text-2xs text-surface-500 font-mono">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                @if($log->patient_id)
                                    <a href="{{ route('patients.show', $log->patient_id) }}" class="font-bold text-surface-900 hover:underline">
                                        {{ $log->paciente_nome }}
                                    </a>
                                @else
                                    <span class="text-surface-400 italic">Notificação Geral</span>
                                @endif
                            </td>
                            <td class="font-mono text-xs font-semibold text-surface-800">
                                {{ $log->telefone }}
                            </td>
                            <td class="max-w-xs">
                                <p class="text-xs text-surface-700 line-clamp-2" title="{{ $log->mensagem }}">
                                    {{ $log->mensagem }}
                                </p>
                            </td>
                            <td>
                                @if($log->status === 'enviado')
                                    <span class="badge-success">
                                        <i class="fas fa-check text-2xs mr-1"></i> Enviado com sucesso
                                    </span>
                                @else
                                    <span class="badge-danger">
                                        <i class="fas fa-circle-exclamation text-2xs mr-1"></i> Falha no Envio
                                    </span>
                                    @hasrole('Administrador')
                                        @if($log->erro)
                                            <span class="text-3xs font-mono text-crimson-800 block mt-1 bg-crimson-50 p-1 rounded border border-crimson-200 truncate max-w-xs" title="{{ $log->erro }}">
                                                <i class="fas fa-terminal text-3xs"></i> Debug: {{ $log->erro }}
                                            </span>
                                        @endif
                                    @endhasrole
                                @endif
                            </td>
                            <td>
                                @if($log->patient_id)
                                    <form method="POST" action="{{ route('sms.send-single') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="patient_id" value="{{ $log->patient_id }}">
                                        <input type="hidden" name="mensagem" value="{{ $log->mensagem }}">
                                        <button type="submit" class="btn-tw bg-gold-400 hover:bg-gold-300 text-surface-900 text-3xs font-bold py-1 px-2.5 rounded-lg shadow-xs flex items-center gap-1" title="Reenviar SMS para esta paciente">
                                            <i class="fas fa-rotate-right text-3xs"></i>
                                            <span>Reenviar</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-3xs text-surface-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-surface-400">
                                <i class="fas fa-comment-slash text-3xl mb-2"></i>
                                <p class="text-sm font-semibold">Nenhum registo de envio de SMS encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($smsLogs->hasPages())
            <div class="p-4 border-t border-surface-100">
                {{ $smsLogs->appends(request()->except('logs_page'))->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DISPARO INDIVIDUAL DE SMS --}}
    <div x-show="openModalSingle" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-2xl border border-surface-200 w-full max-w-lg p-6 space-y-4 text-left" @click.outside="openModalSingle = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane text-brand-600"></i> Enviar SMS para <span x-text="selectedPatient?.nome" class="text-brand-700"></span>
                </h3>
                <button @click="openModalSingle = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('sms.send-single') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="patient_id" :value="selectedPatient?.id">

                <div>
                    <label class="label-tw">Destinatário & Telefone</label>
                    <input type="text" :value="selectedPatient?.nome + ' (' + selectedPatient?.fone + ')'" disabled class="input-tw bg-surface-100 font-semibold text-xs">
                </div>

                <div>
                    <label class="label-tw">Conteúdo do SMS <span class="text-crimson-500">*</span></label>
                    <textarea name="mensagem" x-model="messageText" rows="4" maxlength="480" required class="input-tw text-xs"></textarea>
                    <p class="text-3xs text-surface-400 mt-1">Limite: <span x-text="messageText.length"></span> / 480 caracteres.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openModalSingle = false" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Disparar SMS</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DISPARO EM MASSA DE SMS --}}
    <div id="modalBulkSms" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-2xl border border-surface-200 w-full max-w-lg p-6 space-y-4 text-left">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-users-viewfinder text-crimson-600"></i> Disparo em Massa para Pacientes Faltosas
                </h3>
                <button onclick="document.getElementById('modalBulkSms').classList.add('hidden')" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('sms.send-bulk') }}" method="POST" class="space-y-4">
                @csrf
                <div class="p-3 bg-crimson-50 border border-crimson-200 rounded-xl text-xs text-crimson-900 flex items-start gap-2">
                    <i class="fas fa-triangle-exclamation text-crimson-600 shrink-0 mt-0.5"></i>
                    <p>Esta ação enviará SMS para as <strong>{{ $totalFaltosas }} pacientes faltosas</strong> listadas no sistema. Certifique-se do texto do modelo.</p>
                </div>

                <div>
                    <label class="label-tw">Modelo de Mensagem (Template MISAU) <span class="text-crimson-500">*</span></label>
                    <textarea name="mensagem_template" rows="4" maxlength="480" required class="input-tw text-xs">{{ $templates['faltosa']['texto'] }}</textarea>
                    <p class="text-3xs text-surface-400 mt-1">Variáveis dinâmicas suportadas: <code>{nome}</code> e <code>{data}</code>.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalBulkSms').classList.add('hidden')" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                    <button type="submit" class="btn-danger-tw btn-sm-tw">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Confirmar e Disparar SMS em Massa</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
