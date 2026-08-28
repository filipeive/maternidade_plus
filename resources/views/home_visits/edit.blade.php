@extends('layouts.app-tw')

@section('title', 'Editar Visita Domiciliária')
@section('page-title', 'Editar Visita #' . $homeVisit->id)
@section('title-icon', 'fa-pen-to-square')

@section('breadcrumbs')
    <a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('home_visits.show', $homeVisit) }}">Visita #{{ $homeVisit->id }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- FORMULÁRIO DE EDIÇÃO --}}
        <div class="lg:col-span-2 card-tw p-6 space-y-6">
            <div class="border-b border-surface-100 pb-3 flex items-center justify-between">
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-house-medical text-brand-600"></i> Atualizar Dados da Visita
                </h3>
                <span class="badge-neutral text-2xs uppercase font-bold">{{ $homeVisit->status }}</span>
            </div>

            <form method="POST" action="{{ route('home_visits.update', $homeVisit) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- SELEÇÃO DA GESTANTE --}}
                <div>
                    <label class="label-tw">Paciente / Gestante <span class="text-crimson-500">*</span></label>
                    <select name="patient_id" required class="input-tw text-xs">
                        @foreach ($patients as $patientOption)
                            <option value="{{ $patientOption->id }}" {{ old('patient_id', $homeVisit->patient_id) == $patientOption->id ? 'selected' : '' }}>
                                {{ $patientOption->nome_completo }} — BI: {{ $patientOption->documento_bi ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DATA DA VISITA & TIPO --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Data e Hora Programada <span class="text-crimson-500">*</span></label>
                        <input type="datetime-local" 
                               name="data_visita" 
                               value="{{ old('data_visita', $homeVisit->data_visita ? $homeVisit->data_visita->format('Y-m-d\TH:i') : '') }}" 
                               required 
                               class="input-tw text-xs">
                        @error('data_visita')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Tipo de Visita Domiciliária <span class="text-crimson-500">*</span></label>
                        <select name="tipo_visita" required class="input-tw text-xs">
                            @foreach ($tiposVisita as $key => $tipo)
                                <option value="{{ $key }}" {{ old('tipo_visita', $homeVisit->tipo_visita) == $key ? 'selected' : '' }}>
                                    {{ is_array($tipo) ? $tipo['nome'] : $tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_visita')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- AGENTE COMUNITÁRIO / RESPONSÁVEL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Agente Comunitário / Responsável</label>
                        <select name="user_id" class="input-tw text-xs">
                            <option value="">Manter atual ({{ $homeVisit->user->name ?? 'Agente Comunitário' }})</option>
                            @if(isset($communityAgents))
                                @foreach ($communityAgents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('user_id', $homeVisit->user_id) == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="label-tw">Endereço da Visita <span class="text-crimson-500">*</span></label>
                        <input type="text" name="endereco_visita" value="{{ old('endereco_visita', $homeVisit->endereco_visita) }}" required class="input-tw text-xs">
                    </div>
                </div>

                {{-- MOTIVO DA VISITA --}}
                <div>
                    <label class="label-tw">Motivo da Visita <span class="text-crimson-500">*</span></label>
                    <textarea name="motivo_visita" rows="3" required class="input-tw text-xs leading-relaxed">{{ old('motivo_visita', $homeVisit->motivo_visita) }}</textarea>
                    @error('motivo_visita')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- OBSERVAÇÕES GERAIS --}}
                <div>
                    <label class="label-tw">Observações Gerais</label>
                    <textarea name="observacoes_gerais" rows="3" class="input-tw text-xs leading-relaxed">{{ old('observacoes_gerais', $homeVisit->observacoes_gerais) }}</textarea>
                </div>

                {{-- BOTÕES --}}
                <div class="flex items-center justify-between pt-4 border-t border-surface-100">
                    <a href="{{ route('home_visits.show', $homeVisit) }}" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Cancelar</span>
                    </a>
                    <button type="submit" class="btn-primary-tw btn-sm-tw font-bold">
                        <i class="fas fa-save text-xs"></i>
                        <span>Salvar Alterações</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- PAINEL LATERAL --}}
        <div class="space-y-6">
            <div class="card-tw p-5 space-y-3">
                <h4 class="font-bold text-surface-900 text-xs uppercase tracking-wider text-brand-700 flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-gold-500"></i> Informações
                </h4>
                <p class="text-xs text-surface-600 leading-relaxed">
                    Alterações nos dados da visita atualizam a rota de visitas diária e as instruções do agente de campo comunitário.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
