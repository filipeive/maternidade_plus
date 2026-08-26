@extends('layouts.app-tw')

@section('title', 'Detalhes do Parto')
@section('page-title', 'Registo de Parto & Puerpério MISAU')
@section('title-icon', 'fa-baby')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('births.index') }}">Partos</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Detalhes do Parto</span>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6">

    {{-- HEADER & PACIENTE BANNER --}}
    @php
        $patient = $birth->patient;
        $statusColors = [
            'vivo_saudavel' => 'badge-success',
            'vivo_complicacoes' => 'badge-warning',
            'obito_fetal' => 'badge-danger',
            'obito_neonatal' => 'badge-danger',
        ];
        $statusLabels = [
            'vivo_saudavel' => 'Nascido Vivo & Saudável',
            'vivo_complicacoes' => 'Nascido Vivo c/ Complicações',
            'obito_fetal' => 'Óbito Fetal (Nado-Morto)',
            'obito_neonatal' => 'Óbito Neonatal Precoce',
        ];
    @endphp

    <div class="card-tw p-6 bg-gradient-to-r from-brand-800 via-brand-700 to-ocean-800 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-gold-300 text-3xl font-bold border border-white/20 shrink-0 shadow-lg">
                <i class="fas fa-baby"></i>
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-gold-400 text-surface-900 font-extrabold text-3xs uppercase tracking-wider">
                        MISAU CS Quelimane
                    </span>
                    <span class="text-2xs text-white/70">Registo #{{ str_pad($birth->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-white">
                    @if($patient)
                        <a href="{{ route('patients.show', $patient) }}" class="hover:underline flex items-center gap-2">
                            <span>{{ $patient->nome_completo }}</span>
                            <i class="fas fa-arrow-up-right-from-square text-xs text-gold-300"></i>
                        </a>
                    @else
                        <span>Paciente Desconhecida</span>
                    @endif
                </h2>

                <p class="text-xs text-white/80 flex items-center gap-3">
                    <span><i class="fas fa-id-card text-gold-400 mr-1"></i> BI/NID: {{ $patient->documento_bi ?? 'N/A' }}</span>
                    <span>·</span>
                    <span><i class="fas fa-calendar text-emerald-400 mr-1"></i> Parto em: {{ $birth->data_hora_parto?->format('d/m/Y \à\s H:i') ?? 'N/A' }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <button onclick="window.print()" class="btn-tw bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold py-2 px-3.5 shadow-sm">
                <i class="fas fa-print text-xs"></i>
                <span>Imprimir Ficha</span>
            </button>

            <a href="{{ route('births.edit', $birth) }}" class="btn-tw bg-gold-400 hover:bg-gold-300 text-surface-900 font-bold text-xs py-2 px-3.5 shadow-sm">
                <i class="fas fa-pen-to-square text-xs"></i>
                <span>Editar Parto</span>
            </a>

            @if($patient)
                <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw text-xs py-2 px-3.5">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Ficha da Paciente</span>
                </a>
            @endif
        </div>
    </div>

    {{-- CARDS PRINCIPAIS EM GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUNA 1 & 2: DADOS DO PARTO & RN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card 1: Informações do Parto & Mão --}}
            <div class="card-tw p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                    <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                        <i class="fas fa-notes-medical text-brand-600"></i> Dados Clínicos do Parto
                    </h3>
                    <span class="badge-neutral text-2xs uppercase font-semibold">Tipo: {{ strtoupper($birth->tipo_parto) }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Data & Hora do Parto</span>
                        <span class="font-bold text-surface-900 font-mono">{{ $birth->data_hora_parto?->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Tipo de Parto</span>
                        <span class="font-bold text-brand-700 capitalize">{{ str_replace('_', ' ', $birth->tipo_parto) }}</span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Idade Gestacional ao Parto</span>
                        <span class="font-bold text-surface-900">{{ $birth->idade_gestacional_parto ? $birth->idade_gestacional_parto . ' Semanas' : 'N/A' }}</span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Unidade Sanitária / Local</span>
                        <span class="font-bold text-surface-900 truncate block" title="{{ $birth->hospital_unidade ?? $birth->local_parto }}">
                            {{ $birth->hospital_unidade ?? $birth->local_parto ?? 'CS Quelimane Urbano' }}
                        </span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Obstetra / Parteiro</span>
                        <span class="font-bold text-surface-900 truncate block">{{ $birth->profissional_obstetra ?? 'Não especificado' }}</span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Enfermeira de Saúde Materna</span>
                        <span class="font-bold text-surface-900 truncate block">{{ $birth->profissional_enfermeiro ?? 'Não especificada' }}</span>
                    </div>
                </div>

                @if($birth->complicacoes_maternas)
                    <div class="p-3.5 bg-crimson-50 border border-crimson-200 rounded-xl text-xs text-crimson-900 space-y-1">
                        <span class="font-bold flex items-center gap-1.5 text-crimson-700">
                            <i class="fas fa-triangle-exclamation"></i> Complicações Maternas Registadas:
                        </span>
                        <p class="text-2xs text-crimson-800">{{ $birth->complicacoes_maternas }}</p>
                    </div>
                @endif
            </div>

            {{-- Card 2: Dados do Recém-Nascido & Escala APGAR --}}
            <div class="card-tw p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                    <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                        <i class="fas fa-baby text-gold-500"></i> Avaliação Neonatal & Escala APGAR
                    </h3>
                    <span class="{{ $statusColors[$birth->status_bebe] ?? 'badge-neutral' }}">
                        {{ $statusLabels[$birth->status_bebe] ?? $birth->status_bebe }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Sexo do Recém-Nascido</span>
                        <span class="font-bold text-surface-900 capitalize">
                            <i class="fas {{ $birth->sexo_bebe === 'masculino' ? 'fa-mars text-ocean-600' : 'fa-venus text-brand-600' }} mr-1"></i>
                            {{ $birth->sexo_bebe ?? 'Não Especificado' }}
                        </span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Peso ao Nascer</span>
                        <span class="font-bold text-brand-700 font-mono text-sm">
                            {{ number_format($birth->peso_nascimento / ($birth->peso_nascimento > 100 ? 1000 : 1), 2) }} kg 
                            <span class="text-2xs text-surface-400 font-normal">({{ $birth->peso_nascimento > 100 ? $birth->peso_nascimento : $birth->peso_nascimento * 1000 }} g)</span>
                        </span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Comprimento / Altura</span>
                        <span class="font-bold text-surface-900 font-mono text-sm">{{ $birth->altura_nascimento }} cm</span>
                    </div>

                    <div class="p-3 bg-surface-50 rounded-xl border border-surface-200">
                        <span class="text-3xs text-surface-400 font-bold uppercase tracking-wider block">Tipo de Gestação</span>
                        <span class="font-bold text-surface-900">
                            {{ $birth->parto_multiplo ? 'Parto Múltiplo (' . $birth->numero_bebes . ' bebês)' : 'Única (1 bebê)' }}
                        </span>
                    </div>
                </div>

                {{-- Escala APGAR --}}
                <div class="p-4 bg-surface-50 border border-surface-200 rounded-2xl space-y-3">
                    <h4 class="text-xs font-bold text-surface-900 flex items-center justify-between">
                        <span>Pontuação da Escala APGAR</span>
                        <span class="text-3xs font-normal text-surface-500">Normalidade: 8 a 10 valores</span>
                    </h4>

                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 bg-white rounded-xl border border-surface-200 shadow-xs">
                            <span class="text-3xs text-surface-400 font-bold uppercase block">1º Minuto</span>
                            <span class="text-lg font-black {{ ($birth->apgar_1min >= 8) ? 'text-brand-600' : (($birth->apgar_1min >= 5) ? 'text-gold-600' : 'text-crimson-600') }}">
                                {{ $birth->apgar_1min ?? '-' }} / 10
                            </span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-surface-200 shadow-xs">
                            <span class="text-3xs text-surface-400 font-bold uppercase block">5º Minuto</span>
                            <span class="text-lg font-black {{ ($birth->apgar_5min >= 8) ? 'text-brand-600' : (($birth->apgar_5min >= 5) ? 'text-gold-600' : 'text-crimson-600') }}">
                                {{ $birth->apgar_5min ?? '-' }} / 10
                            </span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-surface-200 shadow-xs">
                            <span class="text-3xs text-surface-400 font-bold uppercase block">10º Minuto</span>
                            <span class="text-lg font-black {{ ($birth->apgar_10min >= 8) ? 'text-brand-600' : (($birth->apgar_10min >= 5) ? 'text-gold-600' : 'text-crimson-600') }}">
                                {{ $birth->apgar_10min ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($birth->observacoes_rn)
                    <div class="p-3 bg-surface-100 rounded-xl text-xs space-y-1">
                        <span class="font-bold text-surface-800">Observações do Recém-Nascido:</span>
                        <p class="text-2xs text-surface-600">{{ $birth->observacoes_rn }}</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- COLUNA 3: PUERPÉRIO MISAU & ACOMPANHAMENTO --}}
        <div class="space-y-6">

            {{-- Card Puerpério Agendado --}}
            <div class="card-tw p-6 space-y-4">
                <h3 class="text-sm font-bold text-surface-900 border-b border-surface-100 pb-3 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-brand-600"></i> Consultas de Puerpério (MISAU)
                </h3>

                <p class="text-xs text-surface-600">
                    Consultas pós-parto agendadas automaticamente para acompanhamento da puérpera e recém-nascido:
                </p>

                <div class="space-y-3 text-xs">
                    {{-- 48 Horas --}}
                    <div class="p-3 bg-brand-50 border border-brand-200 rounded-xl space-y-1">
                        <div class="flex items-center justify-between font-bold text-brand-900">
                            <span>1ª Consulta: 48 Horas</span>
                            <span class="text-3xs px-2 py-0.5 rounded-full bg-brand-200 text-brand-800">Antes da Alta</span>
                        </div>
                        <p class="text-2xs text-brand-800 font-mono">
                            Data prevista: {{ $birth->data_hora_parto?->copy()->addDays(2)->format('d/m/Y') }}
                        </p>
                        <p class="text-3xs text-brand-700">Avaliação de involução uterina, lochia e pega de aleitamento materno.</p>
                    </div>

                    {{-- 7 Dias --}}
                    <div class="p-3 bg-ocean-50 border border-ocean-200 rounded-xl space-y-1">
                        <div class="flex items-center justify-between font-bold text-ocean-900">
                            <span>2ª Consulta: 7 Dias</span>
                            <span class="text-3xs px-2 py-0.5 rounded-full bg-ocean-200 text-ocean-800">Pós-Parto Precoce</span>
                        </div>
                        <p class="text-2xs text-ocean-800 font-mono">
                            Data prevista: {{ $birth->data_hora_parto?->copy()->addDays(7)->format('d/m/Y') }}
                        </p>
                        <p class="text-3xs text-ocean-700">Rastreio de infecções puerperais, cicatrização e estado emocional.</p>
                    </div>

                    {{-- 28 Dias --}}
                    <div class="p-3 bg-gold-50 border border-gold-200 rounded-xl space-y-1">
                        <div class="flex items-center justify-between font-bold text-gold-900">
                            <span>3ª Consulta: 28 a 42 Dias</span>
                            <span class="text-3xs px-2 py-0.5 rounded-full bg-gold-200 text-gold-800">Revisão Puerperal</span>
                        </div>
                        <p class="text-2xs text-gold-800 font-mono">
                            Data prevista: {{ $birth->data_hora_parto?->copy()->addDays(28)->format('d/m/Y') }}
                        </p>
                        <p class="text-3xs text-gold-700">Planeamento familiar pós-parto, vacinação do bebê e encerramento do puerpério.</p>
                    </div>
                </div>
            </div>

            {{-- Card Observações Gerais & Profissional --}}
            <div class="card-tw p-6 space-y-3 text-xs">
                <h4 class="font-bold text-surface-900 border-b border-surface-100 pb-2">Registo e Responsabilidade</h4>

                <div>
                    <span class="text-2xs text-surface-400 block font-semibold">Registado por:</span>
                    <span class="font-bold text-surface-800">{{ $birth->user->name ?? 'Profissional de Saúde' }}</span>
                </div>

                <div>
                    <span class="text-2xs text-surface-400 block font-semibold">Data do Registo:</span>
                    <span class="font-mono text-surface-800">{{ $birth->created_at?->format('d/m/Y H:i:s') }}</span>
                </div>

                @if($birth->observacoes_gerais)
                    <div class="pt-2 border-t border-surface-100">
                        <span class="text-2xs text-surface-400 block font-semibold">Observações Gerais:</span>
                        <p class="text-2xs text-surface-700 mt-1">{{ $birth->observacoes_gerais }}</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
