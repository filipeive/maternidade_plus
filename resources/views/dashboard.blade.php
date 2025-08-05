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
