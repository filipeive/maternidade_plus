@extends('layouts.app-tw')

@section('title', 'Detalhes da Gestante')
@section('page-title', $patient->nome_completo)
@section('title-icon', 'fa-person-pregnant')

@section('breadcrumbs')
    <a href="{{ route('patients.index') }}">Gestantes</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">{{ $patient->nome_completo }}</span>
@endsection

@section('content')
<div x-data="{ transferModalOpen: false }">
    @php
        $alertasAtivosPaciente = $patient->alertasAtivos()->orderByRaw("CASE nivel WHEN 'alto' THEN 1 WHEN 'medio' THEN 2 WHEN 'baixo' THEN 3 ELSE 4 END")->get();
        $temAlertaAlto = $alertasAtivosPaciente->where('nivel', 'alto')->count() > 0;
    @endphp

    {{-- Banner de Paciente Transferida / Inativa --}}
    @if(!$patient->ativo)
        <div class="mb-6 bg-gold-50 border-l-4 border-gold-500 rounded-r-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold-500 text-white flex items-center justify-center shrink-0">
                    <i class="fas fa-arrow-right-from-bracket text-lg"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-gold-900 flex items-center gap-2">
                        <span class="badge-warning">{{ $patient->motivo_inativacao_formatado }}</span>
                        @if($patient->unidade_sanitaria_destino)
                            Destino: {{ $patient->unidade_sanitaria_destino }} ({{ $patient->provincia_destino ?? 'Província N/D' }})
                        @endif
                    </h5>
                    <p class="text-xs text-gold-800 mt-0.5">
                        Data: <strong>{{ $patient->data_transferencia?->format('d/m/Y') ?? 'N/D' }}</strong> · Guia Oficial: <strong class="font-mono">{{ $patient->guia_transferencia_numero ?? 'N/D' }}</strong> · Motivo: {{ $patient->motivo_transferencia }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('patients.transfer-guide.pdf', $patient) }}" target="_blank" class="btn-primary-tw btn-sm-tw bg-gold-600 hover:bg-gold-700">
                    <i class="fas fa-file-pdf text-xs"></i>
                    <span>Imprimir Guia Oficial</span>
                </a>
                <form method="POST" action="{{ route('patients.reactivate', $patient) }}" onsubmit="return confirm('Confirmar reativação desta paciente na Unidade Sanitária?');" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-rotate-left text-xs text-brand-600"></i>
                        <span>Reativar na US</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Bloco de Gestão e Resolução Direta de Alertas Clínicos --}}
    @if($alertasAtivosPaciente->count() > 0 || $alertasResolvidosPaciente->count() > 0)
        <div x-data="{
                openTratarModal: false,
                mostrarHistorico: false,
                alertaId: null,
                alertaTitulo: '',
                alertaNivel: '',
                alertaMensagem: '',
                novoStatus: 'resolvido',
                notaConduta: '',
                abrirTratamento(id, titulo, nivel, mensagem, statusAtual = 'resolvido') {
                    this.alertaId = id;
                    this.alertaTitulo = titulo;
                    this.alertaNivel = nivel;
                    this.alertaMensagem = mensagem;
                    this.novoStatus = statusAtual === 'em_seguimento' ? 'em_seguimento' : 'resolvido';
                    this.notaConduta = '';
                    this.openTratarModal = true;
                }
            }"
            class="card-tw mb-6 border-l-4 {{ $temAlertaAlto ? 'border-l-crimson-500' : ($alertasAtivosPaciente->count() > 0 ? 'border-l-gold-500' : 'border-l-emerald-500') }} shadow-sm">
            
            <div class="card-header-tw flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg {{ $temAlertaAlto ? 'bg-crimson-100 text-crimson-700' : ($alertasAtivosPaciente->count() > 0 ? 'bg-gold-100 text-gold-800' : 'bg-emerald-100 text-emerald-700') }} flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h6 class="font-bold text-surface-900 text-sm">
                            @if($alertasAtivosPaciente->count() > 0)
                                Alertas Clínicos em Aberto ({{ $alertasAtivosPaciente->count() }})
                            @else
                                Alertas Clínicos (Histórico de Resoluções)
                            @endif
                        </h6>
                        <p class="text-3xs text-surface-500">Monitoria e condutas de triagem obstétrica em tempo real</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @if($patient->podeRegistrarParto())
                        <a href="{{ route('births.create', $patient) }}" class="btn-tw bg-crimson-600 hover:bg-crimson-700 text-white btn-xs-tw font-bold shadow-2xs" title="Registar parto e concluir alertas da gestação">
                            <i class="fas fa-baby text-3xs"></i>
                            <span>Registar Parto</span>
                        </a>
                    @endif

                    <a href="{{ route('alertas.avaliacoes') }}" class="btn-secondary-tw btn-xs-tw">
                        <i class="fas fa-clipboard-check text-3xs"></i>
                        <span>Painel de Avaliações</span>
                    </a>
                </div>
            </div>

            @if($alertasAtivosPaciente->count() > 0)
                <div class="divide-y divide-surface-100">
                    @foreach($alertasAtivosPaciente as $alerta)
                        <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:bg-surface-50/60 transition-colors">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="badge-{{ $alerta->nivel === 'alto' ? 'danger' : ($alerta->nivel === 'medio' ? 'warning' : 'info') }} font-bold text-3xs uppercase">
                                        {{ ucfirst($alerta->nivel) }}
                                    </span>

                                    @if($alerta->status === 'em_seguimento')
                                        <span class="badge-warning text-3xs font-bold bg-gold-100 text-gold-900 border border-gold-300">
                                            <i class="fas fa-clock mr-0.5"></i> Em Seguimento
                                        </span>
                                    @endif

                                    <strong class="text-surface-900 text-xs">{{ $alerta->tipo_label }}</strong>
                                    <span class="text-3xs text-surface-400 font-mono">
                                        <i class="far fa-clock mr-0.5"></i>{{ $alerta->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if(!$alerta->lido)
                                        <span class="badge-neutral text-3xs bg-brand-50 text-brand-700 border border-brand-200">Não Lido</span>
                                    @endif
                                </div>
                                <p class="text-xs text-surface-700 leading-relaxed">{{ $alerta->mensagem }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                                @if(!$alerta->lido)
                                    <form method="POST" action="{{ route('alertas.marcar-lido', $alerta) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-secondary-tw btn-xs-tw text-surface-600 hover:text-brand-700" title="Marcar como lido">
                                            <i class="fas fa-check text-3xs"></i>
                                            <span>Lido</span>
                                        </button>
                                    </form>
                                @endif

                                @if($alerta->status === 'em_seguimento')
                                    <button type="button"
                                            @click="abrirTratamento({{ $alerta->id }}, '{{ addslashes($alerta->tipo_label) }}', '{{ $alerta->nivel }}', '{{ addslashes($alerta->mensagem) }}', 'em_seguimento')"
                                            class="btn-tw bg-gold-400 hover:bg-gold-500 text-surface-950 btn-xs-tw font-bold shadow-2xs">
                                        <i class="fas fa-arrows-rotate text-3xs"></i>
                                        <span>Atualizar Conduta</span>
                                    </button>
                                @else
                                    <button type="button"
                                            @click="abrirTratamento({{ $alerta->id }}, '{{ addslashes($alerta->tipo_label) }}', '{{ $alerta->nivel }}', '{{ addslashes($alerta->mensagem) }}', 'resolvido')"
                                            class="btn-tw bg-brand-600 hover:bg-brand-700 text-white btn-xs-tw font-bold shadow-2xs">
                                        <i class="fas fa-stethoscope text-3xs"></i>
                                        <span>Tratar / Resolver</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Histórico de Alertas Resolvidos / Concluídos --}}
            @if($alertasResolvidosPaciente->count() > 0)
                <div class="border-t border-surface-100 p-3 bg-surface-50/50">
                    <button type="button" @click="mostrarHistorico = !mostrarHistorico" class="w-full flex items-center justify-between text-xs font-semibold text-surface-600 hover:text-surface-900 transition-colors">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-history text-3xs text-brand-600"></i>
                            <span>Histórico de Alertas Resolvidos / Partos Concluídos ({{ $alertasResolvidosPaciente->count() }})</span>
                        </span>
                        <i class="fas fa-chevron-down text-3xs transition-transform duration-200" :class="{'rotate-180': mostrarHistorico}"></i>
                    </button>

                    <div x-show="mostrarHistorico" x-cloak class="mt-3 space-y-2 divide-y divide-surface-100 pt-2 border-t border-surface-200">
                        @foreach($alertasResolvidosPaciente as $alertaRes)
                            <div class="pt-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-surface-800 text-3xs">{{ $alertaRes->tipo_label }}</span>
                                    <span class="badge-success text-3xs">Resolvido em {{ $alertaRes->resolvido_em?->format('d/m/Y H:i') ?? $alertaRes->updated_at->format('d/m/Y') }}</span>
                                </div>
                                <p class="text-3xs text-surface-600 mt-0.5">{{ $alertaRes->nota_resolucao ?? 'Resolvido pelo profissional de saúde.' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Modal de Tratamento / Resolução Clínica Direta --}}
            <div x-show="openTratarModal"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/60 backdrop-blur-xs"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                <div @click.away="openTratarModal = false"
                     class="card-tw max-w-lg w-full p-6 space-y-4 shadow-xl border-surface-300">
                    
                    <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-surface-900">Conduta Clínica & Resolução do Alerta</h4>
                                <span class="text-3xs text-surface-500" x-text="alertaTitulo"></span>
                            </div>
                        </div>
                        <button type="button" @click="openTratarModal = false" class="text-surface-400 hover:text-surface-600 text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Alerta Info Box --}}
                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200 text-xs text-surface-700">
                        <span class="font-semibold block text-surface-900 mb-1">Motivo Clínico do Alerta:</span>
                        <p x-text="alertaMensagem" class="text-xs"></p>
                    </div>

                    {{-- Atalho para Parto caso aplicável --}}
                    @if($patient->podeRegistrarParto())
                        <div class="p-3 bg-crimson-50/70 rounded-xl border border-crimson-200 flex items-center justify-between gap-3 text-xs">
                            <div>
                                <span class="font-bold text-crimson-900 block text-3xs uppercase">O parto já ocorreu?</span>
                                <span class="text-3xs text-crimson-700">Ao registar o parto, todos os alertas da gravidez são automaticamente concluídos.</span>
                            </div>
                            <a href="{{ route('births.create', $patient) }}" class="btn-tw bg-crimson-600 hover:bg-crimson-700 text-white btn-xs-tw font-bold shrink-0">
                                <i class="fas fa-baby text-3xs"></i>
                                <span>Registar Parto</span>
                            </a>
                        </div>
                    @endif

                    {{-- Formulário de Resolução --}}
                    <form :action="'{{ url('/alertas') }}/' + alertaId + '/transitar'" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="label-tw">Novo Status Clínico <span class="text-crimson-500">*</span></label>
                            <div class="grid grid-cols-3 gap-2 mt-1">
                                <label class="p-2.5 rounded-xl border cursor-pointer text-center flex flex-col items-center justify-center gap-1 transition-all"
                                       :class="novoStatus === 'resolvido' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-bold ring-2 ring-emerald-500' : 'border-surface-200 hover:bg-surface-50 text-surface-700'">
                                    <input type="radio" name="status" value="resolvido" x-model="novoStatus" class="sr-only">
                                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                    <span class="text-3xs">Resolvido</span>
                                </label>

                                <label class="p-2.5 rounded-xl border cursor-pointer text-center flex flex-col items-center justify-center gap-1 transition-all"
                                       :class="novoStatus === 'em_seguimento' ? 'border-gold-500 bg-gold-50 text-gold-900 font-bold ring-2 ring-gold-500' : 'border-surface-200 hover:bg-surface-50 text-surface-700'">
                                    <input type="radio" name="status" value="em_seguimento" x-model="novoStatus" class="sr-only">
                                    <i class="fas fa-user-clock text-gold-600 text-sm"></i>
                                    <span class="text-3xs">Em Seguimento</span>
                                </label>

                                <label class="p-2.5 rounded-xl border cursor-pointer text-center flex flex-col items-center justify-center gap-1 transition-all"
                                       :class="novoStatus === 'ignorado' ? 'border-surface-400 bg-surface-100 text-surface-900 font-bold ring-2 ring-surface-400' : 'border-surface-200 hover:bg-surface-50 text-surface-700'">
                                    <input type="radio" name="status" value="ignorado" x-model="novoStatus" class="sr-only">
                                    <i class="fas fa-ban text-surface-500 text-sm"></i>
                                    <span class="text-3xs">Ignorado</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="label-tw">Conduta Clínica / Nota de Auditoria <span class="text-crimson-500">*</span></label>
                            <textarea name="nota"
                                      x-model="notaConduta"
                                      rows="3"
                                      required
                                      placeholder="Descreva a conduta tomada (ex: administrada medicação, solicitada ecografia obstétrica, agendada consulta de retorno em 48h)..."
                                      class="input-tw text-xs w-full mt-1"></textarea>
                            <span class="text-3xs text-surface-400 mt-1 block">Registo obrigatório segundo protocolos de auditoria clínica do MISAU.</span>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-100">
                            <button type="button" @click="openTratarModal = false" class="btn-secondary-tw btn-sm-tw">
                                <span>Cancelar</span>
                            </button>
                            <button type="submit" class="btn-primary-tw btn-sm-tw">
                                <i class="fas fa-check text-xs"></i>
                                <span>Gravar Conduta Clínica</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    @endif

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left / Main Content (2 Columns) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informações Pessoais Card --}}
            <div class="card-tw">
                <div class="card-header-tw flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 font-bold text-base flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-surface-900">{{ $patient->nome_completo }}</h3>
                            <p class="text-xs text-surface-500">BI: {{ $patient->documento_bi ?? 'N/D' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('patients.index') }}" class="btn-secondary-tw btn-sm-tw">
                            <i class="fas fa-arrow-left text-xs"></i>
                            <span>Voltar</span>
                        </a>
                        @if ($patient->status_atual === 'gestante' && $patient->podeRegistrarParto())
                            <a href="{{ route('births.create', $patient) }}" class="btn-tw bg-gold-500 text-white hover:bg-gold-600 btn-sm-tw">
                                <i class="fas fa-baby text-xs"></i>
                                <span>Registrar Parto</span>
                            </a>
                        @endif
                        <a href="{{ route('consultations.create', $patient) }}" class="btn-primary-tw btn-sm-tw">
                            <i class="fas fa-calendar-plus text-xs"></i>
                            <span>Nova Consulta</span>
                        </a>
                        <a href="{{ route('patients.card', $patient) }}" class="btn-secondary-tw btn-sm-tw" title="Cartão da Gestante com QR Code">
                            <i class="fas fa-id-card text-brand-600 text-xs"></i>
                            <span>Cartão QR Code</span>
                        </a>
                        <a href="{{ route('patients.edit', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                            <i class="fas fa-edit text-xs"></i>
                            <span>Editar</span>
                        </a>
                        @if($patient->ativo)
                            <button type="button" @click="transferModalOpen = true" class="btn-secondary-tw btn-sm-tw text-crimson-700 bg-crimson-50 border-crimson-200 hover:bg-crimson-100" title="Transferir para outra US ou Província">
                                <i class="fas fa-arrow-right-from-bracket text-xs text-crimson-600"></i>
                                <span>Transferir / Inativar</span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body-tw">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-2">
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Nome:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->nome_completo }}</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Idade:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->idade }} anos</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Data Nascimento:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->data_nascimento?->format('d/m/Y') }}</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Documento (BI):</span>
                                <span class="font-mono text-surface-900">{{ $patient->documento_bi }}</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Contacto:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->contacto }}</span>
                            </p>
                            @if ($patient->email)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Email:</span>
                                    <span class="text-surface-900">{{ $patient->email }}</span>
                                </p>
                            @endif
                            @if ($patient->contacto_emergencia)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Emergência:</span>
                                    <span class="text-surface-900">{{ $patient->contacto_emergencia }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Endereço:</span>
                                <span class="font-semibold text-surface-900 text-right">{{ $patient->endereco }}</span>
                            </p>
                            @if ($patient->tipo_sanguineo)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Tipo Sanguíneo:</span>
                                    <span class="badge-danger font-bold">{{ $patient->tipo_sanguineo }}</span>
                                </p>
                            @endif
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Gestações (GPA):</span>
                                <span class="font-bold text-brand-700">G{{ $patient->numero_gestacoes }}P{{ $patient->numero_partos }}A{{ $patient->numero_abortos }}</span>
                            </p>
                            @if ($patient->data_ultima_menstruacao)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">DUM:</span>
                                    <span class="font-semibold text-surface-900">{{ $patient->data_ultima_menstruacao->format('d/m/Y') }}</span>
                                </p>
                            @endif
                            @if ($patient->data_provavel_parto)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Data Provável do Parto:</span>
                                    <span class="font-semibold text-brand-600">{{ $patient->data_provavel_parto->format('d/m/Y') }}</span>
                                </p>
                            @endif
                            @if ($patient->semanas_gestacao)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Semanas de Gestação:</span>
                                    <span class="badge-info">{{ $patient->idade_gestacional_detalhada ?? $patient->semanas_gestacao . 'ª semana' }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Bloco Rede de Apoio Familiar & SMS --}}
                    <div class="mt-4 pt-3 border-t border-surface-100 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div class="bg-surface-50 p-3 rounded-lg border border-surface-200/80">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-surface-800 flex items-center gap-1.5">
                                    <i class="fas fa-person-breastfeeding text-brand-600"></i> Parceiro / Pai do Bebê:
                                </span>
                                @if($patient->tem_parceiro)
                                    <span class="badge-success text-2xs">Presente</span>
                                @else
                                    <span class="badge-neutral text-2xs">Sem Parceiro / Desconhecido</span>
                                @endif
                            </div>
                            @if($patient->tem_parceiro && $patient->parceiro_nome)
                                <p class="text-surface-900 font-semibold">{{ $patient->parceiro_nome }}</p>
                                <p class="text-surface-600 text-2xs mt-0.5">
                                    <i class="fas fa-phone text-surface-400 mr-1"></i> {{ $patient->parceiro_contacto ?? 'Sem telefone' }}
                                    @if($patient->parceiro_notificar_sms)
                                        <span class="text-emerald-700 font-bold ml-1.5"><i class="fas fa-comment-sms"></i> Notifica SMS</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-surface-500 italic text-2xs">Sem dados do parceiro registados.</p>
                            @endif
                        </div>

                        <div class="bg-surface-50 p-3 rounded-lg border border-surface-200/80">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-surface-800 flex items-center gap-1.5">
                                    <i class="fas fa-hand-holding-heart text-gold-600"></i> Acompanhante / Familiar de Apoio:
                                </span>
                                @if($patient->acompanhante_parentesco)
                                    <span class="badge-info text-2xs">{{ $patient->acompanhante_parentesco }}</span>
                                @endif
                            </div>
                            @if($patient->acompanhante_nome)
                                <p class="text-surface-900 font-semibold">{{ $patient->acompanhante_nome }}</p>
                                <p class="text-surface-600 text-2xs mt-0.5">
                                    <i class="fas fa-phone text-surface-400 mr-1"></i> {{ $patient->acompanhante_contacto ?? 'Sem telefone' }}
                                    @if($patient->acompanhante_notificar_sms)
                                        <span class="text-emerald-700 font-bold ml-1.5"><i class="fas fa-comment-sms"></i> Notifica SMS</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-surface-500 italic text-2xs">Sem acompanhante cadastrado.</p>
                            @endif
                        </div>
                    </div>

                    @if ($patient->alergias)
                        <div class="mt-4 bg-gold-50 border border-gold-200 text-gold-900 p-3 rounded-lg flex items-center gap-2 text-xs">
                            <i class="fas fa-exclamation-triangle text-gold-600 text-sm shrink-0"></i>
                            <div>
                                <strong class="font-semibold">Alergias:</strong> {{ $patient->alergias }}
                            </div>
                        </div>
                    @endif

                    @if ($patient->historico_medico)
                        <div class="mt-4 pt-3 border-t border-surface-100">
                            <span class="text-xs font-semibold text-surface-700 block mb-1">Histórico Médico:</span>
                            <p class="text-xs text-surface-600 bg-surface-50 p-2.5 rounded-lg border border-surface-100 leading-relaxed">{{ $patient->historico_medico }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card Oficial ARO MISAU & Guia de Transferência --}}
            @php
                $aroMisau = $patient->estratificacao_aro_misau;
            @endphp
            @if($aroMisau['is_aro'] || $patient->risco_isoimunizacao_rh)
                <div class="card-tw border-l-4 {{ $aroMisau['nivel'] === 'Nivel_III' ? 'border-l-crimson-500 bg-crimson-50/20' : 'border-l-amber-500 bg-amber-50/20' }}">
                    <div class="card-header-tw flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="badge-{{ $aroMisau['nivel'] === 'Nivel_III' ? 'danger' : 'warning' }} text-xs font-bold uppercase">
                                <i class="fas fa-hospital-user mr-1"></i> {{ $aroMisau['label'] }}
                            </span>
                        </div>
                        <span class="text-2xs text-surface-500 font-medium">Protocolo de Referência MISAU Moçambique</span>
                    </div>
                    <div class="card-body-tw space-y-3">
                        @if(!empty($aroMisau['motivos']))
                            <div>
                                <span class="text-2xs uppercase tracking-wider font-bold text-surface-600 block mb-1">Critérios de Risco Identificados:</span>
                                <ul class="list-disc list-inside text-xs text-surface-800 space-y-1">
                                    @foreach($aroMisau['motivos'] as $motivo)
                                        <li class="font-medium text-crimson-900">{{ $motivo }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($aroMisau['checklist_transferencia']))
                            <div class="pt-2 border-t border-surface-200">
                                <span class="text-2xs uppercase tracking-wider font-bold text-surface-600 block mb-1.5">
                                    <i class="fas fa-clipboard-check text-brand-600 mr-1"></i> Checklist de Transferência e Cuidados Obrigatórios:
                                </span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    @foreach($aroMisau['checklist_transferencia'] as $item)
                                        <div class="flex items-start gap-1.5 text-surface-700 bg-white p-2 rounded-lg border border-surface-200">
                                            <i class="fas fa-check-circle text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                                            <span>{{ $item }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($patient->risco_isoimunizacao_rh)
                            <div class="bg-indigo-50 border border-indigo-200 text-indigo-900 p-2.5 rounded-lg text-xs flex items-center gap-2">
                                <i class="fas fa-dna text-indigo-600 shrink-0 text-sm"></i>
                                <div>
                                    <strong>Alerta de Incompatibilidade Rh:</strong> Mãe {{ $patient->tipo_sanguineo }} e Parceiro {{ $patient->tipo_sanguineo_parceiro }}. Realizar teste de <strong>Coombs Indireto na 30ª semana</strong> de gestação.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Tabs: Consultas, Antecedentes Obstétricos e Partos (Alpine.js) --}}
            <div class="card-tw overflow-hidden" x-data="{ activeTab: 'consultas' }">
                <div class="border-b border-surface-200 bg-surface-50/50 px-4 pt-3 flex gap-2 overflow-x-auto">
                    <button @click="activeTab = 'consultas'"
                            :class="activeTab === 'consultas' ? 'bg-white border-brand-500 text-brand-700 shadow-sm' : 'text-surface-500 hover:text-surface-800'"
                            class="px-4 py-2 text-xs font-semibold rounded-t-lg border-b-2 transition-all flex items-center gap-2 shrink-0">
                        <i class="fas fa-calendar-check text-xs"></i>
                        <span>Consultas CPN ({{ $patient->consultations->count() }})</span>
                    </button>

                    <button @click="activeTab = 'antecedentes'"
                            :class="activeTab === 'antecedentes' ? 'bg-white border-brand-500 text-brand-700 shadow-sm' : 'text-surface-500 hover:text-surface-800'"
                            class="px-4 py-2 text-xs font-semibold rounded-t-lg border-b-2 transition-all flex items-center gap-2 shrink-0">
                        <i class="fas fa-history text-xs"></i>
                        <span>Antecedentes Obstétricos ({{ $patient->obstetricHistories->count() }})</span>
                    </button>

                    <button @click="activeTab = 'partos'"
                            :class="activeTab === 'partos' ? 'bg-white border-brand-500 text-brand-700 shadow-sm' : 'text-surface-500 hover:text-surface-800'"
                            class="px-4 py-2 text-xs font-semibold rounded-t-lg border-b-2 transition-all flex items-center gap-2 shrink-0">
                        <i class="fas fa-baby text-xs"></i>
                        <span>Maternidade & Partos</span>
                        @if ($patient->births->count() > 0)
                            <span class="badge-info text-2xs px-1.5 py-0.2">{{ $patient->births->count() }}</span>
                        @endif
                    </button>
                </div>

                <div class="p-5">
                    {{-- Tab: Antecedentes Obstétricos --}}
                    <div x-show="activeTab === 'antecedentes'" style="display: none;">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h5 class="text-sm font-bold text-surface-900">Histórico de Gestações Anteriores (FPN MISAU)</h5>
                                <p class="text-xs text-surface-500">Registo das gravidezes anteriores para identificação de risco obstétrico prévio</p>
                            </div>
                            <a href="{{ route('patients.edit', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Gerir Antecedentes</span>
                            </a>
                        </div>

                        @if($patient->obstetricHistories->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table-tw">
                                    <thead>
                                        <tr>
                                            <th class="w-12 text-center">Nº</th>
                                            <th>Ano</th>
                                            <th>Tipo Parto</th>
                                            <th>Local Parto</th>
                                            <th class="text-center">Prematuro</th>
                                            <th class="text-center">Gemelar</th>
                                            <th class="text-center">Desfecho</th>
                                            <th>Peso RN</th>
                                            <th>Comentários</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($patient->obstetricHistories as $hist)
                                            <tr>
                                                <td class="text-center font-bold text-xs">{{ $hist->numero_gravidez }}ª</td>
                                                <td class="font-semibold text-xs">{{ $hist->ano ?? 'N/D' }}</td>
                                                <td>
                                                    <span class="badge-{{ $hist->tipo_parto === 'cesariana' ? 'warning' : ($hist->tipo_parto === 'ventosa_forceps' ? 'danger' : 'neutral') }} text-2xs">
                                                        {{ $hist->tipo_parto_label }}
                                                    </span>
                                                </td>
                                                <td class="text-xs">{{ $hist->local_parto_label }}</td>
                                                <td class="text-center text-xs">
                                                    @if($hist->prematuro)
                                                        <span class="badge-danger text-2xs">Sim</span>
                                                    @else
                                                        <span class="text-surface-400">Não</span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-xs">
                                                    @if($hist->gemelar)
                                                        <span class="badge-info text-2xs">Sim</span>
                                                    @else
                                                        <span class="text-surface-400">Não</span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-xs">
                                                    @if($hist->nado_morto)
                                                        <span class="badge-danger text-2xs">Natimorto</span>
                                                    @elseif($hist->tipo_aborto !== 'nenhum')
                                                        <span class="badge-warning text-2xs">Aborto ({{ ucfirst($hist->tipo_aborto) }})</span>
                                                    @else
                                                        <span class="badge-success text-2xs">Nato Vivo</span>
                                                    @endif
                                                </td>
                                                <td class="text-xs font-mono">
                                                    @if($hist->peso_rn_gramas)
                                                        <span class="{{ $hist->peso_rn_gramas < 2500 ? 'text-crimson-600 font-bold' : ($hist->peso_rn_gramas > 4000 ? 'text-amber-600 font-bold' : 'text-surface-800') }}">
                                                            {{ number_format($hist->peso_rn_gramas, 0) }} g
                                                        </span>
                                                    @else
                                                        <span class="text-surface-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-xs text-surface-600">{{ $hist->comentarios ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 bg-surface-50 rounded-xl border border-dashed border-surface-200">
                                <i class="fas fa-female text-3xl text-surface-400 mb-2"></i>
                                <p class="text-xs text-surface-600 font-medium">Primigesta ou nenhum antecedente obstétrico cadastrado.</p>
                                <a href="{{ route('patients.edit', $patient) }}" class="btn-primary-tw btn-sm-tw mt-3 inline-flex">
                                    <i class="fas fa-edit text-xs"></i>
                                    <span>Adicionar Antecedentes na FPN</span>
                                </a>
                            </div>
                        @endif
                    </div>
                    {{-- Tab: Consultas --}}
                    <div x-show="activeTab === 'consultas'">
                        @if ($patient->consultations->count() > 0)
                            <div class="space-y-4">
                                @foreach ($patient->consultations->sortByDesc('data_consulta') as $consultation)
                                    <div class="p-4 rounded-xl border border-surface-200 hover:border-brand-200 transition-colors bg-white shadow-sm">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-surface-100">
                                            <div class="flex items-center gap-2">
                                                <span class="badge-info">
                                                    <i class="fas fa-calendar-alt text-2xs mr-1"></i>
                                                    {{ $consultation->data_consulta->format('d/m/Y H:i') }}
                                                </span>
                                                <h6 class="font-semibold text-surface-900 text-sm">{{ $consultation->tipo_consulta_label }}</h6>
                                            </div>
                                            <span class="badge-{{ $consultation->status === 'realizada' ? 'success' : 'warning' }}">
                                                {{ ucfirst($consultation->status) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-3 text-xs">
                                            @if ($consultation->semanas_gestacao)
                                                <div>
                                                    <span class="text-surface-400 block">Semanas:</span>
                                                    <span class="font-medium text-surface-800">{{ $consultation->semanas_gestacao }}ª sem</span>
                                                </div>
                                            @endif

                                            @if ($consultation->peso)
                                                <div>
                                                    <span class="text-surface-400 block">Peso:</span>
                                                    <span class="font-medium text-surface-800">{{ $consultation->peso }} kg</span>
                                                </div>
                                            @endif

                                            @if ($consultation->pressao_arterial)
                                                <div>
                                                    <span class="text-surface-400 block">Pressão Arterial:</span>
                                                    <span class="font-medium text-surface-800">{{ $consultation->pressao_arterial }} mmHg</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if ($consultation->observacoes)
                                            <p class="text-xs text-surface-600 bg-surface-50 p-2.5 rounded-lg mb-2">
                                                <strong>Obs:</strong> {{ $consultation->observacoes }}
                                            </p>
                                        @endif

                                        @if ($consultation->exams->count() > 0)
                                            <div class="flex items-center gap-1 flex-wrap mt-2">
                                                <span class="text-2xs font-semibold text-surface-400 uppercase">Exames:</span>
                                                @foreach ($consultation->exams as $exam)
                                                    <span class="badge-neutral text-2xs">{{ $exam->tipo_exame_label }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-2 text-2xs text-surface-400 flex items-center gap-1">
                                            <i class="fas fa-user-md"></i>
                                            <span>{{ $consultation->user->name ?? 'Profissional' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-full bg-surface-100 text-surface-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-calendar-times text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-surface-600">Nenhuma consulta registrada ainda.</p>
                                <a href="{{ route('consultations.create', $patient) }}" class="btn-primary-tw btn-sm-tw mt-3 inline-flex">
                                    <i class="fas fa-calendar-plus text-xs"></i>
                                    <span>Agendar Primeira Consulta</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Tab: Partos --}}
                    <div x-show="activeTab === 'partos'" style="display: none;">
                        @if ($patient->births->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table-tw">
                                    <thead>
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>Tipo</th>
                                            <th>Local</th>
                                            <th>Bebê</th>
                                            <th>Status</th>
                                            <th class="text-right">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($patient->births as $birth)
                                            <tr>
                                                <td class="font-medium">{{ $birth->data_hora_parto->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge-{{ $birth->tipo_parto === 'normal' ? 'success' : 'warning' }}">
                                                        {{ $birth->tipo_parto_formatado }}
                                                    </span>
                                                </td>
                                                <td>{{ $birth->local_parto ?? 'Não informado' }}</td>
                                                <td class="text-xs">
                                                    @if ($birth->sexo_bebe)
                                                        {{ ucfirst($birth->sexo_bebe) }}, {{ $birth->peso_formatado }}, {{ $birth->altura_formatada }}
                                                    @else
                                                        Não informado
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge-{{ $birth->status_bebe === 'vivo_saudavel' ? 'success' : 'danger' }}">
                                                        {{ $birth->status_bebe_formatado }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('births.show', $birth) }}" class="btn-secondary-tw btn-sm-tw">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-full bg-surface-100 text-surface-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-baby-carriage text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-surface-600">Nenhum parto registrado ainda.</p>
                                @if ($patient->status_atual === 'gestante' && $patient->podeRegistrarParto())
                                    <a href="{{ route('births.create', $patient) }}" class="btn-primary-tw btn-sm-tw mt-3 inline-flex">
                                        <i class="fas fa-baby text-xs"></i>
                                        <span>Registrar Parto</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar (1 Column) --}}
        <div class="space-y-6">

            {{-- Card Status Gestação / Pós-Parto --}}
            <div class="card-tw">
                <div class="card-header-tw">
                    <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">
                        {{ $patient->status_atual === 'pos_parto' ? 'Informações do Pós-Parto' : 'Status da Gestação' }}
                    </h6>
                </div>
                <div class="card-body-tw">
                    @if ($patient->status_atual === 'pos_parto')
                        @if ($patient->ultimoParto)
                            <div class="text-center mb-4">
                                <div class="text-4xl font-extrabold text-brand-600">
                                    {{ now()->diffInDays($patient->ultimoParto->data_hora_parto) }}
                                </div>
                                <span class="text-2xs uppercase tracking-wider text-surface-500 font-semibold">dias pós-parto</span>
                            </div>

                            <div class="bg-brand-50 border border-brand-200 text-brand-800 p-2.5 rounded-lg text-xs mb-3">
                                <i class="fas fa-baby mr-1"></i> Parto: {{ $patient->ultimoParto->data_hora_parto->format('d/m/Y') }}
                            </div>

                            @if ($patient->ultimoParto->alta_hospitalar)
                                <p class="text-xs text-surface-600 mb-1">
                                    <strong>Alta hospitalar:</strong> {{ $patient->ultimoParto->alta_hospitalar->format('d/m/Y') }}
                                </p>
                            @endif

                            @if ($patient->ultimoParto->condicoes_pos_parto)
                                <div class="bg-ocean-50 border border-ocean-200 text-ocean-900 p-2.5 rounded-lg text-xs mt-3">
                                    <strong>Condições:</strong> {{ $patient->ultimoParto->condicoes_pos_parto }}
                                </div>
                            @endif
                        @endif
                    @else
                        @if ($patient->semanas_gestacao)
                            <div class="text-center mb-4">
                                <div class="text-4xl font-extrabold text-brand-600">{{ $patient->semanas_gestacao }}</div>
                                <span class="text-2xs uppercase tracking-wider text-surface-500 font-semibold block">semanas de gestação</span>
                                @if($patient->idade_gestacional_detalhada)
                                    <span class="text-xs text-brand-700 font-medium bg-brand-50 px-2 py-0.5 rounded-full inline-block mt-1 border border-brand-200">{{ $patient->idade_gestacional_detalhada }}</span>
                                @endif
                            </div>

                            @php
                                $trimestre = $patient->semanas_gestacao <= 13 ? 1 : ($patient->semanas_gestacao <= 27 ? 2 : 3);
                                $progresso = min(100, ($patient->semanas_gestacao / 40) * 100);
                            @endphp

                            <div class="mb-4">
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-surface-700">{{ $trimestre }}º Trimestre</span>
                                    <span class="text-brand-600">{{ number_format($progresso, 1) }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-surface-100 overflow-hidden">
                                    <div class="h-full bg-brand-500 rounded-full transition-all duration-500" style="width: {{ $progresso }}%"></div>
                                </div>
                            </div>

                            @if ($patient->data_provavel_parto)
                                @php
                                    $diasRestantes = now()->diffInDays($patient->data_provavel_parto, false);
                                @endphp
                                <div class="p-3 rounded-lg border text-xs font-medium {{ $diasRestantes <= 28 ? 'bg-gold-50 border-gold-200 text-gold-900' : 'bg-ocean-50 border-ocean-200 text-ocean-900' }}">
                                    <i class="fas fa-calendar mr-1"></i>
                                    @if ($diasRestantes > 0)
                                        {{ $diasRestantes }} dias para o parto
                                    @elseif($diasRestantes == 0)
                                        Data provável é hoje!
                                    @else
                                        {{ abs($diasRestantes) }} dias de atraso
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="text-center text-surface-400 py-4">
                                <i class="fas fa-question-circle text-2xl mb-1"></i>
                                <p class="text-xs">Informe a DUM para acompanhar a gestação</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Próximas Consultas --}}
            <div class="card-tw">
                <div class="card-header-tw">
                    <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Próximas Consultas</h6>
                </div>
                <div class="card-body-tw space-y-3">
                    @php
                        $proximasConsultas = $patient->consultations()
                            ->where('data_consulta', '>', now())
                            ->orderBy('data_consulta')
                            ->limit(3)
                            ->get();
                    @endphp

                    @if ($proximasConsultas->count() > 0)
                        @foreach ($proximasConsultas as $consulta)
                            <div class="p-2.5 rounded-lg bg-surface-50 border border-surface-100 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-surface-900">{{ $consulta->data_consulta->format('d/m/Y H:i') }}</p>
                                    <p class="text-2xs text-surface-500">{{ $consulta->tipo_consulta_label }}</p>
                                </div>
                                <span class="badge-info text-2xs">{{ $consulta->status }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-surface-400 text-center py-2">Nenhuma consulta agendada</p>
                        <a href="{{ route('consultations.create', $patient) }}" class="btn-primary-tw btn-sm-tw w-full">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Agendar Consulta</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Nova Gestação (pós-parto) --}}
            @if ($patient->status_atual === 'pos_parto')
                <div class="card-tw">
                    <div class="card-header-tw">
                        <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Nova Gestação</h6>
                    </div>
                    <div class="card-body-tw">
                        <form action="{{ route('births.nova-gestacao', $patient) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="data_ultima_menstruacao" class="label-tw text-xs">Data da Última Menstruação</label>
                                <input type="date" class="input-tw" name="data_ultima_menstruacao" id="data_ultima_menstruacao" required>
                            </div>
                            <button type="submit" class="btn-primary-tw btn-sm-tw w-full">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Registrar Nova Gestação</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- MODAL DE TRANSFERÊNCIA / INATIVAÇÃO DE PACIENTE (MISAU) --}}
    <div x-show="transferModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/50 backdrop-blur-xs overflow-y-auto"
         @keydown.escape.window="transferModalOpen = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-6 space-y-5 border border-surface-200 my-8 max-h-[90vh] overflow-y-auto" @click.away="transferModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </div>
                    <h3 class="font-bold text-surface-900 text-base">Transferência / Inativação de Paciente</h3>
                </div>
                <button type="button" @click="transferModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('patients.transfer', $patient) }}" class="space-y-4">
                @csrf

                <div class="p-3 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900">
                    <p class="font-bold">Paciente: {{ $patient->nome_completo }}</p>
                    <p class="text-2xs text-brand-700">Ao registar a transferência, será gerada automaticamente a Guia Oficial de Transferência MISAU e as visitas no terreno serão dispensadas.</p>
                </div>

                {{-- Tipo de Saída & Data --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label-tw">Tipo de Saída / Encaminhamento <span class="text-crimson-500">*</span></label>
                        <select name="tipo_saida" required class="input-tw text-xs">
                            <option value="transferencia_us">Transferência para outra US (Distrito)</option>
                            <option value="transferencia_provincia">Transferência Inter-Provincial</option>
                            <option value="mudanca_residencia">Mudança de Bairro / Residência</option>
                            <option value="obito">Óbito Materno/Fetal</option>
                            <option value="abandono">Abandono de Seguimento</option>
                            <option value="outro">Outro Motivo</option>
                        </select>
                    </div>

                    <div>
                        <label class="label-tw">Data da Transferência / Saída <span class="text-crimson-500">*</span></label>
                        <input type="date" name="data_transferencia" value="{{ now()->format('Y-m-d') }}" required class="input-tw text-xs">
                    </div>
                </div>

                {{-- Unidade Sanitária & Província Destino --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label-tw">Unidade Sanitária de Destino</label>
                        <input type="text" name="unidade_sanitaria_destino" placeholder="Ex: Hospital Geral de Mavalane" class="input-tw text-xs" list="us-list">
                        <datalist id="us-list">
                            <option value="Hospital Central de Maputo (HCM)">
                            <option value="Hospital Geral de Mavalane">
                            <option value="Hospital Geral de Chamanculo">
                            <option value="Hospital Geral José Macamo">
                            <option value="Hospital Provincial de Xai-Xai">
                            <option value="Hospital Central da Beira">
                            <option value="Hospital Central de Nampula">
                            <option value="Hospital Provincial de Tete">
                            <option value="Hospital Provincial de Pemba">
                            <option value="Hospital Provincial de Quelimane">
                        </datalist>
                    </div>

                    <div>
                        <label class="label-tw">Província de Destino</label>
                        <select name="provincia_destino" class="input-tw text-xs">
                            <option value="">Selecione a província...</option>
                            <option value="Maputo Cidade">Maputo Cidade</option>
                            <option value="Maputo Província">Maputo Província</option>
                            <option value="Gaza">Gaza</option>
                            <option value="Inhambane">Inhambane</option>
                            <option value="Sofala">Sofala</option>
                            <option value="Manica">Manica</option>
                            <option value="Tete">Tete</option>
                            <option value="Zambézia">Zambézia</option>
                            <option value="Nampula">Nampula</option>
                            <option value="Cabo Delgado">Cabo Delgado</option>
                            <option value="Niassa">Niassa</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Distrito de Destino (Opcional)</label>
                    <input type="text" name="distrito_destino" placeholder="Ex: Kamubukwana, Matola, Xai-Xai..." class="input-tw text-xs">
                </div>

                <div>
                    <label class="label-tw">Motivo Clínico da Transferência <span class="text-crimson-500">*</span></label>
                    <input type="text" name="motivo_transferencia" placeholder="Ex: Alto Risco Obstétrico (ARO Nível III), Mudança de Bairro, Cesariana..." required class="input-tw text-xs">
                </div>

                <div>
                    <label class="label-tw">Resumo Clínico / Observações para a US Receptora</label>
                    <textarea name="resumo_clinico_transferencia" rows="3" placeholder="Idade gestacional, medicações administradas, sinais de perigo, exames realizados..." class="input-tw text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                    <button type="button" @click="transferModalOpen = false" class="btn-secondary-tw btn-sm-tw">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw bg-crimson-600 hover:bg-crimson-700">
                        <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                        <span>Confirmar Transferência</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

