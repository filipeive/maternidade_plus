@extends('layouts.app-tw')

@section('title', 'Registrar Resultado')
@section('page-title', 'Registrar Resultado de Exame')
@section('title-icon', 'fa-file-signature')

@section('breadcrumbs')
    <a href="{{ route('exams.index') }}">Exames</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Resultado</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-surface-900">Lançamento de Resultado de Exame</h3>
                    <p class="text-2xs text-surface-500">Gestante: <strong>{{ $exam->consultation->patient->nome_completo }}</strong> (BI: {{ $exam->consultation->patient->documento_bi }})</p>
                </div>
            </div>
            <a href="{{ route('exams.show', $exam) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <div class="card-body-tw">
            <div class="p-4 bg-surface-50 rounded-xl mb-6 border border-surface-200 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-surface-500 block">Tipo de Exame:</span>
                    <strong class="text-surface-900 font-semibold">{{ $tiposExames[$exam->tipo_exame] ?? $exam->tipo_exame }}</strong>
                </div>
                <div>
                    <span class="text-surface-500 block">Data Solicitação:</span>
                    <span class="text-surface-900">{{ $exam->data_solicitacao->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-surface-500 block">Solicitante:</span>
                    <span class="text-surface-900">{{ $exam->consultation->user->name ?? 'Médico' }}</span>
                </div>
                <div>
                    <span class="text-surface-500 block">Observações Prévia:</span>
                    <span class="text-surface-900">{{ $exam->observacoes ?? 'Nenhuma' }}</span>
                </div>
            </div>

            <form action="{{ route('exams.store-result', $exam) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="data_realizacao" class="label-tw">Data de Realização <span class="text-crimson-500">*</span></label>
                        <input type="date"
                               class="input-tw @error('data_realizacao') input-error-tw @enderror"
                               id="data_realizacao"
                               name="data_realizacao"
                               value="{{ old('data_realizacao', date('Y-m-d')) }}"
                               required>
                        @error('data_realizacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Data de Solicitação</label>
                        <input type="text" class="input-tw bg-surface-100 cursor-not-allowed" value="{{ $exam->data_solicitacao->format('d/m/Y') }}" readonly>
                    </div>
                </div>

                <div>
                    <label for="resultado" class="label-tw">Resultado / Laudo Clínico <span class="text-crimson-500">*</span></label>
                    <textarea class="input-tw @error('resultado') input-error-tw @enderror"
                              id="resultado"
                              name="resultado"
                              rows="6"
                              required
                              placeholder="Descreva detalhadamente o resultado do exame...">{{ old('resultado') }}</textarea>
                    @error('resultado')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="observacoes" class="label-tw">Observações Adicionais do Laboratório</label>
                    <textarea class="input-tw @error('observacoes') input-error-tw @enderror"
                              id="observacoes"
                              name="observacoes"
                              rows="3"
                              placeholder="Observações técnicas ou notas adicionais...">{{ old('observacoes', $exam->observacoes) }}</textarea>
                    @error('observacoes')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="attachments" class="label-tw">Anexos / Documentos (PDF, Imagens)</label>
                    <input type="file"
                           class="input-tw p-1.5 text-xs @error('attachments') input-error-tw @enderror"
                           id="attachments"
                           name="attachments[]"
                           multiple
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <p class="text-2xs text-surface-400 mt-1">Formatos aceites: JPG, PNG, PDF, DOC (Máx. 10MB cada)</p>
                    @error('attachments')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                    <a href="{{ route('exams.show', $exam) }}" class="btn-secondary-tw">Cancelar</a>
                    <button type="submit" class="btn-primary-tw">
                        <i class="fas fa-check-circle text-xs"></i>
                        <span>Registrar Resultado</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection