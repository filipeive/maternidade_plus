@extends('layouts.app')

@section('title', 'Gestão de Vacinas')
@section('page-title', 'Programa de Imunização Pré-natal')
@section('title-icon', 'fa-syringe')

@section('breadcrumbs')
<li class="breadcrumb-item active">Vacinas</li>
@endsection

@push('styles')
<style>
.vaccine-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.vaccine-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.vaccine-card.administered {
    border-left-color: #28a745;
    background-color: #f8fff9;
}

.vaccine-card.pending {
    border-left-color: #ffc107;
    background-color: #fffdf5;
}

.vaccine-card.overdue {
    border-left-color: #dc3545;
    background-color: #fff5f5;
    animation: pulse-red 2s infinite;
}

@keyframes pulse-red {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

.vaccination-schedule {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.vaccine-progress {
    height: 8px;
    border-radius: 4px;
}

.coverage-chart {
    height: 200px;
}
</style>
@endpush

@section('content')
<!-- Dashboard de Vacinação -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-success mb-1">{{ $stats['total_administradas'] }}</h3>
                        <p class="text-muted mb-0">Doses Administradas</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-warning mb-1">{{ $stats['doses_pendentes'] }}</h3>
                        <p class="text-muted mb-0">Doses Pendentes</p>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-danger mb-1">{{ $stats['doses_vencidas'] }}</h3>
                        <p class="text-muted mb-0">Doses Vencidas</p>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-info mb-1">{{ $stats['proximas_7_dias'] }}</h3>
                        <p class="text-muted mb-0">Próximas 7 dias</p>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Ações -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="{{ route('vaccines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nova Vacinação
                    </a>
                    <a href="{{ route('vaccines.pending-alert') }}" class="btn btn-warning">
                        <i class="fas fa-bell me-1"></i> Alertas ({{ $stats['doses_vencidas'] + $stats['proximas_7_dias'] }})
                    </a>
                    <button class="btn btn-info" onclick="showCoverageReport()">
                        <i class="fas fa-chart-pie me-1"></i> Cobertura Vacinal
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-1"></i> Filtros
                </button>
                <a href="{{ route('vaccines.generate-report') }}" class="btn btn-outline-success">
                    <i class="fas fa-file-alt me-1"></i> Relatório
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="collapse mt-3" id="filtersCollapse">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="administrada" {{ request('status') == 'administrada' ? 'selected' : '' }}>Administrada</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="vencida" {{ request('status') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de Vacina</label>
                    <select name="tipo_vacina" class="form-select">
                        <option value="">Todas</option>
                        <option value="tetanica">Tétano</option>
                        <option value="hepatite_b">Hepatite B</option>
                        <option value="influenza">Influenza</option>
                        <option value="covid19">COVID-19</option>
                        <option value="febre_amarela">Febre Amarela</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Buscar Paciente</label>
                    <input type="text" name="patient_search" class="form-control" 
                           placeholder="Nome ou BI" value="{{ request('patient_search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Alertas</label>
                    <select name="alert_type" class="form-select">
                        <option value="">Todos</option>
                        <option value="vencidas" {{ request('alert_type') == 'vencidas' ? 'selected' : '' }}>Vencidas</option>
                        <option value="proximas" {{ request('alert_type') == 'proximas' ? 'selected' : '' }}>Próximas</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('vaccines.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Vacinas -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-syringe me-2"></i>
                    Registro de Vacinações
                </h5>
            </div>
            <div class="card-body p-0">
                @if($vaccines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Paciente</th>
                                <th>Vacina</th>
                                <th>Dose</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vaccines as $vaccine)
                            @php
                                $isOverdue = $vaccine->status == 'pendente' && $vaccine->data_administracao < now();
                                $cardClass = $vaccine->status == 'administrada' ? 'administered' : 
                                           ($isOverdue ? 'overdue' : 'pending');
                            @endphp
                            <tr class="vaccine-row {{ $cardClass }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 35px; height: 35px;">
                                            <i class="fas fa-female"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $vaccine->patient->nome_completo }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $vaccine->patient->documento_bi }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $vaccineClass = match($vaccine->tipo_vacina) {
                                            'tetanica' => 'bg-primary',
                                            'hepatite_b' => 'bg-warning',
                                            'influenza' => 'bg-info',
                                            'covid19' => 'bg-danger',
                                            'febre_amarela' => 'bg-success',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $vaccineClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $vaccine->tipo_vacina)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-dark">{{ $vaccine->dose_numero }}ª dose</span>
                                </td>
                                <td>
                                    {{ $vaccine->data_administracao->format('d/m/Y') }}
                                    @if($vaccine->status == 'pendente')
                                        <br>
                                        <small class="text-muted">
                                            {{ $isOverdue ? 'Atrasada' : $vaccine->data_administracao->diffForHumans() }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($vaccine->status) {
                                            'administrada' => 'bg-success',
                                            'pendente' => $isOverdue ? 'bg-danger' : 'bg-warning',
                                            'vencida' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        $statusText = $vaccine->status == 'pendente' && $isOverdue ? 'Atrasada' : ucfirst($vaccine->status);
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($vaccine->status == 'pendente')
                                            <button class="btn btn-success" onclick="markAsAdministered({{ $vaccine->id }})">
                                                <i class="fas fa-syringe"></i>
                                            </button>
                                            <button class="btn btn-warning" onclick="rescheduleVaccine({{ $vaccine->id }})">
                                                <i class="fas fa-calendar"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('vaccines.show', $vaccine) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-info" onclick="printCard({{ $vaccine->id }})">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-light">
                    {{ $vaccines->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma vacinação registrada</h5>
                    <p class="text-muted">Comece registrando a primeira vacinação.</p>
                    <a href="{{ route('vaccines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Registrar Vacinação
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Painel Lateral -->
    <div class="col-md-4">
        <!-- Esquema Vacinal Recomendado -->
        <div class="card border-0 shadow-sm mb-4 vaccination-schedule">
            <div class="card-header bg-transparent border-0">
                <h6 class="text-white mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Esquema Vacinal Pré-natal
                </h6>
            </div>
            <div class="card-body">
                <div class="vaccination-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50">Tétano</span>
                        <span class="badge bg-white text-primary">3 doses</span>
                    </div>
                    <div class="vaccine-progress bg-white bg-opacity-25">
                        <div class="bg-white" style="height: 100%; width: 75%;"></div>
                    </div>
                    <small class="text-white-50">1ª: Imediata • 2ª: 4 semanas • 3ª: 6 meses</small>
                </div>
                
                <div class="vaccination-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50">Hepatite B</span>
                        <span class="badge bg-white text-primary">3 doses</span>
                    </div>
                    <div class="vaccine-progress bg-white bg-opacity-25">
                        <div class="bg-white" style="height: 100%; width: 60%;"></div>
                    </div>
                    <small class="text-white-50">Esquema padrão: 0, 1, 6 meses</small>
                </div>
                
                <div class="vaccination-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50">Influenza</span>
                        <span class="badge bg-white text-primary">Anual</span>
                    </div>
                    <div class="vaccine-progress bg-white bg-opacity-25">
                        <div class="bg-white" style="height: 100%; width: 90%;"></div>
                    </div>
                    <small class="text-white-50">Uma dose por ano</small>
                </div>
                
                <div class="vaccination-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50">COVID-19</span>
                        <span class="badge bg-white text-primary">2+ doses</span>
                    </div>
                    <div class="vaccine-progress bg-white bg-opacity-25">
                        <div class="bg-white" style="height: 100%; width: 85%;"></div>
                    </div>
                    <small class="text-white-50">Conforme protocolo vigente</small>
                </div>
            </div>
        </div>

        <!-- Alertas de Vacinação -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-bell me-2"></i>
                    Alertas de Vacinação
                </h6>
            </div>
            <div class="card-body">
                @if($stats['doses_vencidas'] > 0)
                <div class="alert alert-danger alert-sm">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>{{ $stats['doses_vencidas'] }}</strong> doses vencidas
                    <a href="{{ route('vaccines.pending-alert') }}?alert_type=vencidas" class="float-end btn btn-sm btn-outline-danger">
                        Ver
                    </a>
                </div>
                @endif
                
                @if($stats['proximas_7_dias'] > 0)
                <div class="alert alert-warning alert-sm">
                    <i class="fas fa-calendar-exclamation me-1"></i>
                    <strong>{{ $stats['proximas_7_dias'] }}</strong> doses próximas
                    <a href="{{ route('vaccines.pending-alert') }}?alert_type=proximas" class="float-end btn btn-sm btn-outline-warning">
                        Ver
                    </a>
                </div>
                @endif
                
                @if($stats['doses_vencidas'] == 0 && $stats['proximas_7_dias'] == 0)
                <div class="text-center py-3">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="text-success mb-0">Todas as vacinas em dia!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Estatísticas do Mês
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-0">92%</h4>
                        <small class="text-muted">Cobertura Tétano</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-warning mb-0">78%</h4>
                        <small class="text-muted">Cobertura Hepatite B</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0">85%</h4>
                        <small class="text-muted">Cobertura Influenza</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-primary mb-0">67%</h4>
                        <small class="text-muted">Cobertura COVID-19</small>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <button class="btn btn-outline-info btn-sm" onclick="showDetailedStats()">
                        <i class="fas fa-chart-pie me-1"></i> Ver Detalhes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Marcar como Administrada -->
<div class="modal fade" id="markAdministeredModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-syringe text-success me-2"></i>
                    Marcar Vacina como Administrada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markAdministeredForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="data_administracao" class="form-label required">Data da Administração</label>
                        <input type="date" class="form-control" id="data_administracao" name="data_administracao" 
                               value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lote" class="form-label">Lote da Vacina</label>
                            <input type="text" class="form-control" id="lote" name="lote" 
                                   placeholder="Ex: L2024001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fabricante" class="form-label">Fabricante</label>
                            <input type="text" class="form-control" id="fabricante" name="fabricante" 
                                   placeholder="Nome do fabricante">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="local_aplicacao" class="form-label required">Local de Aplicação</label>
                        <select class="form-select" id="local_aplicacao" name="local_aplicacao" required>
                            <option value="">Selecione...</option>
                            <option value="braco_esquerdo">Braço Esquerdo</option>
                            <option value="braco_direito">Braço Direito</option>
                            <option value="coxa_esquerda">Coxa Esquerda</option>
                            <option value="coxa_direita">Coxa Direita</option>
                            <option value="gluteo">Glúteo</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"
                                  placeholder="Observações sobre a aplicação..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reacao_adversa" class="form-label">Reação Adversa</label>
                        <textarea class="form-control" id="reacao_adversa" name="reacao_adversa" rows="2"
                                  placeholder="Descreva qualquer reação adversa observada..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Marcar como Administrada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Reagendar -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar text-warning me-2"></i>
                    Reagendar Vacinação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nova_data" class="form-label required">Nova Data</label>
                        <input type="date" class="form-control" id="nova_data" name="nova_data" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivo" class="form-label required">Motivo do Reagendamento</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required
                                  placeholder="Explique o motivo do reagendamento..."></textarea>
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
// Mark vaccine as administered
function markAsAdministered(vaccineId) {
    document.getElementById('markAdministeredForm').action = `/vaccines/${vaccineId}/mark-administered`;
    const modal = new bootstrap.Modal(document.getElementById('markAdministeredModal'));
    modal.show();
}

// Reschedule vaccine
function rescheduleVaccine(vaccineId) {
    document.getElementById('rescheduleForm').action = `/vaccines/${vaccineId}/reschedule`;
    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    modal.show();
}

// Print vaccination card
function printCard(vaccineId) {
    window.open(`/vaccines/${vaccineId}/print-card`, '_blank');
}

// Show coverage report
function showCoverageReport() {
    window.open('{{ route("vaccines.generate-report") }}', '_blank');
}

// Show detailed statistics
function showDetailedStats() {
    window.open('/vaccines/detailed-statistics', '_blank');
}

// Auto-refresh alerts every 5 minutes
setInterval(function() {
    if (document.visibilityState === 'visible') {
        // Update alert badges
        fetch('/vaccines/alert-count')
            .then(response => response.json())
            .then(data => {
                // Update alert counts in the interface
                document.querySelectorAll('.alert-count').forEach(element => {
                    element.textContent = data.total;
                });
            });
    }
}, 300000);

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush