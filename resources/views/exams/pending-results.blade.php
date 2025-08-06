@extends('layouts.app')

@section('title', 'Exames Pendentes')
@section('page-title', 'Exames Pendentes de Resultado')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Exames Pendentes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Exames Solicitados Aguardando Resultado</h5>
            <div class="badge bg-warning text-dark">
                <i class="fas fa-clock me-1"></i> {{ $exams->total() }} Pendentes
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Data Solicitação</th>
                        <th>Médico</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exam->id }}</td>
                        <td>
                            <a href="{{ route('patients.show', $exam->consultation->patient) }}" class="text-primary">
                                {{ $exam->consultation->patient->nome_completo }}
                            </a>
                        </td>
                        <td>{{ $tiposExames[$exam->tipo_exame] ?? $exam->tipo_exame }}</td>
                        <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                        <td>{{ $exam->consultation->user->name }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('exams.result-form', $exam) }}" class="btn btn-sm btn-success" title="Registrar Resultado">
                                    <i class="fas fa-flask"></i>
                                </a>
                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-primary" title="Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h5>Todos os exames estão com resultados registrados!</h5>
                            <p class="text-muted">Nenhum exame pendente encontrado.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($exams->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $exams->links() }}
        </div>
        @endif
    </div>
</div>
@endsection