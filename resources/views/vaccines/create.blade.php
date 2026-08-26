@extends('layouts.app-tw')

@section('title', 'Registar Vacina / IPTp')
@section('page-title', 'Registar Aplicação de Vacina ou IPTp')
@section('title-icon', 'fa-syringe')

@section('breadcrumbs')
    <a href="{{ route('vaccines.index') }}">Vacinas & IPTp</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Registar</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-syringe"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Registo de Imunização Pré-Natal</h3>
            </div>
            <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <form action="{{ route('vaccines.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Gestante --}}
            <div>
                <label for="patient_id" class="label-tw">Gestante <span class="text-crimson-500">*</span></label>
                <select class="input-tw @error('patient_id') input-error-tw @enderror"
                        id="patient_id"
                        name="patient_id"
                        required>
                    <option value="">Selecione a gestante</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (old('patient_id') == $p->id || ($patient && $patient->id == $p->id)) ? 'selected' : '' }}>
                            {{ $p->nome_completo }} — BI: {{ $p->documento_bi }}
                        </option>
                    @endforeach
                </select>
                @error('patient_id')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            {{-- Vacina & Dose --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tipo_vacina" class="label-tw">Tipo de Imunização <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('tipo_vacina') input-error-tw @enderror"
                            id="tipo_vacina"
                            name="tipo_vacina"
                            required>
                        <option value="">Selecione</option>
                        <option value="tetanica" {{ old('tipo_vacina') === 'tetanica' ? 'selected' : '' }}>Tétano (VAT)</option>
                        <option value="iptp" {{ old('tipo_vacina') === 'iptp' ? 'selected' : '' }}>IPTp (Sulfadoxina-Pirimetamina)</option>
                        <option value="hepatite_b" {{ old('tipo_vacina') === 'hepatite_b' ? 'selected' : '' }}>Hepatite B</option>
                        <option value="influenza" {{ old('tipo_vacina') === 'influenza' ? 'selected' : '' }}>Influenza (Gripe)</option>
                        <option value="covid19" {{ old('tipo_vacina') === 'covid19' ? 'selected' : '' }}>COVID-19</option>
                        <option value="febre_amarela" {{ old('tipo_vacina') === 'febre_amarela' ? 'selected' : '' }}>Febre Amarela</option>
                    </select>
                    @error('tipo_vacina')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dose_numero" class="label-tw">Número da Dose <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('dose_numero') input-error-tw @enderror"
                            id="dose_numero"
                            name="dose_numero"
                            required>
                        <option value="1" {{ old('dose_numero') == 1 ? 'selected' : '' }}>1ª Dose (D1)</option>
                        <option value="2" {{ old('dose_numero') == 2 ? 'selected' : '' }}>2ª Dose (D2)</option>
                        <option value="3" {{ old('dose_numero') == 3 ? 'selected' : '' }}>3ª Dose (D3 / IPTp-1)</option>
                        <option value="4" {{ old('dose_numero') == 4 ? 'selected' : '' }}>4ª Dose (D4 / IPTp-2)</option>
                        <option value="5" {{ old('dose_numero') == 5 ? 'selected' : '' }}>5ª Dose (Reforço / IPTp-3)</option>
                    </select>
                    @error('dose_numero')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="label-tw">Status <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('status') input-error-tw @enderror"
                            id="status"
                            name="status"
                            required>
                        <option value="administrada" {{ old('status', 'administrada') === 'administrada' ? 'selected' : '' }}>Administrada</option>
                        <option value="pendente" {{ old('status') === 'pendente' ? 'selected' : '' }}>Agendada / Pendente</option>
                    </select>
                    @error('status')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Aplicação & Lote --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="data_administracao" class="label-tw">Data de Aplicação <span class="text-crimson-500">*</span></label>
                    <input type="date"
                           class="input-tw @error('data_administracao') input-error-tw @enderror"
                           id="data_administracao"
                           name="data_administracao"
                           value="{{ old('data_administracao', date('Y-m-d')) }}"
                           required>
                    @error('data_administracao')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="local_aplicacao" class="label-tw">Local de Aplicação <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('local_aplicacao') input-error-tw @enderror"
                            id="local_aplicacao"
                            name="local_aplicacao"
                            required>
                        <option value="braco_esquerdo" {{ old('local_aplicacao') === 'braco_esquerdo' ? 'selected' : '' }}>Braço Esquerdo (Deltoide)</option>
                        <option value="braco_direito" {{ old('local_aplicacao') === 'braco_direito' ? 'selected' : '' }}>Braço Direito (Deltoide)</option>
                        <option value="coxa_esquerda" {{ old('local_aplicacao') === 'coxa_esquerda' ? 'selected' : '' }}>Coxa Esquerda</option>
                        <option value="coxa_direita" {{ old('local_aplicacao') === 'coxa_direita' ? 'selected' : '' }}>Coxa Direita</option>
                        <option value="gluteo" {{ old('local_aplicacao') === 'gluteo' ? 'selected' : '' }}>Glúteo</option>
                    </select>
                    @error('local_aplicacao')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lote" class="label-tw">Lote do Vacina / Imunobiológico</label>
                    <input type="text"
                           class="input-tw @error('lote') input-error-tw @enderror"
                           id="lote"
                           name="lote"
                           value="{{ old('lote') }}"
                           placeholder="Ex: LT891023">
                    @error('lote')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="fabricante" class="label-tw">Fabricante / Fornecedor</label>
                    <input type="text"
                           class="input-tw @error('fabricante') input-error-tw @enderror"
                           id="fabricante"
                           name="fabricante"
                           value="{{ old('fabricante') }}"
                           placeholder="Ex: Serum Institute, Fiocruz...">
                    @error('fabricante')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="data_vencimento" class="label-tw">Validade do Lote</label>
                    <input type="date"
                           class="input-tw @error('data_vencimento') input-error-tw @enderror"
                           id="data_vencimento"
                           name="data_vencimento"
                           value="{{ old('data_vencimento') }}">
                    @error('data_vencimento')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="observacoes" class="label-tw">Observações / Reações Observadas</label>
                <textarea class="input-tw @error('observacoes') input-error-tw @enderror"
                          id="observacoes"
                          name="observacoes"
                          rows="3"
                          placeholder="Notas da aplicação ou acompanhamento...">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw">Cancelar</a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-check text-xs"></i>
                    <span>Registar Imunização</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
