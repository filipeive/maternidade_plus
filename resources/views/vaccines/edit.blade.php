@extends('layouts.app-tw')

@section('title', 'Editar Vacina')
@section('page-title', 'Atualizar Registo de Vacinação')
@section('title-icon', 'fa-pen-to-square')

@section('breadcrumbs')
    <a href="{{ route('vaccines.index') }}">Vacinas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-syringe"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Editar Imunização</h3>
            </div>
            <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <form action="{{ route('vaccines.update', $vaccine) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PATCH')

            {{-- Gestante --}}
            <div>
                <label for="patient_id" class="label-tw">Gestante <span class="text-crimson-500">*</span></label>
                <select class="input-tw @error('patient_id') input-error-tw @enderror"
                        id="patient_id"
                        name="patient_id"
                        required>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ old('patient_id', $vaccine->patient_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nome_completo }} — BI: {{ $p->documento_bi }}
                        </option>
                    @endforeach
                </select>
                @error('patient_id')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo, Dose & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tipo_vacina" class="label-tw">Tipo de Imunização <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('tipo_vacina') input-error-tw @enderror"
                            id="tipo_vacina"
                            name="tipo_vacina"
                            required>
                        <option value="tetanica" {{ old('tipo_vacina', $vaccine->tipo_vacina) === 'tetanica' ? 'selected' : '' }}>Tétano (VAT)</option>
                        <option value="iptp" {{ old('tipo_vacina', $vaccine->tipo_vacina) === 'iptp' ? 'selected' : '' }}>IPTp (Malária)</option>
                        <option value="hepatite_b" {{ old('tipo_vacina', $vaccine->tipo_vacina) === 'hepatite_b' ? 'selected' : '' }}>Hepatite B</option>
                        <option value="influenza" {{ old('tipo_vacina', $vaccine->tipo_vacina) === 'influenza' ? 'selected' : '' }}>Influenza</option>
                        <option value="covid19" {{ old('tipo_vacina', $vaccine->tipo_vacina) === 'covid19' ? 'selected' : '' }}>COVID-19</option>
                        <option value="febre_amarela" {{ old('tipo_vacina', $vaccine->tipo_vacina) === 'febre_amarela' ? 'selected' : '' }}>Febre Amarela</option>
                    </select>
                </div>

                <div>
                    <label for="dose_numero" class="label-tw">Dose <span class="text-crimson-500">*</span></label>
                    <input type="number" name="dose_numero" min="1" max="5" class="input-tw" value="{{ old('dose_numero', $vaccine->dose_numero ?? 1) }}" required>
                </div>

                <div>
                    <label for="status" class="label-tw">Status <span class="text-crimson-500">*</span></label>
                    <select class="input-tw" id="status" name="status" required>
                        <option value="administrada" {{ old('status', $vaccine->status) === 'administrada' ? 'selected' : '' }}>Administrada</option>
                        <option value="pendente" {{ old('status', $vaccine->status) === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="vencida" {{ old('status', $vaccine->status) === 'vencida' ? 'selected' : '' }}>Vencida</option>
                    </select>
                </div>
            </div>

            {{-- Datas & Local --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="data_administracao" class="label-tw">Data de Aplicação <span class="text-crimson-500">*</span></label>
                    <input type="date"
                           class="input-tw"
                           id="data_administracao"
                           name="data_administracao"
                           value="{{ old('data_administracao', $vaccine->data_administracao ? $vaccine->data_administracao->format('Y-m-d') : date('Y-m-d')) }}"
                           required>
                </div>

                <div>
                    <label for="local_aplicacao" class="label-tw">Local de Aplicação <span class="text-crimson-500">*</span></label>
                    <select class="input-tw" id="local_aplicacao" name="local_aplicacao" required>
                        <option value="braco_esquerdo" {{ old('local_aplicacao', $vaccine->local_aplicacao) === 'braco_esquerdo' ? 'selected' : '' }}>Braço Esquerdo</option>
                        <option value="braco_direito" {{ old('local_aplicacao', $vaccine->local_aplicacao) === 'braco_direito' ? 'selected' : '' }}>Braço Direito</option>
                        <option value="coxa_esquerda" {{ old('local_aplicacao', $vaccine->local_aplicacao) === 'coxa_esquerda' ? 'selected' : '' }}>Coxa Esquerda</option>
                        <option value="coxa_direita" {{ old('local_aplicacao', $vaccine->local_aplicacao) === 'coxa_direita' ? 'selected' : '' }}>Coxa Direita</option>
                        <option value="gluteo" {{ old('local_aplicacao', $vaccine->local_aplicacao) === 'gluteo' ? 'selected' : '' }}>Glúteo</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="lote" class="label-tw">Lote</label>
                    <input type="text" name="lote" class="input-tw" value="{{ old('lote', $vaccine->lote) }}">
                </div>

                <div>
                    <label for="fabricante" class="label-tw">Fabricante</label>
                    <input type="text" name="fabricante" class="input-tw" value="{{ old('fabricante', $vaccine->fabricante) }}">
                </div>
            </div>

            <div>
                <label for="observacoes" class="label-tw">Observações</label>
                <textarea name="observacoes" class="input-tw" rows="3">{{ old('observacoes', $vaccine->observacoes) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw">Cancelar</a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Atualizar Registo</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
