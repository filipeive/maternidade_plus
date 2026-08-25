@extends('layouts.app')

@section('title', 'Alertas Críticos de Laboratório')
@section('page-title', 'Alertas Críticos de Laboratório')
@section('title-icon', 'fa-exclamation-triangle')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('laboratory.index') }}">Laboratório</a></li>
<li class="breadcrumb-item active">Alertas Críticos</li>
@endsection

@section('content')
<!-- Cabeçalho / Estatísticas -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm border-start border-4 border-danger">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-danger mb-1">
                        <i class="fas fa-biohazard me-2"></i>Exames Críticos Detectados (Últimos 7 dias)
                    </h5>
                    <p class="text-muted mb-0">
                        Resultados reagentes ou alterados (HIV+, Sífilis+, Anemia Grave, Diabetes) que exigem conduta clínica prioritária.
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-danger fs-6 px-3 py-2">
                        {{ $criticalExams->count() }} Resultados Críticos
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('laboratory.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Voltar ao Laboratório
    </a>
    <a href="{{ route('alertas.index') }}" class="btn btn-danger">
        <i class="fas fa-shield-alt me-1"></i> Ver Módulo Geral de Alerta Precoce
    </a>
</div>

<!-- Tabela de Exames Críticos -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data Realização</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Resultado Encontrado</th>
                        <th>Prioridade / Sinal</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($criticalExams as $exam)
                    @php
                        $patient = $exam->consultation?->patient;
                        $resLower = strtolower($exam->resultado ?? '');
                        $badgeColor = 'danger';
                        if (str_contains($resLower, 'hiv')) $badgeColor = 'danger';
                        elseif (str_contains($resLower, 'sífilis') || str_contains($resLower, 'sifilis')) $badgeColor = 'warning text-dark';
                        elseif (str_contains($resLower, 'anemia')) $badgeColor = 'danger';
                    @endphp
                    <tr>
                        <td>
                            <i class="far fa-calendar-alt text-muted me-1"></i>
                            {{ $exam->data_realizacao ? \Carbon\Carbon::parse($exam->data_realizacao)->format('d/m/Y') : 'N/D' }}
                        </td>
                        <td>
                            @if($patient)
                                <a href="{{ route('patients.show', $patient) }}" class="fw-bold text-decoration-none text-primary">
                                    {{ $patient->nome_completo }}
                                </a>
                                <div class="small text-muted">BI: {{ $patient->documento_bi ?? 'N/A' }} | Tel: {{ $patient->contacto ?? 'N/A' }}</div>
                            @else
                                <span class="text-muted">Paciente não vinculado</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ ucfirst(str_replace('_', ' ', $exam->tipo_exame)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $badgeColor }} fs-6">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $exam->resultado }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger">ALTO RISCO</span>
                        </td>
                        <td class="text-end">
                            @if($patient)
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-user-md me-1"></i> Ficha Médica
                                </a>
                            @endif
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i> Detalhes
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="mb-0">Nenhum exame crítico registrado nos últimos 7 dias.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
