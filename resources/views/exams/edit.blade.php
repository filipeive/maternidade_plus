@extends('layouts.app-tw')

@section('title', 'Editar Exame')
@section('page-title', 'Editar Exame Laboratorial')
@section('title-icon', 'fa-pen-to-square')

@section('breadcrumbs')
    <a href="{{ route('exams.index') }}">Exames</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar</span>
@endsection

@section('content')
<div class="max-w-full mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-surface-900">Editar Exame #{{ $exam->id }}</h3>
                    <p class="text-2xs text-surface-500">Gestante: <strong>{{ $exam->consultation->patient->nome_completo }}</strong> (BI: {{ $exam->consultation->patient->documento_bi }})</p>
                </div>
            </div>
            <a href="{{ route('exams.show', $exam) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <div class="card-body-tw">
            <form action="{{ route('exams.update', $exam) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="tipo_exame" class="label-tw">Tipo de Exame <span class="text-crimson-500">*</span></label>
                        <select class="input-tw @error('tipo_exame') input-error-tw @enderror"
                                id="tipo_exame"
                                name="tipo_exame"
                                required>
                            <option value="">Selecione o tipo</option>
                            @foreach($tiposExames as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo_exame', $exam->tipo_exame) === $key ? 'selected' : '' }}>
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
                               value="{{ old('data_solicitacao', $exam->data_solicitacao->format('Y-m-d')) }}"
                               required>
                        @error('data_solicitacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="descricao_exame" class="label-tw">Descrição Específica</label>
                    <input type="text"
                           class="input-tw @error('descricao_exame') input-error-tw @enderror"
                           id="descricao_exame"
                           name="descricao_exame"
                           value="{{ old('descricao_exame', $exam->descricao_exame) }}"
                           placeholder="Detalhes específicos do exame (opcional)">
                    @error('descricao_exame')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="data_realizacao" class="label-tw">Data de Realização</label>
                        <input type="date"
                               class="input-tw @error('data_realizacao') input-error-tw @enderror"
                               id="data_realizacao"
                               name="data_realizacao"
                               value="{{ old('data_realizacao', optional($exam->data_realizacao)->format('Y-m-d')) }}">
                        @error('data_realizacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="label-tw">Status <span class="text-crimson-500">*</span></label>
                        <select class="input-tw @error('status') input-error-tw @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="solicitado" {{ old('status', $exam->status) === 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                            <option value="realizado" {{ old('status', $exam->status) === 'realizado' ? 'selected' : '' }}>Realizado</option>
                            <option value="pendente" {{ old('status', $exam->status) === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        </select>
                        @error('status')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="resultado" class="label-tw">Resultado</label>
                    <textarea class="input-tw @error('resultado') input-error-tw @enderror"
                              id="resultado"
                              name="resultado"
                              rows="4"
                              placeholder="Resultado do exame">{{ old('resultado', $exam->resultado) }}</textarea>
                    @error('resultado')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                    <a href="{{ route('exams.show', $exam) }}" class="btn-secondary-tw">Cancelar</a>
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