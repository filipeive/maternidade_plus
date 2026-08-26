@extends('layouts.app-tw')

@section('title', 'Central de Ajuda & IA')
@section('page-title', 'Central de Ajuda & Assistente Virtual')
@section('title-icon', 'fa-headset')

@section('breadcrumbs')
    <span class="active">Ajuda & IA</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="aiAssistant()">

    {{-- Interface Principal de Chat com IA (2/3) --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="card-tw overflow-hidden flex flex-col h-[650px]">
            {{-- Cabeçalho do Chat --}}
            <div class="card-header-tw bg-gradient-to-r from-brand-600 to-brand-700 text-white p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg shadow-sm">
                        <i class="fas fa-robot text-gold-300"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            Assistente IA Maternidade+
                            <span class="badge-warning text-2xs px-2 py-0.5">MISAU Moçambique</span>
                        </h3>
                        <p class="text-xs text-white/80">Guia virtual para navegação no sistema e normas pré-natais do MISAU</p>
                    </div>
                </div>
                <button @click="clearChat()" class="btn-ghost-tw btn-sm-tw text-white/80 hover:text-white hover:bg-white/10">
                    <i class="fas fa-trash-can text-xs"></i>
                    <span>Limpar Chat</span>
                </button>
            </div>

            {{-- Janela de Histórico do Chat --}}
            <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-surface-50/50" id="chat-box">
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="flex gap-3" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                        <template x-if="msg.role === 'assistant'">
                            <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center shrink-0 text-xs shadow-sm">
                                <i class="fas fa-robot"></i>
                            </div>
                        </template>
                        <div class="max-w-[85%] rounded-2xl p-3.5 text-xs leading-relaxed shadow-xs"
                             :class="msg.role === 'user' ? 'bg-brand-600 text-white rounded-tr-none' : 'bg-white border border-surface-200 text-surface-900 rounded-tl-none'">
                            <p x-html="formatMessage(msg.content)" class="whitespace-pre-line"></p>
                        </div>
                    </div>
                </template>

                <div x-show="loading" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center shrink-0 text-xs">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="bg-white border border-surface-200 p-3.5 rounded-2xl rounded-tl-none text-xs text-surface-500 flex items-center gap-2 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-brand-500 animate-ping"></span>
                        <span>O assistente IA está a processar a resposta...</span>
                    </div>
                </div>
            </div>

            {{-- Perguntas Rápidas com Ícones FontAwesome --}}
            <div class="p-3 border-t border-surface-100 bg-white flex items-center gap-2 overflow-x-auto whitespace-nowrap">
                <span class="text-2xs font-bold uppercase tracking-wider text-surface-400 shrink-0">Perguntas Rápidas:</span>
                
                <button @click="sendPreset('Como registar uma consulta de puerpério de 7 dias pós-parto?')"
                        class="btn-secondary-tw text-2xs py-1.5 px-3 rounded-full shrink-0 flex items-center gap-1.5 hover:border-brand-300 hover:text-brand-700">
                    <i class="fas fa-baby text-brand-600 text-xs"></i>
                    <span>Pós-Parto (Puerpério)</span>
                </button>

                <button @click="sendPreset('Quais são as doses recomendadas de IPTp-SP para prevenção da Malária?')"
                        class="btn-secondary-tw text-2xs py-1.5 px-3 rounded-full shrink-0 flex items-center gap-1.5 hover:border-gold-300 hover:text-gold-700">
                    <i class="fas fa-pills text-gold-600 text-xs"></i>
                    <span>IPTp (Malária)</span>
                </button>

                <button @click="sendPreset('Como tratar um Alerta Precoce de Alto Risco de paciente sem consulta?')"
                        class="btn-secondary-tw text-2xs py-1.5 px-3 rounded-full shrink-0 flex items-center gap-1.5 hover:border-crimson-300 hover:text-crimson-700">
                    <i class="fas fa-triangle-exclamation text-crimson-600 text-xs"></i>
                    <span>Alertas Precoces</span>
                </button>

                <button @click="sendPreset('Quais os exames obrigatórios no 1º Trimestre em Moçambique?')"
                        class="btn-secondary-tw text-2xs py-1.5 px-3 rounded-full shrink-0 flex items-center gap-1.5 hover:border-ocean-300 hover:text-ocean-700">
                    <i class="fas fa-vial-virus text-ocean-600 text-xs"></i>
                    <span>Exames 1º Trimestre</span>
                </button>
            </div>

            {{-- Formulário de Envio --}}
            <div class="p-3 bg-white border-t border-surface-200">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                    <input type="text"
                           x-model="inputPrompt"
                           placeholder="Digite sua dúvida clínica ou sobre o sistema Maternidade+..."
                           class="input-tw flex-1"
                           :disabled="loading">
                    <button type="submit" class="btn-primary-tw shrink-0" :disabled="loading || !inputPrompt.trim()">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Enviar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Barra Lateral Direita: Navegação e Suporte (1/3) --}}
    <div class="space-y-6">
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <i class="fas fa-book-bookmark text-brand-600 text-sm"></i>
                    <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Recursos da Central de Ajuda</h6>
                </div>
            </div>
            <div class="card-body-tw space-y-3">
                <a href="{{ route('help.manual') }}" class="p-3.5 rounded-xl border border-surface-200 hover:border-brand-300 hover:bg-brand-50/40 transition-all flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-base group-hover:scale-105 transition-transform shrink-0">
                        <i class="fas fa-book-medical"></i>
                    </div>
                    <div>
                        <h6 class="font-bold text-surface-900 text-xs group-hover:text-brand-700">Manual do Utilizador</h6>
                        <p class="text-2xs text-surface-500">Guia completo dos módulos e fluxos do sistema</p>
                    </div>
                </a>

                <a href="{{ route('help.faq') }}" class="p-3.5 rounded-xl border border-surface-200 hover:border-gold-300 hover:bg-gold-50/40 transition-all flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-base group-hover:scale-105 transition-transform shrink-0">
                        <i class="fas fa-circle-question"></i>
                    </div>
                    <div>
                        <h6 class="font-bold text-surface-900 text-xs group-hover:text-gold-700">Perguntas Frequentes</h6>
                        <p class="text-2xs text-surface-500">Respostas sobre protocolos e normas do MISAU</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <i class="fas fa-headset text-ocean-600 text-sm"></i>
                    <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Suporte Técnico & Contato</h6>
                </div>
            </div>
            <div class="card-body-tw text-xs space-y-3">
                <p class="text-surface-600">Para suporte técnico da Unidade Sanitária ou assistência de conta:</p>
                <div class="p-3 bg-surface-50 rounded-lg border border-surface-200/60 space-y-2 font-medium">
                    <p class="flex items-center gap-2 text-surface-800"><i class="fas fa-envelope text-brand-600 w-4 text-center"></i> suporte@maternidadeplus.gov.mz</p>
                    <p class="flex items-center gap-2 text-surface-800"><i class="fas fa-phone text-brand-600 w-4 text-center"></i> +258 84 123 4567 / 86 213 4230</p>
                    <p class="flex items-center gap-2 text-surface-800"><i class="fas fa-hospital text-brand-600 w-4 text-center"></i> MISAU — Direcção de Saúde Zambézia</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function aiAssistant() {
        return {
            inputPrompt: '',
            loading: false,
            messages: [
                {
                    role: 'assistant',
                    content: 'Olá! Sou o Assistente IA do Maternidade+. Posso ajudar com orientações sobre como utilizar o sistema ou esclarecer dúvidas sobre os protocolos de Cuidados Pré-Natais, Parto e Puerpério do MISAU Moçambique. Como posso ajudar hoje?'
                }
            ],

            sendPreset(question) {
                this.inputPrompt = question;
                this.sendMessage();
            },

            async sendMessage() {
                const prompt = this.inputPrompt.trim();
                if (!prompt || this.loading) return;

                this.messages.push({ role: 'user', content: prompt });
                this.inputPrompt = '';
                this.loading = true;
                this.scrollToBottom();

                try {
                    const response = await fetch("{{ route('help.ai.ask') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            prompt: prompt,
                            history: this.messages.slice(-6)
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.messages.push({ role: 'assistant', content: data.response });
                    } else {
                        this.messages.push({ role: 'assistant', content: 'Erro: ' + (data.message || 'Ocorreu um erro ao consultar a IA.') });
                    }
                } catch (e) {
                    this.messages.push({ role: 'assistant', content: 'Erro de ligação com o servidor de IA.' });
                } finally {
                    this.loading = false;
                    this.scrollToBottom();
                }
            },

            clearChat() {
                this.messages = [{
                    role: 'assistant',
                    content: 'Conversa reiniciada. Em que posso ajudar?'
                }];
            },

            formatMessage(text) {
                if (!text) return '';
                return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const chatBox = document.getElementById('chat-box');
                    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
                });
            }
        }
    }
</script>
@endpush
@endsection
