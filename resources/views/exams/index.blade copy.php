@extends('layouts.app')

@section('title', 'Lista de Exames')
@section('page-title', 'Exames Solicitados')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Todos os Exames</h5>
            <a href="{{ route('exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Registrar Novo Exame
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Tipo de Exame</th>
                        <th>Data da Solicitação</th>
                        <th>Data da Realização</th>
                        <th>Status</th>
                        <th>Médico Solicitante</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exams as $exam)
                        <tr>
                            <td>
                                <a href="{{ route('patients.show', $exam->consultation->patient) }}">
                                    {{ $exam->consultation->patient->nome_completo }}
                                </a>
                            </td>
                            <td>{{ $exam->tipo_exame_label }}</td>
                            <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                            <td>{{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = match($exam->status) {
                                        'solicitado' => 'bg-warning',
                                        'realizado' => 'bg-success',
                                        'pendente' => 'bg-info',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($exam->status) }}</span>
                            </td>
                            <td>{{ $exam->consultation->user->name }}</td>
                            <td class="text-center">
                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este exame?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Remover"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum exame registrado ainda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">{{ $exams->links() }}</div>
    </div>
</div>
@endsection