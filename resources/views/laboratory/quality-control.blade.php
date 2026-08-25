@extends('layouts.app')

@section('title', 'Controle de Qualidade - Laboratório')
@section('page-title', 'Controle de Qualidade Laboratorial')
@section('title-icon', 'fa-shield-virus')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('laboratory.index') }}">Laboratório</a></li>
<li class="breadcrumb-item active">Controle de Qualidade</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Tempo Médio Entrega</div>
                <h3 class="fw-bold text-primary mb-0">{{ $qualityMetrics['tempo_medio_entrega'] ?? 0 }}d</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Resultados Críticos</div>
                <h3 class="fw-bold text-danger mb-0">{{ $qualityMetrics['exames_criticos'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Taxa de Reprocessamento</div>
                <h3 class="fw-bold text-warning mb-0">{{ $qualityMetrics['taxa_reprocessamento'] ?? 0 }}%</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Conformidade Qualidade</div>
                <h3 class="fw-bold text-success mb-0">{{ $qualityMetrics['satisfacao_cliente'] ?? 95 }}%</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold py-3">
        <i class="fas fa-microscope text-warning me-2"></i>Resultados Alterados ou Anormais (Este Mês)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data Realização</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alteredResults as $exam)
                    <tr>
                        <td>{{ $exam->data_realizacao ? \Carbon\Carbon::parse($exam->data_realizacao)->format('d/m/Y') : 'N/D' }}</td>
                        <td>
                            @if($exam->consultation?->patient)
                                <a href="{{ route('patients.show', $exam->consultation->patient) }}" class="fw-bold text-primary">
                                    {{ $exam->consultation->patient->nome_completo }}
                                </a>
                            @else
                                <span class="text-muted">N/D</span>
                            @endif
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $exam->resultado }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Nenhum resultado alterado encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($alteredResults, 'links'))
        <div class="card-footer bg-white py-3">
            {{ $alteredResults->links() }}
        </div>
    @endif
</div>
@endsection
