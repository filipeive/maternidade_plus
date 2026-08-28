@extends('layouts.app-tw')

@section('title', 'Solicitar Exame')
@section('page-title', 'Solicitar Novo Exame Laboratorial')
@section('title-icon', 'fa-vial')

@section('breadcrumbs')
    <a href="{{ route('exams.index') }}">Exames</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Solicitar</span>
@endsection

@section('content')
<div class="max-w-full mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-ocean-100 text-ocean-700 flex items-center justify-center text-sm">
                    <i class="fas fa-vial"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-surface-900">Solicitação de Exame</h3>
                    @if($patient)
                        <p class="text-2xs text-surface-500">
                            Gestante: <strong class="text-surface-800">{{ $patient->nome_completo }}</strong> · BI: {{ $patient->documento_bi }}
                        </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('exams.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <form action="{{ route('exams.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            @if($consultation)
                <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
            @else
                <div>
                    <label for="consultation_id" class="label-tw">Consulta Relacionada <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('consultation_id') input-error-tw @enderror"
                            id="consultation_id"
                            name="consultation_id"
                            required>
                        <option value="">Selecione uma consulta</option>
                        @php
                            $consultations = \App\Models\Consultation::with('patient')
                                ->where('status', '!=', 'cancelada')
                                ->orderBy('data_consulta', 'desc')
                                ->limit(50)
                                ->get();
                        @endphp
                        @foreach($consultations as $cons)
                            <option value="{{ $cons->id }}" {{ old('consultation_id') == $cons->id ? 'selected' : '' }}>
                                {{ $cons->patient->nome_completo }} — {{ $cons->data_consulta->format('d/m/Y H:i') }} ({{ $cons->tipo_consulta_label }})
                            </option>
                        @endforeach
                    </select>
                    @error('consultation_id')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="tipo_exame" class="label-tw">Tipo de Exame <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('tipo_exame') input-error-tw @enderror"
                            id="tipo_exame"
                            name="tipo_exame"
                            required>
                        <option value="">Selecione o tipo de exame</option>
                        @foreach($tiposExames as $key => $label)
                            <option value="{{ $key }}" {{ old('tipo_exame') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_exame')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="data_solicitacao" class="label-tw">Data da Solicitação <span class="text-crimson-500">*</span></label>
                    <input type="date"
                           class="input-tw @error('data_solicitacao') input-error-tw @enderror"
                           id="data_solicitacao"
                           name="data_solicitacao"
                           value="{{ old('data_solicitacao', date('Y-m-d')) }}"
                           required>
                    @error('data_solicitacao')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="laboratorio" class="label-tw">Laboratório Destino</label>
                <input type="text"
                       class="input-tw @error('laboratorio') input-error-tw @enderror"
                       id="laboratorio"
                       name="laboratorio"
                       value="{{ old('laboratorio', 'Laboratório Central') }}"
                       placeholder="Ex: Laboratório Central, Hospital Geral...">
                @error('laboratorio')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="observacoes" class="label-tw">Observações / Instruções Especiais</label>
                <textarea class="input-tw @error('observacoes') input-error-tw @enderror"
                          id="observacoes"
                          name="observacoes"
                          rows="3"
                          placeholder="Indicações clínicas, urgência...">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                <a href="{{ route('exams.index') }}" class="btn-secondary-tw">Cancelar</a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-check text-xs"></i>
                    <span>Solicitar Exame</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection