@extends('layouts.app')

@section('title', 'Carga de Trabalho - Laboratório')
@section('page-title', 'Carga de Trabalho do Laboratório')
@section('title-icon', 'fa-chart-bar')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('laboratory.index') }}">Laboratório</a></li>
<li class="breadcrumb-item active">Carga de Trabalho</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-4 border-info">
            <div class="card-body">
                <div class="text-muted small">Tempo Médio de Processamento</div>
                <h3 class="fw-bold text-info mb-0">
                    {{ round($avgProcessingTime ?? 0, 1) }} <span class="fs-6 text-muted">dias</span>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-8 text-end align-self-center">
        <a href="{{ route('laboratory.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar ao Laboratório
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Carga por Dia -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="fas fa-calendar-day text-primary me-2"></i>Solicitações por Dia da Semana (Este Mês)
            </div>
            <div class="card-body">
                @if(isset($workloadByDay) && count($workloadByDay) > 0)
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Dia</th>
                                    <th>Solicitações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workloadByDay as $day => $total)
                                <tr>
                                    <td class="fw-bold">{{ $day }}</td>
                                    <td><span class="badge bg-primary fs-6">{{ $total }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">Sem dados de carga registrados.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Exames por Tipo -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="fas fa-list-check text-success me-2"></i>Exames por Tipo (Solicitados vs Realizados)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo de Exame</th>
                                <th>Solicitados</th>
                                <th>Realizados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examsByType as $item)
                            <tr>
                                <td class="fw-bold">{{ ucfirst(str_replace('_', ' ', $item->tipo_exame)) }}</td>
                                <td><span class="badge bg-secondary">{{ $item->total }}</span></td>
                                <td><span class="badge bg-success">{{ $item->realizados }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Sem registros este mês.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
