@extends('layouts.app')

@section('title', 'Detalhes da Consulta')
@section('page-title', 'Consulta - ' . $consultation->patient->nome_completo)

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Informações da Consulta -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dados da Consulta</h5>
                <div>
                    @if($consultation->status !== 'realizada')
                        <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    @endif
                    @if($consultation->status === 'agendada')
                        <form method="POST" action="{{ route('consultations.confirm', $consultation) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check me-1"></i>Confirmar
                            </button>
                        </form>
                    @endif
                    @if($consultation->status === 'confirmada')
                        <form method="POST" action="{{ route('consultations.complete', $consultation) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-check-double me-1"></i>Marcar como Realizada
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Informações Básicas</h6>
                        <p><strong>Data:</strong> {{ $consultation->data_consulta->format('d/m/Y') }}</p>
                        <p><strong>Horário:</strong> {{ $consultation->data_consulta->format('H:i') }}</p>
                        <p><strong>Tipo:</strong> 
                            <span class="badge bg-info">{{ $consultation->tipo_consulta_label }}</span>
                        </p>
                        <p><strong>Status:</strong> 
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
                        </p>
                        <p><strong>Profissional:</strong> {{ $consultation->user->name }}</p>
                        @if($consultation->proxima_consulta)
                            <p><strong>Próxima Consulta:</strong> {{ $consultation->proxima_consulta->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary">Medições</h6>
                        @if($consultation->semanas_gestacao)
                            <p><strong>Semanas de Gestação:</strong> {{ $consultation->semanas_gestacao }}ª semana</p>
                        @endif
                        @if($consultation->peso)
                            <p><strong>Peso:</strong> {{ $consultation->peso }} kg</p>
                        @endif
                        @if($consultation->pressao_arterial)
                            <p><strong>Pressão Arterial:</strong> {{ $consultation->pressao_arterial }} mmHg</p>
                        @endif
                        @if($consultation->batimentos_fetais)
                            <p><strong>Batimentos Fetais:</strong> {{ $consultation->batimentos_fetais }} bpm</p>
                        @endif
                        @if($consultation->altura_uterina)
                            <p><strong>Altura Uterina:</strong> {{ $consultation->altura_uterina }} cm</p>
                        @endif
                    </div>
                </div>
                
                @if($consultation->observacoes)
                    <div class="mt-3">
                        <h6 class="text-primary">Observações</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $consultation->observacoes }}
                        </div>
                    </div>
                @endif
                
                @if($consultation->orientacoes)
                    <div class="mt-3">
                        <h6 class="text-primary">Orientações e Recomendações</h6>
                        <div class="bg-success bg-opacity-10 p-3 rounded border-start border-success border-3">
                            {{ $consultation->orientacoes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Exames Relacionados -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Exames</h5>
                <a href="{{ route('exams.create', ['consultation_id' => $consultation->id]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i>Solicitar Exame
                </a>
            </div>
            <div class="card-body">
                @if($consultation->exams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo de Exame</th>
                                    <th>Data Solicitação</th>
                                    <th>Data Realização</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultation->exams as $exam)
                                <tr>
                                    <td>
                                        <strong>{{ $exam->tipo_exame_label }}</strong>
                                        @if($exam->descricao_exame)
                                            <br><small class="text-muted">{{ $exam->descricao_exame }}</small>
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
                                            $examStatusColors = [
                                                'solicitado' => 'warning',
                                                'realizado' => 'success',
                                                'pendente' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $examStatusColors[$exam->status] ?? 'secondary' }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($exam->status !== 'realizado')
                                            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-xs btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-flask fa-2x mb-2"></i>
                        <p>Nenhum exame solicitado para esta consulta.</p>
                        <a href="{{ route('exams.create', ['consultation_id' => $consultation->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Solicitar Primeiro Exame
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar com informações da gestante -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informações da Gestante</h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">{{ $consultation->patient->nome_completo }}</h6>
                <p class="mb-1"><strong>Idade:</strong> {{ $consultation->patient->idade }} anos</p>
                <p class="mb-1"><strong>BI:</strong> {{ $consultation->patient->documento_bi }}</p>
                <p class="mb-1"><strong>Contacto:</strong> {{ $consultation->patient->contacto }}</p>
                
                @if($consultation->patient->semanas_gestacao)
                    <hr>
                    <div class="text-center">
                        <div class="display-6 text-primary">{{ $consultation->patient->semanas_gestacao }}</div>
                        <small class="text-muted">semanas de gestação</small>
                    </div>
                @endif
                
                @if($consultation->patient->tipo_sanguineo)
                    <p class="mb-1"><strong>Tipo Sanguíneo:</strong> {{ $consultation->patient->tipo_sanguineo }}</p>
                @endif
                
                @if($consultation->patient->alergias)
                    <div class="alert alert-warning py-2 mt-2">
                        <small><strong>⚠️ Alergias:</strong> {{ $consultation->patient->alergias }}</small>
                    </div>
                @endif
                
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('patients.show', $consultation->patient) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user me-1"></i>Ver Perfil Completo
                    </a>
                    <a href="{{ route('consultations.create', $consultation->patient) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-calendar-plus me-1"></i>Nova Consulta
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Histórico Resumido -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Últimas Consultas</h6>
            </div>
            <div class="card-body">
                @php
                    $ultimasConsultas = $consultation->patient->consultations()
                        ->where('id', '!=', $consultation->id)
                        ->where('status', 'realizada')
                        ->orderBy('data_consulta', 'desc')
                        ->limit(3)
                        ->get();
                @endphp
                
                @if($ultimasConsultas->count() > 0)
                    @foreach($ultimasConsultas as $consulta)
                        <div class="border-bottom py-2">
                            <small class="fw-bold">{{ $consulta->data_consulta->format('d/m/Y') }}</small>
                            <small class="text-muted d-block">{{ $consulta->tipo_consulta_label }}</small>
                            @if($consulta->semanas_gestacao)
                                <small class="text-muted">{{ $consulta->semanas_gestacao }}ª semana</small>
                            @endif
                        </div>
                    @endforeach
                    <div class="text-center mt-2">
                        <a href="{{ route('patients.history', $consultation->patient) }}" class="btn btn-link btn-sm">
                            Ver histórico completo
                        </a>
                    </div>
                @else
                    <p class="text-muted small text-center">Esta é a primeira consulta realizada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection