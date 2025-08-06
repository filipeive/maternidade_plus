@extends('layouts.app')

@section('title', 'Registrar Resultado')
@section('page-title', 'Registrar Resultado de Exame')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exames</a></li>
    <li class="breadcrumb-item active">Resultado</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registrar Resultado do Exame</h5>
                <div class="mt-2">
                    <small>
                        <strong>Gestante:</strong> {{ $exam->consultation->patient->nome_completo }} | 
                        <strong>BI:</strong> {{ $exam->consultation->patient->documento_bi }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-primary">Detalhes do Exame</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tipo:</strong> {{ $tiposExames[$exam->tipo_exame] ?? $exam->tipo_exame }}</p>
                            <p><strong>Solicitado em:</strong> {{ $exam->data_solicitacao->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Médico:</strong> {{ $exam->consultation->user->name }}</p>
                            <p><strong>Observações:</strong> {{ $exam->observacoes ?? 'Nenhuma' }}</p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('exams.store-result', $exam) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_realizacao" class="form-label">Data de Realização <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('data_realizacao') is-invalid @enderror" 
                                   id="data_realizacao" name="data_realizacao" 
                                   value="{{ old('data_realizacao', date('Y-m-d')) }}" required>
                            @error('data_realizacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data de Solicitação</label>
                            <input type="text" class="form-control" 
                                   value="{{ $exam->data_solicitacao->format('d/m/Y') }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resultado" class="form-label">Resultado <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('resultado') is-invalid @enderror" 
                                  id="resultado" name="resultado" rows="6" required
                                  placeholder="Descreva os resultados do exame...">{{ old('resultado') }}</textarea>
                        @error('resultado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações do Laboratório</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" name="observacoes" rows="3"
                                  placeholder="Observações adicionais...">{{ old('observacoes', $exam->observacoes) }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="attachments" class="form-label">Anexos</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror" 
                               id="attachments" name="attachments[]" multiple
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small class="text-muted">Formatos aceitos: JPG, PNG, PDF, DOC (Máx. 10MB cada)</small>
                        @error('attachments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div id="file-preview" class="mt-2 d-none">
                            <h6 class="text-muted">Arquivos selecionados:</h6>
                            <ul id="file-list" class="list-group"></ul>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-1"></i>Registrar Resultado
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
    const dataRealizacao = document.getElementById('data_realizacao');
    const dataSolicitacao = new Date('{{ $exam->data_solicitacao->format('Y-m-d') }}');
    
    dataRealizacao.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        
        if (selectedDate < dataSolicitacao) {
            this.setCustomValidity('A data de realização não pode ser anterior à data de solicitação.');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Preview dos arquivos selecionados
    const fileInput = document.getElementById('attachments');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');
    
    fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';
        
        if (this.files.length > 0) {
            filePreview.classList.remove('d-none');
            
            Array.from(this.files).forEach(file => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                
                const fileInfo = document.createElement('span');
                fileInfo.textContent = `${file.name} (${formatFileSize(file.size)})`;
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-outline-danger';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.addEventListener('click', () => {
                    // Cria um novo DataTransfer para remover o arquivo
                    const newFiles = new DataTransfer();
                    Array.from(fileInput.files).forEach(f => {
                        if (f !== file) newFiles.items.add(f);
                    });
                    
                    fileInput.files = newFiles.files;
                    fileInput.dispatchEvent(new Event('change'));
                });
                
                listItem.appendChild(fileInfo);
                listItem.appendChild(removeBtn);
                fileList.appendChild(listItem);
            });
        } else {
            filePreview.classList.add('d-none');
        }
    });
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endpush