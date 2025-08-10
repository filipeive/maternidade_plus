@extends('layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Gestão de Usuários')

@section('content')
<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-user-shield fa-2x text-danger mb-2"></i>
                <h4 class="mb-0">{{ $stats['admins'] }}</h4>
                <small class="text-muted">Admins</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-user-md fa-2x text-info mb-2"></i>
                <h4 class="mb-0">{{ $stats['medicos'] }}</h4>
                <small class="text-muted">Médicos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-user-nurse fa-2x text-success mb-2"></i>
                <h4 class="mb-0">{{ $stats['enfermeiros'] }}</h4>
                <small class="text-muted">Enfermeiros</small>
            </div>
        </div>
    </div>
    {{-- <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-flask fa-2x text-warning mb-2"></i>
                <h4 class="mb-0">{{ $stats['laboratorio'] }}</h4>
                <small class="text-muted">Laboratório</small>
            </div>
        </div>
    </div> --}}
    <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h4 class="mb-0">{{ $stats['ativos'] }}</h4>
                <small class="text-muted">Ativos</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Pesquisar</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Nome, email ou CRM">
            </div>
            <div class="col-md-3">
                <label class="form-label">Função</label>
                <select class="form-select" name="role">
                    <option value="">Todas</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Todos</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ações -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        @if(request()->hasAny(['search', 'role', 'status']))
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> Limpar Filtros
            </a>
        @endif
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Usuário
    </a>
</div>

<!-- Lista de Usuários -->
<div class="card">
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Função</th>
                            <th>Especialidade/CRM</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
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
                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->especialidade)
                                    <strong>{{ $user->especialidade }}</strong><br>
                                @endif
                                @if($user->crm)
                                    <small class="text-muted">CRM: {{ $user->crm }}</small>
                                @endif
                            </td>
                            <td>
                                @if($user->telefone)
                                    <i class="fas fa-phone fa-sm text-muted"></i> {{ $user->telefone }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <button class="btn btn-sm btn-outline-warning toggle-status" 
                                                data-user-id="{{ $user->id }}" 
                                                title="{{ $user->email_verified_at ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas {{ $user->email_verified_at ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    @endif
                                    
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('users.activity', $user) }}">
                                                    <i class="fas fa-chart-line me-1"></i> Atividades
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" 
                                                            onclick="return confirm('Resetar senha do usuário?')">
                                                        <i class="fas fa-key me-1"></i> Resetar Senha
                                                    </button>
                                                </form>
                                            </li>
                                            @if($user->id !== auth()->id())
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                            <i class="fas fa-trash me-1"></i> Excluir
                                                        </button>
                                                    </form>
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
            
            <div class="d-flex justify-content-center mt-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum usuário encontrado</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'role', 'status']))
                        Nenhum usuário corresponde aos filtros aplicados.
                    @else
                        Comece criando o primeiro usuário.
                    @endif
                </p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Usuário
                </a>
            </div>
        @endif
    </div>
</div>
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
});
</script>
@endpush