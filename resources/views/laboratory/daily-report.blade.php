@extends('layouts.app')

@section('title', 'Relatório Diário - Laboratório')
@section('page-title', 'Relatório Diário do Laboratório')
@section('title-icon', 'fa-file-invoice')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('laboratory.index') }}">Laboratório</a></li>
<li class="breadcrumb-item active">Relatório Diário</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-center g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Data do Relatório</label>
                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::parse($report['data'] ?? now())->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Filtrar Data
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h4 class="text-primary fw-bold mb-0">{{ $report['exames_solicitados'] ?? 0 }}</h4>
                <div class="text-muted small">Exames Solicitados</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h4 class="text-success fw-bold mb-0">{{ $report['exames_realizados'] ?? 0 }}</h4>
                <div class="text-muted small">Exames Realizados</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h4 class="text-warning fw-bold mb-0">{{ $report['exames_pendentes'] ?? 0 }}</h4>
                <div class="text-muted small">Pendentes</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold py-3">
        Resultados Alterados no Dia ({{ \Carbon\Carbon::parse($report['data'] ?? now())->format('d/m/Y') }})
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['resultados_alterados'] ?? [] as $exam)
                    <tr>
                        <td class="fw-bold">{{ $exam->consultation?->patient?->nome_completo ?? 'N/D' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $exam->resultado }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">Sem resultados alterados nesta data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
