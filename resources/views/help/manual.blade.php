@extends('layouts.app-tw')

@section('title', 'Manual do Utilizador')
@section('page-title', 'Manual do Utilizador — Maternidade+')
@section('title-icon', 'fa-book-medical')

@section('breadcrumbs')
    <a href="{{ route('help.index') }}">Ajuda & IA</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Manual</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card-tw p-6">
        <div class="flex items-center justify-between border-b border-surface-100 pb-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-surface-900">Guia de Utilização do Sistema</h2>
                <p class="text-xs text-surface-500">Manual operacional para Enfermeiras SMI, Médicos e Técnicos de Saúde de Moçambique</p>
            </div>
            <a href="{{ route('help.index') }}" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-robot text-xs"></i>
                <span>Abrir Assistente IA</span>
            </a>
        </div>

        <div class="space-y-8 text-xs text-surface-700 leading-relaxed">

            {{-- Modulo 1: Gestantes --}}
            <section class="space-y-3">
                <h3 class="text-sm font-bold text-brand-700 flex items-center gap-2">
                    <i class="fas fa-person-pregnant text-base"></i> 1. Gestão de Gestantes (Inscrição & Perfil)
                </h3>
                <p>O módulo de Gestantes é o ponto central do acompanhamento pré-natal:</p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-surface-600">
                    <li><strong>Cadastrar Nova Gestante:</strong> Aceda a <code>Clínico -> Gestantes -> Nova Gestante</code>. Preencha os dados pessoais (BI, contacto, endereço completo) e histórico obstétrico (GPA — Gestações, Partos, Abortos e DUM).</li>
                    <li><strong>Perfil da Gestante:</strong> Exibe a idade gestacional atual, trimestre, contagem decrescente para a Data Provável do Parto (DPP), alertas clínicos ativos e abas de histórico de consultas, exames e partos.</li>
                </ul>
            </section>

            {{-- Modulo 2: Consultas ANC --}}
            <section class="space-y-3 border-t border-surface-100 pt-6">
                <h3 class="text-sm font-bold text-brand-700 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-base"></i> 2. Consultas ANC (Cuidados Pré-Natais)
                </h3>
                <p>Registo e agendamento de consultas de rotina e de urgência:</p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-surface-600">
                    <li><strong>Agendamento:</strong> Permite agendar por trimestre (1º, 2º, 3º Trimestre ou Pós-Parto) selecionando a gestante.</li>
                    <li><strong>Medições Vitais:</strong> Durante a consulta, registe Peso, Pressão Arterial (mmHg), Semanas de Gestação, Batimentos Fetais (BCF) e Altura Uterina.</li>
                    <li><strong>Solicitação de Exames:</strong> Na própria consulta, pode solicitar exames laboratoriais diretos.</li>
                </ul>
            </section>

            {{-- Modulo 3: Exames & Laboratorio --}}
            <section class="space-y-3 border-t border-surface-100 pt-6">
                <h3 class="text-sm font-bold text-brand-700 flex items-center gap-2">
                    <i class="fas fa-flask text-base"></i> 3. Exames Laboratoriais & Rastreio MISAU
                </h3>
                <p>Gestão de testes rápidos de rastreio de rotina:</p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-surface-600">
                    <li><strong>Teste de HIV & Sífilis (VDRL):</strong> Registados com prioridade. Se houver resultado reagente, o sistema ativa automaticamente um Alerta Precoce no painel.</li>
                    <li><strong>Fila de Pendentes:</strong> O laboratório consulta os exames solicitados e lança os laudos e resultados.</li>
                </ul>
            </section>

            {{-- Modulo 4: Parto & Puerperio --}}
            <section class="space-y-3 border-t border-surface-100 pt-6">
                <h3 class="text-sm font-bold text-brand-700 flex items-center gap-2">
                    <i class="fas fa-baby text-base"></i> 4. Registo de Parto & Acompanhamento Pós-Parto (Puerpério)
                </h3>
                <p>Fluxo de encerramento da gestação e cuidados pós-parto:</p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-surface-600">
                    <li><strong>Como Registar um Parto:</strong> Na ficha da gestante ou diretamente na resolução de um Alerta no painel de Alertas Precoces, clique em <code>Registar Parto</code>.</li>
                    <li><strong>Transição Automática:</strong> Ao guardar o parto (dados do parto, tipo eutócico/cesariana, APGAR, peso e sexo do bebê), o status da paciente muda para <code>pos_parto</code>.</li>
                    <li><strong>Consultas de Puerpério MISAU:</strong> O sistema agenda automaticamente o calendário puerperal recomendado: 1ª consulta (48h/alta), 2ª consulta (7 dias) e 3ª consulta (28 dias / 6 semanas com planeamento familiar).</li>
                </ul>
            </section>

            {{-- Modulo 5: Alertas Precoces --}}
            <section class="space-y-3 border-t border-surface-100 pt-6">
                <h3 class="text-sm font-bold text-brand-700 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-base"></i> 5. Módulo de Alerta Precoce
                </h3>
                <p>Classificação automatizada baseada nos sinais vitais e histórico de presenças:</p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-surface-600">
                    <li><strong>Prioridades:</strong> Alto (Vermelho - risco imediato), Médio (Amarelo - atenção), Baixo (Azul - informativo).</li>
                    <li><strong>Tratar Alertas:</strong> Ao clicar em "Tratar", o profissional pode alterar o status do alerta, agendar visita domiciliária (APE), marcar consulta ou selecionar "Parto Realizado".</li>
                </ul>
            </section>

        </div>
    </div>
</div>
@endsection
