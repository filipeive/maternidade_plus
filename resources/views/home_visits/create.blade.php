@extends('layouts.app')

@section('title', 'Agendar Visita Domiciliária')
@section('page-title', 'Agendar Nova Visita Domiciliária')
@section('title-icon', 'fa-calendar-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a></li>
    <li class="breadcrumb-item active">Agendar Visita</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                        Dados da Visita
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('home_visits.store') }}">
                        @csrf

                        <!-- Seleção da Gestante -->
                        <div class="mb-4">
                            <label for="patient_id" class="form-label required">Gestante</label>
                            @if ($patient)
                                <!-- Gestante já selecionada -->
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-pink text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 50px; height: 50px; background-color: #e91e63;">
                                                <i class="fas fa-female fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $patient->nome_completo }}</h6>
                                                <small class="text-muted">
                                                    BI: {{ $patient->documento_bi }} |
                                                    Contato: {{ $patient->contacto }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $patient->endereco }}
                                                </small>
                                            </div>
                                            <a href="{{ route('home_visits.create') }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-exchange-alt"></i> Trocar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            @else
                                <!-- Seletor de gestante -->
                                <select class="form-select select2" id="patient_id" name="patient_id" required>
                                    <option value="">Selecione a gestante...</option>
                                    @foreach ($patients as $patientOption)
                                        <option value="{{ $patientOption->id }}"
                                            data-endereco="{{ $patientOption->endereco }}"
                                            data-contacto="{{ $patientOption->contacto }}">
                                            {{ $patientOption->nome_completo }} - BI: {{ $patientOption->documento_bi }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('patient_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dados da Visita -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_visita" class="form-label required">Data e Hora da Visita</label>
                                <input type="datetime-local" class="form-control @error('data_visita') is-invalid @enderror"
                                    id="data_visita" name="data_visita" value="{{ old('data_visita') }}" required>
                                @error('data_visita')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_visita" class="form-label required">Tipo de Visita</label>
                                <select class="form-select @error('tipo_visita') is-invalid @enderror" id="tipo_visita"
                                    name="tipo_visita" required>
                                    <option value="">Selecione o tipo...</option>
                                    @if (!is_array($tiposVisita) && !$tiposVisita instanceof \Illuminate\Support\Collection)
                                        @php
                                            $tiposVisita = [
                                                'rotina' => 'Visita de Rotina',
                                                'pos_parto' => 'Visita Pós-parto',
                                                'alto_risco' => 'Visita de Alto Risco',
                                                'faltosa' => 'Visita a Gestante Faltosa',
                                                'emergencia' => 'Visita de Emergência',
                                                'educacao' => 'Visita Educativa',
                                                'seguimento' => 'Visita de Seguimento',
                                            ];
                                        @endphp
                                    @endif
                                    @foreach ($tiposVisita as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('tipo_visita') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_visita')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="motivo_visita" class="form-label required">Motivo da Visita</label>
                            <textarea class="form-control @error('motivo_visita') is-invalid @enderror" id="motivo_visita" name="motivo_visita"
                                rows="3" required placeholder="Descreva o motivo da visita domiciliária...">{{ old('motivo_visita') }}</textarea>
                            @error('motivo_visita')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="endereco_visita" class="form-label">Endereço da Visita</label>
                            <textarea class="form-control @error('endereco_visita') is-invalid @enderror" id="endereco_visita"
                                name="endereco_visita" rows="2"
                                placeholder="Endereço onde será realizada a visita (deixe em branco para usar o endereço da gestante)">{{ old('endereco_visita', $patient->endereco ?? '') }}</textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Se não preenchido, será usado o endereço cadastrado da gestante.
                            </div>
                            @error('endereco_visita')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="observacoes_gerais" class="form-label">Observações Gerais</label>
                            <textarea class="form-control @error('observacoes_gerais') is-invalid @enderror" id="observacoes_gerais"
                                name="observacoes_gerais" rows="3" placeholder="Observações adicionais sobre a visita...">{{ old('observacoes_gerais') }}</textarea>
                            @error('observacoes_gerais')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('home_visits.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-1"></i> Agendar Visita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Painel Lateral com Informações -->
        <div class="col-md-4">
            <!-- Informações da Gestante Selecionada -->
            <div class="card border-0 shadow-sm mb-4" id="patient-info" style="display: none;">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Informações da Gestante
                    </h6>
                </div>
                <div class="card-body" id="patient-details">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>

            <!-- Dicas e Orientações -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Dicas para Visita Domiciliária
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Confirme o endereço antes de sair</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Leve material educativo</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Verifique equipamentos necessários</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Respeite a privacidade da família</small>
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Documente todas as observações</small>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tipos de Visita - Referência -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Tipos de Visita
                    </h6>
                </div>
                <div class="card-body">
                    <div class="accordion accordion-flush" id="tiposVisitaAccordion">
                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#rotina">
                                    <span class="badge bg-primary me-2">Rotina</span>
                                </button>
                            </h6>
                            <div id="rotina" class="accordion-collapse collapse"
                                data-bs-parent="#tiposVisitaAccordion">
                                <div class="accordion-body">
                                    <small>Visita de acompanhamento regular da gestação.</small>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#posParto">
                                    <span class="badge bg-success me-2">Pós-parto</span>
                                </button>
                            </h6>
                            <div id="posParto" class="accordion-collapse collapse"
                                data-bs-parent="#tiposVisitaAccordion">
                                <div class="accordion-body">
                                    <small>Acompanhamento no período puerperal.</small>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#altoRisco">
                                    <span class="badge bg-danger me-2">Alto Risco</span>
                                </button>
                            </h6>
                            <div id="altoRisco" class="accordion-collapse collapse"
                                data-bs-parent="#tiposVisitaAccordion">
                                <div class="accordion-body">
                                    <small>Gestante com complicações que requer acompanhamento especial.</small>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h6 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faltosa">
                                    <span class="badge bg-warning me-2">Faltosa</span>
                                </button>
                            </h6>
                            <div id="faltosa" class="accordion-collapse collapse"
                                data-bs-parent="#tiposVisitaAccordion">
                                <div class="accordion-body">
                                    <small>Busca ativa de gestante que faltou às consultas.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to today
            const today = new Date();
            const todayString = today.toISOString().slice(0, 16);
            document.getElementById('data_visita').min = todayString;

            // Patient selection change handler
            const patientSelect = document.getElementById('patient_id');
            const patientInfo = document.getElementById('patient-info');
            const patientDetails = document.getElementById('patient-details');
            const enderecoVisita = document.getElementById('endereco_visita');

            if (patientSelect) {
                patientSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];

                    if (this.value) {
                        const endereco = selectedOption.dataset.endereco;
                        const contacto = selectedOption.dataset.contacto;

                        // Fill address field if empty
                        if (!enderecoVisita.value) {
                            enderecoVisita.value = endereco;
                        }

                        // Show patient info
                        patientDetails.innerHTML = `
                    <div class="mb-2">
                        <strong>Nome:</strong><br>
                        <small class="text-muted">${selectedOption.text.split(' - BI:')[0]}</small>
                    </div>
                    <div class="mb-2">
                        <strong>Documento:</strong><br>
                        <small class="text-muted">${selectedOption.text.split('BI: ')[1]}</small>
                    </div>
                    <div class="mb-2">
                        <strong>Contato:</strong><br>
                        <small class="text-muted">${contacto || 'Não informado'}</small>
                    </div>
                    <div class="mb-0">
                        <strong>Endereço:</strong><br>
                        <small class="text-muted">${endereco || 'Não informado'}</small>
                    </div>
                `;

                        patientInfo.style.display = 'block';
                    } else {
                        patientInfo.style.display = 'none';
                        enderecoVisita.value = '';
                    }
                });
            }

            // Auto-fill suggestions based on visit type
            const tipoVisitaSelect = document.getElementById('tipo_visita');
            const motivoVisita = document.getElementById('motivo_visita');

            const motivoSuggestions = {
                'rotina': 'Acompanhamento de rotina da gestação',
                'pos_parto': 'Acompanhamento pós-parto - verificação da saúde materna e do recém-nascido',
                'alto_risco': 'Acompanhamento de gestação de alto risco',
                'faltosa': 'Busca ativa - gestante faltosa às consultas agendadas',
                'emergencia': 'Visita de emergência',
                'educacao': 'Visita educativa - orientações sobre cuidados na gestação',
                'seguimento': 'Visita de seguimento'
            };

            tipoVisitaSelect.addEventListener('change', function() {
                if (this.value && !motivoVisita.value) {
                    motivoVisita.value = motivoSuggestions[this.value] || '';
                }
            });

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const dataVisita = new Date(document.getElementById('data_visita').value);
                const now = new Date();

                if (dataVisita < now) {
                    e.preventDefault();
                    alert('A data da visita não pode ser no passado.');
                    return false;
                }
            });

            // Initialize Select2 if available
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#patient_id').select2({
                    placeholder: 'Pesquisar gestante...',
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "Nenhuma gestante encontrada";
                        },
                        searching: function() {
                            return "Pesquisando...";
                        }
                    }
                });
            }
        });
    </script>
@endpush
