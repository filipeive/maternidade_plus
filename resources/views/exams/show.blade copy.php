@extends('layouts.app')

@section('title', 'Detalhes do Exame')
@section('page-title', 'Detalhes do Exame')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <!-- Detalhes do Exame -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-flask me-2"></i>
                    {{ $exam->tipo_exame_label }}
                </h5>
                <div>
                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este exame?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Remover</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Paciente:</strong> <a href="{{ route('patients.show', $exam->consultation->patient) }}">{{ $exam->consultation->patient->nome_completo }}</a></p>
                        <p><strong>Médico Solicitante:</strong> {{ $exam->consultation->user->name }}</p>
                        <p><strong>Consulta de Origem:</strong> <a href="{{ route('consultations.show', $exam->consultation) }}">{{ $exam->consultation->data_consulta->format('d/m/Y') }}</a></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Data da Solicitação:</strong> {{ $exam->data_solicitacao->format('d/m/Y') }}</p>
                        <p><strong>Data da Realização:</strong> {{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : 'Não realizada' }}</p>
                        <p><strong>Status:</strong>
                            @php
                                $statusClass = match($exam->status) {
                                    'solicitado' => 'bg-warning',
                                    'realizado' => 'bg-success',
                                    'pendente' => 'bg-info',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($exam->status) }}</span>
                        </p>
                    </div>
                </div>
                @if($exam->tipo_exame === 'outros' && $exam->descricao_exame)
                    <hr>
                    <p><strong>Descrição:</strong> {{ $exam->descricao_exame }}</p>
                @endif
                @if($exam->observacoes)
                    <hr>
                    <p><strong>Observações:</strong> {{ $exam->observacoes }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <!-- Ações -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar para Lista de Exames
                    </a>
                    <a href="{{ route('consultations.show', $exam->consultation) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>Ver Consulta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>  
@endsection