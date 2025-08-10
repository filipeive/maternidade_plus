@extends('layouts.app')

@section('title', 'Solicitar Exame')
@section('page-title', 'Solicitar Novo Exame')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Solicitação de Exame</h5>
                @if($patient)
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>Gestante:</strong> {{ $patient->nome_completo }} | 
                            <strong>BI:</strong> {{ $patient->documento_bi }}
                        </small>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('exams.store') }}" method="POST">
                    @csrf
                    
                    @if($consultation)
                        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                    @else
                        <div class="mb-3">
                            <label for="consultation_id" class="form-label">Consulta <span class="text-danger">*</span></label>
                            <select class="form-select @error('consultation_id') is-invalid @enderror" 
                                    id="consultation_id" name="consultation_id" required>
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
                                        {{ $cons->patient->nome_completo }} - {{ $cons->data_consulta->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('consultation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="tipo_exame" class="form-label">Tipo de Exame <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_exame') is-invalid @enderror" 
                                    id="tipo_exame" name="tipo_exame" required>
                                <option value="">Selecione o tipo de exame</option>
                                @foreach($tiposExames as $key => $label)
                                    <option value="{{ $key }}" {{ old('tipo_exame') === $key ? 'selected' : '' }}>
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
                                   value="{{ old('data_solicitacao', date('Y-m-d')) }}" required>
                            @error('data_solicitacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao_exame" class="form-label">Descrição Específica</label>
                        <input type="text" class="form-control @error('descricao_exame') is-invalid @enderror" 
                               id="descricao_exame" name="descricao_exame" 
                               value="{{ old('descricao_exame') }}"
                               placeholder="Detalhes específicos do exame (opcional)">
                        @error('descricao_exame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações Clínicas</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" name="observacoes" rows="3"
                                  placeholder="Informações relevantes para o laboratório">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Orientações baseadas no tipo de exame -->
                    <div id="exam-guidelines" class="alert alert-info d-none">
                        <h6><i class="fas fa-info-circle me-1"></i>Orientações para o Exame</h6>
                        <div id="guidelines-content"></div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-flask me-1"></i>Solicitar Exame
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
    const tipoExameSelect = document.getElementById('tipo_exame');
    const guidelinesDiv = document.getElementById('exam-guidelines');
    const guidelinesContent = document.getElementById('guidelines-content');
    
    const examGuidelines = {
        'hemograma_completo': 'Não requer jejum. Coleta de sangue por punção venosa.',
        'glicemia_jejum': 'Jejum de 8-12 horas. Não ingerir alimentos ou bebidas (exceto água).',
        'teste_tolerancia_glicose': 'Jejum de 8-12 horas. Teste durará aproximadamente 2 horas.',
        'urina_tipo_1': 'Primeira urina da manhã. Higiene íntima antes da coleta.',
        'urocultura': 'Primeira urina da manhã. Higiene rigorosa. Coleta do jato médio.',
        'ultrassom_obstetrico': 'Bexiga cheia (beber 4-6 copos de água 1 hora antes do exame).',
        'teste_hiv': 'Não requer preparo especial. Aconselhamento pré e pós-teste.',
        'teste_sifilis': 'Não requer preparo especial. Coleta de sangue.',
        'hepatite_b': 'Não requer preparo especial. Coleta de sangue.',
        'toxoplasmose': 'Não requer preparo especial. Coleta de sangue.',
        'rubeola': 'Não requer preparo especial. Coleta de sangue.',
        'estreptococo_grupo_b': 'Coleta entre 35-37 semanas de gestação. Swab vaginal e anal.'
    };
    
    tipoExameSelect.addEventListener('change', function() {
        const selectedExam = this.value;
        
        if (selectedExam && examGuidelines[selectedExam]) {
            guidelinesContent.textContent = examGuidelines[selectedExam];
            guidelinesDiv.classList.remove('d-none');
        } else {
            guidelinesDiv.classList.add('d-none');
        }
    });
    
    // Validação da data
    const dataSolicitacao = document.getElementById('data_solicitacao');
    dataSolicitacao.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate > today) {
            this.setCustomValidity('A data da solicitação não pode ser futura.');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush