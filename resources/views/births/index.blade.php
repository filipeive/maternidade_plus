@extends('layouts.app')

@section('title', 'Lista de Partos')
@section('page-title', 'Partos Registrados')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Partos</h5>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-1"></i>
                {{ request()->has('tipo') ? ucfirst(request()->tipo) : 'Todos' }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('births.index') }}">Todos os Partos</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('births.index') }}?tipo=normal">Partos Normais</a></li>
                <li><a class="dropdown-item" href="{{ route('births.index') }}?tipo=cesariana">Cesarianas</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Paciente</th>
                        <th>Tipo</th>
                        <th>Bebê</th>
                        <th>Responsável</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($births as $birth)
                    <tr>
                        <td>{{ $birth->data_hora_parto->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('patients.show', $birth->patient) }}">
                                {{ $birth->patient->nome_completo }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-{{ $birth->tipo_parto === 'normal' ? 'success' : 'warning' }}">
                                {{ $birth->tipo_parto_formatado }}
                            </span>
                        </td>
                        <td>
                            @if($birth->peso_nascimento)
                                {{ $birth->peso_formatado }}<br>
                                <small class="text-muted">{{ $birth->sexo_bebe ? ucfirst($birth->sexo_bebe) : 'N/A' }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $birth->user->name }}</td>
                        <td>
                            <a href="{{ route('births.show', $birth) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $births->links() }}
        </div>
    </div>
</div>
@endsection