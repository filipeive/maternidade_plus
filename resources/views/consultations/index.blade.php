@extends('layouts.app')

@section('title', 'Consultas')
@section('page-title', 'Gestão de Consultas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4>Lista de Consultas</h4>
        <p class="text-muted">Gerencie as consultas pré-natais agendadas</p>
    </div>
    <div>
        <a href="{{ route('consultations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nova Consulta
        </a>
        <a href="{{ route('exams.pending') }}" class="btn btn-warning">
            <i class="fas fa-flask me-1"></i>Exames Pendentes
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="agendada" {{ request('status') === 'agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="confirmada" {{ request('status') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="realizada" {{ request('status') === 'realizada' ? 'selected' : '' }}>Realizada</option>
                    <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="1_trimestre" {{ request('tipo') === '1_trimestre' ? 'selected' : '' }}>1º Trimestre</option>
                    <option value="2_trimestre" {{ request('tipo') === '2_trimestre' ? 'selected' : '' }}>2º Trimestre</option>
                    <option value="3_trimestre" {{ request('tipo') === '3_trimestre' ? 'selected' : '' }}>3º Trimestre</option>
                    <option value="pos_parto" {{ request('tipo') === 'pos_parto' ? 'selected' : '' }}>Pós-parto</option>
                    <option value="emergencia" {{ request('tipo') === 'emergencia' ? 'selected' : '' }}>Emergência</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data</label>
                <input type="date" name="data" class="form-control" value="{{ request('data') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($consultations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Gestante</th>
                            <th>Data/Hora</th>
                            <th>Tipo</th>
                            <th>Profissional</th>
                            <th>Status</th>
                            <th>Semanas</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $consultation->patient->nome_completo }}</strong><br>
                                    <small class="text-muted">BI: {{ $consultation->patient->documento_bi }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{ $consultation->data_consulta->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $consultation->data_consulta->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $consultation->tipo_consulta_label }}</span>
                            </td>
                            <td>{{ $consultation->user->name }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'agendada' => 'warning',
                                        'confirmada' => 'info',
                                        'realizada' => 'success',
                                        'cancelada' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$consultation->status] ?? 'secondary' }}">
                                    {{ ucfirst($consultation->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($consultation->semanas_gestacao)
                                    <span class="badge bg-light text-dark">{{ $consultation->semanas_gestacao }}ª</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('consultations.show', $consultation) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($consultation->status !== 'realizada')
                                        <a href="{{ route('consultations.edit', $consultation) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($consultation->status === 'agendada')
                                        <form method="POST" action="{{ route('consultations.confirm', $consultation) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Confirmar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $consultations->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma consulta encontrada</h5>
                <p class="text-muted">Comece agendando a primeira consulta.</p>
                <a href="{{ route('consultations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Agendar Consulta
                </a>
            </div>
        @endif
    </div>
</div>
@endsection