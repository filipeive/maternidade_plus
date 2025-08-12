@extends('layouts.app')

@section('title', 'Laboratório')
@section('page-title', 'Gestão Laboratorial')
@section('title-icon', 'fa-flask')

@section('breadcrumbs')
<li class="breadcrumb-item active">Laboratório</li>
@endsection

@section('content')
<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-warning mb-1">{{ $stats['exames_pendentes'] }}</h3>
                        <p class="text-muted mb-0">Exames Pendentes</p>
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
                        <h3 class="text-success mb-1">{{ $stats['exames_realizados_hoje'] }}</h3>
                        <p class="text-muted mb-0">Realizados Hoje</p>
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
                        <h3 class="text-danger mb-1">{{ $stats['exames_atrasados'] }}</h3>
                        <p class="text-muted mb-0">Atrasados</p>
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
                        <h3 class="text-primary mb-1">{{ $stats['total_este_mes'] }}</h3>
                        <p class="text-muted mb-0">Total Este Mês</p>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Ações e Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="{{ route('laboratory.pending-queue') }}" class="btn btn-warning">
                        <i class="fas fa-clock me-1"></i> Fila de Pendentes
                    </a>
                    <a href="{{ route('laboratory.critical-alerts') }}" class="btn btn-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i> Alertas Críticos
                    </a>
                    <a href="{{ route('laboratory.workload') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-1"></i> Carga de Trabalho
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-1"></i> Filtros
                </button>
                <a href="{{ route('laboratory.export-results') }}" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i> Exportar
                </a>
            </div>
        </div>

        <!-- Filtros Expansíveis -->
        <div class="collapse mt-3" id="filtersCollapse">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="realizado" {{ request('status') == 'realizado' ? 'selected' : '' }}>Realizado</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de Exame</label>
                    <select name="tipo_exame" class="form-select">
                        <option value="">Todos</option>
                        <option value="hemograma">Hemograma</option>
                        <option value="glicemia_jejum">Glicemia de Jejum</option>
                        <option value="teste_hiv">Teste HIV</option>
                        <option value="teste_sifilis">Teste Sífilis</option>
                        <option value="urina_rotina">Urina de Rotina</option>
                        <option value="ultrassom">Ultrassom</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('laboratory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Exames -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Lista de Exames
                </h5>
                <div>
                    <select class="form-select form-select-sm" onchange="changeOrder(this.value)">
                        <option value="data_solicitacao">Data de Solicitação</option>
                        <option value="urgencia">Urgência</option>
                        <option value="tipo_exame">Tipo de Exame</option>
                        <option value="status">Status</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                @if($exams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Paciente</th>
                                <th>Tipo de Exame</th>
                                <th>Data Solicitação</th>
                                <th>Status</th>
                                <th>Prioridade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                            <tr class="{{ $exam->status == 'pendente' && $exam->data_solicitacao->diffInDays() > 7 ? 'table-warning' : '' }}">
                                <td>
                                    <input type="checkbox" class="form-check-input exam-checkbox" value="{{ $exam->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 35px; height: 35px;">
                                            <i class="fas fa-female"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $exam->consultation->patient->nome_completo }}</strong>
                                            <br>
                                            <small class="text-muted">BI: {{ $exam->consultation->patient->documento_bi }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $tipoClass = match($exam->tipo_exame) {
                                            'teste_hiv', 'teste_sifilis' => 'bg-danger',
                                            'hemograma', 'glicemia_jejum' => 'bg-warning',
                                            'ultrassom' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $tipoClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $exam->data_solicitacao->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $exam->data_solicitacao->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($exam->status) {
                                            'pendente' => 'bg-warning',
                                            'realizado' => 'bg-success',
                                            'cancelado' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                    @if($exam->status == 'realizado' && $exam->data_realizacao)
                                        <br>
                                        <small class="text-success">{{ $exam->data_realizacao->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($exam->tipo_exame, ['teste_hiv', 'teste_sifilis']))
                                        <span class="badge bg-danger">Alta</span>
                                    @elseif($exam->data_solicitacao->diffInDays() > 7)
                                        <span class="badge bg-warning">Urgente</span>
                                    @else
                                        <span class="badge bg-secondary">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($exam->status == 'pendente')
                                            <button class="btn btn-outline-success" onclick="processExam({{ $exam->id }})">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($exam->status == 'realizado')
                                            <button class="btn btn-outline-info" onclick="printResult({{ $exam->id }})">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Ações em Lote -->
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-success btn-sm" onclick="bulkProcess()" disabled id="bulkProcessBtn">
                            <i class="fas fa-play me-1"></i> Processar Selecionados
                        </button>
                    </div>
                    <div>
                        {{ $exams->links() }}
                    </div>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum exame encontrado</h5>
                    <p class="text-muted">Não há exames com os filtros aplicados.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Painel Lateral -->
    <div class="col-md-4">
        <!-- Exames Mais Solicitados -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Exames Mais Solicitados
                </h6>
            </div>
            <div class="card-body">
                @foreach($tiposExamePopulares as $tipo)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ ucfirst(str_replace('_', ' ', $tipo->tipo_exame)) }}</span>
                    <span class="badge bg-primary">{{ $tipo->total }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('laboratory.pending-queue') }}" class="btn btn-outline-warning">
                        <i class="fas fa-clock me-1"></i> Ver Fila Pendente
                    </a>
                    <a href="{{ route('laboratory.quality-control') }}" class="btn btn-outline-info">
                        <i class="fas fa-shield-alt me-1"></i> Controle de Qualidade
                    </a>
                    <button class="btn btn-outline-primary" onclick="generateDailyReport()">
                        <i class="fas fa-file-alt me-1"></i> Relatório do Dia
                    </button>
                </div>
            </div>
        </div>

        <!-- Indicadores de Performance -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Indicadores
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small>Taxa de Processamento</small>
                        <small>85%</small>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small>Tempo Médio</small>
                        <small>2.5 dias</small>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: 60%"></div>
                    </div>
                </div>
                
                <div class="mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small>Qualidade</small>
                        <small>98%</small>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: 98%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Processar Exame -->
<div class="modal fade" id="processExamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-flask text-success me-2"></i>
                    Processar Exame
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processExamForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="data_realizacao" class="form-label required">Data de Realização</label>
                        <input type="date" class="form-control" id="data_realizacao" name="data_realizacao" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resultado" class="form-label required">Resultado</label>
                        <textarea class="form-control" id="resultado" name="resultado" rows="4" required
                                  placeholder="Digite o resultado do exame..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"
                                  placeholder="Observações adicionais (opcional)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Processar Exame
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Checkbox management
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.exam-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkButton();
});

document.querySelectorAll('.exam-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkButton);
});

function updateBulkButton() {
    const selected = document.querySelectorAll('.exam-checkbox:checked').length;
    const bulkBtn = document.getElementById('bulkProcessBtn');
    bulkBtn.disabled = selected === 0;
    bulkBtn.textContent = selected > 0 ? `Processar Selecionados (${selected})` : 'Processar Selecionados';
}

// Process single exam
function processExam(examId) {
    const form = document.getElementById('processExamForm');
    form.action = `/laboratory/process/${examId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('processExamModal'));
    modal.show();
}

// Bulk processing
function bulkProcess() {
    const selected = Array.from(document.querySelectorAll('.exam-checkbox:checked')).map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Selecione pelo menos um exame para processar.');
        return;
    }
    
    if (confirm(`Processar ${selected.length} exames selecionados?`)) {
        // Implementation for bulk processing
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("laboratory.bulk-process") }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add exam IDs
        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'exam_ids';
        idsInput.value = JSON.stringify(selected);
        form.appendChild(idsInput);
        
        // Add date
        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'data_realizacao';
        dateInput.value = new Date().toISOString().split('T')[0];
        form.appendChild(dateInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Change order
function changeOrder(orderBy) {
    const url = new URL(window.location);
    url.searchParams.set('order_by', orderBy);
    window.location = url;
}

// Generate daily report
function generateDailyReport() {
    const date = new Date().toISOString().split('T')[0];
    window.open(`{{ route('laboratory.daily-report') }}?date=${date}`, '_blank');
}

// Print result
function printResult(examId) {
    window.open(`/laboratory/print-result/${examId}`, '_blank');
}

// Auto-refresh every 5 minutes for real-time updates
setInterval(function() {
    if (document.visibilityState === 'visible') {
        window.location.reload();
    }
}, 300000);
</script>
@endpush