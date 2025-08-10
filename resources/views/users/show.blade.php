@extends('layouts.app')

@section('title', 'Usuário')
@section('page-title', 'Detalhes do Usuário')
@section('title-icon', 'fa-user')

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('users.index') }}">Usuários</a>
</li>
<li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Informações do Usuário -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                        style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @if($user->email_verified_at)
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" 
                              title="Usuário Ativo">
                            <span class="visually-hidden">Ativo</span>
                        </span>
                    @else
                        <span class="position-absolute bottom-0 end-0 p-2 bg-danger border border-light rounded-circle" 
                              title="Usuário Inativo">
                            <span class="visually-hidden">Inativo</span>
                        </span>
                    @endif
                </div>

                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>

                @if($user->roles->isNotEmpty())
                    <div class="mb-3">
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary mb-0">{{ $stats['total_consultations'] }}</h5>
                            <small class="text-muted">Total Consultas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success mb-0">{{ $stats['this_month'] }}</h5>
                        <small class="text-muted">Este Mês</small>
                    </div>
                </div>

                <hr class="my-3">

                @if($user->especialidade)
                    <div class="mb-2">
                        <i class="fas fa-stethoscope text-primary me-2"></i>
                        <strong>Especialidade:</strong> {{ $user->especialidade }}
                    </div>
                @endif

                @if($user->crm)
                    <div class="mb-2">
                        <i class="fas fa-id-card text-primary me-2"></i>
                        <strong>CRM:</strong> {{ $user->crm }}
                    </div>
                @endif

                @if($user->telefone)
                    <div class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <strong>Telefone:</strong> {{ $user->telefone }}
                    </div>
                @endif

                <div class="mb-2">
                    <i class="fas fa-calendar text-primary me-2"></i>
                    <strong>Cadastro:</strong> {{ $user->created_at->format('d/m/Y') }}
                </div>

                <div class="mb-3">
                    <i class="fas fa-clock text-primary me-2"></i>
                    <strong>Último acesso:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Editar Usuário
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <button class="btn btn-outline-warning toggle-status" 
                                data-user-id="{{ $user->id }}">
                            <i class="fas {{ $user->email_verified_at ? 'fa-ban' : 'fa-check' }} me-1"></i>
                            {{ $user->email_verified_at ? 'Desativar' : 'Ativar' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estatísticas Adicionais -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Estatísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-warning mb-0">{{ $stats['pending_consultations'] }}</h4>
                        <small class="text-muted">Consultas Agendadas</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info mb-0">{{ $stats['exams_requested'] }}</h4>
                        <small class="text-muted">Exames Solicitados</small>
                    </div>
                </div>

                @if($user->hasRole('Laboratorista'))
                    <hr class="my-3">
                    <div class="text-center">
                        <h4 class="text-success mb-0">{{ $stats['exams_processed'] }}</h4>
                        <small class="text-muted">Exames Processados</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Atividades e Histórico -->
    <div class="col-lg-8">
        <!-- Consultas Recentes -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-check text-primary me-2"></i>
                    Consultas Recentes
                </h6>
                <span class="badge bg-primary">{{ $recentConsultations->count() }}</span>
            </div>
            <div class="card-body">
                @if($recentConsultations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Paciente</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentConsultations as $consultation)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($consultation->data_consulta)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $consultation->patient->nome_completo }}</strong>
                                    </td>
                                    <td>
                                        @switch($consultation->status)
                                            @case('realizada')
                                                <span class="badge bg-success">Realizada</span>
                                                @break
                                            @case('agendada')
                                                <span class="badge bg-warning">Agendada</span>
                                                @break
                                            @case('cancelada')
                                                <span class="badge bg-danger">Cancelada</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $consultation->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('consultations.show', $consultation) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Nenhuma consulta registrada</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Exames (se for laboratorista) -->
        @if($user->hasRole('Laboratorista') && $recentExams->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-flask text-success me-2"></i>
                    Exames Processados Recentemente
                </h6>
                <span class="badge bg-success">{{ $recentExams->count() }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Paciente</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentExams as $exam)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($exam->data_realizacao)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $exam->tipo_exame_label }}</span>
                                </td>
                                <td>
                                    <strong>{{ $exam->consultation->patient->nome_completo }}</strong>
                                </td>
                                <td>
                                    @switch($exam->status)
                                        @case('concluido')
                                            <span class="badge bg-success">Concluído</span>
                                            @break
                                        @case('em_andamento')
                                            <span class="badge bg-warning">Em Andamento</span>
                                            @break
                                        @case('solicitado')
                                            <span class="badge bg-secondary">Solicitado</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $exam->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('exams.show', $exam) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Ações Administrativas -->
        @if(auth()->user()->hasRole('Administrador'))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-cogs text-danger me-2"></i>
                    Ações Administrativas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100" 
                                    onclick="return confirm('Resetar senha do usuário?')">
                                <i class="fas fa-key me-1"></i> Resetar Senha
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('users.activity', $user) }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-chart-line me-1"></i> Ver Atividades
                        </a>
                    </div>
                </div>

                @if($user->id !== auth()->id())
                    <hr class="my-3">
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                onclick="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')">
                            <i class="fas fa-trash me-1"></i> Excluir Usuário
                        </button>
                    </form>
                @endif
            </div>
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
            const button = this;
            
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