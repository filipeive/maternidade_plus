@extends('layouts.app-tw')

@section('title', 'Agendar Visita Domiciliária')
@section('page-title', 'Agendar Nova Visita Domiciliária')
@section('title-icon', 'fa-calendar-plus')

@section('breadcrumbs')
    <a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Agendar Visita</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- FORMULÁRIO PRINCIPAL --}}
        <div class="lg:col-span-2 card-tw p-6 space-y-6">
            <div class="border-b border-surface-100 pb-3 flex items-center justify-between">
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-house-medical text-brand-600"></i> Dados da Visita Domiciliária
                </h3>
                <span class="badge-neutral text-2xs uppercase">MISAU Form</span>
            </div>

            <form method="POST" action="{{ route('home_visits.store') }}" class="space-y-5">
                @csrf

                {{-- SELEÇÃO DA GESTANTE --}}
                <div>
                    <label class="label-tw">Selecione a Paciente / Gestante <span class="text-crimson-500">*</span></label>
                    @if ($patient)
                        <div class="p-4 bg-brand-50/60 border border-brand-200 rounded-xl flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-xs">
                                    <i class="fas fa-person-pregnant"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-surface-900 text-sm">{{ $patient->nome_completo }}</h4>
                                    <p class="text-2xs text-surface-600 font-mono">
                                        BI: {{ $patient->documento_bi ?? 'N/A' }} · Contacto: {{ $patient->contacto ?? $patient->contacto_emergencia ?? 'Sem Telefone' }}
                                    </p>
                                    <p class="text-2xs text-surface-500">
                                        <i class="fas fa-location-dot text-brand-500 mr-1"></i>{{ $patient->endereco ?? 'Sem Endereço Cadastrado' }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('home_visits.create') }}" class="btn-secondary-tw btn-xs-tw" title="Trocar Paciente">
                                <i class="fas fa-rotate text-3xs"></i>
                                <span>Trocar</span>
                            </a>
                        </div>
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    @else
                        <select name="patient_id" required class="input-tw text-xs">
                            <option value="">Escolha a gestante na lista...</option>
                            @foreach ($patients as $patientOption)
                                <option value="{{ $patientOption->id }}" {{ old('patient_id') == $patientOption->id ? 'selected' : '' }}>
                                    {{ $patientOption->nome_completo }} — BI: {{ $patientOption->documento_bi ?? 'N/A' }} ({{ $patientOption->contacto ?? 'Sem Telefone' }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('patient_id')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DATA DA VISITA & TIPO --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Data e Hora Programada <span class="text-crimson-500">*</span></label>
                        <input type="datetime-local" name="data_visita" value="{{ old('data_visita', now()->format('Y-m-d\TH:i')) }}" required class="input-tw text-xs">
                        @error('data_visita')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Tipo de Visita Domiciliária <span class="text-crimson-500">*</span></label>
                        <select name="tipo_visita" required class="input-tw text-xs">
                            <option value="">Selecione o tipo de acompanhamento...</option>
                            @foreach ($tiposVisita as $key => $tipo)
                                <option value="{{ $key }}" {{ old('tipo_visita') == $key ? 'selected' : '' }}>
                                    {{ is_array($tipo) ? $tipo['nome'] : $tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_visita')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- MOTIVO DA VISITA --}}
                <div>
                    <label class="label-tw">Motivo da Visita Domiciliária <span class="text-crimson-500">*</span></label>
                    <textarea name="motivo_visita" rows="3" required placeholder="Descreva a finalidade do acompanhamento no domicílio..." class="input-tw text-xs leading-relaxed">{{ old('motivo_visita') }}</textarea>
                    @error('motivo_visita')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- AGENTE COMUNITÁRIO / PROFISSIONAL RESPONSÁVEL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Agente Comunitário / Responsável</label>
                        <select name="user_id" class="input-tw text-xs">
                            <option value="">Atribuir a mim ({{ auth()->user()->name }})</option>
                            @if(isset($communityAgents))
                                @foreach ($communityAgents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('user_id') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-3xs text-surface-400 mt-1">Selecione o activista ou enfermeiro que realizará a visita.</p>
                    </div>

                    <div>
                        <label class="label-tw">Endereço da Visita (Opcional se igual ao cadastro)</label>
                        <input type="text" name="endereco_visita" value="{{ old('endereco_visita', $patient->endereco ?? '') }}" placeholder="Deixe em branco para usar endereço da gestante..." class="input-tw text-xs">
                        <p class="text-3xs text-surface-400 mt-1">Se não preenchido, será utilizado o endereço da ficha da gestante.</p>
                    </div>
                </div>

                {{-- OBSERVAÇÕES GERAIS --}}
                <div>
                    <label class="label-tw">Observações / Instruções Clínicas</label>
                    <textarea name="observacoes_gerais" rows="3" placeholder="Informações e orientações específicas para o activista no terreno..." class="input-tw text-xs leading-relaxed">{{ old('observacoes_gerais') }}</textarea>
                </div>

                {{-- BOTÕES DE SUBMISSÃO --}}
                <div class="flex items-center justify-between pt-4 border-t border-surface-100">
                    <a href="{{ route('home_visits.index') }}" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Cancelar</span>
                    </a>
                    <button type="submit" class="btn-primary-tw btn-sm-tw font-bold py-2.5 px-6">
                        <i class="fas fa-calendar-plus text-xs"></i>
                        <span>Confirmar e Agendar Visita</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- PAINEL LATERAL DE ORIENTAÇÕES --}}
        <div class="space-y-6">
            <div class="card-tw p-5 space-y-3">
                <h4 class="font-bold text-surface-900 text-xs uppercase tracking-wider text-brand-700 flex items-center gap-1.5">
                    <i class="fas fa-lightbulb text-gold-500"></i> Boas Práticas na Visita
                </h4>
                <ul class="space-y-2 text-xs text-surface-700">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-500 mt-0.5 shrink-0"></i>
                        <span>Confirmar o contacto telefónico da gestante antes do deslocamento.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-500 mt-0.5 shrink-0"></i>
                        <span>Levar os suplementos nutricionais e fichas de avaliação clínica.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-500 mt-0.5 shrink-0"></i>
                        <span>Verificar se a paciente possui sinais de alarme obstétrico.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>

</div>
@endsection