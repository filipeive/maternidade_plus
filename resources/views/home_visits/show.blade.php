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
        /* Formulários */
        .complete-visit-form,
        .reschedule-visit-form {
            display: none;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            animation: slideDown 0.3s ease-in-out;
        }

        .complete-visit-form {
            background: #f8f9fa;
            border: 2px solid #28a745;
        }

        .reschedule-visit-form {
            background: #fff9e6;
            border: 2px solid #ffc107;
        }

        /* Exibição quando ativo */
        .complete-visit-form.show,
        .reschedule-visit-form.show {
            display: block;
        }

        /* Animação */
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

        /* Seções de formulário */
        .form-section {
            background: #fff;
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

        .reschedule-form-section h6 {
            color: #ffc107;
            border-bottom: 2px solid #ffc107;
        }

        /* Mensagens de erro */
        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
        }

        .is-invalid~.invalid-feedback {
            display: block;
        }

        /* Alertas */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            min-width: 350px;
        }

        /* Grids */
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
    <!-- Container para alertas flutuantes -->
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
            @if ($homeVisit->status == 'agendada' && $homeVisit->canBeCompleted())
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

                    <form method="POST" action="{{ route('home_visits.complete', $homeVisit) }}" id="completeForm">
                        @csrf
                        @method('PUT')

                        <!-- Informações Básicas da Visita -->
                        <div class="form-section">
                            <h6><i class="fas fa-clipboard-list me-2"></i>Informações Básicas da Visita</h6>

                            <div class="mb-3">
                                <label for="observacoes_ambiente" class="form-label required">
                                    <i class="fas fa-home me-1"></i>Observações do Ambiente
                                </label>
                                <textarea class="form-control @error('observacoes_ambiente') is-invalid @enderror" id="observacoes_ambiente"
                                    name="observacoes_ambiente" rows="3" required
                                    placeholder="Descreva as condições do ambiente familiar, estrutura da casa, condições de saneamento...">{{ old('observacoes_ambiente') }}</textarea>
                                @error('observacoes_ambiente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="condicoes_higiene" class="form-label required">
                                        <i class="fas fa-soap me-1"></i>Condições de Higiene
                                    </label>
                                    <select class="form-select @error('condicoes_higiene') is-invalid @enderror"
                                        id="condicoes_higiene" name="condicoes_higiene" required>
                                        <option value="">Selecione...</option>
                                        <option value="bom" {{ old('condicoes_higiene') == 'bom' ? 'selected' : '' }}>
                                            Bom</option>
                                        <option value="regular"
                                            {{ old('condicoes_higiene') == 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="ruim" {{ old('condicoes_higiene') == 'ruim' ? 'selected' : '' }}>
                                            Ruim</option>
                                    </select>
                                    @error('condicoes_higiene')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="apoio_familiar" class="form-label required">
                                        <i class="fas fa-users me-1"></i>Apoio Familiar
                                    </label>
                                    <select class="form-select @error('apoio_familiar') is-invalid @enderror"
                                        id="apoio_familiar" name="apoio_familiar" required>
                                        <option value="">Selecione...</option>
                                        <option value="adequado"
                                            {{ old('apoio_familiar') == 'adequado' ? 'selected' : '' }}>Adequado</option>
                                        <option value="parcial"
                                            {{ old('apoio_familiar') == 'parcial' ? 'selected' : '' }}>Parcial</option>
                                        <option value="inadequado"
                                            {{ old('apoio_familiar') == 'inadequado' ? 'selected' : '' }}>Inadequado
                                        </option>
                                    </select>
                                    @error('apoio_familiar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="orientacoes_dadas" class="form-label required">
                                    <i class="fas fa-lightbulb me-1"></i>Orientações Dadas
                                </label>
                                <textarea class="form-control @error('orientacoes_dadas') is-invalid @enderror" id="orientacoes_dadas"
                                    name="orientacoes_dadas" rows="3" required
                                    placeholder="Descreva detalhadamente as orientações fornecidas à gestante e família...">{{ old('orientacoes_dadas') }}</textarea>
                                @error('orientacoes_dadas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informações Clínicas -->
                        <div class="form-section">
                            <h6><i class="fas fa-notes-medical me-2"></i>Informações Clínicas (Opcional)</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="estado_nutricional" class="form-label">Estado Nutricional</label>
                                    <textarea class="form-control @error('estado_nutricional') is-invalid @enderror" id="estado_nutricional"
                                        name="estado_nutricional" rows="2" placeholder="Observações sobre o estado nutricional da gestante...">{{ old('estado_nutricional') }}</textarea>
                                    @error('estado_nutricional')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="queixas_principais" class="form-label">Queixas Principais</label>
                                    <textarea class="form-control @error('queixas_principais') is-invalid @enderror" id="queixas_principais"
                                        name="queixas_principais" rows="2" placeholder="Principais queixas relatadas pela gestante...">{{ old('queixas_principais') }}</textarea>
                                    @error('queixas_principais')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sinais Vitais - CORRIGIDO -->
                        <div class="form-section">
                            <h6><i class="fas fa-stethoscope me-2"></i>Sinais Vitais (Opcional)</h6>
                            <div class="vital-signs-grid">
                                <div>
                                    <label for="pressao_arterial" class="form-label">Pressão Arterial</label>
                                    <input type="text"
                                        class="form-control @error('sinais_vitais.pressao_arterial') is-invalid @enderror"
                                        name="sinais_vitais[pressao_arterial]" id="pressao_arterial"
                                        value="{{ old('sinais_vitais.pressao_arterial') }}"
                                        placeholder="Ex: 120/80 mmHg">
                                    @error('sinais_vitais.pressao_arterial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="frequencia_cardiaca" class="form-label">Freq. Cardíaca</label>
                                    <input type="text"
                                        class="form-control @error('sinais_vitais.frequencia_cardiaca') is-invalid @enderror"
                                        name="sinais_vitais[frequencia_cardiaca]" id="frequencia_cardiaca"
                                        value="{{ old('sinais_vitais.frequencia_cardiaca') }}" placeholder="Ex: 80 bpm">
                                    @error('sinais_vitais.frequencia_cardiaca')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="temperatura" class="form-label">Temperatura</label>
                                    <input type="text"
                                        class="form-control @error('sinais_vitais.temperatura') is-invalid @enderror"
                                        name="sinais_vitais[temperatura]" id="temperatura"
                                        value="{{ old('sinais_vitais.temperatura') }}" placeholder="Ex: 36.5°C">
                                    @error('sinais_vitais.temperatura')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="peso" class="form-label">Peso</label>
                                    <input type="text"
                                        class="form-control @error('sinais_vitais.peso') is-invalid @enderror"
                                        name="sinais_vitais[peso]" id="peso"
                                        value="{{ old('sinais_vitais.peso') }}" placeholder="Ex: 65 kg">
                                    @error('sinais_vitais.peso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Materiais Entregues - CORRIGIDO -->
                        <div class="form-section">
                            <h6><i class="fas fa-box me-2"></i>Materiais Entregues</h6>
                            <div class="materials-grid">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Material Educativo" id="material1"
                                        {{ in_array('Material Educativo', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material1">
                                        <i class="fas fa-book me-1"></i> Material Educativo
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Cartão da Gestante" id="material2"
                                        {{ in_array('Cartão da Gestante', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material2">
                                        <i class="fas fa-id-card me-1"></i> Cartão da Gestante
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Suplementos" id="material3"
                                        {{ in_array('Suplementos', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material3">
                                        <i class="fas fa-pills me-1"></i> Suplementos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Preservativos" id="material4"
                                        {{ in_array('Preservativos', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material4">
                                        <i class="fas fa-shield-alt me-1"></i> Preservativos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Medicamentos" id="material5"
                                        {{ in_array('Medicamentos', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material5">
                                        <i class="fas fa-prescription-bottle me-1"></i> Medicamentos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Kit Higiene" id="material6"
                                        {{ in_array('Kit Higiene', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material6">
                                        <i class="fas fa-soap me-1"></i> Kit Higiene
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="materiais_entregues[]"
                                        value="Outros" id="material7"
                                        {{ in_array('Outros', old('materiais_entregues', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="material7">
                                        <i class="fas fa-plus me-1"></i> Outros
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="form-section">
                            <h6><i class="fas fa-plus-circle me-2"></i>Informações Adicionais</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="acompanhante_presente"
                                            value="1" id="acompanhante"
                                            {{ old('acompanhante_presente') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="acompanhante">
                                            <i class="fas fa-user-friends me-1"></i> Acompanhante presente durante a visita
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="necessita_referencia"
                                            value="1" id="referencia"
                                            {{ old('necessita_referencia') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="referencia">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Necessita referência médica
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="proxima_visita" class="form-label">
                                    <i class="fas fa-calendar-plus me-1"></i>Próxima Visita (Opcional)
                                </label>
                                <input type="date" class="form-control @error('proxima_visita') is-invalid @enderror"
                                    id="proxima_visita" name="proxima_visita" value="{{ old('proxima_visita') }}">
                                <small class="form-text text-muted">Deixe em branco se não houver necessidade de nova
                                    visita</small>
                                @error('proxima_visita')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="hideCompleteForm()">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" id="submitCompleteBtn">
                                <i class="fas fa-check-circle me-1"></i> Completar Visita
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Formulário para Reagendar Visita (Oculto inicialmente) -->
            @if ($homeVisit->status == 'agendada')
                <div id="rescheduleVisitForm" class="reschedule-visit-form">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-warning mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Reagendar Visita Domiciliária
                        </h4>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideRescheduleForm()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>

                    <form method="POST" action="{{ route('home_visits.reschedule', $homeVisit) }}" id="rescheduleForm">
                        @csrf
                        @method('PUT')
                        <div class="form-section reschedule-form-section">
                            <h6><i class="fas fa-calendar-day me-2"></i>Nova Data e Hora</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nova_data_visita" class="form-label required">Nova Data</label>
                                    <input type="date"
                                        class="form-control @error('nova_data_visita') is-invalid @enderror"
                                        id="nova_data_visita" name="nova_data_visita" required
                                        value="{{ old('nova_data_visita') }}">
                                    @error('nova_data_visita')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nova_hora_visita" class="form-label required">Nova Hora</label>
                                    <input type="time"
                                        class="form-control @error('nova_hora_visita') is-invalid @enderror"
                                        id="nova_hora_visita" name="nova_hora_visita" required
                                        value="{{ old('nova_hora_visita') }}">
                                    @error('nova_hora_visita')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section reschedule-form-section">
                            <h6><i class="fas fa-comment-dots me-2"></i>Motivo do Reagendamento</h6>
                            <div class="mb-3">
                                <label for="motivo_reagendamento" class="form-label required">Descreva o motivo</label>
                                <textarea class="form-control @error('motivo_reagendamento') is-invalid @enderror" id="motivo_reagendamento"
                                    name="motivo_reagendamento" rows="3" required
                                    placeholder="Explique detalhadamente o motivo do reagendamento...">{{ old('motivo_reagendamento') }}</textarea>
                                @error('motivo_reagendamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="hideRescheduleForm()">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning" id="submitRescheduleBtn">
                                <i class="fas fa-calendar-check me-1"></i> Confirmar Reagendamento
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
                        @if ($homeVisit->sinais_vitais && is_array($homeVisit->sinais_vitais) && count($homeVisit->sinais_vitais) > 0)
                            <div class="mt-4">
                                <h6 class="border-bottom pb-2">Sinais Vitais</h6>
                                <div class="row">
                                    @foreach ($homeVisit->sinais_vitais as $sinal => $valor)
                                        @if (!empty($valor))
                                            <div class="col-md-3 mb-2">
                                                <strong
                                                    class="text-muted">{{ ucfirst(str_replace('_', ' ', $sinal)) }}:</strong>
                                                <p class="mb-0">{{ $valor }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Materiais Entregues -->
                        @if (
                            $homeVisit->materiais_entregues &&
                                is_array($homeVisit->materiais_entregues) &&
                                count($homeVisit->materiais_entregues) > 0)
                            <div class="mt-4">
                                <h6 class="border-bottom pb-2">Materiais Entregues</h6>
                                <ul class="list-unstyled">
                                    @foreach ($homeVisit->materiais_entregues as $material)
                                        @if (!empty($material))
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success me-2"></i>
                                                {{ $material }}
                                            </li>
                                        @endif
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
            <div class="alert-container" style="position: sticky; top: 0; right: 0; z-index: 1000; width: 100%;">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Erros encontrados:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ======= CONFIGURAÇÃO DE DATAS =======
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowString = tomorrow.toISOString().slice(0, 10);

            // Configurar datas mínimas
            const novaDataInput = document.getElementById('nova_data_visita');
            if (novaDataInput) novaDataInput.min = tomorrowString;

            const proximaVisitaInput = document.getElementById('proxima_visita');
            if (proximaVisitaInput) proximaVisitaInput.min = tomorrowString;

            // ======= FORMULÁRIO COMPLETAR VISITA =======
            const completeForm = document.getElementById('completeForm');
            if (completeForm) {
                completeForm.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitCompleteBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';
                    }

                    // Processar sinais vitais
                    const sinaisVitais = {};
                    const vitalInputs = ['pressao_arterial', 'frequencia_cardiaca', 'temperatura', 'peso'];

                    vitalInputs.forEach(input => {
                        const element = document.getElementById(input);
                        if (element && element.value.trim()) {
                            sinaisVitais[input] = element.value.trim();
                        }
                    });

                    // Adicionar sinais vitais ao formulário se houver dados
                    if (Object.keys(sinaisVitais).length > 0) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'sinais_vitais_json';
                        hiddenInput.value = JSON.stringify(sinaisVitais);
                        completeForm.appendChild(hiddenInput);
                    }

                    // Processar materiais entregues
                    const materiaisCheckboxes = document.querySelectorAll(
                        'input[name="materiais_entregues[]"]:checked');
                    const materiaisEntregues = Array.from(materiaisCheckboxes).map(cb => cb.value);

                    // Se nenhum material selecionado, garantir que seja enviado como array vazio
                    if (materiaisEntregues.length === 0) {
                        const emptyInput = document.createElement('input');
                        emptyInput.type = 'hidden';
                        emptyInput.name = 'materiais_entregues';
                        emptyInput.value = '[]';
                        completeForm.appendChild(emptyInput);
                    }
                });
            }

            // ======= VALIDAÇÃO REAGENDAMENTO =======
            const rescheduleForm = document.getElementById('rescheduleForm');
            if (rescheduleForm) {
                rescheduleForm.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitRescheduleBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';
                    }

                    let isValid = true;
                    const dataVisita = document.getElementById('nova_data_visita');
                    const horaVisita = document.getElementById('nova_hora_visita');
                    const motivo = document.getElementById('motivo_reagendamento');

                    // Limpar erros anteriores
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                        'is-invalid'));

                    // Validar data
                    if (!dataVisita || !dataVisita.value) {
                        showError('nova_data_visita', 'Por favor, selecione uma data.');
                        isValid = false;
                    } else {
                        const selectedDate = new Date(dataVisita.value);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        if (selectedDate < today) {
                            showError('nova_data_visita', 'A data deve ser no futuro.');
                            isValid = false;
                        }
                    }

                    // Validar hora
                    if (!horaVisita || !horaVisita.value) {
                        showError('nova_hora_visita', 'Por favor, selecione um horário.');
                        isValid = false;
                    }

                    // Validar motivo
                    if (!motivo || !motivo.value.trim() || motivo.value.trim().length < 10) {
                        showError('motivo_reagendamento',
                            'Por favor, forneça um motivo detalhado (mínimo 10 caracteres).');
                        isValid = false;
                    }

                    if (!isValid) {
                        e.preventDefault();
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                '<i class="fas fa-calendar-check me-1"></i> Confirmar Reagendamento';
                        }
                        showAlert('Por favor, corrija os erros no formulário.', 'danger');

                        // Focar no primeiro campo com erro
                        const firstError = document.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.focus();
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                });
            }

            // ======= AUTO-EXPAND TEXTAREAS =======
            document.querySelectorAll('textarea').forEach(function(textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });
        });

        // ======= FUNÇÕES GLOBAIS =======
        function showCompleteForm() {
            console.log('Mostrando formulário de completar'); // Debug
            hideRescheduleForm();
            const form = document.getElementById('completeVisitForm');
            if (form) {
                form.classList.add('show');
                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Configurar data mínima para próxima visita
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                const tomorrowString = tomorrow.toISOString().slice(0, 10);
                const proximaVisitaInput = document.getElementById('proxima_visita');
                if (proximaVisitaInput) proximaVisitaInput.min = tomorrowString;
            } else {
                console.error('Formulário completeVisitForm não encontrado'); // Debug
            }
        }

        function hideCompleteForm() {
            console.log('Escondendo formulário de completar'); // Debug
            const form = document.getElementById('completeVisitForm');
            if (form) {
                form.classList.remove('show');

                // Limpar validações visuais
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // Reset do botão
                const submitBtn = document.getElementById('submitCompleteBtn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Completar Visita';
                }
            }
        }

        function showRescheduleForm() {
            console.log('Mostrando formulário de reagendar'); // Debug
            hideCompleteForm();
            const form = document.getElementById('rescheduleVisitForm');
            if (form) {
                form.classList.add('show');

                // Configurar data mínima
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                const tomorrowString = tomorrow.toISOString().slice(0, 10);
                const novaDataInput = document.getElementById('nova_data_visita');
                if (novaDataInput) novaDataInput.min = tomorrowString;

                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Focar no primeiro campo se houver erro
                const firstError = document.querySelector('.is-invalid');
                if (firstError) firstError.focus();
            } else {
                console.error('Formulário rescheduleVisitForm não encontrado'); // Debug
            }
        }

        function hideRescheduleForm() {
            console.log('Escondendo formulário de reagendar'); // Debug
            const form = document.getElementById('rescheduleVisitForm');
            if (form) {
                form.classList.remove('show');

                // Limpar validações visuais
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

                // Reset do botão
                const submitBtn = document.getElementById('submitRescheduleBtn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-calendar-check me-1"></i> Confirmar Reagendamento';
                }
            }
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('is-invalid');
                let errorDiv = field.nextElementSibling;

                // Se não existe div de erro, criar
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.classList.add('invalid-feedback');
                    field.parentNode.insertBefore(errorDiv, field.nextSibling);
                }

                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        }

        function showAlert(message, type = 'success') {
            // Remover alertas existentes
            document.querySelectorAll('.alert-floating').forEach(alert => alert.remove());

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show alert-floating`;
            alert.style.cssText =
                'position: fixed; top: 20px; right: 20px; z-index: 1100; min-width: 350px; max-width: 500px;';

            const icon = type === 'success' ? 'fa-check-circle' :
                type === 'danger' ? 'fa-exclamation-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

            alert.innerHTML = `
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            document.body.appendChild(alert);

            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        if (alert.parentNode) alert.remove();
                    }, 150);
                }
            }, 5000);
        }

        // ======= DEBUG - REMOVER DEPOIS DE TESTAR =======
        // Verificar se os elementos existem ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Verificando elementos:');
            console.log('completeVisitForm:', document.getElementById('completeVisitForm'));
            console.log('rescheduleVisitForm:', document.getElementById('rescheduleVisitForm'));
            console.log('Botões:', {
                completeBtn: document.querySelector('button[onclick="showCompleteForm()"]'),
                rescheduleBtn: document.querySelector('button[onclick="showRescheduleForm()"]')
            });
        });
    </script>
@endpush
