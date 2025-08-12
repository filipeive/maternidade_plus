@extends('layouts.app')

@section('title', 'Agenda Diária de Visitas')
@section('page-title', 'Agenda Diária - ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))
@section('title-icon', 'fa-calendar-day')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a></li>
<li class="breadcrumb-item active">Agenda Diária</li>
@endsection

@section('content')
<!-- Header com Controles de Data -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('home_visits.daily-schedule') }}" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Data:</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                    
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <a href="{{ route('home_visits.daily-schedule', ['date' => now()->subDay()->format('Y-m-d')]) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i> Ontem
                            </a>
                            <a href="{{ route('home_visits.daily-schedule', ['date' => now()->format('Y-m-d')]) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-calendar-day"></i> Hoje
                            </a>
                            <a href="{{ route('home_visits.daily-schedule', ['date' => now()->addDay()->format('Y-m-d')]) }}" 
                               class="btn btn-outline-secondary">
                                Amanhã <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <a href="{{ route('home_visits.route-planning', ['date' => $date]) }}" 
                           class="btn btn-info">
                            <i class="fas fa-route"></i> Rota
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Resumo do Dia -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-chart-pie me-2"></i>
                    Resumo do Dia
                </h6>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-warning mb-0">{{ $stats['total_agendadas'] }}</h4>
                            <small class="text-muted">Agendadas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $stats['realizadas'] }}</h4>
                        <small class="text-muted">Realizadas</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-info mb-0">{{ $stats['pendentes'] }}</h5>
                            <small class="text-muted">Pendentes</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-primary mb-0">{{ floor($stats['tempo_estimado'] / 60) }}h{{ $stats['tempo_estimado'] % 60 }}m</h5>
                        <small class="text-muted">Tempo Est.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timeline das Visitas -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-clock me-2"></i>
            Cronograma de Visitas - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
        </h5>
        
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="view" id="timeline" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="timeline">
                <i class="fas fa-clock"></i> Timeline
            </label>

            <input type="radio" class="btn-check" name="view" id="list" autocomplete="off">
            <label class="btn btn-outline-primary" for="list">
                <i class="fas fa-list"></i> Lista
            </label>
        </div>
    </div>
    
    <div class="card-body">
        @if($visits->count() > 0)
            <!-- Vista Timeline -->
            <div id="timeline-view">
                <div class="timeline">
                    @foreach($visits->sortBy('data_visita') as $visit)
                    <div class="timeline-item {{ $visit->status == 'realizada' ? 'completed' : ($visit->data_visita->isPast() ? 'overdue' : 'pending') }}">
                        <div class="timeline-marker">
                            @if($visit->status == 'realizada')
                                <i class="fas fa-check text-success"></i>
                            @elseif($visit->data_visita->isPast() && $visit->status == 'agendada')
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                            @else
                                <i class="fas fa-clock text-warning"></i>
                            @endif
                        </div>
                        
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <h5 class="mb-0 text-primary">{{ $visit->data_visita->format('H:i') }}</h5>
                                                <small class="text-muted">
                                                    @if($visit->data_visita->isPast() && $visit->status == 'agendada')
                                                        Atrasada
                                                    @else
                                                        {{ $visit->data_visita->diffForHumans() }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar bg-pink text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                     style="width: 40px; height: 40px; background-color: #e91e63;">
                                                    <i class="fas fa-female"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $visit->patient->nome_completo }}</h6>
                                                    <small class="text-muted">{{ $visit->patient->documento_bi }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
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
                                                <span class="badge {{ $tipoClass }} me-2">
                                                    {{ ucfirst(str_replace('_', ' ', $visit->tipo_visita)) }}
                                                </span>
                                                
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
                                            </div>
                                            
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ Str::limit($visit->endereco_visita, 60) }}
                                            </p>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <small class="text-muted">Contato:</small><br>
                                                <span class="badge bg-light text-dark">
                                                    {{ $visit->patient->contacto ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="d-grid gap-1">
                                                <a href="{{ route('home_visits.show', $visit) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($visit->status == 'agendada')
                                                    <button class="btn btn-sm btn-success complete-visit" 
                                                            data-visit-id="{{ $visit->id }}"
                                                            data-patient-name="{{ $visit->patient->nome_completo }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                
                                                <a href="https://maps.google.com/?q={{ urlencode($visit->endereco_visita) }}" 
                                                   target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-directions"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($visit->motivo_visita)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <strong>Motivo:</strong> {{ $visit->motivo_visita }}
                                            </small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Vista Lista -->
            <div id="list-view" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Hora</th>
                                <th>Gestante</th>
                                <th>Tipo</th>
                                <th>Endereço</th>
                                <th>Contato</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits->sortBy('data_visita') as $visit)
                            <tr class="{{ $visit->data_visita->isPast() && $visit->status == 'agendada' ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $visit->data_visita->format('H:i') }}</strong>
                                    @if($visit->data_visita->isPast() && $visit->status == 'agendada')
                                        <br><small class="text-danger">Atrasada</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $visit->patient->nome_completo }}</strong><br>
                                    <small class="text-muted">{{ $visit->patient->documento_bi }}</small>
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
                                    <small>{{ Str::limit($visit->endereco_visita, 40) }}</small>
                                    <br>
                                    <a href="https://maps.google.com/?q={{ urlencode($visit->endereco_visita) }}" 
                                       target="_blank" class="btn btn-xs btn-outline-info">
                                        <i class="fas fa-directions"></i> Direções
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $visit->patient->contacto ?? 'N/A' }}
                                    </span>
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
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('home_visits.show', $visit) }}" 
                                           class="btn btn-outline-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($visit->status == 'agendada')
                                            <button class="btn btn-success complete-visit" 
                                                    data-visit-id="{{ $visit->id }}"
                                                    data-patient-name="{{ $visit->patient->nome_completo }}"
                                                    title="Completar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-day fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma visita agendada para este dia</h5>
                <p class="text-muted mb-4">
                    Não há visitas domiciliárias programadas para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.
                </p>
                <a href="{{ route('home_visits.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Agendar Nova Visita
                </a>
            </div>
        @endif
    </div>
    
    @if($visits->count() > 0)
    <div class="card-footer bg-light">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Total de {{ $visits->count() }} visita(s) programada(s) para este dia
                </small>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('home_visits.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nova Visita
                    </a>
                    <a href="{{ route('home_visits.weekly-schedule') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar-week me-1"></i> Visão Semanal
                    </a>
                    <button class="btn btn-outline-info" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal de Completar Visita Rápida -->
<div class="modal fade" id="quickCompleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Completar Visita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickCompleteForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Completando visita para: <strong id="patientName"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_observacoes_ambiente" class="form-label required">Observações do Ambiente</label>
                        <textarea class="form-control" id="quick_observacoes_ambiente" name="observacoes_ambiente" 
                                  rows="2" required placeholder="Breve descrição do ambiente..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quick_condicoes_higiene" class="form-label required">Condições de Higiene</label>
                            <select class="form-select" id="quick_condicoes_higiene" name="condicoes_higiene" required>
                                <option value="">Selecione...</option>
                                <option value="bom">Bom</option>
                                <option value="regular">Regular</option>
                                <option value="ruim">Ruim</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="quick_apoio_familiar" class="form-label required">Apoio Familiar</label>
                            <select class="form-select" id="quick_apoio_familiar" name="apoio_familiar" required>
                                <option value="">Selecione...</option>
                                <option value="adequado">Adequado</option>
                                <option value="parcial">Parcial</option>
                                <option value="inadequado">Inadequado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_orientacoes_dadas" class="form-label required">Orientações Dadas</label>
                        <textarea class="form-control" id="quick_orientacoes_dadas" name="orientacoes_dadas" 
                                  rows="2" required placeholder="Principais orientações..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="quick_necessita_referencia" name="necessita_referencia">
                        <label class="form-check-label" for="quick_necessita_referencia">
                            Necessita Referência Médica
                        </label>
                    </div>
                    
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Para mais detalhes, acesse a visita completa após salvar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Completar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 40px;
    width: 4px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 100px;
}

.timeline-marker {
    position: absolute;
    left: 25px;
    top: 0;
    width: 30px;
    height: 30px;
    background: #fff;
    border: 4px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.timeline-item.completed .timeline-marker {
    border-color: #28a745;
    background: #28a745;
}

.timeline-item.overdue .timeline-marker {
    border-color: #dc3545;
    background: #dc3545;
}

.timeline-item.pending .timeline-marker {
    border-color: #ffc107;
    background: #ffc107;
}

.timeline-content {
    position: relative;
}

.timeline-content:before {
    content: '';
    position: absolute;
    left: -15px;
    top: 15px;
    border: 8px solid transparent;
    border-right-color: #dee2e6;
}

@media print {
    .card-header, .card-footer, .btn, .btn-group {
        display: none !important;
    }
    
    .timeline-item {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}

@media (max-width: 768px) {
    .timeline:before {
        left: 15px;
    }
    
    .timeline-item {
        padding-left: 50px;
    }
    
    .timeline-marker {
        left: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between timeline and list view
    const timelineRadio = document.getElementById('timeline');
    const listRadio = document.getElementById('list');
    const timelineView = document.getElementById('timeline-view');
    const listView = document.getElementById('list-view');
    
    timelineRadio.addEventListener('change', function() {
        if (this.checked) {
            timelineView.style.display = 'block';
            listView.style.display = 'none';
        }
    });
    
    listRadio.addEventListener('change', function() {
        if (this.checked) {
            timelineView.style.display = 'none';
            listView.style.display = 'block';
        }
    });
    
    // Quick complete visit functionality
    document.querySelectorAll('.complete-visit').forEach(button => {
        button.addEventListener('click', function() {
            const visitId = this.dataset.visitId;
            const patientName = this.dataset.patientName;
            
            document.getElementById('patientName').textContent = patientName;
            document.getElementById('quickCompleteForm').action = `/home-visits/${visitId}/complete`;
            
            const modal = new bootstrap.Modal(document.getElementById('quickCompleteModal'));
            modal.show();
        });
    });
    
    // Auto-refresh every 5 minutes
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 300000); // 5 minutes
    
    // Notification for overdue visits
    const overdueVisits = document.querySelectorAll('.timeline-item.overdue');
    if (overdueVisits.length > 0 && 'Notification' in window) {
        if (Notification.permission === 'granted') {
            new Notification(`Você tem ${overdueVisits.length} visita(s) atrasada(s)`, {
                body: 'Verifique sua agenda e atualize o status das visitas.',
                icon: '/images/logo.png'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    new Notification(`Você tem ${overdueVisits.length} visita(s) atrasada(s)`, {
                        body: 'Verifique sua agenda e atualize o status das visitas.',
                        icon: '/images/logo.png'
                    });
                }
            });
        }
    }
    
    // Add current time indicator
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();
    const currentTimeString = `${currentHour.toString().padStart(2, '0')}:${currentMinute.toString().padStart(2, '0')}`;
    
    // Find the closest time slot and add indicator
    const timeSlots = document.querySelectorAll('.timeline-item');
    timeSlots.forEach(item => {
        const timeElement = item.querySelector('h5');
        if (timeElement) {
            const visitTime = timeElement.textContent.trim();
            if (visitTime === currentTimeString) {
                item.classList.add('current-time');
                const indicator = document.createElement('div');
                indicator.className = 'badge bg-danger position-absolute';
                indicator.style.cssText = 'top: -5px; right: -5px; z-index: 3;';
                indicator.innerHTML = '<i class="fas fa-clock"></i> AGORA';
                item.querySelector('.timeline-marker').appendChild(indicator);
            }
        }
    });
});
</script>
@endpush