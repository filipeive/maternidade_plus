@extends('layouts.app')

@section('title', 'Planejamento de Rota')
@section('page-title', 'Planejamento de Rota - ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))
@section('title-icon', 'fa-route')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a></li>
<li class="breadcrumb-item"><a href="{{ route('home_visits.daily-schedule') }}">Agenda Diária</a></li>
<li class="breadcrumb-item active">Planejamento de Rota</li>
@endsection

@section('content')
<!-- Header com Controles -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-calendar-day me-2"></i>
                            Data Selecionada
                        </h6>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                    </div>
                    
                    <div class="col-md-4">
                        <h6 class="text-success mb-2">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            Total de Visitas
                        </h6>
                        <p class="mb-0">{{ $visits->count() }} visita(s)</p>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-primary" id="optimizeRoute">
                                <i class="fas fa-route me-2"></i>
                                Otimizar Rota
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Resumo da Rota -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Resumo da Rota
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h5 class="text-primary mb-0">{{ $optimizedRoute['total_distance'] ?? '0 km' }}</h5>
                        <small class="text-muted">Distância</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-warning mb-0">{{ $optimizedRoute['estimated_time'] ?? '0h' }}</h5>
                        <small class="text-muted">Tempo Est.</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h6 class="text-success mb-0">{{ $optimizedRoute['fuel_cost'] ?? 'MZN 0' }}</h6>
                    <small class="text-muted">Custo Combustível</small>
                </div>
            </div>
        </div>
    </div>
</div>

@if($visits->count() > 0)
<div class="row">
    <div class="col-md-8">
        <!-- Mapa da Rota -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-map me-2"></i>
                    Mapa da Rota
                </h5>
                
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary" id="showTraffic">
                        <i class="fas fa-traffic-light"></i> Trânsito
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="showSatellite">
                        <i class="fas fa-satellite"></i> Satélite
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="fullscreenMap">
                        <i class="fas fa-expand"></i> Tela Cheia
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="routeMap" style="height: 500px; width: 100%;">
                    <!-- Placeholder do Mapa -->
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <div class="text-center">
                            <i class="fas fa-map fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Mapa da Rota</h5>
                            <p class="text-muted">
                                O mapa será carregado aqui mostrando a rota otimizada<br>
                                entre todas as visitas programadas.
                            </p>
                            <button class="btn btn-primary" onclick="initializeMap()">
                                <i class="fas fa-play me-1"></i> Carregar Mapa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Instruções de Navegação -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-directions me-2"></i>
                    Instruções de Navegação
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush" id="navigationInstructions">
                    @foreach($optimizedRoute['visits'] ?? $visits as $index => $visit)
                    <div class="list-group-item d-flex align-items-center">
                        <div class="me-3">
                            <div class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 30px; height: 30px;">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $visit->patient->nome_completo }}</h6>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $visit->data_visita->format('H:i') }}
                                        <span class="ms-3">
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
                                        </span>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $visit->endereco_visita }}
                                    </small>
                                </div>
                                
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="tel:{{ $visit->patient->contacto }}" 
                                       class="btn btn-outline-success" title="Ligar">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                    <a href="https://maps.google.com/?q={{ urlencode($visit->endereco_visita) }}" 
                                       target="_blank" class="btn btn-outline-primary" title="Navegação">
                                        <i class="fas fa-directions"></i>
                                    </a>
                                    <a href="{{ route('home_visits.show', $visit) }}" 
                                       class="btn btn-outline-info" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            
                            @if($visit->motivo_visita)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <strong>Motivo:</strong> {{ Str::limit($visit->motivo_visita, 80) }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Controles da Rota -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Controles da Rota
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" id="startNavigation">
                        <i class="fas fa-play me-1"></i> Iniciar Navegação
                    </button>
                    
                    <button class="btn btn-outline-info" id="shareRoute">
                        <i class="fas fa-share me-1"></i> Compartilhar Rota
                    </button>
                    
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimir Rota
                    </button>
                    
                    <a href="{{ route('home_visits.daily-schedule', ['date' => $date]) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Voltar à Agenda
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Checklist de Preparação -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-check-square me-2"></i>
                    Checklist de Preparação
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item p-0 border-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_materials">
                            <label class="form-check-label" for="check_materials">
                                Preparar materiais educativos
                            </label>
                        </div>
                    </div>
                    
                    <div class="list-group-item p-0 border-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_equipment">
                            <label class="form-check-label" for="check_equipment">
                                Verificar equipamentos médicos
                            </label>
                        </div>
                    </div>
                    
                    <div class="list-group-item p-0 border-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_forms">
                            <label class="form-check-label" for="check_forms">
                                Levar formulários de registro
                            </label>
                        </div>
                    </div>
                    
                    <div class="list-group-item p-0 border-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_contacts">
                            <label class="form-check-label" for="check_contacts">
                                Confirmar contatos das gestantes
                            </label>
                        </div>
                    </div>
                    
                    <div class="list-group-item p-0 border-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_gps">
                            <label class="form-check-label" for="check_gps">
                                Testar GPS do dispositivo móvel
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="progress mt-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="checklistProgress">
                        0%
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informações Úteis -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle text-muted me-2"></i>
                    Informações Úteis
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">Contatos de Emergência</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><strong>SAMU:</strong> 190</small></li>
                        <li><small><strong>Hospital Central:</strong> (21) 123-4567</small></li>
                        <li><small><strong>Coordenação:</strong> (21) 987-6543</small></li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-primary">Tempo Médio por Visita</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><strong>Rotina:</strong> 30-45 min</small></li>
                        <li><small><strong>Pós-parto:</strong> 45-60 min</small></li>
                        <li><small><strong>Alto risco:</strong> 60-90 min</small></li>
                        <li><small><strong>Faltosa:</strong> 20-30 min</small></li>
                    </ul>
                </div>
                
                <div>
                    <h6 class="text-primary">Dicas de Navegação</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• Use sempre GPS atualizado</small></li>
                        <li><small>• Confirme endereços por telefone</small></li>
                        <li><small>• Considere horário de trânsito</small></li>
                        <li><small>• Mantenha combustível suficiente</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="text-center py-5">
    <i class="fas fa-route fa-4x text-muted mb-3"></i>
    <h5 class="text-muted">Nenhuma visita para otimizar rota</h5>
    <p class="text-muted mb-4">
        Não há visitas agendadas para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.<br>
        Agende visitas para poder planejar sua rota.
    </p>
    <div class="btn-group" role="group">
        <a href="{{ route('home_visits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Agendar Visita
        </a>
        <a href="{{ route('home_visits.daily-schedule') }}" class="btn btn-outline-secondary">
            <i class="fas fa-calendar-day me-1"></i> Ver Agenda
        </a>
    </div>
</div>
@endif

<!-- Modal de Compartilhamento -->
<div class="modal fade" id="shareRouteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-share text-primary me-2"></i>
                    Compartilhar Rota
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Compartilhe a rota planejada com outros membros da equipe:</p>
                
                <div class="mb-3">
                    <label for="shareEmail" class="form-label">Email</label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="shareEmail" placeholder="email@exemplo.com">
                        <button class="btn btn-outline-primary" type="button" id="sendEmail">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="shareWhatsApp" class="form-label">WhatsApp</label>
                    <div class="input-group">
                        <input type="tel" class="form-control" id="shareWhatsApp" placeholder="+258 84 123 4567">
                        <button class="btn btn-outline-success" type="button" id="sendWhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="routeLink" class="form-label">Link da Rota</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="routeLink" 
                               value="{{ url()->current() }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyLink">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    .card-header, .btn, .btn-group {
        display: none !important;
    }
    
    #routeMap {
        height: 300px !important;
    }
    
    .list-group-item {
        border: 1px solid #dee2e6 !important;
        page-break-inside: avoid;
    }
}

.progress {
    height: 8px;
}

.form-check-input:checked ~ .form-check-label {
    text-decoration: line-through;
    color: #6c757d;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

#routeMap {
    border-radius: 0.375rem;
}

.badge.rounded-circle {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checklist functionality
    const checkboxes = document.querySelectorAll('.form-check-input');
    const progressBar = document.getElementById('checklistProgress');
    
    function updateProgress() {
        const total = checkboxes.length;
        const checked = document.querySelectorAll('.form-check-input:checked').length;
        const percentage = Math.round((checked / total) * 100);
        
        progressBar.style.width = percentage + '%';
        progressBar.textContent = percentage + '%';
        
        if (percentage === 100) {
            progressBar.classList.remove('bg-success');
            progressBar.classList.add('bg-primary');
        } else {
            progressBar.classList.remove('bg-primary');
            progressBar.classList.add('bg-success');
        }
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateProgress);
    });
    
    // Optimize route functionality
    document.getElementById('optimizeRoute').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Otimizando...';
        this.disabled = true;
        
        // Simulate route optimization
        setTimeout(() => {
            // Shuffle the navigation instructions to simulate optimization
            const container = document.getElementById('navigationInstructions');
            const items = Array.from(container.children);
            
            // Simple shuffle algorithm
            for (let i = items.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [items[i], items[j]] = [items[j], items[i]];
            }
            
            // Update the numbering
            items.forEach((item, index) => {
                const badge = item.querySelector('.badge');
                badge.textContent = index + 1;
                container.appendChild(item);
            });
            
            this.innerHTML = '<i class="fas fa-route me-2"></i>Rota Otimizada!';
            this.classList.remove('btn-primary');
            this.classList.add('btn-success');
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-route me-2"></i>Otimizar Rota';
                this.classList.remove('btn-success');
                this.classList.add('btn-primary');
                this.disabled = false;
            }, 2000);
        }, 2000);
    });
    
    // Share route functionality
    document.getElementById('shareRoute').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('shareRouteModal'));
        modal.show();
    });
    
    // Copy link functionality
    document.getElementById('copyLink').addEventListener('click', function() {
        const linkInput = document.getElementById('routeLink');
        linkInput.select();
        document.execCommand('copy');
        
        this.innerHTML = '<i class="fas fa-check"></i>';
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-success');
        
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-copy"></i>';
            this.classList.remove('btn-success');
            this.classList.add('btn-outline-secondary');
        }, 2000);
    });
    
    // WhatsApp share functionality
    document.getElementById('sendWhatsApp').addEventListener('click', function() {
        const phone = document.getElementById('shareWhatsApp').value;
        const message = `Rota de visitas domiciliárias para {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}: {{ url()->current() }}`;
        
        if (phone) {
            const whatsappUrl = `https://wa.me/${phone.replace(/\D/g, '')}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        } else {
            alert('Por favor, insira um número de WhatsApp.');
        }
    });
    
    // Email share functionality
    document.getElementById('sendEmail').addEventListener('click', function() {
        const email = document.getElementById('shareEmail').value;
        const subject = `Rota de Visitas Domiciliárias - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}`;
        const body = `Confira a rota planejada para as visitas domiciliárias: {{ url()->current() }}`;
        
        if (email) {
            const mailtoUrl = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = mailtoUrl;
        } else {
            alert('Por favor, insira um endereço de email.');
        }
    });
    
    // Start navigation functionality
    document.getElementById('startNavigation').addEventListener('click', function() {
        const firstVisit = document.querySelector('#navigationInstructions .list-group-item');
        if (firstVisit) {
            const address = firstVisit.querySelector('small').textContent.replace('📍 ', '');
            const googleMapsUrl = `https://maps.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}`;
            window.open(googleMapsUrl, '_blank');
        }
    });
    
    // Initialize map placeholder functionality
    window.initializeMap = function() {
        const mapContainer = document.getElementById('routeMap');
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 bg-primary text-white">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                    <h5>Mapa Carregado</h5>
                    <p class="mb-0">
                        Aqui seria exibido o mapa interativo com a rota otimizada<br>
                        entre todos os pontos de visita.
                    </p>
                </div>
            </div>
        `;
    };
    
    // Auto-save checklist state
    checkboxes.forEach((checkbox, index) => {
        const savedState = localStorage.getItem(`checklist_${index}`);
        if (savedState === 'true') {
            checkbox.checked = true;
        }
        
        checkbox.addEventListener('change', function() {
            localStorage.setItem(`checklist_${index}`, this.checked);
        });
    });
    
    // Update initial progress
    updateProgress();
    
    // Map controls simulation
    document.getElementById('showTraffic').addEventListener('click', function() {
        this.classList.toggle('active');
        // Simulate traffic layer toggle
    });
    
    document.getElementById('showSatellite').addEventListener('click', function() {
        this.classList.toggle('active');
        // Simulate satellite view toggle
    });
    
    document.getElementById('fullscreenMap').addEventListener('click', function() {
        const mapElement = document.getElementById('routeMap');
        if (mapElement.requestFullscreen) {
            mapElement.requestFullscreen();
        } else if (mapElement.webkitRequestFullscreen) {
            mapElement.webkitRequestFullscreen();
        } else if (mapElement.msRequestFullscreen) {
            mapElement.msRequestFullscreen();
        }
    });
});
</script>
@endpush