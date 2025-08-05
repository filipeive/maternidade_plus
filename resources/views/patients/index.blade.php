@extends('layouts.app')

@section('title', 'Gestantes')
@section('page-title', 'Gestão de Gestantes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4>Lista de Gestantes</h4>
        <p class="text-muted">Gerencie o cadastro das gestantes em acompanhamento</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nova Gestante
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome Completo</th>
                            <th>Idade</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th>Semanas</th>
                            <th>Próxima Consulta</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $patient->nome_completo }}</strong><br>
                                    <small class="text-muted">
                                        G{{ $patient->numero_gestacoes }}P{{ $patient->numero_partos }}A{{ $patient->numero_abortos }}
                                    </small>
                                </div>
                            </td>
                            <td>{{ $patient->idade }} anos</td>
                            <td>{{ $patient->documento_bi }}</td>
                            <td>{{ $patient->contacto }}</td>
                            <td>
                                @if($patient->semanas_gestacao)
                                    <span class="badge bg-info">{{ $patient->semanas_gestacao }}ª semana</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($patient->proxima_consulta)
                                    {{ $patient->proxima_consulta->data_consulta->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Nenhuma agendada</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('consultations.create', $patient) }}" class="btn btn-sm btn-outline-success" title="Nova Consulta">
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $patients->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-female fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma gestante cadastrada</h5>
                <p class="text-muted">Comece adicionando a primeira gestante ao sistema.</p>
                <a href="{{ route('patients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Cadastrar Primeira Gestante
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
