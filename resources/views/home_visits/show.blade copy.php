@extends('layouts.app')

@section('title', 'Detalhes da Visita Domiciliária')
@section('page-title', 'Visita Domiciliária #' . $homeVisit->id)
@section('title-icon', 'fa-home-medical')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a></li>
    <li class="breadcrumb-item active">Visita #{{ $homeVisit->id }}</li>
@endsection

@push('styles')
    <style>
        .complete-visit-form {
            display: none;
            background: #f8f9fa;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            animation: slideDown 0.3s ease-in-out;
        }

        .complete-visit-form.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-section h6 {
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .vital-signs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .materials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Informações Básicas da Visita -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informações da Visita
                    </h5>
                    <div>
                        @php
                            $statusClass = match ($homeVisit->status) {
                                'agendada' => 'bg-warning',
                                'realizada' => 'bg-success',
                                'reagendada' => 'bg-info',
                                'nao_encontrada' => 'bg-secondary',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $homeVisit->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">Data e Hora:</strong>
                                <p class="mb-0">{{ $homeVisit->data_visita->format('d/m/Y \à\s H:i') }}</p>
                                @if ($homeVisit->data_visita->isPast() && $homeVisit->status == 'agendada')
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Visita atrasada
                                    </small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong class="text-muted">Tipo de Visita:</strong>
                                @php
                                    $tipoClass = match ($homeVisit->tipo_visita) {
                                        'rotina' => 'bg-primary',
                                        'pos_parto' => 'bg-success',
                                        'alto_risco' => 'bg-danger',
                                        'faltosa' => 'bg-warning',
                                        'emergencia' => 'bg-danger',
                                        'educacao' => 'bg-info',
                                        'seguimento' => 'bg-secondary',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <p class="mb-0">
                                    <span class="badge {{ $tipoClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $homeVisit->tipo_visita)) }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <strong class="text-muted">Responsável:</strong>
                                <p class="mb-0">{{ $homeVisit->user->name }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">Motivo da Visita:</strong>
                                <p class="mb-0">{{ $homeVisit->motivo_visita }}</p>
                            </div>

                            <div class="mb-3">
                                <strong class="text-muted">Endereço da Visita:</strong>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                    {{ $homeVisit->endereco_visita }}
                                </p>
                            </div>

                            @if ($homeVisit->observacoes_gerais)
                                <div class="mb-3">
                                    <strong class="text-muted">Observações Gerais:</strong>
                                    <p class="mb-0">{{ $homeVisit->observacoes_gerais }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações da Gestante -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-female text-pink me-2" style="color: #e91e63;"></i>
                        Informações da Gestante
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-pink text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background-color: #e91e63;">
                            <i class="fas fa-female fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $homeVisit->patient->nome_completo }}</h5>
                            <p class="text-muted mb-0">
                                BI: {{ $homeVisit->patient->documento_bi }} |
                                Contato: {{ $homeVisit->patient->contacto ?? 'Não informado' }}
                            </p>
                        </div>
                        <a href="{{ route('patients.show', $homeVisit->patient) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> Ver Perfil
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong class="text-muted">Endereço Cadastrado:</strong>
                            <p class="mb-0">{{ $homeVisit->patient->endereco }}</p>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('home_visits.by-patient', $homeVisit->patient) }}"
                                class="btn btn-outline-info btn-sm">
                                <i class="fas fa-history me-1"></i> Histórico de Visitas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário para Completar Visita (Oculto inicialmente) -->
            @if ($homeVisit->status == 'agendada')
                <div id="completeVisitForm" class="complete-visit-form">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Completar Visita Domiciliária
                        </h4>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideCompleteForm()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>

                    <form method="POST" action="{{ route('home_visits.complete', $homeVisit) }}">
                        @csrf

                        <!-- Observações Obrigatórias -->
                        <div class="form-section">
                            <h6><i class="fas fa-home me-2"></i>Observações do Ambiente</h6>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="observacoes_ambiente" class="form-label required">Descrição do
                                        Ambiente</label>
                                    <textarea class="form-control" id="observacoes_ambiente" name="observacoes_ambiente" rows="3" required
                                        placeholder="Descreva as condições do ambiente domiciliar..."></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="condicoes_higiene" class="form-label required">Condições de Higiene</label>
                                    <select class="form-select" id="condicoes_higiene" name="condicoes_higiene" required>
                                        <option value="">Selecione...</option>
                                        <option value="bom">Bom</option>
                                        <option value="regular">Regular</option>
                                        <option value="ruim">Ruim</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="apoio_familiar" class="form-label required">Apoio Familiar</label>
                                    <select class="form-select" id="apoio_familiar" name="apoio_familiar" required>
                                        <option value="">Selecione...</option>
                                        <option value="adequado">Adequado</option>
                                        <option value="parcial">Parcial</option>
                                        <option value="inadequado">Inadequado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Estado da Gestante -->
                        <div class="form-section">
                            <h6><i class="fas fa-heartbeat me-2"></i>Estado da Gestante</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="estado_nutricional" class="form-label">Estado Nutricional</label>
                                    <textarea class="form-control" id="estado_nutricional" name="estado_nutricional" rows="2"
                                        placeholder="Avaliação do estado nutricional..."></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="queixas_principais" class="form-label">Queixas Principais</label>
                                    <textarea class="form-control" id="queixas_principais" name="queixas_principais" rows="2"
                                        placeholder="Principais queixas relatadas..."></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="orientacoes_dadas" class="form-label required">Orientações
                                        Fornecidas</label>
                                    <textarea class="form-control" id="orientacoes_dadas" name="orientacoes_dadas" rows="3" required
                                        placeholder="Descreva as orientações fornecidas à gestante e família..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Sinais Vitais -->
                        <div class="form-section">
                            <h6><i class="fas fa-stethoscope me-2"></i>Sinais Vitais (Opcional)</h6>
                            <div class="vital-signs-grid">
                                <div>
                                    <label for="pressao_arterial" class="form-label">Pressão Arterial</label>
                                    <input type="text" class="form-control" id="pressao_arterial"
                                        placeholder="Ex: 120/80 mmHg">
                                </div>
                                <div>
                                    <label for="frequencia_cardiaca" class="form-label">Freq. Cardíaca</label>
                                    <input type="text" class="form-control" id="frequencia_cardiaca"
                                        placeholder="Ex: 80 bpm">
                                </div>
                                <div>
                                    <label for="temperatura" class="form-label">Temperatura</label>
                                    <input type="text" class="form-control" id="temperatura"
                                        placeholder="Ex: 36.5°C">
                                </div>
                                <div>
                                    <label for="peso" class="form-label">Peso</label>
                                    <input type="text" class="form-control" id="peso" placeholder="Ex: 65 kg">
                                </div>
                            </div>
                        </div>

                        <!-- Materiais Entregues -->
                        <div class="form-section">
                            <h6><i class="fas fa-box me-2"></i>Materiais Entregues</h6>
                            <div class="materials-grid">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_educativo"
                                        name="materiais[]" value="Material Educativo">
                                    <label class="form-check-label" for="material_educativo">
                                        <i class="fas fa-book me-1"></i> Material Educativo
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cartao_gestante"
                                        name="materiais[]" value="Cartão da Gestante">
                                    <label class="form-check-label" for="cartao_gestante">
                                        <i class="fas fa-id-card me-1"></i> Cartão da Gestante
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="suplementos" name="materiais[]"
                                        value="Suplementos">
                                    <label class="form-check-label" for="suplementos">
                                        <i class="fas fa-pills me-1"></i> Suplementos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="preservativos"
                                        name="materiais[]" value="Preservativos">
                                    <label class="form-check-label" for="preservativos">
                                        <i class="fas fa-shield-alt me-1"></i> Preservativos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="medicamentos" name="materiais[]"
                                        value="Medicamentos">
                                    <label class="form-check-label" for="medicamentos">
                                        <i class="fas fa-prescription-bottle me-1"></i> Medicamentos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="outros_materiais"
                                        name="materiais[]" value="Outros">
                                    <label class="form-check-label" for="outros_materiais">
                                        <i class="fas fa-plus me-1"></i> Outros
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="form-section">
                            <h6><i class="fas fa-plus-circle me-2"></i>Informações Adicionais</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="acompanhante_presente"
                                            name="acompanhante_presente" value="1">
                                        <label class="form-check-label" for="acompanhante_presente">
                                            <i class="fas fa-users me-1"></i> Acompanhante Presente
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="necessita_referencia"
                                            name="necessita_referencia" value="1">
                                        <label class="form-check-label" for="necessita_referencia">
                                            <i class="fas fa-hospital me-1"></i> Necessita Referência Médica
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="proxima_visita" class="form-label">Próxima Visita (Opcional)</label>
                                    <input type="date" class="form-control" id="proxima_visita"
                                        name="proxima_visita">
                                    <div class="form-text">Se necessário agendar nova visita</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="hideCompleteForm()">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Completar Visita
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Detalhes da Visita Realizada -->
            @if ($homeVisit->status == 'realizada')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Detalhes da Visita Realizada
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @if ($homeVisit->observacoes_ambiente)
                                    <div class="mb-3">
                                        <strong class="text-muted">Observações do Ambiente:</strong>
                                        <p class="mb-0">{{ $homeVisit->observacoes_ambiente }}</p>
                                    </div>
                                @endif

                                @if ($homeVisit->condicoes_higiene)
                                    <div class="mb-3">
                                        <strong class="text-muted">Condições de Higiene:</strong>
                                        @php
                                            $higieneClass = match ($homeVisit->condicoes_higiene) {
                                                'bom' => 'text-success',
                                                'regular' => 'text-warning',
                                                'ruim' => 'text-danger',
                                                default => 'text-muted',
                                            };
                                        @endphp
                                        <p class="mb-0 {{ $higieneClass }}">
                                            <strong>{{ ucfirst($homeVisit->condicoes_higiene) }}</strong>
                                        </p>
                                    </div>
                                @endif

                                @if ($homeVisit->apoio_familiar)
                                    <div class="mb-3">
                                        <strong class="text-muted">Apoio Familiar:</strong>
                                        @php
                                            $apoioClass = match ($homeVisit->apoio_familiar) {
                                                'adequado' => 'text-success',
                                                'parcial' => 'text-warning',
                                                'inadequado' => 'text-danger',
                                                default => 'text-muted',
                                            };
                                        @endphp
                                        <p class="mb-0 {{ $apoioClass }}">
                                            <strong>{{ ucfirst($homeVisit->apoio_familiar) }}</strong>
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                @if ($homeVisit->orientacoes_dadas)
                                    <div class="mb-3">
                                        <strong class="text-muted">Orientações Dadas:</strong>
                                        <p class="mb-0">{{ $homeVisit->orientacoes_dadas }}</p>
                                    </div>
                                @endif

                                @if ($homeVisit->estado_nutricional)
                                    <div class="mb-3">
                                        <strong class="text-muted">Estado Nutricional:</strong>
                                        <p class="mb-0">{{ $homeVisit->estado_nutricional }}</p>
                                    </div>
                                @endif

                                @if ($homeVisit->queixas_principais)
                                    <div class="mb-3">
                                        <strong class="text-muted">Queixas Principais:</strong>
                                        <p class="mb-0">{{ $homeVisit->queixas_principais }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Alertas especiais -->
                        <div class="row mt-3">
                            @if ($homeVisit->necessita_referencia)
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Atenção:</strong> Esta gestante necessita referência médica especializada.
                                    </div>
                                </div>
                            @endif

                            @if ($homeVisit->acompanhante_presente)
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-users me-2"></i>
                                        <strong>Informação:</strong> Acompanhante estava presente durante a visita.
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sinais Vitais -->
                        @if ($homeVisit->sinais_vitais)
                            <div class="mt-4">
                                <h6 class="border-bottom pb-2">Sinais Vitais</h6>
                                <div class="row">
                                    @foreach (json_decode($homeVisit->sinais_vitais, true) as $sinal => $valor)
                                        <div class="col-md-3 mb-2">
                                            <strong
                                                class="text-muted">{{ ucfirst(str_replace('_', ' ', $sinal)) }}:</strong>
                                            <p class="mb-0">{{ $valor }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Materiais Entregues -->
                        @if ($homeVisit->materiais_entregues)
                            <div class="mt-4">
                                <h6 class="border-bottom pb-2">Materiais Entregues</h6>
                                <ul class="list-unstyled">
                                    @foreach (json_decode($homeVisit->materiais_entregues, true) as $material)
                                        <li class="mb-1">
                                            <i class="fas fa-check text-success me-2"></i>
                                            {{ $material }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Outras Visitas desta Gestante -->
            @if ($outrasVisitas->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history text-muted me-2"></i>
                            Outras Visitas desta Gestante
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th>Responsável</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($outrasVisitas as $visita)
                                        <tr>
                                            <td>{{ $visita->data_visita->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $visita->tipo_visita)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match ($visita->status) {
                                                        'agendada' => 'bg-warning',
                                                        'realizada' => 'bg-success',
                                                        'reagendada' => 'bg-info',
                                                        'nao_encontrada' => 'bg-secondary',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $visita->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $visita->user->name }}</td>
                                            <td>
                                                <a href="{{ route('home_visits.show', $visita) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Painel Lateral -->
        <div class="col-md-4">
            <!-- Ações -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Ações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($homeVisit->status == 'agendada')
                            @if ($homeVisit->canBeCompleted())
                                <button class="btn btn-success" onclick="showCompleteForm()">
                                    <i class="fas fa-check me-1"></i> Completar Visita
                                </button>
                            @endif

                            <a href="{{ route('home_visits.edit', $homeVisit) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>

                            <button class="btn btn-outline-warning" onclick="showRescheduleForm()">
                                <i class="fas fa-calendar me-1"></i> Reagendar
                            </button>

                            <form method="POST" action="{{ route('home_visits.mark-not-found', $homeVisit) }}"
                                class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline-info w-100"
                                    onclick="return confirm('Marcar como não encontrada?')">
                                    <i class="fas fa-question-circle me-1"></i> Não Encontrada
                                </button>
                            </form>
                        @else
                            <a href="{{ route('home_visits.create', ['patient_id' => $homeVisit->patient_id]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Nova Visita
                            </a>
                        @endif

                        <a href="{{ route('home_visits.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
                        </a>

                        @if ($homeVisit->status != 'realizada')
                            <form method="POST" action="{{ route('home_visits.destroy', $homeVisit) }}"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Tem certeza que deseja excluir esta visita?')">
                                    <i class="fas fa-trash me-1"></i> Excluir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-muted me-2"></i>
                        Informações Adicionais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted">Criada em:</strong>
                        <p class="mb-0 small">{{ $homeVisit->created_at->format('d/m/Y H:i') }}</p>
                        <small class="text-muted">{{ $homeVisit->created_at->diffForHumans() }}</small>
                    </div>

                    @if ($homeVisit->updated_at != $homeVisit->created_at)
                        <div class="mb-3">
                            <strong class="text-muted">Última atualização:</strong>
                            <p class="mb-0 small">{{ $homeVisit->updated_at->format('d/m/Y H:i') }}</p>
                            <small class="text-muted">{{ $homeVisit->updated_at->diffForHumans() }}</small>
                        </div>
                    @endif

                    @if ($homeVisit->coordenadas_gps)
                        <div class="mb-3">
                            <strong class="text-muted">Localização GPS:</strong>
                            @php $coords = json_decode($homeVisit->coordenadas_gps, true); @endphp
                            <p class="mb-0 small">
                                Lat: {{ $coords['latitude'] ?? 'N/A' }}<br>
                                Lng: {{ $coords['longitude'] ?? 'N/A' }}
                            </p>
                            @if (isset($coords['latitude']) && isset($coords['longitude']))
                                <a href="https://maps.google.com/?q={{ $coords['latitude'] }},{{ $coords['longitude'] }}"
                                    target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                    <i class="fas fa-map-marker-alt me-1"></i> Ver no Mapa
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Próximas Ações -->
            @if ($homeVisit->status == 'realizada' && $homeVisit->proxima_visita)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Próxima Visita Agendada
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Data:</strong> {{ \Carbon\Carbon::parse($homeVisit->proxima_visita)->format('d/m/Y') }}
                        </p>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($homeVisit->proxima_visita)->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Reagendamento -->
    <!-- Modal de Reagendamento - Versão Corrigida -->
    @if ($homeVisit->status == 'agendada')
        <div class="modal fade modal-custom" id="rescheduleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-content-custom">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Reagendar Visita
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('home_visits.reschedule', $homeVisit) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nova_data" class="form-label required">Nova Data e Hora</label>
                                <input type="datetime-local" class="form-control" id="nova_data" name="nova_data"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="motivo_reagendamento" class="form-label required">Motivo do
                                    Reagendamento</label>
                                <textarea class="form-control" id="motivo_reagendamento" name="motivo_reagendamento" rows="3" required
                                    placeholder="Explique o motivo do reagendamento..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-calendar-check me-1"></i> Confirmar Reagendamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date for reschedule to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowString = tomorrow.toISOString().slice(0, 16);

            const novaDataInput = document.getElementById('nova_data');
            if (novaDataInput) {
                novaDataInput.min = tomorrowString;
            }

            // Set minimum date for next visit to tomorrow
            const proximaVisitaInput = document.getElementById('proxima_visita');
            if (proximaVisitaInput) {
                proximaVisitaInput.min = tomorrow.toISOString().slice(0, 10);
            }

            // Handle complete visit form submission
            const completeForm = document.querySelector('#completeVisitForm form');

            if (completeForm) {
                completeForm.addEventListener('submit', function() {
                    const sinaisVitais = {};
                    let hasVitalSigns = false;

                    // Collect vital signs
                    const vitalSignsInputs = ['pressao_arterial', 'frequencia_cardiaca', 'temperatura',
                        'peso'
                    ];
                    vitalSignsInputs.forEach(function(inputId) {
                        const input = document.getElementById(inputId);
                        if (input && input.value.trim()) {
                            sinaisVitais[inputId] = input.value.trim();
                            hasVitalSigns = true;
                        }
                    });

                    if (hasVitalSigns) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'sinais_vitais';
                        hiddenInput.value = JSON.stringify(sinaisVitais);
                        this.appendChild(hiddenInput);
                    }

                    // Handle materials
                    const selectedMaterials = Array.from(document.querySelectorAll(
                            'input[name="materiais[]"]:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedMaterials.length > 0) {
                        const materialsInput = document.createElement('input');
                        materialsInput.type = 'hidden';
                        materialsInput.name = 'materiais_entregues';
                        materialsInput.value = JSON.stringify(selectedMaterials);
                        this.appendChild(materialsInput);
                    }
                });
            }

            // Auto-expand textareas
            document.querySelectorAll('textarea').forEach(function(textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        });

        // Functions to show/hide complete form
        function showCompleteForm() {
            const form = document.getElementById('completeVisitForm');
            form.classList.add('show');
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function hideCompleteForm() {
            const form = document.getElementById('completeVisitForm');
            form.classList.remove('show');
        }
        // Corrige o z-index dos modais
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function() {
                // Garante que o backdrop tenha z-index correto
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    backdrop.style.zIndex = '1050';
                });

                // Garante que o modal tenha z-index acima do backdrop
                this.style.zIndex = '1060';
            });
        });

        // Função para mostrar o modal de reagendamento
        function showRescheduleForm() {
            const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'), {
                backdrop: 'static', // Impede fechar clicando no backdrop
                keyboard: false // Impede fechar com ESC
            });

            // Configura a data mínima para amanhã
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowString = tomorrow.toISOString().slice(0, 16);
            document.getElementById('nova_data').min = tomorrowString;

            // Mostra o modal
            modal.show();

            // Foca no primeiro campo
            setTimeout(() => {
                document.getElementById('nova_data').focus();
            }, 500);
        }
    </script>
@endpush
