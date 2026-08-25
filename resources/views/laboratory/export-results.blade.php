@extends('layouts.app')

@section('title', 'Exportar Resultados - Laboratório')
@section('page-title', 'Exportação de Resultados Laboratoriais')
@section('title-icon', 'fa-file-export')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('laboratory.index') }}">Laboratório</a></li>
<li class="breadcrumb-item active">Exportar Resultados</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-center g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Data Início</label>
                <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Data Fim</label>
                <input type="date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="fas fa-print me-1"></i> Imprimir / PDF
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
        <span>Resultados de Exames Realizados ({{ count($exams) }} exames encontrados)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Data Realização</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Resultado</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $index => $exam)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $exam->data_realizacao ? \Carbon\Carbon::parse($exam->data_realizacao)->format('d/m/Y') : 'N/D' }}</td>
                        <td class="fw-bold">{{ $exam->consultation?->patient?->nome_completo ?? 'N/D' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}</td>
                        <td>{{ $exam->resultado }}</td>
                        <td><span class="badge bg-success">Realizado</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Nenhum exame realizado no período selecionado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
