@extends('layouts.app-tw')

@section('title', 'Editar Consulta')
@section('page-title', 'Editar Consulta')
@section('title-icon', 'fa-calendar-pen')

@section('breadcrumbs')
    <a href="{{ route('consultations.index') }}">Consultas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-calendar-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-surface-900">Editar Consulta ANC</h3>
                    <p class="text-xs text-surface-500">Gestante: <strong>{{ $consultation->patient->nome_completo }}</strong> (BI: {{ $consultation->patient->documento_bi }})</p>
                </div>
            </div>
            <a href="{{ route('consultations.show', $consultation) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <div class="card-body-tw">
            @if ($errors->any())
                <div class="mb-6 bg-crimson-50 border-l-4 border-crimson-500 text-crimson-800 p-4 rounded-r-lg text-xs">
                    <strong class="font-bold flex items-center gap-2 mb-1 text-sm">
                        <i class="fas fa-triangle-exclamation"></i> Por favor corrija os seguintes erros:
                    </strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('consultations.update', $consultation) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Gestante Readonly --}}
                <div>
                    <label class="label-tw">Gestante</label>
                    <input type="text" class="input-tw bg-surface-100 cursor-not-allowed" value="{{ $consultation->patient->nome_completo }} — BI: {{ $consultation->patient->documento_bi }}" readonly>
                </div>

                {{-- Data & Tipo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="data_consulta" class="label-tw">Data e Hora <span class="text-crimson-500">*</span></label>
                        <input type="datetime-local"
                               class="input-tw @error('data_consulta') input-error-tw @enderror"
                               id="data_consulta"
                               name="data_consulta"
                               value="{{ old('data_consulta', $consultation->data_consulta->format('Y-m-d\TH:i')) }}"
                               required>
                        @error('data_consulta')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tipo_consulta" class="label-tw">Tipo de Consulta <span class="text-crimson-500">*</span></label>
                        <select class="input-tw @error('tipo_consulta') input-error-tw @enderror"
                                id="tipo_consulta"
                                name="tipo_consulta"
                                required>
                            <option value="">Selecione o tipo</option>
                            <option value="1_trimestre" {{ old('tipo_consulta', $consultation->tipo_consulta) === '1_trimestre' ? 'selected' : '' }}>1º Trimestre (até 12 semanas)</option>
                            <option value="2_trimestre" {{ old('tipo_consulta', $consultation->tipo_consulta) === '2_trimestre' ? 'selected' : '' }}>2º Trimestre (13-28 semanas)</option>
                            <option value="3_trimestre" {{ old('tipo_consulta', $consultation->tipo_consulta) === '3_trimestre' ? 'selected' : '' }}>3º Trimestre (29-40 semanas)</option>
                            <option value="pos_parto" {{ old('tipo_consulta', $consultation->tipo_consulta) === 'pos_parto' ? 'selected' : '' }}>Pós-parto</option>
                            <option value="emergencia" {{ old('tipo_consulta', $consultation->tipo_consulta) === 'emergencia' ? 'selected' : '' }}>Emergência</option>
                        </select>
                        @error('tipo_consulta')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Biometria & Vitais --}}
                <div class="border-t border-surface-100 pt-6">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-4 flex items-center gap-2">
                        <i class="fas fa-heart-pulse text-brand-500"></i> Medições e Exame Físico
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="semanas_gestacao" class="label-tw">Semanas de Gestação</label>
                            <input type="number"
                                   min="1"
                                   max="45"
                                   class="input-tw @error('semanas_gestacao') input-error-tw @enderror"
                                   id="semanas_gestacao"
                                   name="semanas_gestacao"
                                   value="{{ old('semanas_gestacao', $consultation->semanas_gestacao) }}">
                            @error('semanas_gestacao')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="peso" class="label-tw">Peso (kg)</label>
                            <input type="number"
                                   step="0.1"
                                   min="30"
                                   max="200"
                                   class="input-tw @error('peso') input-error-tw @enderror"
                                   id="peso"
                                   name="peso"
                                   value="{{ old('peso', $consultation->peso) }}">
                            @error('peso')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pressao_arterial" class="label-tw">Pressão Arterial (mmHg)</label>
                            <input type="text"
                                   class="input-tw @error('pressao_arterial') input-error-tw @enderror"
                                   id="pressao_arterial"
                                   name="pressao_arterial"
                                   value="{{ old('pressao_arterial', $consultation->pressao_arterial) }}"
                                   placeholder="Ex: 120/80">
                            @error('pressao_arterial')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="batimentos_fetais" class="label-tw">Batimentos Fetais (bpm)</label>
                            <input type="number"
                                   min="110"
                                   max="180"
                                   class="input-tw @error('batimentos_fetais') input-error-tw @enderror"
                                   id="batimentos_fetais"
                                   name="batimentos_fetais"
                                   value="{{ old('batimentos_fetais', $consultation->batimentos_fetais) }}">
                            @error('batimentos_fetais')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="altura_uterina" class="label-tw">Altura Uterina (cm)</label>
                            <input type="number"
                                   step="0.1"
                                   min="10"
                                   max="50"
                                   class="input-tw @error('altura_uterina') input-error-tw @enderror"
                                   id="altura_uterina"
                                   name="altura_uterina"
                                   value="{{ old('altura_uterina', $consultation->altura_uterina) }}">
                            @error('altura_uterina')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Observações & Orientações --}}
                <div class="border-t border-surface-100 pt-6 space-y-4">
                    <div>
                        <label for="observacoes" class="label-tw">Observações / Achados Clínicos</label>
                        <textarea class="input-tw @error('observacoes') input-error-tw @enderror"
                                  id="observacoes"
                                  name="observacoes"
                                  rows="3">{{ old('observacoes', $consultation->observacoes) }}</textarea>
                        @error('observacoes')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="orientacoes" class="label-tw">Orientações e Recomendações à Gestante</label>
                        <textarea class="input-tw @error('orientacoes') input-error-tw @enderror"
                                  id="orientacoes"
                                  name="orientacoes"
                                  rows="3">{{ old('orientacoes', $consultation->orientacoes) }}</textarea>
                        @error('orientacoes')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Próxima Consulta & Status --}}
                <div class="border-t border-surface-100 pt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="proxima_consulta" class="label-tw">Agendar Próxima Consulta</label>
                        <input type="date"
                               class="input-tw @error('proxima_consulta') input-error-tw @enderror"
                               id="proxima_consulta"
                               name="proxima_consulta"
                               value="{{ old('proxima_consulta', optional($consultation->proxima_consulta)->format('Y-m-d')) }}">
                        @error('proxima_consulta')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="label-tw">Status <span class="text-crimson-500">*</span></label>
                        <select class="input-tw @error('status') input-error-tw @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="agendada" {{ old('status', $consultation->status) === 'agendada' ? 'selected' : '' }}>Agendada</option>
                            <option value="confirmada" {{ old('status', $consultation->status) === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="realizada" {{ old('status', $consultation->status) === 'realizada' ? 'selected' : '' }}>Realizada</option>
                            <option value="cancelada" {{ old('status', $consultation->status) === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('status')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                    <a href="{{ route('consultations.show', $consultation) }}" class="btn-secondary-tw">Cancelar</a>
                    <button type="submit" class="btn-primary-tw">
                        <i class="fas fa-save text-xs"></i>
                        <span>Salvar Alterações</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection