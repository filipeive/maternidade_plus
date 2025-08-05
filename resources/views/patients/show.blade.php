@extends('layouts.app')

@section('title', 'Detalhes da Gestante')
@section('page-title', $patient->nome_completo)

@section('content')
<div class="row">
    <!-- Informações Principais -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informações Pessoais</h5>
                <div>
                    <a href="{{ route('consultations.create', $patient) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-calendar-plus me-1"></i>Nova Consulta
                    </a>
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nome:</strong> {{ $patient->nome_completo }}</p>
                        <p><strong>Idade:</strong> {{ $patient->idade }} anos</p>
                        <p><strong>Data de Nascimento:</strong> {{ $patient->data_nascimento->format('d/m/Y') }}</p>
                        <p><strong>Documento (BI):</strong> {{ $patient->documento_bi }}</p>
                        <p><strong>Contacto:</strong> {{ $patient->contacto }}</p>
                        @if($patient->email)
                            <p><strong>Email:</strong> {{ $patient->email }}</p>
                        @endif
                        @if($patient->contacto_emergencia)
                            <p><strong>Emergência:</strong> {{ $patient->contacto_emergencia }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p><strong>Endereço:</strong> {{ $patient->endereco }}</p>
                        @if($patient->tipo_sanguineo)
                            <p><strong>Tipo Sanguíneo:</strong> {{ $patient->tipo_sanguineo }}</p>
                        @endif
                        <p><strong>Gestações:</strong> G{{ $patient->numero_gestacoes }}P{{ $patient->numero_partos }}A{{ $patient->numero_abortos }}</p>
                        @if($patient->data_ultima_menstruacao)
                            <p><strong>DUM:</strong> {{ $patient->data_ultima_menstruacao->format('d/m/Y') }}</p>
                        @endif
                        @if($patient->data_provavel_parto)
                            <p><strong>Data Provável do Parto:</strong> {{ $patient->data_provavel_parto->format('d/m/Y') }}</p>
                        @endif
                        @if($patient->semanas_gestacao)
                            <p><strong>Semanas de Gestação:</strong> {{ $patient->semanas_gestacao }}ª semana</p>
                        @endif
                    </div>
                </div>
                
                @if($patient->alergias)
                    <div class="alert alert-warning mt-3">
                        <strong><i class="fas fa-exclamation-triangle me-1"></i>Alergias:</strong> {{ $patient->alergias }}
                    </div>
                @endif
                
                @if($patient->historico_medico)
                    <div class="mt-3">
                        <strong>Histórico Médico:</strong>
                        <p class="text-muted">{{ $patient->historico_medico }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Histórico de Consultas -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Histórico de Consultas</h5>
            </div>
            <div class="card-body">
                @if($patient->consultations->count() > 0)
                    <div class="timeline">
                        @foreach($patient->consultations->sortByDesc('data_consulta') as $consultation)
                        <div class="timeline-item mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="badge bg-info mb-1">{{ $consultation->data_consulta->format('d/m/Y') }}</div>
                                        <small class="d-block text-muted">{{ $consultation->data_consulta->format('H:i') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">{{ $consultation->tipo_consulta_label }}</h6>
                                                <span class="badge bg-{{ $consultation->status === 'realizada' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($consultation->status) }}
                                                </span>
                                            </div>
                                            
                                            @if($consultation->semanas_gestacao)
                                                <p class="mb-1"><strong>Semanas:</strong> {{ $consultation->semanas_gestacao }}ª</p>
                                            @endif
                                            
                                            @if($consultation->peso)
                                                <p class="mb-1"><strong>Peso:</strong> {{ $consultation->peso }} kg</p>
                                            @endif
                                            
                                            @if($consultation->pressao_arterial)
                                                <p class="mb-1"><strong>PA:</strong> {{ $consultation->pressao_arterial }} mmHg</p>
                                            @endif
                                            
                                            @if($consultation->observacoes)
                                                <p class="mb-1"><strong>Observações:</strong> {{ $consultation->observacoes }}</p>
                                            @endif
                                            
                                            @if($consultation->exams->count() > 0)
                                                <div class="mt-2">
                                                    <strong>Exames:</strong>
                                                    @foreach($consultation->exams as $exam)
                                                        <span class="badge bg-secondary me-1">{{ $exam->tipo_exame_label }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <small class="text-muted">
                                                <i class="fas fa-user-md me-1"></i>{{ $consultation->user->name }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma consulta registrada ainda.</p>
                        <a href="{{ route('consultations.create', $patient) }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-1"></i>Agendar Primeira Consulta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar com informações resumidas -->
    <div class="col-md-4">
        <!-- Status da Gestação -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Status da Gestação</h6>
            </div>
            <div class="card-body">
                @if($patient->semanas_gestacao)
                    <div class="text-center mb-3">
                        <div class="display-6 text-primary">{{ $patient->semanas_gestacao }}</div>
                        <small class="text-muted">semanas de gestação</small>
                    </div>
                    
                    @php
                        $trimestre = $patient->semanas_gestacao <= 12 ? 1 : ($patient->semanas_gestacao <= 28 ? 2 : 3);
                        $progresso = min(100, ($patient->semanas_gestacao / 40) * 100);
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">{{ $trimestre }}º Trimestre</span>
                            <span class="small">{{ number_format($progresso, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: {{ $progresso }}%"></div>
                        </div>
                    </div>
                    
                    @if($patient->data_provavel_parto)
                        @php
                            $diasRestantes = now()->diffInDays($patient->data_provavel_parto, false);
                        @endphp
                        <div class="alert {{ $diasRestantes <= 28 ? 'alert-warning' : 'alert-info' }} py-2">
                            <small>
                                <i class="fas fa-calendar me-1"></i>
                                @if($diasRestantes > 0)
                                    {{ $diasRestantes }} dias para o parto
                                @elseif($diasRestantes == 0)
                                    Data provável é hoje!
                                @else
                                    {{ abs($diasRestantes) }} dias de atraso
                                @endif
                            </small>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-question-circle fa-2x mb-2"></i>
                        <p class="small">Informe a data da última menstruação para acompanhar a gestação</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Próximas Consultas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Próximas Consultas</h6>
            </div>
            <div class="card-body">
                @php
                    $proximasConsultas = $patient->consultations()
                        ->where('data_consulta', '>', now())
                        ->orderBy('data_consulta')
                        ->limit(3)
                        ->get();
                @endphp
                
                @if($proximasConsultas->count() > 0)
                    @foreach($proximasConsultas as $consulta)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <small class="fw-bold">{{ $consulta->data_consulta->format('d/m/Y H:i') }}</small><br>
                                <small class="text-muted">{{ $consulta->tipo_consulta_label }}</small>
                            </div>
                            <span class="badge bg-info">{{ $consulta->status }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted small text-center">Nenhuma consulta agendada</p>
                    <a href="{{ route('consultations.create', $patient) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-plus me-1"></i>Agendar
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Alertas -->
        @php
            $alertas = collect();
            
            // Verificar se precisa de consulta
            $ultimaConsulta = $patient->consultations()->latest('data_consulta')->first();
            if (!$ultimaConsulta || $ultimaConsulta->data_consulta->lt(now()->subDays(30))) {
                $alertas->push([
                    'tipo' => 'warning',
                    'icone' => 'exclamation-triangle',
                    'mensagem' => 'Sem consulta há mais de 30 dias'
                ]);
            }
            
            // Verificar exames pendentes
            $examesPendentes = \App\Models\Exam::whereHas('consultation', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->where('status', 'solicitado')->count();
            
            if ($examesPendentes > 0) {
                $alertas->push([
                    'tipo' => 'info',
                    'icone' => 'flask',
                    'mensagem' => "{$examesPendentes} exame(s) pendente(s)"
                ]);
            }
        @endphp
        
        @if($alertas->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Alertas</h6>
                </div>
                <div class="card-body">
                    @foreach($alertas as $alerta)
                        <div class="alert alert-{{ $alerta['tipo'] }} py-2 mb-2">
                            <small>
                                <i class="fas fa-{{ $alerta['icone'] }} me-1"></i>
                                {{ $alerta['mensagem'] }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
