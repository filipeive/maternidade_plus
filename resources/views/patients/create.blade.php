@extends('layouts.app')

@section('title', 'Nova Gestante')
@section('page-title', 'Cadastrar Nova Gestante')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dados da Gestante</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome_completo" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nome_completo') is-invalid @enderror" 
                                   id="nome_completo" name="nome_completo" value="{{ old('nome_completo') }}" required>
                            @error('nome_completo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" 
                                   id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" required>
                            @error('data_nascimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="documento_bi" class="form-label">Documento (BI) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('documento_bi') is-invalid @enderror" 
                                   id="documento_bi" name="documento_bi" value="{{ old('documento_bi') }}" 
                                   placeholder="Ex: 120000123456A" required>
                            @error('documento_bi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contacto" class="form-label">Contacto <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('contacto') is-invalid @enderror" 
                                   id="contacto" name="contacto" value="{{ old('contacto') }}" 
                                   placeholder="Ex: +258 82 123 4567" required>
                            @error('contacto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contacto_emergencia" class="form-label">Contacto de Emergência</label>
                            <input type="tel" class="form-control @error('contacto_emergencia') is-invalid @enderror" 
                                   id="contacto_emergencia" name="contacto_emergencia" value="{{ old('contacto_emergencia') }}">
                            @error('contacto_emergencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço Completo <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('endereco') is-invalid @enderror" 
                                  id="endereco" name="endereco" rows="2" required>{{ old('endereco') }}</textarea>
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                            <select class="form-select @error('tipo_sanguineo') is-invalid @enderror" 
                                    id="tipo_sanguineo" name="tipo_sanguineo">
                                <option value="">Selecione</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo_sanguineo') === $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_sanguineo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="data_ultima_menstruacao" class="form-label">Data da Última Menstruação</label>
                            <input type="date" class="form-control @error('data_ultima_menstruacao') is-invalid @enderror" 
                                   id="data_ultima_menstruacao" name="data_ultima_menstruacao" 
                                   value="{{ old('data_ultima_menstruacao') }}">
                            @error('data_ultima_menstruacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Para calcular a data provável do parto</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="numero_gestacoes" class="form-label">Nº de Gestações <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('numero_gestacoes') is-invalid @enderror" 
                                   id="numero_gestacoes" name="numero_gestacoes" value="{{ old('numero_gestacoes', 1) }}" 
                                   min="1" required>
                            @error('numero_gestacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="numero_partos" class="form-label">Nº de Partos <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('numero_partos') is-invalid @enderror" 
                                   id="numero_partos" name="numero_partos" value="{{ old('numero_partos', 0) }}" 
                                   min="0" required>
                            @error('numero_partos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="numero_abortos" class="form-label">Nº de Abortos</label>
                            <input type="number" class="form-control @error('numero_abortos') is-invalid @enderror" 
                                   id="numero_abortos" name="numero_abortos" value="{{ old('numero_abortos', 0) }}" 
                                   min="0">
                            @error('numero_abortos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alergias" class="form-label">Alergias</label>
                        <textarea class="form-control @error('alergias') is-invalid @enderror" 
                                  id="alergias" name="alergias" rows="2" 
                                  placeholder="Liste qualquer alergia conhecida">{{ old('alergias') }}</textarea>
                        @error('alergias')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="historico_medico" class="form-label">Histórico Médico</label>
                        <textarea class="form-control @error('historico_medico') is-invalid @enderror" 
                                  id="historico_medico" name="historico_medico" rows="3" 
                                  placeholder="Descreva condições médicas relevantes, cirurgias anteriores, medicamentos em uso">{{ old('historico_medico') }}</textarea>
                        @error('historico_medico')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Cadastrar Gestante
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
    // Validação do formulário
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const gestacoes = document.getElementById('numero_gestacoes');
        const partos = document.getElementById('numero_partos');
        const abortos = document.getElementById('numero_abortos');
        
        function validateNumbers() {
            const g = parseInt(gestacoes.value) || 0;
            const p = parseInt(partos.value) || 0;
            const a = parseInt(abortos.value) || 0;
            
            if (p + a > g) {
                partos.setCustomValidity('O número de partos + abortos não pode ser maior que o número de gestações');
                abortos.setCustomValidity('O número de partos + abortos não pode ser maior que o número de gestações');
            } else {
                partos.setCustomValidity('');
                abortos.setCustomValidity('');
            }
        }
        
        gestacoes.addEventListener('input', validateNumbers);
        partos.addEventListener('input', validateNumbers);
        abortos.addEventListener('input', validateNumbers);
        
        // Calcular idade automaticamente
        const dataNascimento = document.getElementById('data_nascimento');
        dataNascimento.addEventListener('change', function() {
            const hoje = new Date();
            const nascimento = new Date(this.value);
            const idade = hoje.getFullYear() - nascimento.getFullYear();
            
            if (idade < 12 || idade > 50) {
                this.setCustomValidity('Verifique a data de nascimento. Idade fora do padrão esperado.');
            } else {
                this.setCustomValidity('');
            }
        });
    });
</script>
@endpush
