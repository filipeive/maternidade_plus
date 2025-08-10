@extends('layouts.app')

@section('title', 'Nova Consulta')
@section('page-title', 'Agendar Nova Consulta')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dados da Consulta</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('consultations.store') }}" method="POST">
                    @csrf
                    
                    <!-- Seleção da Gestante -->
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Gestante <span class="text-danger">*</span></label>
                        <select class="form-select @error('patient_id') is-invalid @enderror" 
                                id="patient_id" name="patient_id" required>
                            <option value="">Selecione a gestante</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" 
                                        {{ (old('patient_id') == $p->id || ($patient && $patient->id == $p->id)) ? 'selected' : '' }}
                                        data-semanas="{{ $p->semanas_gestacao }}"
                                        data-tipo-sanguineo="{{ $p->tipo_sanguineo }}"
                                        data-alergias="{{ $p->alergias }}">
                                    {{ $p->nome_completo }} - BI: {{ $p->documento_bi }}
                                    @if($p->semanas_gestacao)
                                        ({{ $p->semanas_gestacao }}ª semana)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informações da gestante selecionada -->
                    <div id="patient-info" class="alert alert-info" style="display: none;">
                        <h6><i class="fas fa-info-circle me-1"></i>Informações da Gestante:</h6>
                        <div id="patient-details"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_consulta" class="form-label">Data e Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('data_consulta') is-invalid @enderror" 
                                   id="data_consulta" name="data_consulta" 
                                   value="{{ old('data_consulta') }}" required>
                            @error('data_consulta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_consulta" class="form-label">Tipo de Consulta <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_consulta') is-invalid @enderror" 
                                    id="tipo_consulta" name="tipo_consulta" required>
                                <option value="">Selecione o tipo</option>
                                <option value="1_trimestre" {{ old('tipo_consulta') === '1_trimestre' ? 'selected' : '' }}>
                                    1º Trimestre (até 12 semanas)
                                </option>
                                <option value="2_trimestre" {{ old('tipo_consulta') === '2_trimestre' ? 'selected' : '' }}>
                                    2º Trimestre (13-28 semanas)
                                </option>
                                <option value="3_trimestre" {{ old('tipo_consulta') === '3_trimestre' ? 'selected' : '' }}>
                                    3º Trimestre (29-40 semanas)
                                </option>
                                <option value="pos_parto" {{ old('tipo_consulta') === 'pos_parto' ? 'selected' : '' }}>
                                    Pós-parto
                                </option>
                                <option value="emergencia" {{ old('tipo_consulta') === 'emergencia' ? 'selected' : '' }}>
                                    Emergência
                                </option>
                            </select>
                            @error('tipo_consulta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="semanas_gestacao" class="form-label">Semanas de Gestação</label>
                            <input type="number" 
                                   class="form-control @error('semanas_gestacao') is-invalid @enderror" 
                                   id="semanas_gestacao" name="semanas_gestacao" 
                                   value="{{ old('semanas_gestacao') }}" 
                                   min="1" max="42">
                            @error('semanas_gestacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="peso" class="form-label">Peso (kg)</label>
                            <input type="number" step="0.1" 
                                   class="form-control @error('peso') is-invalid @enderror" 
                                   id="peso" name="peso" 
                                   value="{{ old('peso') }}" 
                                   min="30" max="200">
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="pressao_arterial" class="form-label">Pressão Arterial</label>
                            <input type="text" 
                                   class="form-control @error('pressao_arterial') is-invalid @enderror" 
                                   id="pressao_arterial" name="pressao_arterial" 
                                   value="{{ old('pressao_arterial') }}" 
                                   placeholder="Ex: 120/80">
                            @error('pressao_arterial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batimentos_fetais" class="form-label">Batimentos Fetais (bpm)</label>
                            <input type="number" 
                                   class="form-control @error('batimentos_fetais') is-invalid @enderror" 
                                   id="batimentos_fetais" name="batimentos_fetais" 
                                   value="{{ old('batimentos_fetais') }}" 
                                   min="110" max="180">
                            @error('batimentos_fetais')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="altura_uterina" class="form-label">Altura Uterina (cm)</label>
                            <input type="number" step="0.1" 
                                   class="form-control @error('altura_uterina') is-invalid @enderror" 
                                   id="altura_uterina" name="altura_uterina" 
                                   value="{{ old('altura_uterina') }}" 
                                   min="10" max="50">
                            @error('altura_uterina')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações da Consulta</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" name="observacoes" rows="3" 
                                  placeholder="Registre queixas, sintomas, achados do exame físico...">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="orientacoes" class="form-label">Orientações e Recomendações</label>
                        <textarea class="form-control @error('orientacoes') is-invalid @enderror" 
                                  id="orientacoes" name="orientacoes" rows="3" 
                                  placeholder="Orientações sobre alimentação, cuidados, medicamentos...">{{ old('orientacoes') }}</textarea>
                        @error('orientacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="proxima_consulta" class="form-label">Próxima Consulta</label>
                            <input type="date" 
                                   class="form-control @error('proxima_consulta') is-invalid @enderror" 
                                   id="proxima_consulta" name="proxima_consulta" 
                                   value="{{ old('proxima_consulta') }}">
                            @error('proxima_consulta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="agendada" {{ old('status') === 'agendada' ? 'selected' : '' }}>
                                    Agendada
                                </option>
                                <option value="confirmada" {{ old('status') === 'confirmada' ? 'selected' : '' }}>
                                    Confirmada
                                </option>
                                <option value="realizada" {{ old('status') === 'realizada' ? 'selected' : '' }}>
                                    Realizada
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('consultations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Agendar Consulta
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
    const patientSelect = document.getElementById('patient_id');
    const patientInfo = document.getElementById('patient-info');
    const patientDetails = document.getElementById('patient-details');
    const semanasInput = document.getElementById('semanas_gestacao');
    const tipoConsultaSelect = document.getElementById('tipo_consulta');
    
    // Mostrar informações da gestante quando selecionada
    patientSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const semanas = selectedOption.dataset.semanas;
            const tipoSanguineo = selectedOption.dataset.tipoSanguineo;
            const alergias = selectedOption.dataset.alergias;
            
            let details = '';
            if (semanas) {
                details += `<strong>Semanas de gestação:</strong> ${semanas}ª semana<br>`;
                semanasInput.value = semanas;
                
                // Sugerir tipo de consulta baseado nas semanas
                if (semanas <= 12) {
                    tipoConsultaSelect.value = '1_trimestre';
                } else if (semanas <= 28) {
                    tipoConsultaSelect.value = '2_trimestre';
                } else {
                    tipoConsultaSelect.value = '3_trimestre';
                }
            }
            if (tipoSanguineo) {
                details += `<strong>Tipo sanguíneo:</strong> ${tipoSanguineo}<br>`;
            }
            if (alergias) {
                details += `<strong>⚠️ Alergias:</strong> ${alergias}`;
            }
            
            patientDetails.innerHTML = details;
            patientInfo.style.display = 'block';
        } else {
            patientInfo.style.display = 'none';
            semanasInput.value = '';
            tipoConsultaSelect.value = '';
        }
    });
    
    // Validação de pressão arterial
    const pressaoInput = document.getElementById('pressao_arterial');
    pressaoInput.addEventListener('blur', function() {
        const value = this.value.trim();
        if (value && !value.match(/^\d{2,3}\/\d{2,3}$/)) {
            this.setCustomValidity('Formato inválido. Use: 120/80');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Auto-sugerir próxima consulta baseada no tipo
    tipoConsultaSelect.addEventListener('change', function() {
        const proximaConsultaInput = document.getElementById('proxima_consulta');
        const hoje = new Date();
        let diasProxima = 28; // 4 semanas por padrão
        
        switch(this.value) {
            case '1_trimestre':
                diasProxima = 28; // 4 semanas
                break;
            case '2_trimestre':
                diasProxima = 21; // 3 semanas
                break;
            case '3_trimestre':
                diasProxima = 14; // 2 semanas
                break;
            case 'emergencia':
                diasProxima = 7; // 1 semana
                break;
        }
        
        const proximaData = new Date(hoje.getTime() + (diasProxima * 24 * 60 * 60 * 1000));
        proximaConsultaInput.value = proximaData.toISOString().split('T')[0];
    });
});
</script>
@endpush