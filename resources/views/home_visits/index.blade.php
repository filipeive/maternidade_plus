@extends('layouts.app')

@section('title', 'Visitas Domiciliárias')
@section('page-title', 'Gestão de Visitas Domiciliárias')
@section('title-icon', 'fa-home-medical')

@section('breadcrumbs')
<li class="breadcrumb-item active">Visitas Domiciliárias</li>
@endsection

@section('content')
<!-- Header com Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-day fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['agendadas_hoje'] }}</h4>
                        <small>Agendadas Hoje</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['realizadas_semana'] }}</h4>
                        <small>Realizadas (Semana)</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['atrasadas'] }}</h4>
                        <small>Atrasadas</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['total_mes'] }}</h4>
                        <small>Total do Mês</small>
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
                    <a href="{{ route('home_visits.create') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Nova Visita
                    </a>
                    <a href="{{ route('home_visits.daily-schedule') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar-day me-2"></i>
                        Agenda Diária
                    </a>
                    <a href="{{ route('home_visits.active-search') }}" class="btn btn-outline-warning">
                        <i class="fas fa-search me-2"></i>
                        Busca Ativa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Pesquisa -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('home_visits.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="patient_search" class="form-label">Pesquisar Gestante</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="patient_search" name="patient_search" 
                           value="{{ request('patient_search') }}" placeholder="Nome da gestante...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="agendada" {{ request('status') == 'agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="realizada" {{ request('status') == 'realizada' ? 'selected' : '' }}>Realizada</option>
                    <option value="reagendada" {{ request('status') == 'reagendada' ? 'selected' : '' }}>Reagendada</option>
                    <option value="nao_encontrada" {{ request('status') == 'nao_encontrada' ? 'selected' : '' }}>Não Encontrada</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="tipo_visita" class="form-label">Tipo</label>
                <select class="form-select" id="tipo_visita" name="tipo_visita">
                    <option value="">Todos</option>
                    <option value="rotina" {{ request('tipo_visita') == 'rotina' ? 'selected' : '' }}>Rotina</option>
                    <option value="pos_parto" {{ request('tipo_visita') == 'pos_parto' ? 'selected' : '' }}>Pós-parto</option>
                    <option value="alto_risco" {{ request('tipo_visita') == 'alto_risco' ? 'selected' : '' }}>Alto Risco</option>
                    <option value="faltosa" {{ request('tipo_visita') == 'faltosa' ? 'selected' : '' }}>Faltosa</option>
                    <option value="emergencia" {{ request('tipo_visita') == 'emergencia' ? 'selected' : '' }}>Emergência</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="data_inicio" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                       value="{{ request('data_inicio') }}">
            </div>
            
            <div class="col-md-2">
                <label for="data_fim" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" 
                       value="{{ request('data_fim') }}">
            </div>
            
            <div class="col-md-1">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['patient_search', 'status', 'tipo_visita', 'data_inicio', 'data_fim']))
                        <a href="{{ route('home_visits.index') }}" class="btn btn-outline-secondary btn-sm mt-1">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Visitas -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Visitas Domiciliárias
            @if(request()->hasAny(['patient_search', 'status', 'tipo_visita', 'data_inicio', 'data_fim']))
                <span class="badge bg-primary ms-2">{{ $visits->total() }} encontradas</span>
            @endif
        </h6>
        
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i> Relatórios
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('home_visits.generate-report', ['format' => 'excel']) }}">
                    <i class="fas fa-file-excel me-1 text-success"></i> Relatório Excel
                </a></li>
                <li><a class="dropdown-item" href="{{ route('home_visits.generate-report', ['format' => 'pdf']) }}">
                    <i class="fas fa-file-pdf me-1 text-danger"></i> Relatório PDF
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('home_visits.route-planning') }}">
                    <i class="fas fa-route me-1 text-info"></i> Planejamento de Rota
                </a></li>
            </ul>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($visits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Gestante</th>
                            <th>Data/Hora</th>
                            <th>Tipo</th>
                            <th>Endereço</th>
                            <th>Responsável</th>
                            <th>Status</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visits as $visit)
                        <tr class="{{ $visit->status == 'atrasada' ? 'table-danger' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-pink text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px; background-color: #e91e63;">
                                        <i class="fas fa-female"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $visit->patient->nome_completo }}</strong><br>
                                        <small class="text-muted">{{ $visit->patient->documento_bi }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $visit->data_visita->format('d/m/Y') }}</strong><br>
                                <small class="text-muted">{{ $visit->data_visita->format('H:i') }}</small>
                                @if($visit->data_visita->isPast() && $visit->status == 'agendada')
                                    <br><span class="badge bg-danger">Atrasada</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tipoClass = match($visit->tipo_visita) {
                                        'rotina' => 'bg-primary',
                                        'pos_parto' => 'bg-success',
                                        'alto_risco' => 'bg-danger',
                                        'faltosa' => 'bg-warning',
                                        'emergencia' => 'bg-danger',
                                        'educacao' => 'bg-info',
                                        'seguimento' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $tipoClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $visit->tipo_visita)) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ Str::limit($visit->endereco_visita, 40) }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $visit->user->name }}</small>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($visit->status) {
                                        'agendada' => 'bg-warning',
                                        'realizada' => 'bg-success',
                                        'reagendada' => 'bg-info',
                                        'nao_encontrada' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('home_visits.show', $visit) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($visit->status == 'agendada')
                                        <a href="{{ route('home_visits.edit', $visit) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($visit->status == 'agendada')
                                                <li>
                                                    <button class="dropdown-item complete-visit" 
                                                            data-visit-id="{{ $visit->id }}">
                                                        <i class="fas fa-check text-success me-1"></i> Completar
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item reschedule-visit" 
                                                            data-visit-id="{{ $visit->id }}">
                                                        <i class="fas fa-calendar text-warning me-1"></i> Reagendar
                                                    </button>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('home_visits.mark-not-found', $visit) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-question-circle text-info me-1"></i> Não Encontrada
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger delete-visit" 
                                                            data-visit-id="{{ $visit->id }}">
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
            @if($visits->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $visits->firstItem() }} a {{ $visits->lastItem() }} 
                            de {{ $visits->total() }} visitas
                        </div>
                        {{ $visits->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-home-medical fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma visita encontrada</h5>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['patient_search', 'status', 'tipo_visita', 'data_inicio', 'data_fim']))
                        Nenhuma visita corresponde aos filtros aplicados.
                        <br>
                        <a href="{{ route('home_visits.index') }}" class="text-primary">Limpar filtros</a>
                    @else
                        Comece agendando a primeira visita domiciliária.
                    @endif
                </p>
                <a href="{{ route('home_visits.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Agendar Primeira Visita
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Completar Visita (Simplificado) -->
<div class="modal fade" id="completeVisitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Completar Visita Domiciliária
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeVisitForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="condicoes_higiene" class="form-label">Condições de Higiene</label>
                            <select class="form-select" id="condicoes_higiene" name="condicoes_higiene" required>
                                <option value="">Selecione...</option>
                                <option value="bom">Bom</option>
                                <option value="regular">Regular</option>
                                <option value="ruim">Ruim</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apoio_familiar" class="form-label">Apoio Familiar</label>
                            <select class="form-select" id="apoio_familiar" name="apoio_familiar" required>
                                <option value="">Selecione...</option>
                                <option value="adequado">Adequado</option>
                                <option value="parcial">Parcial</option>
                                <option value="inadequado">Inadequado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes_ambiente" class="form-label">Observações do Ambiente</label>
                        <textarea class="form-control" id="observacoes_ambiente" name="observacoes_ambiente" 
                                  rows="3" required placeholder="Descreva as condições do ambiente..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="orientacoes_dadas" class="form-label">Orientações Dadas</label>
                        <textarea class="form-control" id="orientacoes_dadas" name="orientacoes_dadas" 
                                  rows="3" required placeholder="Descreva as orientações fornecidas..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="necessita_referencia" name="necessita_referencia">
                                <label class="form-check-label" for="necessita_referencia">
                                    Necessita Referência Médica
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="proxima_visita" class="form-label">Próxima Visita (Opcional)</label>
                            <input type="date" class="form-control" id="proxima_visita" name="proxima_visita">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Completar Visita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Reagendamento -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar text-warning me-2"></i>
                    Reagendar Visita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nova_data" class="form-label">Nova Data</label>
                        <input type="datetime-local" class="form-control" id="nova_data" name="nova_data" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo_reagendamento" class="form-label">Motivo do Reagendamento</label>
                        <textarea class="form-control" id="motivo_reagendamento" name="motivo_reagendamento" 
                                  rows="3" required placeholder="Explique o motivo do reagendamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar me-1"></i> Reagendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Complete visit functionality
    document.querySelectorAll('.complete-visit').forEach(button => {
        button.addEventListener('click', function() {
            const visitId = this.dataset.visitId;
            document.getElementById('completeVisitForm').action = `/home-visits/${visitId}/complete`;
            
            const modal = new bootstrap.Modal(document.getElementById('completeVisitModal'));
            modal.show();
        });
    });

    // Reschedule visit functionality
    document.querySelectorAll('.reschedule-visit').forEach(button => {
        button.addEventListener('click', function() {
            const visitId = this.dataset.visitId;
            document.getElementById('rescheduleForm').action = `/home-visits/${visitId}/reschedule`;
            
            const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
            modal.show();
        });
    });

    // Delete visit functionality
    document.querySelectorAll('.delete-visit').forEach(button => {
        button.addEventListener('click', function() {
            const visitId = this.dataset.visitId;
            
            if (confirm('Tem certeza que deseja excluir esta visita?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/home-visits/${visitId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Auto-submit form on filter change
    document.querySelectorAll('#status, #tipo_visita').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Set minimum date for reschedule to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowString = tomorrow.toISOString().slice(0, 16);
    document.getElementById('nova_data').min = tomorrowString;
    
    // Set minimum date for next visit to tomorrow
    document.getElementById('proxima_visita').min = tomorrow.toISOString().slice(0, 10);
});
</script>
@endpush