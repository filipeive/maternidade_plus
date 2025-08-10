@extends('layouts.app')

@section('title', 'Editar Consulta')
@section('page-title', 'Editar Consulta')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('consultations.index') }}">Consultas</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    {{-- erros --}}
    

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Editar Consulta</h5>
                <div class="mt-2">
                    <small class="text-muted">
                        <strong>Gestante:</strong> {{ $consultation->patient->nome_completo }} | 
                        <strong>BI:</strong> {{ $consultation->patient->documento_bi }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <!-- Seção de alertas de erro -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erros encontrados:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Seção de sucesso (se houver) -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <form action="{{ route('consultations.update', $consultation) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Dados da Gestante (somente exibição) -->
                    <div class="mb-3">
                        <label class="form-label">Gestante</label>
                        <input type="text" class="form-control" 
                               value="{{ $consultation->patient->nome_completo }}" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_consulta" class="form-label">Data e Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('data_consulta') is-invalid @enderror" 
                                   id="data_consulta" name="data_consulta" 
                                   value="{{ old('data_consulta', $consultation->data_consulta->format('Y-m-d\TH:i')) }}" required>
                            @error('data_consulta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_consulta" class="form-label">Tipo de Consulta <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_consulta') is-invalid @enderror" 
                                    id="tipo_consulta" name="tipo_consulta" required>
                                <option value="">Selecione o tipo</option>
                                <option value="1_trimestre" {{ old('tipo_consulta', $consultation->tipo_consulta) === '1_trimestre' ? 'selected' : '' }}>
                                    1º Trimestre (até 12 semanas)
                                </option>
                                <option value="2_trimestre" {{ old('tipo_consulta', $consultation->tipo_consulta) === '2_trimestre' ? 'selected' : '' }}>
                                    2º Trimestre (13-28 semanas)
                                </option>
                                <option value="3_trimestre" {{ old('tipo_consulta', $consultation->tipo_consulta) === '3_trimestre' ? 'selected' : '' }}>
                                    3º Trimestre (29-40 semanas)
                                </option>
                                <option value="pos_parto" {{ old('tipo_consulta', $consultation->tipo_consulta) === 'pos_parto' ? 'selected' : '' }}>
                                    Pós-parto
                                </option>
                                <option value="emergencia" {{ old('tipo_consulta', $consultation->tipo_consulta) === 'emergencia' ? 'selected' : '' }}>
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
                                   value="{{ old('semanas_gestacao', $consultation->semanas_gestacao) }}" 
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
                                   value="{{ old('peso', $consultation->peso) }}" 
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
                                   value="{{ old('pressao_arterial', $consultation->pressao_arterial) }}" 
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
                                   value="{{ old('batimentos_fetais', $consultation->batimentos_fetais) }}" 
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
                                   value="{{ old('altura_uterina', $consultation->altura_uterina) }}" 
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
                                  placeholder="Registre queixas, sintomas, achados do exame físico...">{{ old('observacoes', $consultation->observacoes) }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="orientacoes" class="form-label">Orientações e Recomendações</label>
                        <textarea class="form-control @error('orientacoes') is-invalid @enderror" 
                                  id="orientacoes" name="orientacoes" rows="3" 
                                  placeholder="Orientações sobre alimentação, cuidados, medicamentos...">{{ old('orientacoes', $consultation->orientacoes) }}</textarea>
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
                                   value="{{ old('proxima_consulta', optional($consultation->proxima_consulta)->format('Y-m-d')) }}">
                            @error('proxima_consulta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="agendada" {{ old('status', $consultation->status) === 'agendada' ? 'selected' : '' }}>
                                    Agendada
                                </option>
                                <option value="confirmada" {{ old('status', $consultation->status) === 'confirmada' ? 'selected' : '' }}>
                                    Confirmada
                                </option>
                                <option value="realizada" {{ old('status', $consultation->status) === 'realizada' ? 'selected' : '' }}>
                                    Realizada
                                </option>
                                <option value="cancelada" {{ old('status', $consultation->status) === 'cancelada' ? 'selected' : '' }}>
                                    Cancelada
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
    const tipoConsultaSelect = document.getElementById('tipo_consulta');
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