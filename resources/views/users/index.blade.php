@extends('layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Gestão de Usuários')
@section('title-icon', 'fa-users-gear')

@section('breadcrumbs')
<li class="breadcrumb-item active">Usuários</li>
@endsection

@section('content')
<!-- Header com Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="row">
            @php
                $stats = [
                    'total' => $users->total(),
                    'active' => \App\Models\User::whereNotNull('email_verified_at')->count(),
                    'doctors' => \App\Models\User::role('Médico')->count(),
                    'nurses' => \App\Models\User::role('Enfermeiro')->count(),
                ];
            @endphp
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white" style="background-color: skyblue;">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <small>Total de Usuários</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['active'] }}</h4>
                        <small>Usuários Ativos</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-user-md fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['doctors'] }}</h4>
                        <small>Médicos</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-user-nurse fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['nurses'] }}</h4>
                        <small>Enfermeiros</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ações Rápidas
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Novo Usuário
                    </a>
                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                        <i class="fas fa-file-import me-2"></i>
                        Importar Usuários
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Pesquisa -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Pesquisar</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome, email ou CRM...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-md-2">
                <label for="role" class="form-label">Função</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Todas</option>
                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="sort" class="form-label">Ordenar por</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome</option>
                    <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Data</option>
                    <option value="role" {{ request('sort') == 'role' ? 'selected' : '' }}>Função</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status', 'sort']))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i> Limpar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Usuários -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Usuários
            @if(request()->hasAny(['search', 'role', 'status']))
                <span class="badge bg-primary ms-2">{{ $users->total() }} encontrados</span>
            @endif
        </h6>
        
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i> Exportar
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('users.export', ['format' => 'excel']) }}">
                    <i class="fas fa-file-excel me-1 text-success"></i> Excel
                </a></li>
                <li><a class="dropdown-item" href="{{ route('users.export', ['format' => 'pdf']) }}">
                    <i class="fas fa-file-pdf me-1 text-danger"></i> PDF
                </a></li>
                <li><a class="dropdown-item" href="{{ route('users.export', ['format' => 'csv']) }}">
                    <i class="fas fa-file-csv me-1 text-info"></i> CSV
                </a></li>
            </ul>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>Usuário</th>
                            <th>Função</th>
                            <th>Especialidade/CRM</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    @php
                                        $badgeClass = match($role->name) {
                                            'Administrador' => 'bg-danger',
                                            'Médico' => 'bg-primary',
                                            'Enfermeiro' => 'bg-success',
                                            'Laboratorista' => 'bg-info',
                                            'Recepcionista' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->especialidade)
                                    <strong>{{ $user->especialidade }}</strong><br>
                                @endif
                                @if($user->crm)
                                    <small class="text-muted">CRM: {{ $user->crm }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->telefone)
                                    <i class="fas fa-phone fa-sm text-muted me-1"></i>
                                    <small>{{ $user->telefone }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Ativo
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Inativo
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at->format('d/m/Y') }}<br>
                                    {{ $user->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($user->id !== auth()->id())
                                                <li>
                                                    <button class="dropdown-item toggle-status" 
                                                            data-user-id="{{ $user->id }}">
                                                        <i class="fas {{ $user->email_verified_at ? 'fa-ban text-warning' : 'fa-check text-success' }} me-1"></i>
                                                        {{ $user->email_verified_at ? 'Desativar' : 'Ativar' }}
                                                    </button>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="{{ route('users.activity', $user) }}">
                                                    <i class="fas fa-chart-line text-info me-1"></i> Atividades
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" 
                                                            onclick="return confirm('Resetar senha do usuário?')">
                                                        <i class="fas fa-key text-warning me-1"></i> Resetar Senha
                                                    </button>
                                                </form>
                                            </li>
                                            @if($user->id !== auth()->id() && auth()->user()->hasRole('Administrador'))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger delete-user" 
                                                            data-user-id="{{ $user->id }}" 
                                                            data-user-name="{{ $user->name }}">
                                                        <i class="fas fa-trash me-1"></i> Excluir
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            @if($users->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} 
                            de {{ $users->total() }} usuários
                        </div>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum usuário encontrado</h5>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'role', 'status']))
                        Nenhum usuário corresponde aos filtros aplicados.
                        <br>
                        <a href="{{ route('users.index') }}" class="text-primary">Limpar filtros</a>
                    @else
                        Comece criando o primeiro usuário do sistema.
                    @endif
                </p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Criar Primeiro Usuário
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Ações em Lote -->
<div class="card border-0 shadow-sm mt-4" id="bulkActions" style="display: none;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span id="selectedCount">0</span> usuário(s) selecionado(s)
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-success" id="bulkActivate">
                        <i class="fas fa-check me-1"></i> Ativar
                    </button>
                    <button class="btn btn-sm btn-outline-warning" id="bulkDeactivate">
                        <i class="fas fa-ban me-1"></i> Desativar
                    </button>
                    <button class="btn btn-sm btn-outline-info" id="bulkExport">
                        <i class="fas fa-download me-1"></i> Exportar
                    </button>
                    <button class="btn btn-sm btn-outline-danger" id="bulkDelete">
                        <i class="fas fa-trash me-1"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Importação -->
<div class="modal fade" id="importUsersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-import text-primary me-2"></i>
                    Importar Usuários
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Arquivo Excel (.xlsx)</label>
                        <input type="file" class="form-control" id="import_file" name="file" 
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Faça o download do <a href="{{route('users.template')}}">modelo de planilha</a>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Importante:</strong> Use apenas o modelo fornecido para garantir a importação correta.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="importBtn">
                    <i class="fas fa-upload me-1"></i> Importar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
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
                    Tem certeza que deseja excluir o usuário <strong id="deleteUserName"></strong>?
                </p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita e todos os dados relacionados serão perdidos.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteUserForm" method="POST" class="d-inline">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox functionality
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateBulkActions();
        });
    });

    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        selectAllCheckbox.checked = checkedBoxes.length === userCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < userCheckboxes.length;
    }

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = count;
        } else {
            bulkActions.style.display = 'none';
        }
    }

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

    // Delete user functionality
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserForm').action = `/users/${userId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        });
    });

    // Import functionality
    document.getElementById('importBtn').addEventListener('click', function() {
        const form = document.getElementById('importForm');
        const formData = new FormData(form);
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Importando...';
        this.disabled = true;
        
        fetch('/users/import', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Erro ao importar usuários');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao importar usuários');
        })
        .finally(() => {
            this.innerHTML = '<i class="fas fa-upload me-1"></i> Importar';
            this.disabled = false;
        });
    });

    // Bulk actions
    document.getElementById('bulkActivate').addEventListener('click', function() {
        performBulkAction('activate', 'Ativar usuários selecionados?');
    });

    document.getElementById('bulkDeactivate').addEventListener('click', function() {
        performBulkAction('deactivate', 'Desativar usuários selecionados?');
    });

    document.getElementById('bulkDelete').addEventListener('click', function() {
        performBulkAction('delete', 'Excluir usuários selecionados? Esta ação não pode ser desfeita!');
    });

    function performBulkAction(action, confirmMessage) {
        if (!confirm(confirmMessage)) return;

        const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked'))
            .map(cb => cb.value);

        fetch(`/users/bulk-${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ users: selectedUsers })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || `Erro ao ${action} usuários`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Erro ao ${action} usuários`);
        });
    }

    // Auto-submit form on filter change
    document.querySelectorAll('#role, #status, #sort').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Clear search on ESC key
    document.getElementById('search').addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.form.submit();
        }
    });
});
</script>
@endpush