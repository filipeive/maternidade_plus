@extends('layouts.app-tw')

@section('title', 'Editar Gestante')
@section('page-title', 'Editar Cadastro de Gestante')
@section('title-icon', 'fa-user-pen')

@section('breadcrumbs')
    <a href="{{ route('patients.index') }}">Gestantes</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('patients.show', $patient) }}">{{ $patient->nome_completo }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-user-pen"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Editar Cadastro: {{ $patient->nome_completo }}</h3>
            </div>
            <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Informações Pessoais --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card text-brand-500"></i> Informações Pessoais
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="nome_completo" class="label-tw">Nome Completo <span class="text-crimson-500">*</span></label>
                        <input type="text"
                               class="input-tw @error('nome_completo') input-error-tw @enderror"
                               id="nome_completo"
                               name="nome_completo"
                               value="{{ old('nome_completo', $patient->nome_completo) }}"
                               required>
                        @error('nome_completo')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="data_nascimento" class="label-tw">Data de Nascimento <span class="text-crimson-500">*</span></label>
                        <input type="date"
                               class="input-tw @error('data_nascimento') input-error-tw @enderror"
                               id="data_nascimento"
                               name="data_nascimento"
                               value="{{ old('data_nascimento', $patient->data_nascimento?->format('Y-m-d')) }}"
                               required>
                        @error('data_nascimento')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="documento_bi" class="label-tw">Documento BI <span class="text-crimson-500">*</span></label>
                        <input type="text"
                               class="input-tw @error('documento_bi') input-error-tw @enderror"
                               id="documento_bi"
                               name="documento_bi"
                               value="{{ old('documento_bi', $patient->documento_bi) }}"
                               required>
                        @error('documento_bi')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contacto" class="label-tw">Contacto Telefónico <span class="text-crimson-500">*</span></label>
                        <input type="tel"
                               class="input-tw @error('contacto') input-error-tw @enderror"
                               id="contacto"
                               name="contacto"
                               value="{{ old('contacto', $patient->contacto) }}"
                               required>
                        @error('contacto')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="email" class="label-tw">Endereço de Email</label>
                        <input type="email"
                               class="input-tw @error('email') input-error-tw @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $patient->email) }}">
                        @error('email')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contacto_emergencia" class="label-tw">Contacto de Emergência</label>
                        <input type="tel"
                               class="input-tw @error('contacto_emergencia') input-error-tw @enderror"
                               id="contacto_emergencia"
                               name="contacto_emergencia"
                               value="{{ old('contacto_emergencia', $patient->contacto_emergencia) }}">
                        @error('contacto_emergencia')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="endereco" class="label-tw">Endereço Completo <span class="text-crimson-500">*</span></label>
                    <textarea class="input-tw @error('endereco') input-error-tw @enderror"
                              id="endereco"
                              name="endereco"
                              rows="2"
                              required>{{ old('endereco', $patient->endereco) }}</textarea>
                    @error('endereco')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-surface-100 pt-6"></div>

            {{-- Histórico Obstétrico --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-stethoscope text-brand-500"></i> Histórico Obstétrico & Clínico
                </h4>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="tipo_sanguineo" class="label-tw">Tipo Sanguíneo</label>
                        <select class="input-tw @error('tipo_sanguineo') input-error-tw @enderror"
                                id="tipo_sanguineo"
                                name="tipo_sanguineo">
                            <option value="">Selecione</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo_sanguineo', $patient->tipo_sanguineo) === $tipo ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_sanguineo')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_gestacoes" class="label-tw">Gestações (G)</label>
                        <input type="number"
                               min="1"
                               class="input-tw @error('numero_gestacoes') input-error-tw @enderror"
                               id="numero_gestacoes"
                               name="numero_gestacoes"
                               value="{{ old('numero_gestacoes', $patient->numero_gestacoes) }}"
                               required>
                        @error('numero_gestacoes')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_partos" class="label-tw">Partos (P)</label>
                        <input type="number"
                               min="0"
                               class="input-tw @error('numero_partos') input-error-tw @enderror"
                               id="numero_partos"
                               name="numero_partos"
                               value="{{ old('numero_partos', $patient->numero_partos) }}"
                               required>
                        @error('numero_partos')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_abortos" class="label-tw">Abortos (A)</label>
                        <input type="number"
                               min="0"
                               class="input-tw @error('numero_abortos') input-error-tw @enderror"
                               id="numero_abortos"
                               name="numero_abortos"
                               value="{{ old('numero_abortos', $patient->numero_abortos) }}"
                               required>
                        @error('numero_abortos')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="data_ultima_menstruacao" class="label-tw">Data da Última Menstruação (DUM)</label>
                        <input type="date"
                               class="input-tw @error('data_ultima_menstruacao') input-error-tw @enderror"
                               id="data_ultima_menstruacao"
                               name="data_ultima_menstruacao"
                               value="{{ old('data_ultima_menstruacao', $patient->data_ultima_menstruacao?->format('Y-m-d')) }}">
                        @error('data_ultima_menstruacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="alergias" class="label-tw">Alergias Conhecidas</label>
                        <input type="text"
                               class="input-tw @error('alergias') input-error-tw @enderror"
                               id="alergias"
                               name="alergias"
                               value="{{ old('alergias', $patient->alergias) }}">
                        @error('alergias')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="historico_medico" class="label-tw">Histórico Médico Relevante</label>
                    <textarea class="input-tw @error('historico_medico') input-error-tw @enderror"
                              id="historico_medico"
                              name="historico_medico"
                              rows="3">{{ old('historico_medico', $patient->historico_medico) }}</textarea>
                    @error('historico_medico')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw">Cancelar</a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
