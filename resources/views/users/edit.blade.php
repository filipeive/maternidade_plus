@extends('layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')
@section('title-icon', 'fa-user-edit')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('users.index') }}">Usuários</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
</li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <!-- Informações Atuais do Usuário -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informações Atuais
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                        style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @if($user->email_verified_at)
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle">
                            <span class="visually-hidden">Ativo</span>
                        </span>
                    @else
                        <span class="position-absolute bottom-0 end-0 p-2 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">Inativo</span>
                        </span>
                    @endif
                </div>

                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>

                @if($user->roles->isNotEmpty())
                    <span class="badge bg-primary mb-3">{{ $user->roles->first()->name }}</span>
                @endif

                <hr class="my-3">

                <div class="text-start">
                    @if($user->especialidade)
                        <div class="mb-2">
                            <i class="fas fa-stethoscope text-primary me-2"></i>
                            <small><strong>Especialidade:</strong> {{ $user->especialidade }}</small>
                        </div>
                    @endif

                    @if($user->crm)
                        <div class="mb-2">
                            <i class="fas fa-id-card text-primary me-2"></i>
                            <small><strong>CRM:</strong> {{ $user->crm }}</small>
                        </div>
                    @endif

                    @if($user->telefone)
                        <div class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <small><strong>Telefone:</strong> {{ $user->telefone }}</small>
                        </div>
                    @endif

                    <div class="mb-2">
                        <i class="fas fa-calendar text-primary me-2"></i>
                        <small><strong>Cadastro:</strong> {{ $user->created_at->format('d/m/Y') }}</small>
                    </div>

                    <div class="mb-2">
                        <i class="fas fa-clock text-primary me-2"></i>
                        <small><strong>Último acesso:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i> Ver Perfil
                    </a>
                    
                    <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm w-100" 
                                onclick="return confirm('Resetar senha do usuário?')">
                            <i class="fas fa-key me-1"></i> Resetar Senha
                        </button>
                    </form>

                    @if($user->id !== auth()->id())
                        <button class="btn btn-outline-{{ $user->email_verified_at ? 'danger' : 'success' }} btn-sm toggle-status" 
                                data-user-id="{{ $user->id }}">
                            <i class="fas {{ $user->email_verified_at ? 'fa-ban' : 'fa-check' }} me-1"></i>
                            {{ $user->email_verified_at ? 'Desativar' : 'Ativar' }} Usuário
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Edição -->
    <div class="col-lg-8">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')

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
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
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
                                    <option value="{{ $role->name }}" 
                                            {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
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
                                   id="telefone" name="telefone" value="{{ old('telefone', $user->telefone) }}"
                                   placeholder="+258 XX XXX XXXX">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="especialidade" class="form-label">Especialidade</label>
                            <input type="text" class="form-control @error('especialidade') is-invalid @enderror" 
                                   id="especialidade" name="especialidade" value="{{ old('especialidade', $user->especialidade) }}"
                                   placeholder="Ex: Ginecologia e Obstetrícia">
                            @error('especialidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="crm" class="form-label">CRM</label>
                            <input type="text" class="form-control @error('crm') is-invalid @enderror" 
                                   id="crm" name="crm" value="{{ old('crm', $user->crm) }}"
                                   placeholder="Ex: 12345/MZ">
                            @error('crm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Senha -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lock text-warning me-2"></i>
                        Alterar Senha
                        <small class="text-muted">(deixe em branco para manter a atual)</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Mínimo 8 caracteres</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status e Configurações -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs text-secondary me-2"></i>
                        Status e Configurações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                       {{ old('active', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    Usuário Ativo
                                </label>
                            </div>
                            <div class="form-text">
                                Usuários inativos não podem fazer login no sistema
                            </div>
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
                                    <i class="fas fa-save me-1"></i> Salvar Alterações
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Zona de Perigo -->
        @if($user->id !== auth()->id() && auth()->user()->hasRole('Administrador'))
        <div class="card border-danger mt-4">
            <div class="card-header bg-light border-danger">
                <h6 class="mb-0 text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Zona de Perigo
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    As ações abaixo são irreversíveis. Use com cuidado.
                </p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="fas fa-trash me-1"></i> Excluir Usuário
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
@if($user->id !== auth()->id() && auth()->user()->hasRole('Administrador'))
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Tem certeza que deseja excluir o usuário <strong>{{ $user->name }}</strong>?
                </p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita e todos os dados relacionados serão perdidos.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Excluir Usuário
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status functionality
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            
            fetch(`/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Erro ao alterar status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao alterar status');
            });
        });
    });

    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePasswords() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('As senhas não coincidem');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
    }

    password.addEventListener('input', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);

    // Role-based field visibility
    const roleSelect = document.getElementById('role');
    const especialidadeField = document.getElementById('especialidade').closest('.col-md-6');
    const crmField = document.getElementById('crm').closest('.col-md-6');

    function toggleMedicalFields() {
        const selectedRole = roleSelect.value;
        const showMedicalFields = ['Médico', 'Enfermeiro', 'Laboratorista'].includes(selectedRole);
        
        especialidadeField.style.display = showMedicalFields ? 'block' : 'none';
        crmField.style.display = showMedicalFields ? 'block' : 'none';
    }

    roleSelect.addEventListener('change', toggleMedicalFields);
    toggleMedicalFields(); // Call on page load

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password && password !== passwordConfirmation) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            return false;
        }
    });
});
</script>
@endpush