@extends('layouts.app')

@section('title', 'Novo Usuário')
@section('page-title', 'Criar Novo Usuário')
@section('title-icon', 'fa-user-plus')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('users.index') }}">Usuários</a>
</li>
<li class="breadcrumb-item active">Novo Usuário</li>
@endsection

@section('content')
<div class="row">
    <!-- Informações e Dicas -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações Importantes
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">
                        <i class="fas fa-users me-2"></i>
                        Funções Disponíveis
                    </h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1">
                            <span class="badge bg-danger me-2">Administrador</span>
                            Acesso total ao sistema
                        </li>
                        <li class="mb-1">
                            <span class="badge bg-primary me-2">Médico</span>
                            Consultas e prescrições
                        </li>
                        <li class="mb-1">
                            <span class="badge bg-success me-2">Enfermeiro</span>
                            Consultas e cuidados
                        </li>
                        <li class="mb-1">
                            <span class="badge bg-info me-2">Laboratorista</span>
                            Processamento de exames
                        </li>
                        <li class="mb-1">
                            <span class="badge bg-warning me-2">Recepcionista</span>
                            Agendamentos e cadastros
                        </li>
                    </ul>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-success">
                        <i class="fas fa-shield-alt me-2"></i>
                        Segurança
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li>• Senha mínima de 8 caracteres</li>
                        <li>• Email único no sistema</li>
                        <li>• Usuário ativo por padrão</li>
                        <li>• Email de verificação automático</li>
                    </ul>
                </div>

                <hr>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> O usuário receberá um email com as credenciais de acesso.
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Usuários no Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @php
                        $userStats = [
                            'total' => \App\Models\User::count(),
                            'active' => \App\Models\User::whereNotNull('email_verified_at')->count(),
                            'doctors' => \App\Models\User::role('Médico')->count(),
                            'nurses' => \App\Models\User::role('Enfermeiro')->count(),
                        ];
                    @endphp
                    
                    <div class="col-6 mb-3">
                        <h4 class="text-primary mb-0">{{ $userStats['total'] }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-0">{{ $userStats['active'] }}</h4>
                        <small class="text-muted">Ativos</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0">{{ $userStats['doctors'] }}</h4>
                        <small class="text-muted">Médicos</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0">{{ $userStats['nurses'] }}</h4>
                        <small class="text-muted">Enfermeiros</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Criação -->
    <div class="col-lg-8">
        <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
            @csrf

            <!-- Informações Básicas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Informações Básicas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Ex: Dr. João Silva">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required
                                   placeholder="joao.silva@hospital.mz">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Função <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Selecione uma função</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                   id="telefone" name="telefone" value="{{ old('telefone') }}"
                                   placeholder="+258 XX XXX XXXX">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Profissionais -->
            <div class="card border-0 shadow-sm mb-4" id="professionalInfo">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-stethoscope text-success me-2"></i>
                        Informações Profissionais
                        <small class="text-muted">(para profissionais de saúde)</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="especialidade" class="form-label">Especialidade</label>
                            <input type="text" class="form-control @error('especialidade') is-invalid @enderror" 
                                   id="especialidade" name="especialidade" value="{{ old('especialidade') }}"
                                   placeholder="Ex: Ginecologia e Obstetrícia">
                            @error('especialidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="crm" class="form-label">CRM/Registro Profissional</label>
                            <input type="text" class="form-control @error('crm') is-invalid @enderror" 
                                   id="crm" name="crm" value="{{ old('crm') }}"
                                   placeholder="Ex: 12345/MZ">
                            @error('crm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credenciais de Acesso -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lock text-warning me-2"></i>
                        Credenciais de Acesso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Senha <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required autocomplete="new-password"
                                       placeholder="Mínimo 8 caracteres">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Use uma senha forte com pelo menos 8 caracteres
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Repita a senha">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="passwordMatch"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="generatePassword">
                                <i class="fas fa-magic me-1"></i> Gerar Senha Segura
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações Iniciais -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs text-secondary me-2"></i>
                        Configurações Iniciais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                                <label class="form-check-label" for="active">
                                    Usuário Ativo
                                </label>
                            </div>
                            <div class="form-text">
                                Usuário poderá fazer login imediatamente após criação
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
                                <label class="form-check-label" for="send_email">
                                    Enviar Email de Boas-vindas
                                </label>
                            </div>
                            <div class="form-text">
                                Enviar credenciais por email automaticamente
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo -->
            <div class="card border-0 shadow-sm mb-4" id="summaryCard" style="display: none;">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-check text-info me-2"></i>
                        Resumo do Usuário
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nome:</strong> <span id="summaryName">-</span><br>
                            <strong>Email:</strong> <span id="summaryEmail">-</span><br>
                            <strong>Função:</strong> <span id="summaryRole">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Telefone:</strong> <span id="summaryPhone">-</span><br>
                            <strong>Especialidade:</strong> <span id="summarySpecialty">-</span><br>
                            <strong>CRM:</strong> <span id="summaryCrm">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Criar Usuário
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-info" id="previewBtn">
                                    <i class="fas fa-eye me-1"></i> Visualizar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    const roleSelect = document.getElementById('role');
    const professionalInfo = document.getElementById('professionalInfo');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchDiv = document.getElementById('passwordMatch');
    const summaryCard = document.getElementById('summaryCard');

    // Toggle professional info visibility based on role
    function toggleProfessionalInfo() {
        const selectedRole = roleSelect.value;
        const showProfessionalFields = ['Médico', 'Enfermeiro', 'Laboratorista'].includes(selectedRole);
        
        if (showProfessionalFields) {
            professionalInfo.style.display = 'block';
            professionalInfo.classList.add('fade-in');
        } else {
            professionalInfo.style.display = 'none';
            professionalInfo.classList.remove('fade-in');
            // Clear professional fields
            document.getElementById('especialidade').value = '';
            document.getElementById('crm').value = '';
        }
    }

    roleSelect.addEventListener('change', toggleProfessionalInfo);
    toggleProfessionalInfo(); // Call on page load

    // Password visibility toggles
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
        const type = passwordConfirmField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Password matching validation
    function validatePasswords() {
        const password = passwordField.value;
        const confirmation = passwordConfirmField.value;
        
        if (password && confirmation) {
            if (password === confirmation) {
                passwordMatchDiv.innerHTML = '<i class="fas fa-check text-success me-1"></i>Senhas coincidem';
                passwordMatchDiv.className = 'form-text text-success';
                passwordConfirmField.classList.remove('is-invalid');
                passwordConfirmField.classList.add('is-valid');
            } else {
                passwordMatchDiv.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Senhas não coincidem';
                passwordMatchDiv.className = 'form-text text-danger';
                passwordConfirmField.classList.remove('is-valid');
                passwordConfirmField.classList.add('is-invalid');
            }
        } else {
            passwordMatchDiv.innerHTML = '';
            passwordConfirmField.classList.remove('is-valid', 'is-invalid');
        }
    }

    passwordField.addEventListener('input', validatePasswords);
    passwordConfirmField.addEventListener('input', validatePasswords);

    // Generate secure password
    document.getElementById('generatePassword').addEventListener('click', function() {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        
        passwordField.value = password;
        passwordConfirmField.value = password;
        validatePasswords();
        
        // Show password temporarily
        passwordField.type = 'text';
        passwordConfirmField.type = 'text';
        
        // Hide after 3 seconds
        setTimeout(() => {
            passwordField.type = 'password';
            passwordConfirmField.type = 'password';
        }, 3000);
        
        // Show notification
        showToast('Senha gerada automaticamente! Visível por 3 segundos.', 'success');
    });

    // Preview functionality
    document.getElementById('previewBtn').addEventListener('click', function() {
        updateSummary();
        summaryCard.style.display = summaryCard.style.display === 'none' ? 'block' : 'none';
        
        if (summaryCard.style.display === 'block') {
            summaryCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            this.innerHTML = '<i class="fas fa-eye-slash me-1"></i> Ocultar';
        } else {
            this.innerHTML = '<i class="fas fa-eye me-1"></i> Visualizar';
        }
    });

    function updateSummary() {
        document.getElementById('summaryName').textContent = document.getElementById('name').value || '-';
        document.getElementById('summaryEmail').textContent = document.getElementById('email').value || '-';
        document.getElementById('summaryRole').textContent = document.getElementById('role').value || '-';
        document.getElementById('summaryPhone').textContent = document.getElementById('telefone').value || '-';
        document.getElementById('summarySpecialty').textContent = document.getElementById('especialidade').value || '-';
        document.getElementById('summaryCrm').textContent = document.getElementById('crm').value || '-';
    }

    // Form validation before submit
    form.addEventListener('submit', function(e) {
        const password = passwordField.value;
        const confirmation = passwordConfirmField.value;
        
        if (password !== confirmation) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            passwordConfirmField.focus();
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('A senha deve ter pelo menos 8 caracteres!');
            passwordField.focus();
            return false;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Criando...';
        submitBtn.disabled = true;

        // Re-enable button if form submission fails
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });

    // Real-time form validation
    const requiredFields = ['name', 'email', 'role', 'password', 'password_confirmation'];
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        }
    });

    // Email validation
    document.getElementById('email').addEventListener('input', function() {
        const email = this.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (email) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Auto-format phone number
    document.getElementById('telefone').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.startsWith('258')) {
                value = '+' + value;
            } else if (!value.startsWith('+')) {
                value = '+258' + value;
            }
        }
        this.value = value;
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
});
</script>
@endpush