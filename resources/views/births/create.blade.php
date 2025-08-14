@extends('layouts.app')

@section('title', 'Registrar Parto')
@section('page-title', 'Registro de Parto')

@section('content')
    <div class="row justify-content-center">
        <div class="container-wrapper">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-baby me-2"></i>Registrar Parto
                    </h5>
                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>

                <div class="card-body">
                    <!-- Dados da Gestante -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-user me-2"></i>Gestante: {{ $patient->nome_completo }}</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small><strong>Idade:</strong> {{ $patient->idade }} anos</small>
                            </div>
                            <div class="col-md-4">
                                <small><strong>IG Atual:</strong> {{ $patient->idade_gestacional ?? 'N/A' }} semanas</small>
                            </div>
                            <div class="col-md-4">
                                <small><strong>DPP:</strong>
                                    {{ $patient->data_provavel_parto?->format('d/m/Y') ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('births.store', $patient) }}" method="POST">
                        @csrf

                        <!-- Dados do Parto -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Dados do Parto</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Data e Hora do Parto *</label>
                                            <input type="datetime-local"
                                                class="form-control @error('data_hora_parto') is-invalid @enderror"
                                                name="data_hora_parto"
                                                value="{{ old('data_hora_parto', now()->format('Y-m-d\TH:i')) }}"
                                                max="{{ now()->format('Y-m-d\TH:i') }}" required>
                                            @error('data_hora_parto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Parto *</label>
                                            <select class="form-select @error('tipo_parto') is-invalid @enderror"
                                                name="tipo_parto" required>
                                                <option value="">Selecionar...</option>
                                                <option value="normal"
                                                    {{ old('tipo_parto') == 'normal' ? 'selected' : '' }}>Parto Normal
                                                </option>
                                                <option value="cesariana"
                                                    {{ old('tipo_parto') == 'cesariana' ? 'selected' : '' }}>Cesariana
                                                </option>
                                                <option value="forceps"
                                                    {{ old('tipo_parto') == 'forceps' ? 'selected' : '' }}>Parto com
                                                    Fórceps</option>
                                                <option value="vacuum"
                                                    {{ old('tipo_parto') == 'vacuum' ? 'selected' : '' }}>Parto com Vácuo
                                                </option>
                                                <option value="outros"
                                                    {{ old('tipo_parto') == 'outros' ? 'selected' : '' }}>Outros</option>
                                            </select>
                                            @error('tipo_parto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Local do Parto</label>
                                            <input type="text"
                                                class="form-control @error('local_parto') is-invalid @enderror"
                                                name="local_parto" value="{{ old('local_parto') }}"
                                                placeholder="Ex: Hospital Central de Maputo">
                                            @error('local_parto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Unidade/Departamento</label>
                                            <input type="text"
                                                class="form-control @error('hospital_unidade') is-invalid @enderror"
                                                name="hospital_unidade" value="{{ old('hospital_unidade') }}"
                                                placeholder="Ex: Bloco de Partos">
                                            @error('hospital_unidade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Obstetra Responsável</label>
                                            <input type="text"
                                                class="form-control @error('profissional_obstetra') is-invalid @enderror"
                                                name="profissional_obstetra" value="{{ old('profissional_obstetra') }}">
                                            @error('profissional_obstetra')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Enfermeiro(a) Responsável</label>
                                            <input type="text"
                                                class="form-control @error('profissional_enfermeiro') is-invalid @enderror"
                                                name="profissional_enfermeiro"
                                                value="{{ old('profissional_enfermeiro') }}">
                                            @error('profissional_enfermeiro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do Recém-Nascido -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-baby me-2"></i>Dados do Recém-Nascido</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Sexo</label>
                                            <select class="form-select @error('sexo_bebe') is-invalid @enderror"
                                                name="sexo_bebe">
                                                <option value="">Não informado</option>
                                                <option value="masculino"
                                                    {{ old('sexo_bebe') == 'masculino' ? 'selected' : '' }}>Masculino
                                                </option>
                                                <option value="feminino"
                                                    {{ old('sexo_bebe') == 'feminino' ? 'selected' : '' }}>Feminino
                                                </option>
                                            </select>
                                            @error('sexo_bebe')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Peso (gramas) *</label>
                                            <input type="number"
                                                class="form-control @error('peso_nascimento') is-invalid @enderror"
                                                name="peso_nascimento" value="{{ old('peso_nascimento') }}"
                                                min="300" max="6000" step="10" required
                                                placeholder="Ex: 3200 (em gramas)"
                                                title="Digite o peso do bebê em gramas, entre 300 e 6000">
                                            @error('peso_nascimento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Altura (cm) *</label>
                                            <input type="number"
                                                class="form-control @error('altura_nascimento') is-invalid @enderror"
                                                name="altura_nascimento" value="{{ old('altura_nascimento') }}"
                                                min="25" max="60" step="0.1" required
                                                placeholder="Ex: 48.5">
                                            @error('altura_nascimento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Status do Bebê *</label>
                                            <select class="form-select @error('status_bebe') is-invalid @enderror"
                                                name="status_bebe" required>
                                                <option value="">Selecionar...</option>
                                                <option value="vivo_saudavel"
                                                    {{ old('status_bebe') == 'vivo_saudavel' ? 'selected' : '' }}>Vivo e
                                                    Saudável</option>
                                                <option value="vivo_complicacoes"
                                                    {{ old('status_bebe') == 'vivo_complicacoes' ? 'selected' : '' }}>Vivo
                                                    com Complicações</option>
                                                <option value="obito_fetal"
                                                    {{ old('status_bebe') == 'obito_fetal' ? 'selected' : '' }}>Óbito Fetal
                                                </option>
                                                <option value="obito_neonatal"
                                                    {{ old('status_bebe') == 'obito_neonatal' ? 'selected' : '' }}>Óbito
                                                    Neonatal</option>
                                            </select>
                                            @error('status_bebe')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- APGAR Score -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label">Escala APGAR *</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">1º Minuto</label>
                                            <input type="number"
                                                class="form-control @error('apgar_1min') is-invalid @enderror"
                                                name="apgar_1min" value="{{ old('apgar_1min') }}" min="0"
                                                max="10" required>
                                            @error('apgar_1min')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">5º Minuto</label>
                                            <input type="number"
                                                class="form-control @error('apgar_5min') is-invalid @enderror"
                                                name="apgar_5min" value="{{ old('apgar_5min') }}" min="0"
                                                max="10" required>
                                            @error('apgar_5min')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">10º Minuto (opcional)</label>
                                            <input type="number"
                                                class="form-control @error('apgar_10min') is-invalid @enderror"
                                                name="apgar_10min" value="{{ old('apgar_10min') }}" min="0"
                                                max="10">
                                            @error('apgar_10min')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Parto Múltiplo -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="parto_multiplo"
                                                    id="parto_multiplo" value="1"
                                                    {{ old('parto_multiplo') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="parto_multiplo">
                                                    Parto Múltiplo (gêmeos, trigêmeos, etc.)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Número de Bebês *</label>
                                            <input type="number"
                                                class="form-control @error('numero_bebes') is-invalid @enderror"
                                                name="numero_bebes" value="{{ old('numero_bebes', 1) }}" min="1"
                                                max="5" required>
                                            @error('numero_bebes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Observações e Complicações
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Complicações Maternas</label>
                                            <textarea class="form-control @error('complicacoes_maternas') is-invalid @enderror" name="complicacoes_maternas"
                                                rows="3" placeholder="Descrever complicações durante o trabalho de parto ou parto">{{ old('complicacoes_maternas') }}</textarea>
                                            @error('complicacoes_maternas')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Observações do Recém-Nascido</label>
                                            <textarea class="form-control @error('observacoes_rn') is-invalid @enderror" name="observacoes_rn" rows="3"
                                                placeholder="Observações sobre o estado do recém-nascido">{{ old('observacoes_rn') }}</textarea>
                                            @error('observacoes_rn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Medicamentos Utilizados</label>
                                            <textarea class="form-control @error('medicamentos_utilizados') is-invalid @enderror" name="medicamentos_utilizados"
                                                rows="2" placeholder="Medicamentos administrados durante o parto">{{ old('medicamentos_utilizados') }}</textarea>
                                            @error('medicamentos_utilizados')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Condições Pós-Parto</label>
                                            <textarea class="form-control @error('condicoes_pos_parto') is-invalid @enderror" name="condicoes_pos_parto"
                                                rows="2" placeholder="Estado da mãe no pós-parto imediato">{{ old('condicoes_pos_parto') }}</textarea>
                                            @error('condicoes_pos_parto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Data da Alta Hospitalar</label>
                                            <input type="datetime-local"
                                                class="form-control @error('alta_hospitalar') is-invalid @enderror"
                                                name="alta_hospitalar" value="{{ old('alta_hospitalar') }}">
                                            @error('alta_hospitalar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Observações Gerais</label>
                                            <textarea class="form-control @error('observacoes_gerais') is-invalid @enderror" name="observacoes_gerais"
                                                rows="2" placeholder="Outras observações relevantes">{{ old('observacoes_gerais') }}</textarea>
                                            @error('observacoes_gerais')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Registrar Parto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-ajustar número de bebês quando marcar parto múltiplo
                const partoMultiplo = document.getElementById('parto_multiplo');
                const numeroBebês = document.querySelector('input[name="numero_bebes"]');

                partoMultiplo.addEventListener('change', function() {
                    if (this.checked) {
                        if (numeroBebês.value == 1) {
                            numeroBebês.value = 2;
                        }
                        numeroBebês.min = 2;
                    } else {
                        numeroBebês.value = 1;
                        numeroBebês.min = 1;
                    }
                });

                // Validação do APGAR Score
                const apgarInputs = document.querySelectorAll('input[name^="apgar_"]');
                apgarInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.value > 10) this.value = 10;
                        if (this.value < 0) this.value = 0;
                    });
                });
            });
        </script>
    @endpush
@endsection
