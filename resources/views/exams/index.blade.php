@extends('layouts.app')

@section('title', 'Exames')
@section('page-title', 'Gestão de Exames')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4>Lista de Exames</h4>
        <p class="text-muted">Gerencie os exames solicitados e resultados</p>
    </div>
    <div>
        <a href="{{ route('exams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Solicitar Exame
        </a>
        <a href="{{ route('exams.pending') }}" class="btn btn-warning">
            <i class="fas fa-clock me-1"></i>Pendentes
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
                    <option value="solicitado" {{ request('status') === 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                    <option value="realizado" {{ request('status') === 'realizado' ? 'selected' : '' }}>Realizado</option>
                    <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo de Exame</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="hemograma_completo" {{ request('tipo') === 'hemograma_completo' ? 'selected' : '' }}>Hemograma Completo</option>
                    <option value="glicemia_jejum" {{ request('tipo') === 'glicemia_jejum' ? 'selected' : '' }}>Glicemia de Jejum</option>
                    <option value="urina_tipo_1" {{ request('tipo') === 'urina_tipo_1' ? 'selected' : '' }}>EAS (Urina Tipo 1)</option>
                    <option value="ultrassom_obstetrico" {{ request('tipo') === 'ultrassom_obstetrico' ? 'selected' : '' }}>Ultrassom Obstétrico</option>
                    <option value="teste_hiv" {{ request('tipo') === 'teste_hiv' ? 'selected' : '' }}>Teste HIV</option>
                    <option value="teste_sifilis" {{ request('tipo') === 'teste_sifilis' ? 'selected' : '' }}>Teste de Sífilis</option>
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
                    <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

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
                            <th>Consulta</th>
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
                                <strong>{{ $exam->tipo_exame_label }}</strong>
                                @if($exam->descricao_exame)
                                    <br><small class="text-muted">{{ Str::limit($exam->descricao_exame, 40) }}</small>
                                @endif
                            </td>
                            <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                            <td>
                                @if($exam->data_realizacao)
                                    {{ $exam->data_realizacao->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Pendente</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'solicitado' => 'warning',
                                        'realizado' => 'success',
                                        'pendente' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$exam->status] ?? 'secondary' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $exam->consultation->data_consulta->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $exam->consultation->tipo_consulta_label }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('exams.show', $exam) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($exam->status !== 'realizado')
                                        <a href="{{ route('exams.edit', $exam) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('exams.markAsCompleted', $exam) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como Realizado">
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
                {{ $exams->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-flask fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame encontrado</h5>
                <p class="text-muted">Comece solicitando o primeiro exame.</p>
                <a href="{{ route('exams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Solicitar Exame
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
</div>