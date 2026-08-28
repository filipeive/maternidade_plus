@extends('layouts.app-tw')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações & Parâmetros Globais')
@section('title-icon', 'fa-sliders')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Configurações</span>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6" x-data="{ activeTab: 'general', logSearch: '' }">

    {{-- Header Banner --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-800 via-brand-700 to-ocean-800 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-md border-none">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20">
                <i class="fas fa-sliders"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span>Painel Central de Parâmetros & Manutenção</span>
                    <span class="badge-neutral text-3xs uppercase bg-white/10 text-white/90">MISAU Moçambique</span>
                </h2>
                <p class="text-xs text-white/70">Configure a Unidade Sanitária, Gateway SMS, Assistente IA, Protocolos Clínicos ARO e Saúde Comunitária</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('settings.backup') }}" class="btn-tw bg-white/15 hover:bg-white/25 text-white border border-white/20 btn-sm-tw font-semibold text-xs">
                <i class="fas fa-download text-xs"></i>
                <span>Backup (.JSON)</span>
            </a>

            <form method="POST" action="{{ route('settings.clear-cache') }}">
                @csrf
                <button type="submit" class="btn-tw bg-gold-400 text-surface-950 hover:bg-gold-300 btn-sm-tw font-bold text-xs shadow-sm">
                    <i class="fas fa-rotate text-xs"></i>
                    <span>Limpar Caches</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Tabs Bar --}}
    <div class="flex flex-wrap items-center gap-1 border-b border-surface-200 overflow-x-auto pb-px">
        <button @click="activeTab = 'general'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                :class="activeTab === 'general' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-hospital-user text-xs"></i>
            <span>1. Unidade Sanitária</span>
        </button>

        <button @click="activeTab = 'sms'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                :class="activeTab === 'sms' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-comment-sms text-xs"></i>
            <span>2. Gateway SMS</span>
        </button>

        <button @click="activeTab = 'ai'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                :class="activeTab === 'ai' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-robot text-xs"></i>
            <span>3. Assistente IA</span>
        </button>

        <button @click="activeTab = 'clinical'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                :class="activeTab === 'clinical' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-stethoscope text-xs"></i>
            <span>4. Protocolos & ARO</span>
        </button>

        <button @click="activeTab = 'community'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                :class="activeTab === 'community' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-house-medical text-xs"></i>
            <span>5. Busca Ativa (APEs)</span>
        </button>

        <button @click="activeTab = 'logs'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap relative"
                :class="activeTab === 'logs' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-terminal text-xs"></i>
            <span>Logs</span>
            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
        </button>

        <button @click="activeTab = 'system'"
                class="py-3 px-3.5 border-b-2 text-xs font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                :class="activeTab === 'system' ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-server text-xs"></i>
            <span>Servidor</span>
        </button>
    </div>

    {{-- ============================================================
         TAB 1: UNIDADE SANITÁRIA & MISAU
         ============================================================ --}}
    <div x-show="activeTab === 'general'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Identificação da Unidade Sanitária (US)</h3>
                    <p class="text-3xs text-surface-400">Estes dados são exibidos no Dashboard, Guias de Transferência e Fichas Pré-Natais</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.update-general') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Nome Oficial da Unidade Sanitária <span class="text-crimson-500">*</span></label>
                    <input type="text" name="unidade_sanitaria" class="input-tw font-semibold" value="{{ $unidadeSanitaria }}" required placeholder="Ex: Centro de Saúde Urbano de Quelimane">
                </div>

                <div>
                    <label class="label-tw">Província</label>
                    <select name="provincia" class="input-tw text-xs">
                        @php
                            $provinciasMoz = ['Maputo Cidade', 'Maputo Província', 'Gaza', 'Inhambane', 'Sofala', 'Manica', 'Tete', 'Zambézia', 'Nampula', 'Cabo Delgado', 'Niassa'];
                        @endphp
                        @foreach($provinciasMoz as $p)
                            <option value="{{ $p }}" {{ $provincia === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label-tw">Distrito / Município</label>
                    <input type="text" name="distrito" class="input-tw" value="{{ $distrito }}" placeholder="Ex: Quelimane, Kamubukwana, Matola...">
                </div>

                <div>
                    <label class="label-tw">Código SISMA / MISAU</label>
                    <input type="text" name="codigo_misau" class="input-tw font-mono" value="{{ $codigoMisau }}" placeholder="Ex: US-0421">
                </div>

                <div>
                    <label class="label-tw">Telefone de Contacto / Urgência Obstétrica</label>
                    <input type="text" name="telefone_maternidade" class="input-tw font-mono" value="{{ $telefoneMaternidade }}" placeholder="Ex: +258 24 212 345">
                </div>

                <div>
                    <label class="label-tw">E-mail Institucional da Maternidade</label>
                    <input type="email" name="email_institucional" class="input-tw" value="{{ $emailInstitucional }}" placeholder="Ex: maternidade@misau.gov.mz">
                </div>

                <div class="md:col-span-2">
                    <label class="label-tw">Médico Chefe / Responsável de SMI</label>
                    <input type="text" name="responsavel_smi" class="input-tw" value="{{ $responsavelSmi }}" placeholder="Ex: Dra. Maria Mondlane (Médica Chefe SMI)">
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-surface-100">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Dados da Unidade</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TAB 2: GATEWAY SMS & MODELOS DE MENSAGENS
         ============================================================ --}}
    <div x-show="activeTab === 'sms'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-ocean-100 text-ocean-700 flex items-center justify-center text-sm">
                    <i class="fas fa-comment-sms"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Configurações do Gateway SMS (httpSMS) & Modelos</h3>
                    <p class="text-3xs text-surface-400">Automação de lembretes de consultas CPN e busca ativa por SMS</p>
                </div>
            </div>
            <a href="{{ route('sms.index') }}" class="btn-secondary-tw btn-xs-tw">
                <span>Central de SMS</span>
                <i class="fas fa-arrow-right text-3xs"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('settings.update-sms') }}" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            <div class="p-4 bg-ocean-50 border border-ocean-200 rounded-xl text-xs text-ocean-900 flex items-start gap-3">
                <i class="fas fa-mobile-screen-button text-ocean-600 text-lg shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold">Integração SMS Ativa:</span>
                    <p class="text-ocean-800 mt-0.5">Utiliza a API do httpSMS para enviar alertas clínicos, notificações de faltosas e lembretes de CPN diretamente para os números das gestantes e parceiros em Moçambique.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Chave de API httpSMS (API Key)</label>
                    <input type="text" name="httpsms_key" value="{{ $httpsmsKey }}" placeholder="Deixe em branco para manter a atual..." class="input-tw font-mono text-xs">
                </div>

                <div>
                    <label class="label-tw">Número do Cartão SIM Remetente (From)</label>
                    <input type="text" name="httpsms_from" value="{{ $httpsmsFrom }}" class="input-tw font-mono text-xs" placeholder="+258862134230">
                </div>

                <div>
                    <label class="label-tw">Antecedência do Lembrete de Consulta</label>
                    <select name="sms_lembrete_dias" class="input-tw text-xs">
                        <option value="1" {{ $smsLembreteDias == '1' ? 'selected' : '' }}>1 dia antes da consulta</option>
                        <option value="2" {{ $smsLembreteDias == '2' ? 'selected' : '' }}>2 dias antes da consulta (Recomendado)</option>
                        <option value="3" {{ $smsLembreteDias == '3' ? 'selected' : '' }}>3 dias antes da consulta</option>
                    </select>
                </div>

                <div class="space-y-2 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="sms_enabled" value="1" {{ $smsEnabled == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-semibold text-surface-800">Ativar Envio Automático de Notificações SMS</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="sms_notificar_parceiro" value="1" {{ $smsNotificarParceiro == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-semibold text-surface-800">Notificar também o parceiro/acompanhante quando disponível</span>
                    </label>
                </div>
            </div>

            {{-- Modelos de Mensagens --}}
            <div class="space-y-3 pt-3 border-t border-surface-100">
                <h4 class="text-xs font-bold text-surface-800 uppercase tracking-wider">Modelos de Mensagem (Tags dinâmicas: {NOME}, {DATA}, {HORA}, {US})</h4>
                
                <div>
                    <label class="label-tw">Modelo SMS de Lembrete de Consulta CPN</label>
                    <textarea name="sms_template_lembrete" rows="2" class="input-tw text-xs font-mono">{{ $smsTemplateLembrete }}</textarea>
                </div>

                <div>
                    <label class="label-tw">Modelo SMS de Busca Ativa para Gestante Faltosa</label>
                    <textarea name="sms_template_falta" rows="2" class="input-tw text-xs font-mono">{{ $smsTemplateFalta }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-surface-100">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Configurações de SMS</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TAB 3: ASSISTENTE CLÍNICO IA
         ============================================================ --}}
    <div x-show="activeTab === 'ai'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Assistente Clínico IA (Google Gemini / OpenRouter)</h3>
                    <p class="text-3xs text-surface-400">Inteligência artificial para suporte à decisão clínica com memória conversacional</p>
                </div>
            </div>
            <a href="{{ route('help.index') }}" class="btn-secondary-tw btn-xs-tw">
                <span>Testar IA na Ajuda</span>
                <i class="fas fa-arrow-right text-3xs"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('settings.update-ai') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Provedor de IA <span class="text-crimson-500">*</span></label>
                    <select name="ai_provider" class="input-tw text-xs">
                        <option value="gemini_direct" {{ $aiProvider === 'gemini_direct' ? 'selected' : '' }}>Google Gemini Direct (Recomendado)</option>
                        <option value="openrouter" {{ $aiProvider === 'openrouter' ? 'selected' : '' }}>OpenRouter API (Claude / GPT / Llama)</option>
                    </select>
                </div>

                <div>
                    <label class="label-tw">Modelo de IA <span class="text-crimson-500">*</span></label>
                    <input type="text" name="ai_model_name" class="input-tw font-mono text-xs" value="{{ $aiModelName }}" placeholder="gemini-2.5-flash ou anthropic/claude-3.5-sonnet">
                </div>

                <div>
                    <label class="label-tw">Temperatura (0.0 = Rigoroso/Clínico, 1.0 = Criativo)</label>
                    <input type="number" name="ai_temperature" step="0.05" min="0" max="1" value="{{ $aiTemperature }}" class="input-tw font-mono text-xs">
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ai_floating_widget" value="1" {{ $aiFloatingWidget == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-semibold text-surface-800">Exibir Widget Flutuante de IA no canto inferior</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="label-tw">Diretrizes & Protocolos Customizados da Unidade (System Prompt Add-on)</label>
                    <textarea name="ai_custom_instructions" rows="3" class="input-tw text-xs" placeholder="Instruções adicionais de protocolo de saúde para guiar as respostas da IA...">{{ $aiCustomInstructions }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-surface-100">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Parâmetros de IA</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TAB 4: PROTOCOLOS CLÍNICOS & REGRAS DE ALERTA ARO
         ============================================================ --}}
    <div x-show="activeTab === 'clinical'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Protocolos Clínicos & Parâmetros de Alerta ARO (MISAU)</h3>
                    <p class="text-3xs text-surface-400">Limites de corte para geração automática de alertas precoces e triagem de risco</p>
                </div>
            </div>
            <a href="{{ route('alertas.index') }}" class="btn-secondary-tw btn-xs-tw">
                <span>Ver Alertas Ativos</span>
                <i class="fas fa-arrow-right text-3xs"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('settings.update-clinical') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Dias para classificar gestante como "Faltosa" (Busca Ativa)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="dias_para_faltosa" min="1" max="30" value="{{ $diasParaFaltosa }}" class="input-tw text-xs" required>
                        <span class="text-xs text-surface-500 whitespace-nowrap">dias após data prevista</span>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Semanas de Gestação para Alerta de Proximidade do Parto</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="semanas_aviso_parto" min="28" max="42" value="{{ $semanasAvisoParto }}" class="input-tw text-xs" required>
                        <span class="text-xs text-surface-500 whitespace-nowrap">semanas de gestação</span>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Limite de Pressão Arterial Sistólica (Alerta Pré-eclâmpsia)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="limite_pa_sistolica" min="120" max="200" value="{{ $limitePaSistolica }}" class="input-tw text-xs" required>
                        <span class="text-xs text-surface-500 whitespace-nowrap">mmHg</span>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Limite de Pressão Arterial Diastólica (Alerta Pré-eclâmpsia)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="limite_pa_diastolica" min="70" max="140" value="{{ $limitePaDiastolica }}" class="input-tw text-xs" required>
                        <span class="text-xs text-surface-500 whitespace-nowrap">mmHg</span>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Limite de Hemoglobina (Hb) para Alerta de Anemia Severa</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" name="limite_hb_anemia" min="4" max="12" value="{{ $limiteHbAnemia }}" class="input-tw text-xs" required>
                        <span class="text-xs text-surface-500 whitespace-nowrap">g/dL</span>
                    </div>
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_gerar_alertas" value="1" {{ $autoGerarAlertas == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-crimson-600 focus:ring-crimson-500">
                        <span class="text-xs font-semibold text-surface-800">Gerar Alertas Precoces Automáticos nos exames e consultas</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-surface-100">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Protocolos Clínicos</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TAB 5: SAÚDE COMUNITÁRIA & VISITAS DE TERRENO (APEs)
         ============================================================ --}}
    <div x-show="activeTab === 'community'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="fas fa-house-medical"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Regras de Saúde Comunitária & Busca Ativa (APEs)</h3>
                    <p class="text-3xs text-surface-400">Automatização de fluxos de terreno para as activistas e agentes polivalentes</p>
                </div>
            </div>
            <a href="{{ route('home_visits.active-search') }}" class="btn-secondary-tw btn-xs-tw">
                <span>Busca Ativa</span>
                <i class="fas fa-arrow-right text-3xs"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('settings.update-community') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Reagendamento automático em caso de "Não Encontrada"</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="visita_dias_reagendamento" min="1" max="15" value="{{ $visitaDiasReagendamento }}" class="input-tw text-xs" required>
                        <span class="text-xs text-surface-500 whitespace-nowrap">dias após a tentativa falhada</span>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_dispensar_visita_na_us" value="1" {{ $autoDispensarVisitaNaUs == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-surface-800">Dispensar visita comunitária automaticamente se a paciente for atendida na US</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="notificar_activista_sms" value="1" {{ $notificarActivistaSms == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-surface-800">Notificar a Activista Comunitária por SMS ao ser-lhe atribuída uma gestante</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-surface-100">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Regras Comunitárias</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TAB 6: LOGS DO SISTEMA
         ============================================================ --}}
    <div x-show="activeTab === 'logs'" class="card-tw">
        <div class="card-header-tw flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-surface-100 text-surface-700 flex items-center justify-center text-sm">
                    <i class="fas fa-terminal"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Logs do Sistema (storage/logs/laravel.log)</h3>
                    <p class="text-3xs text-surface-400">Últimas 80 entradas de execução e registos de atividade</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative w-48 sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                    <input type="text" x-model="logSearch" placeholder="Filtrar logs..." class="input-tw pl-8 py-1.5 text-xs">
                </div>
                <form method="POST" action="{{ route('settings.clear-logs') }}" onsubmit="return confirm('Deseja realmente limpar o ficheiro de logs?');">
                    @csrf
                    <button type="submit" class="btn-secondary-tw btn-sm-tw text-crimson-600 hover:bg-crimson-50 border-crimson-200">
                        <i class="fas fa-trash text-xs"></i>
                        <span>Limpar</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="p-4 bg-surface-950 text-surface-200 font-mono text-2xs rounded-b-xl overflow-x-auto max-h-[500px] overflow-y-auto space-y-1">
            @forelse($systemLogs as $log)
                <div x-show="!logSearch || '{{ strtolower(addslashes($log)) }}'.includes(logSearch.toLowerCase())"
                     class="py-0.5 border-b border-surface-800/50 hover:bg-surface-900 px-1 rounded transition-colors {{ str_contains($log, 'ERROR') ? 'text-crimson-400 font-bold' : (str_contains($log, 'WARNING') ? 'text-gold-400' : 'text-surface-300') }}">
                    {{ $log }}
                </div>
            @empty
                <div class="p-8 text-center text-surface-500">
                    <i class="fas fa-check-circle text-lg mb-1 text-emerald-500"></i>
                    <p>O ficheiro de logs está limpo e sem erros registados.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ============================================================
         TAB 7: SERVIDOR & AMBIENTE
         ============================================================ --}}
    <div x-show="activeTab === 'system'" class="space-y-6">
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-surface-100 text-surface-700 flex items-center justify-center text-sm">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">Diagnóstico do Servidor de Produção</h3>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div class="p-3.5 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-surface-400 text-3xs font-semibold uppercase block mb-1">Versão PHP</span>
                        <strong class="font-mono text-surface-900 text-sm">{{ $systemInfo['php_version'] }}</strong>
                    </div>

                    <div class="p-3.5 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-surface-400 text-3xs font-semibold uppercase block mb-1">Framework Laravel</span>
                        <strong class="font-mono text-surface-900 text-sm">v{{ $systemInfo['laravel_version'] }}</strong>
                    </div>

                    <div class="p-3.5 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-surface-400 text-3xs font-semibold uppercase block mb-1">Servidor Web</span>
                        <strong class="text-surface-900">{{ $systemInfo['server'] }}</strong>
                    </div>

                    <div class="p-3.5 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-surface-400 text-3xs font-semibold uppercase block mb-1">Base de Dados</span>
                        <strong class="font-mono text-surface-900 uppercase">{{ $systemInfo['database'] }} (MySQL / MariaDB)</strong>
                    </div>

                    <div class="p-3.5 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-surface-400 text-3xs font-semibold uppercase block mb-1">Sistema Operativo</span>
                        <strong class="text-surface-900">{{ $systemInfo['os'] }}</strong>
                    </div>

                    <div class="p-3.5 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-surface-400 text-3xs font-semibold uppercase block mb-1">Gateway SMS</span>
                        <strong class="text-emerald-700 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                            {{ $systemInfo['httpsms_key'] }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
