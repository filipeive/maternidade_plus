@extends('layouts.app')

@section('title', 'Exames')
@section('page-title', 'Gestão de Exames Laboratoriais')

@section('content')
<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card exam-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-flask fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $stats['total'] }}</h5>
                        <p class="card-text text-muted">Total de Exames</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card emergency-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $stats['pendentes'] }}</h5>
                        <p class="card-text text-muted">Pendentes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card prenatal-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $stats['realizados'] }}</h5>
                        <p class="card-text text-muted">Realizados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-calendar-day fa-2x text-info"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">{{ $stats['hoje'] }}</h5>
                        <p class="card-text text-muted">Hoje</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Ações -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Pesquisar Gestante</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Nome ou BI">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Todos</option>
                    <option value="solicitado" {{ request('status') === 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                    <option value="realizado" {{ request('status') === 'realizado' ? 'selected' : '' }}>Realizado</option>
                    <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Tipo de Exame</label>
                <select class="form-select" name="tipo_exame">
                    <option value="">Todos</option>
                    <option value="hemograma_completo" {{ request('tipo_exame') === 'hemograma_completo' ? 'selected' : '' }}>Hemograma</option>
                    <option value="glicemia_jejum" {{ request('tipo_exame') === 'glicemia_jejum' ? 'selected' : '' }}>Glicemia</option>
                    <option value="urina_tipo_1" {{ request('tipo_exame') === 'urina_tipo_1' ? 'selected' : '' }}>EAS</option>
                    <option value="ultrassom_obstetrico" {{ request('tipo_exame') === 'ultrassom_obstetrico' ? 'selected' : '' }}>Ultrassom</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Data Início</label>
                <input type="date" class="form-control" name="data_inicio" value="{{ request('data_inicio') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Data Fim</label>
                <input type="date" class="form-control" name="data_fim" value="{{ request('data_fim') }}">
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group" role="group">
        <a href="{{ route('exams.pending-results') }}" class="btn btn-warning">
            <i class="fas fa-clock me-1"></i>Resultados Pendentes
        </a>
        <a href="{{ route('exams.report') }}" class="btn btn-info">
            <i class="fas fa-chart-bar me-1"></i>Relatório
        </a>
    </div>
    
    <a href="{{ route('exams.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Solicitar Exame
    </a>
</div>

<!-- Lista de Exames -->
<div class="card">
    <div class="card-body">
        @if($exams->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Gestante</th>
                            <th>Tipo de Exame</th>
                            <th>Data Solicitação</th>
                            <th>Data Realização</th>
                            <th>Status</th>
                            <th>Solicitado por</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $exam->consultation->patient->nome_completo }}</strong><br>
                                    <small class="text-muted">BI: {{ $exam->consultation->patient->documento_bi }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $exam->tipo_exame_label }}</span>
                                @if($exam->descricao_exame)
                                    <br><small class="text-muted">{{ $exam->descricao_exame }}</small>
                                @endif
                            </td>
                            <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                            <td>
                                @if($exam->data_realizacao)
                                    {{ $exam->data_realizacao->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = match($exam->status) {
                                        'realizado' => 'bg-success',
                                        'solicitado' => 'bg-warning',
                                        'pendente' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $exam->consultation->user->name }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($exam->status === 'solicitado')
                                        <a href="{{ route('exams.result-form', $exam) }}" class="btn btn-sm btn-outline-success" title="Adicionar Resultado">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $exams->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-flask fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame encontrado</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status', 'tipo_exame', 'data_inicio', 'data_fim']))
                        Nenhum exame corresponde aos filtros aplicados.
                        <a href="{{ route('exams.index') }}" class="btn btn-outline-primary btn-sm ms-2">Limpar Filtros</a>
                    @else
                        Comece solicitando o primeiro exame.
                    @endif
                </p>
                
                @if(!request()->hasAny(['search', 'status', 'tipo_exame', 'data_inicio', 'data_fim']))
                    <a href="{{ route('exams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Solicitar Primeiro Exame
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection