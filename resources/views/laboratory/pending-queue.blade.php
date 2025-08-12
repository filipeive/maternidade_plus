@extends('layouts.app')

@section('title', 'Fila de Exames Pendentes')
@section('page-title', 'Fila de Processamento - Laboratório')
@section('title-icon', 'fa-clock')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('laboratory.index') }}">Laboratório</a></li>
<li class="breadcrumb-item active">Fila Pendente</li>
@endsection

@push('styles')
<style>
.priority-high { border-left: 4px solid #dc3545; }
.priority-urgent { border-left: 4px solid #fd7e14; }
.priority-normal { border-left: 4px solid #6c757d; }

.exam-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.exam-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.timer-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.processing-queue {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
</style>
@endpush

@section('content')
<!-- Header da Fila -->
<div class="card border-0 shadow-sm mb-4 processing-queue">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="text-white mb-2">
                    <i class="fas fa-flask me-2"></i>
                    Fila de Processamento Laboratorial
                </h4>
                <p class="text-white-50 mb-0">
                    Gerencie e processe os exames pendentes por ordem de prioridade
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <div class="text-center">
                        <h3 class="text-white mb-0">{{ $examsPendentes->total() }}</h3>
                        <small class="text-white-50">Total na Fila</small>
                    </div>
                    <div class="text-center">
                        <h3 class="text-white mb-0">{{ $examsPendentes->where('data_solicitacao', '<', now()->subDays(7))->count() }}</h3>
                        <small class="text-white-50">Atrasados</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Controles da Fila -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <button class="btn btn-success" onclick="processNext()">
                        <i class="fas fa-play me-1"></i> Processar Próximo
                    </button>
                    <button class="btn btn-warning" onclick="selectByPriority('high')">
                        <i class="fas fa-exclamation-triangle me-1"></i> Selecionar Alta Prioridade
                    </button>
                    <button class="btn btn-info" onclick="refreshQueue()">
                        <i class="fas fa-sync me-1"></i> Atualizar Fila
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <div class="input-group" style="max-width: 300px; margin-left: auto;">
                    <input type="text" class="form-control" placeholder="Buscar por paciente..." id="searchPatient">
                    <button class="btn btn-outline-secondary" onclick="searchInQueue()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fila de Exames -->
<div class="row">
    @if($examsPendentes->count() > 0)
        @foreach($examsPendentes as $exam)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card exam-card border-0 shadow-sm h-100 
                {{ in_array($exam->tipo_exame, ['teste_hiv', 'teste_sifilis']) ? 'priority-high' : 
                   ($exam->data_solicitacao->diffInDays() > 7 ? 'priority-urgent' : 'priority-normal') }}"
                 data-exam-id="{{ $exam->id }}"
                 onclick="selectExam({{ $exam->id }})">
                
                <!-- Header do Card -->
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="form-check-input me-2 exam-checkbox" 
                               value="{{ $exam->id }}" onclick="event.stopPropagation()">
                        
                        <!-- Priority Badge -->
                        @if(in_array($exam->tipo_exame, ['teste_hiv', 'teste_sifilis']))
                            <span class="badge bg-danger">ALTA</span>
                        @elseif($exam->data_solicitacao->diffInDays() > 7)
                            <span class="badge bg-warning timer-badge">URGENTE</span>
                        @else
                            <span class="badge bg-secondary">NORMAL</span>
                        @endif
                    </div>
                    
                    <!-- Time Badge -->
                    <span class="badge bg-light text-dark">
                        {{ $exam->data_solicitacao->diffForHumans() }}
                    </span>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Patient Info -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 45px; height: 45px;">
                            <i class="fas fa-female"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $exam->consultation->patient->nome_completo }}</h6>
                            <small class="text-muted">BI: {{ $exam->consultation->patient->documento_bi }}</small>
                        </div>
                    </div>

                    <!-- Exam Info -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Tipo de Exame:</strong>
                            @php
                                $tipoClass = match($exam->tipo_exame) {
                                    'teste_hiv', 'teste_sifilis' => 'bg-danger',
                                    'hemograma', 'glicemia_jejum' => 'bg-warning',
                                    'ultrassom' => 'bg-info',
                                    default => 'bg-primary'
                                };
                            @endphp
                            <span class="badge {{ $tipoClass }}">
                                {{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Solicitado em:</small>
                            <small>{{ $exam->data_solicitacao->format('d/m/Y H:i') }}</small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Médico solicitante:</small>
                            <small>{{ $exam->consultation->user->name }}</small>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    @php
                        $daysWaiting = $exam->data_solicitacao->diffInDays();
                        $progressClass = $daysWaiting <= 2 ? 'bg-success' : ($daysWaiting <= 5 ? 'bg-warning' : 'bg-danger');
                        $progressWidth = min(100, ($daysWaiting / 7) * 100);
                    @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Tempo na fila:</small>
                            <small class="fw-bold {{ $daysWaiting > 7 ? 'text-danger' : 'text-success' }}">
                                {{ $daysWaiting }} dias
                            </small>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $progressWidth }}%"></div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    @if($exam->observacoes)
                    <div class="mb-2">
                        <small class="text-muted">Observações:</small>
                        <p class="small mb-0">{{ Str::limit($exam->observacoes, 80) }}</p>
                    </div>
                    @endif
                </div>

                <!-- Card Footer -->
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-success btn-sm" onclick="event.stopPropagation(); processExam({{ $exam->id }})">
                            <i class="fas fa-play me-1"></i> Processar
                        </button>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="event.stopPropagation(); viewDetails({{ $exam->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="event.stopPropagation(); postponeExam({{ $exam->id }})">
                                <i class="fas fa-clock"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $examsPendentes->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                <h3 class="text-success mb-2">Fila Vazia!</h3>
                <p class="text-muted mb-4">Parabéns! Todos os exames foram processados.</p>
                <a href="{{ route('laboratory.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar ao Laboratório
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Floating Action Panel -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div class="card border-0 shadow" id="actionPanel" style="display: none;">
        <div class="card-body">
            <h6 class="mb-3">
                <i class="fas fa-tasks me-1"></i>
                Ações Selecionadas (<span id="selectedCount">0</span>)
            </h6>
            <div class="d-grid gap-2">
                <button class="btn btn-success btn-sm" onclick="bulkProcess()">
                    <i class="fas fa-play me-1"></i> Processar Selecionados
                </button>
                <button class="btn btn-warning btn-sm" onclick="bulkPostpone()">
                    <i class="fas fa-clock me-1"></i> Adiar Selecionados
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i> Limpar Seleção
                </button>
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
                    <!-- Patient Info Display -->
                    <div class="alert alert-info" id="patientInfo">
                        <!-- Will be populated by JavaScript -->
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_realizacao" class="form-label required">Data de Realização</label>
                            <input type="date" class="form-control" id="data_realizacao" name="data_realizacao" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tecnico_responsavel" class="form-label">Técnico Responsável</label>
                            <input type="text" class="form-control" id="tecnico_responsavel" name="tecnico_responsavel" 
                                   value="{{ auth()->user()->name }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resultado" class="form-label required">Resultado</label>
                        <textarea class="form-control" id="resultado" name="resultado" rows="4" required
                                  placeholder="Digite o resultado do exame de forma clara e objetiva..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="valores_referencia" class="form-label">Valores de Referência</label>
                        <textarea class="form-control" id="valores_referencia" name="valores_referencia" rows="2"
                                  placeholder="Valores de referência aplicáveis (opcional)..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações Técnicas</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"
                                  placeholder="Observações sobre o processamento, qualidade da amostra, etc..."></textarea>
                    </div>
                    
                    <!-- Critical Results Alert -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="resultado_critico" name="resultado_critico">
                        <label class="form-check-label" for="resultado_critico">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            Este é um resultado crítico que requer notificação imediata
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Processar e Finalizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedExams = new Set();

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateActionPanel();
    
    // Setup checkbox listeners
    document.querySelectorAll('.exam-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedExams.add(this.value);
            } else {
                selectedExams.delete(this.value);
            }
            updateActionPanel();
        });
    });
});

// Update action panel visibility and count
function updateActionPanel() {
    const panel = document.getElementById('actionPanel');
    const count = document.getElementById('selectedCount');
    
    count.textContent = selectedExams.size;
    panel.style.display = selectedExams.size > 0 ? 'block' : 'none';
}

// Select exam card
function selectExam(examId) {
    const checkbox = document.querySelector(`input[value="${examId}"]`);
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        selectedExams.add(examId.toString());
    } else {
        selectedExams.delete(examId.toString());
    }
    updateActionPanel();
}

// Process single exam
function processExam(examId) {
    // Find exam data from the card
    const examCard = document.querySelector(`[data-exam-id="${examId}"]`);
    const patientName = examCard.querySelector('h6').textContent;
    const examType = examCard.querySelector('.badge').textContent;
    
    // Update patient info in modal
    document.getElementById('patientInfo').innerHTML = `
        <strong>Paciente:</strong> ${patientName}<br>
        <strong>Exame:</strong> ${examType}
    `;
    
    // Set form action
    document.getElementById('processExamForm').action = `/laboratory/process/${examId}`;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('processExamModal'));
    modal.show();
}

// Process next exam (highest priority)
function processNext() {
    const firstExam = document.querySelector('.priority-high .btn-success, .priority-urgent .btn-success, .priority-normal .btn-success');
    if (firstExam) {
        firstExam.click();
    } else {
        alert('Não há exames pendentes para processar.');
    }
}

// Select by priority
function selectByPriority(priority) {
    // Clear current selection
    selectedExams.clear();
    document.querySelectorAll('.exam-checkbox').forEach(cb => cb.checked = false);
    
    // Select based on priority
    let selector;
    switch(priority) {
        case 'high':
            selector = '.priority-high .exam-checkbox';
            break;
        case 'urgent':
            selector = '.priority-urgent .exam-checkbox';
            break;
        default:
            selector = '.exam-checkbox';
    }
    
    document.querySelectorAll(selector).forEach(checkbox => {
        checkbox.checked = true;
        selectedExams.add(checkbox.value);
    });
    
    updateActionPanel();
}

// Clear selection
function clearSelection() {
    selectedExams.clear();
    document.querySelectorAll('.exam-checkbox').forEach(cb => cb.checked = false);
    updateActionPanel();
}

// Bulk process
function bulkProcess() {
    if (selectedExams.size === 0) return;
    
    if (confirm(`Processar ${selectedExams.size} exames selecionados?`)) {
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
        idsInput.value = JSON.stringify(Array.from(selectedExams));
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

// Search in queue
function searchInQueue() {
    const searchTerm = document.getElementById('searchPatient').value.toLowerCase();
    const examCards = document.querySelectorAll('.exam-card');
    
    examCards.forEach(card => {
        const patientName = card.querySelector('h6').textContent.toLowerCase();
        const patientBI = card.querySelector('small').textContent.toLowerCase();
        
        if (patientName.includes(searchTerm) || patientBI.includes(searchTerm)) {
            card.closest('.col-md-6').style.display = 'block';
        } else {
            card.closest('.col-md-6').style.display = 'none';
        }
    });
}

// Refresh queue
function refreshQueue() {
    window.location.reload();
}

// View details
function viewDetails(examId) {
    window.open(`/exams/${examId}`, '_blank');
}

// Postpone exam
function postponeExam(examId) {
    const reason = prompt('Motivo do adiamento:');
    if (reason) {
        // Implementation for postponing exam
        fetch(`/laboratory/postpone/${examId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao adiar o exame.');
            }
        });
    }
}

// Auto-refresh every 2 minutes
setInterval(function() {
    if (document.visibilityState === 'visible' && selectedExams.size === 0) {
        window.location.reload();
    }
}, 120000);
</script>
@endpush