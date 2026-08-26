@extends('layouts.app-tw')

@section('title', 'Configurações')
@section('page-title', 'Configurações & Manutenção do Sistema')
@section('title-icon', 'fa-sliders')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Configurações</span>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6" x-data="{ activeTab: 'general', logSearch: '' }">

    {{-- Header Banner --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20">
                <i class="fas fa-sliders"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Painel de Configurações & Parâmetros</h2>
                <p class="text-xs text-white/70">Gestão da Unidade Sanitária, Gateway SMS, Integração de IA, Logs do Sistema e Manutenção</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.clear-cache') }}">
            @csrf
            <button type="submit" class="btn-tw bg-gold-400 text-surface-900 hover:bg-gold-300 btn-sm-tw font-bold shadow-sm">
                <i class="fas fa-rotate text-xs"></i>
                <span>Limpar & Otimizar Caches</span>
            </button>
        </form>
    </div>

    {{-- Tabs Bar --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-surface-200">
        <button @click="activeTab = 'general'"
                class="py-3 px-3 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                :class="activeTab === 'general' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-hospital-user"></i>
            <span>Unidade Sanitária</span>
        </button>

        <button @click="activeTab = 'sms'"
                class="py-3 px-3 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                :class="activeTab === 'sms' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-comment-sms"></i>
            <span>Serviço de SMS</span>
        </button>

        <button @click="activeTab = 'ai'"
                class="py-3 px-3 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                :class="activeTab === 'ai' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-robot"></i>
            <span>Assistente IA</span>
        </button>

        <button @click="activeTab = 'logs'"
                class="py-3 px-3 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2 relative"
                :class="activeTab === 'logs' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-terminal"></i>
            <span>Logs do Sistema</span>
            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
        </button>

        <button @click="activeTab = 'system'"
                class="py-3 px-3 border-b-2 text-sm font-semibold transition-colors flex items-center gap-2"
                :class="activeTab === 'system' ? 'border-brand-600 text-brand-700' : 'border-transparent text-surface-500 hover:text-surface-800'">
            <i class="fas fa-server"></i>
            <span>Sistema & Servidor</span>
        </button>
    </div>

    {{-- TAB 1: UNIDADE SANITÁRIA --}}
    <div x-show="activeTab === 'general'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Parâmetros da Unidade Sanitária & MISAU</h3>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.update-general') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Nome da Unidade Sanitária</label>
                    <input type="text" name="unidade_sanitaria" class="input-tw font-semibold" value="Centro de Saúde de Quelimane Urbano">
                </div>

                <div>
                    <label class="label-tw">Província / Distrito</label>
                    <input type="text" name="provincia" class="input-tw" value="Zambézia — Quelimane">
                </div>

                <div>
                    <label class="label-tw">Código SISMA / Módulo de Saúde</label>
                    <input type="text" class="input-tw bg-surface-100 font-mono" value="MZ-ZMB-QLM-CS01" disabled>
                </div>

                <div>
                    <label class="label-tw">Telefone de Contacto da Maternidade</label>
                    <input type="text" class="input-tw font-mono" value="+258 24 212 345">
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </div>

    {{-- TAB 2: SERVIÇO DE SMS (Sem input type=password para evitar prompt do navegador) --}}
    <div x-show="activeTab === 'sms'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-ocean-100 text-ocean-700 flex items-center justify-center text-sm">
                    <i class="fas fa-comment-sms"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Gateway de Envio de SMS (httpSMS Driver)</h3>
            </div>
            <a href="{{ route('sms.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-right text-xs"></i>
                <span>Abrir Central de SMS</span>
            </a>
        </div>

        <div class="p-6 space-y-4">
            <div class="p-4 bg-ocean-50 border border-ocean-200 rounded-xl text-xs text-ocean-900 flex items-start gap-3">
                <i class="fas fa-mobile-screen-button text-ocean-600 text-lg shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold">Integração httpSMS Ativa:</span>
                    <p class="text-ocean-800 mt-0.5">O sistema utiliza a API do httpSMS para enviar alertas clínicos, notificações de faltosas e lembretes de consultas diretamente para os telemóveis das pacientes em Moçambique.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Chave API httpSMS</label>
                    <div class="flex items-center gap-2 p-2.5 bg-surface-100 rounded-xl border border-surface-200 text-xs font-mono text-surface-700">
                        <i class="fas fa-key text-ocean-600"></i>
                        <span>{{ $systemInfo['httpsms_key'] }}</span>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Número Remetente E.164 (HTTPSMS_FROM)</label>
                    <div class="flex items-center gap-2 p-2.5 bg-surface-100 rounded-xl border border-surface-200 text-xs font-mono text-surface-700">
                        <i class="fas fa-phone text-ocean-600"></i>
                        <span>{{ $systemInfo['httpsms_from'] }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 text-xs">
                <i class="fas fa-check-circle text-brand-600 text-sm"></i>
                <span class="font-semibold text-surface-800">Envio de SMS automático ativo para Notificação de Faltosas e Puerpério.</span>
            </div>
        </div>
    </div>

    {{-- TAB 3: ASSISTENTE IA --}}
    <div x-show="activeTab === 'ai'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Assistente Clínico Virtual & IA (OpenRouter / Gemini)</h3>
            </div>
            <a href="{{ route('help.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-circle-question text-xs"></i>
                <span>Testar Assistente IA</span>
            </a>
        </div>

        <div class="p-6 space-y-4">
            <div class="p-4 bg-gold-50 border border-gold-200 rounded-xl text-xs text-gold-900 flex items-start gap-3">
                <i class="fas fa-brain text-gold-600 text-lg shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold">IA Treinada para o MISAU:</span>
                    <p class="text-gold-800 mt-0.5">O assistente responde dúvidas clínicas com base nos Manuais de Cuidados Pré-Natais e de Puerpério do Ministério da Saúde de Moçambique (IPTp-SP, TPI, rastreios e consultas puerperais).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Provedor Ativo</label>
                    <input type="text" class="input-tw font-semibold" value="{{ $systemInfo['ai_provider'] }}" disabled>
                </div>

                <div>
                    <label class="label-tw">Modelo Clínico Predefinido</label>
                    <input type="text" class="input-tw font-mono" value="google/gemini-2.5-flash" disabled>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 4: LOGS DO SISTEMA --}}
    <div x-show="activeTab === 'logs'" class="card-tw space-y-4">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-terminal"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-surface-900">Logs de Atividade & Erros do Sistema</h3>
                    <p class="text-2xs text-surface-500">Últimos eventos registados no ficheiro storage/logs/laravel.log</p>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.clear-logs') }}" onsubmit="return confirm('Tem a certeza que deseja limpar todos os logs do sistema?');">
                @csrf
                <button type="submit" class="btn-danger-tw btn-sm-tw">
                    <i class="fas fa-trash-can text-xs"></i>
                    <span>Limpar Ficheiro de Logs</span>
                </button>
            </form>
        </div>

        <div class="p-6 space-y-3">
            {{-- Filtro de Log --}}
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                <input type="text" x-model="logSearch" placeholder="Pesquisar nos logs (ex: SMS, erro, HTTP)..." class="input-tw pl-9 text-xs font-mono">
            </div>

            {{-- Caixa de Código de Logs --}}
            <div class="bg-surface-900 text-surface-100 rounded-2xl p-4 font-mono text-2xs max-h-[420px] overflow-y-auto space-y-1.5 border border-surface-800 shadow-inner">
                @forelse($systemLogs as $index => $logLine)
                    <div x-show="!logSearch || '{{ strtolower(addslashes($logLine)) }}'.includes(logSearch.toLowerCase())"
                         class="py-1 px-2 rounded hover:bg-surface-800/80 transition-colors flex items-start gap-2 border-b border-surface-800/40">
                        <span class="text-surface-500 shrink-0 select-none">#{{ count($systemLogs) - $index }}</span>
                        
                        @if(str_contains($logLine, 'ERROR'))
                            <span class="badge-danger text-3xs px-1.5 py-0.5 uppercase shrink-0">ERROR</span>
                        @elseif(str_contains($logLine, 'WARNING'))
                            <span class="badge-warning text-3xs px-1.5 py-0.5 uppercase shrink-0">WARNING</span>
                        @elseif(str_contains($logLine, 'INFO'))
                            <span class="badge-info text-3xs px-1.5 py-0.5 uppercase shrink-0">INFO</span>
                        @else
                            <span class="badge-neutral text-3xs px-1.5 py-0.5 uppercase shrink-0">LOG</span>
                        @endif

                        <span class="break-all text-surface-200 leading-relaxed">{{ $logLine }}</span>
                    </div>
                @empty
                    <div class="py-12 text-center text-surface-500">
                        <i class="fas fa-file-code text-3xl mb-2"></i>
                        <p class="text-xs">Nenhum registo no ficheiro de log no momento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TAB 5: SISTEMA & SERVIDOR --}}
    <div x-show="activeTab === 'system'" class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-surface-100 text-surface-700 flex items-center justify-center text-sm">
                    <i class="fas fa-server"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Informações Técnicas do Servidor</h3>
            </div>
        </div>

        <div class="p-6 space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Versão PHP</span>
                    <span class="text-sm font-bold font-mono text-surface-900">{{ $systemInfo['php_version'] }}</span>
                </div>

                <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Versão Laravel</span>
                    <span class="text-sm font-bold font-mono text-brand-700">{{ $systemInfo['laravel_version'] }}</span>
                </div>

                <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Sistema Operativo</span>
                    <span class="text-sm font-bold text-surface-900 truncate block">{{ $systemInfo['os'] }}</span>
                </div>

                <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Base de Dados</span>
                    <span class="text-sm font-bold font-mono text-ocean-700 capitalize">{{ $systemInfo['database'] }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-surface-200 flex justify-between items-center">
                <div>
                    <h4 class="font-bold text-surface-900">Manutenção de Caches e Otimização</h4>
                    <p class="text-2xs text-surface-500">Recarregar ficheiros compilados de rotas, Blade templates e configurações do ambiente.</p>
                </div>
                <form method="POST" action="{{ route('settings.clear-cache') }}">
                    @csrf
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-rotate text-xs"></i>
                        <span>Executar Limpeza de Cache</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
