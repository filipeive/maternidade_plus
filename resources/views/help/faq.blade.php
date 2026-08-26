@extends('layouts.app-tw')

@section('title', 'Perguntas Frequentes (FAQ)')
@section('page-title', 'Perguntas Frequentes & Diretrizes MISAU')
@section('title-icon', 'fa-circle-question')

@section('breadcrumbs')
    <a href="{{ route('help.index') }}">Ajuda & IA</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">FAQ</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ openFaq: null }">
    <div class="card-tw p-6">
        <div class="flex items-center justify-between border-b border-surface-100 pb-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-surface-900">Perguntas Frequentes (FAQ)</h2>
                <p class="text-xs text-surface-500">Respostas rápidas sobre regras do sistema e diretrizes do Ministério da Saúde de Moçambique</p>
            </div>
            <a href="{{ route('help.index') }}" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-robot text-xs"></i>
                <span>Tirar Dúvida com IA</span>
            </a>
        </div>

        <div class="space-y-3 text-xs">

            {{-- FAQ 1 --}}
            <div class="border border-surface-200 rounded-xl overflow-hidden">
                <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-4 text-left font-bold text-surface-900 flex items-center justify-between bg-surface-50/50 hover:bg-surface-100/50 transition-colors">
                    <span>1. Quantas consultas pré-natais (ANC) a gestante deve realizar em Moçambique?</span>
                    <i class="fas fa-chevron-down transition-transform duration-200" :class="openFaq === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 1" x-collapse class="p-4 bg-white border-t border-surface-100 text-surface-700 leading-relaxed">
                    Segundo as diretrizes do MISAU, recomenda-se que toda a gestante realize no mínimo **8 contactos de cuidados pré-natais** durante a gravidez. A 1ª consulta deve ocorrer idealmente antes das 12 semanas de gestação.
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div class="border border-surface-200 rounded-xl overflow-hidden">
                <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-4 text-left font-bold text-surface-900 flex items-center justify-between bg-surface-50/50 hover:bg-surface-100/50 transition-colors">
                    <span>2. Como funciona a prevenção da Malária com IPTp-SP na gestante?</span>
                    <i class="fas fa-chevron-down transition-transform duration-200" :class="openFaq === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 2" x-collapse class="p-4 bg-white border-t border-surface-100 text-surface-700 leading-relaxed">
                    A administração do Tratamento Intermitente Preventivo (IPTp-SP) com Sulfadoxina-Pirimetamina inicia a partir da 13ª semana de gestação (2º trimestre). Devem ser administradas no mínimo 3 a 4 doses até ao parto, mantendo um intervalo mínimo de 4 semanas entre cada dose.
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div class="border border-surface-200 rounded-xl overflow-hidden">
                <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-4 text-left font-bold text-surface-900 flex items-center justify-between bg-surface-50/50 hover:bg-surface-100/50 transition-colors">
                    <span>3. O que acontece quando um parto é registrado no Maternidade+?</span>
                    <i class="fas fa-chevron-down transition-transform duration-200" :class="openFaq === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 3" x-collapse class="p-4 bg-white border-t border-surface-100 text-surface-700 leading-relaxed">
                    Ao registar o parto (seja pela Ficha da Gestante ou no painel de Alertas), o status da paciente altera automaticamente para **Pós-Parto (`pos_parto`)**. O sistema encerra os alertas da gestação concluída e agenda o acompanhamento de puerpério (48h, 7 dias e 28 dias).
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div class="border border-surface-200 rounded-xl overflow-hidden">
                <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full p-4 text-left font-bold text-surface-900 flex items-center justify-between bg-surface-50/50 hover:bg-surface-100/50 transition-colors">
                    <span>4. Como o sistema identifica uma gestante faltosa ou de alto risco?</span>
                    <i class="fas fa-chevron-down transition-transform duration-200" :class="openFaq === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 4" x-collapse class="p-4 bg-white border-t border-surface-100 text-surface-700 leading-relaxed">
                    O módulo de Alertas Precoces analisa a ausência de consultas por mais de 30 dias, exames laboratoriais reagentes (HIV, VDRL, anemia grave) ou hipertensão arterial. Gestantes faltosas recebem alerta de acompanhamento para agendamento de visita domiciliária por Agentes Polivalentes de Elementos (APE).
                </div>
            </div>

            {{-- FAQ 5 --}}
            <div class="border border-surface-200 rounded-xl overflow-hidden">
                <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full p-4 text-left font-bold text-surface-900 flex items-center justify-between bg-surface-50/50 hover:bg-surface-100/50 transition-colors">
                    <span>5. Como enviar notificações por SMS para a paciente?</span>
                    <i class="fas fa-chevron-down transition-transform duration-200" :class="openFaq === 5 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 5" x-collapse class="p-4 bg-white border-t border-surface-100 text-surface-700 leading-relaxed">
                    O sistema integra o serviço **httpSMS** para envio de SMS diretos para números de Moçambique (`+258`). Os lembretes podem ser disparados no agendamento de consultas ou na confirmação de parto.
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
