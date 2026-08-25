@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Painel de Controle')

@section('content')
<div class="row mb-4">
    <!-- Cards de Estatísticas -->
    <div class="col-md-3 mb-3">
        <div class="card prenatal-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-female fa-2x text-success"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $totalGestantes }}</h5>
                        <p class="card-text text-muted">Total de Gestantes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card exam-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-calendar-week fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $consultasEstaSemana }}</h5>
                        <p class="card-text text-muted">Consultas Esta Semana</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card emergency-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clock fa-2x text-danger"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $consultasPendentes }}</h5>
                        <p class="card-text text-muted">Consultas Pendentes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-flask fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $examesPendentes }}</h5>
                        <p class="card-text text-muted">Exames Pendentes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Próximas Consultas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Próximas Consultas</h5>
                <a href="{{ route('consultations.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Nova Consulta
                </a>
            </div>
            <div class="card-body">
                @if($proximasConsultas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Gestante</th>
                                    <th>Data/Hora</th>
                                    <th>Tipo</th>
                                    <th>Semanas</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proximasConsultas as $consulta)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $consulta->patient->nome_completo }}</strong><br>
                                            <small class="text-muted">BI: {{ $consulta->patient->documento_bi }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $consulta->data_consulta->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $consulta->data_consulta->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $consulta->tipo_consulta_label }}</span>
                                    </td>
                                    <td>{{ $consulta->semanas_gestacao ?? 'N/A' }}ª</td>
                                    <td>
                                        <span class="badge {{ $consulta->status === 'confirmada' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($consulta->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('consultations.show', $consulta) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('consultations.edit', $consulta) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma consulta agendada para os próximos dias.</p>
                        <a href="{{ route('consultations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Agendar Consulta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Alertas Precoces - Módulo de Alerta Precoce (Early Warning) -->
@if(isset($alertasPrecoces) && $alertasPrecoces->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2 text-danger">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h5 class="mb-0 fw-bold text-dark">Módulo de Alerta Precoce — Alertas Ativos</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('alertas.metricas') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-chart-line me-1"></i>Métricas
                    </a>
                    <a href="{{ route('alertas.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list me-1"></i>Ver Todos os Alertas
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Severidade</th>
                                <th>Gestante</th>
                                <th>Tipo</th>
                                <th>Mensagem Clínica</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertasPrecoces as $alerta)
                            <tr>
                                <td>
                                    @if($alerta->nivel === 'alto')
                                        <span class="badge bg-danger text-white">
                                            <i class="fas fa-bolt me-1"></i>Alto
                                        </span>
                                    @elseif($alerta->nivel === 'medio')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation me-1"></i>Médio
                                        </span>
                                    @else
                                        <span class="badge bg-info text-dark">
                                            <i class="fas fa-info-circle me-1"></i>Baixo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($alerta->patient)
                                        <a href="{{ route('patients.show', $alerta->patient) }}" class="fw-bold text-primary text-decoration-none">
                                            {{ $alerta->patient->nome_completo }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/D</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small fw-semibold">{{ $alerta->tipo_label }}</span>
                                </td>
                                <td>
                                    <span class="small text-dark">{{ $alerta->mensagem }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $alerta->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alerta->status === 'ativo' ? 'danger' : ($alerta->status === 'em_seguimento' ? 'warning' : 'success') }}">
                                        {{ $alerta->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('alertas.index', ['search' => $alerta->patient?->nome_completo]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-stethoscope me-1"></i>Tratar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Alertas de Acompanhamento -->
@if($alertas->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Alertas de Acompanhamento</h5>
            </div>
            <div class="card-body">
                @foreach($alertas as $alerta)
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>{{ $alerta['gestante'] }}</strong>: {{ $alerta['mensagem'] }}
                        <a href="{{ $alerta['link'] }}" class="btn btn-sm btn-outline-warning ms-2">Ver Detalhes</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection
