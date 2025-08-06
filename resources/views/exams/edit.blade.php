@extends('layouts.app')

@section('title', 'Editar Exame')
@section('page-title', 'Editar Exame')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exames</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Editar Exame</h5>
                <div class="mt-2">
                    <small class="text-muted">
                        <strong>Gestante:</strong> {{ $exam->consultation->patient->nome_completo }} | 
                        <strong>BI:</strong> {{ $exam->consultation->patient->documento_bi }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('exams.update', $exam) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="tipo_exame" class="form-label">Tipo de Exame <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_exame') is-invalid @enderror" 
                                    id="tipo_exame" name="tipo_exame" required>
                                <option value="">Selecione o tipo de exame</option>
                                @foreach($tiposExames as $key => $label)
                                    <option value="{{ $key }}" {{ old('tipo_exame', $exam->tipo_exame) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_exame')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="data_solicitacao" class="form-label">Data da Solicitação <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('data_solicitacao') is-invalid @enderror" 
                                   id="data_solicitacao" name="data_solicitacao" 
                                   value="{{ old('data_solicitacao', $exam->data_solicitacao->format('Y-m-d')) }}" required>
                            @error('data_solicitacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao_exame" class="form-label">Descrição Específica</label>
                        <input type="text" class="form-control @error('descricao_exame') is-invalid @enderror" 
                               id="descricao_exame" name="descricao_exame" 
                               value="{{ old('descricao_exame', $exam->descricao_exame) }}"
                               placeholder="Detalhes específicos do exame (opcional)">
                        @error('descricao_exame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_realizacao" class="form-label">Data de Realização</label>
                            <input type="date" class="form-control @error('data_realizacao') is-invalid @enderror" 
                                   id="data_realizacao" name="data_realizacao" 
                                   value="{{ old('data_realizacao', optional($exam->data_realizacao)->format('Y-m-d')) }}">
                            @error('data_realizacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="solicitado" {{ old('status', $exam->status) === 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                                <option value="realizado" {{ old('status', $exam->status) === 'realizado' ? 'selected' : '' }}>Realizado</option>
                                <option value="pendente" {{ old('status', $exam->status) === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resultado" class="form-label">Resultado</label>
                        <textarea class="form-control @error('resultado') is-invalid @enderror" 
                                  id="resultado" name="resultado" rows="4"
                                  placeholder="Resultado do exame">{{ old('resultado', $exam->resultado) }}</textarea>
                        @error('resultado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" name="observacoes" rows="3"
                                  placeholder="Observações adicionais">{{ old('observacoes', $exam->observacoes) }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação da data de realização
    const dataSolicitacao = document.getElementById('data_solicitacao');
    const dataRealizacao = document.getElementById('data_realizacao');
    
    dataSolicitacao.addEventListener('change', function() {
        if (dataRealizacao.value && new Date(dataRealizacao.value) < new Date(this.value)) {
            dataRealizacao.setCustomValidity('A data de realização não pode ser anterior à data de solicitação.');
        } else {
            dataRealizacao.setCustomValidity('');
        }
    });
    
    dataRealizacao.addEventListener('change', function() {
        if (this.value && new Date(this.value) < new Date(dataSolicitacao.value)) {
            this.setCustomValidity('A data de realização não pode ser anterior à data de solicitação.');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush