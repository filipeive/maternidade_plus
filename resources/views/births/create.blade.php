@extends('layouts.app-tw')

@section('title', 'Registrar Parto')
@section('page-title', 'Registro de Parto & Desfecho Obstétrico')
@section('title-icon', 'fa-baby')

@section('breadcrumbs')
    <a href="{{ route('patients.index') }}">Gestantes</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('patients.show', $patient) }}">{{ $patient->nome_completo }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Registrar Parto</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Banner de Dados da Gestante --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-50 to-surface-50 border border-brand-200 shadow-2xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-brand-600 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                    <i class="fas fa-person-pregnant"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-surface-900">{{ $patient->nome_completo }}</h3>
                    <p class="text-xs text-surface-500">BI: {{ $patient->documento_bi ?? 'N/A' }} · Tel: {{ $patient->telefone ?? $patient->contacto }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs shrink-0">
                <div class="px-3 py-1.5 bg-white rounded-lg border border-surface-200 text-center">
                    <span class="text-surface-400 block text-2xs uppercase tracking-wider font-semibold">Idade</span>
                    <span class="font-bold text-surface-800">{{ $patient->idade }} anos</span>
                </div>
                <div class="px-3 py-1.5 bg-white rounded-lg border border-surface-200 text-center">
                    <span class="text-surface-400 block text-2xs uppercase tracking-wider font-semibold">IG Atual</span>
                    <span class="font-bold text-brand-700">{{ $patient->idade_gestacional_detalhada ?? ($patient->semanas_gestacao ? $patient->semanas_gestacao . 'ª semana' : 'N/A') }}</span>
                </div>
                <div class="px-3 py-1.5 bg-white rounded-lg border border-surface-200 text-center">
                    <span class="text-surface-400 block text-2xs uppercase tracking-wider font-semibold">DPP</span>
                    <span class="font-bold text-surface-800">{{ $patient->data_provavel_parto?->format('d/m/Y') ?? 'N/A' }}</span>
                </div>
                <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Voltar</span>
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('births.store', $patient) }}" method="POST" class="space-y-6">
        @csrf

        {{-- 1. Dados do Parto --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">1. Dados do Parto</h3>
                </div>
            </div>

            <div class="card-body-tw grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Data e Hora do Parto <span class="text-crimson-500">*</span></label>
                    <input type="datetime-local"
                           class="input-tw @error('data_hora_parto') input-error-tw @enderror"
                           name="data_hora_parto"
                           value="{{ old('data_hora_parto', now()->format('Y-m-d\TH:i')) }}"
                           max="{{ now()->format('Y-m-d\TH:i') }}"
                           required>
                    @error('data_hora_parto')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label-tw">Tipo de Parto <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('tipo_parto') input-error-tw @enderror" name="tipo_parto" required>
                        <option value="">Selecionar tipo de parto...</option>
                        <option value="normal" {{ old('tipo_parto') == 'normal' ? 'selected' : '' }}>Parto Normal (Eutócico)</option>
                        <option value="cesariana" {{ old('tipo_parto') == 'cesariana' ? 'selected' : '' }}>Cesariana</option>
                        <option value="forceps" {{ old('tipo_parto') == 'forceps' ? 'selected' : '' }}>Parto com Fórceps</option>
                        <option value="vacuum" {{ old('tipo_parto') == 'vacuum' ? 'selected' : '' }}>Parto com Vácuo</option>
                        <option value="outros" {{ old('tipo_parto') == 'outros' ? 'selected' : '' }}>Outros</option>
                    </select>
                    @error('tipo_parto')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label-tw">Local do Parto / Unidade Sanitária</label>
                    <input type="text"
                           class="input-tw @error('local_parto') input-error-tw @enderror"
                           name="local_parto"
                           value="{{ old('local_parto', 'Centro de Saúde de Quelimane Urbano') }}"
                           placeholder="Ex: Maternidade / Centro de Saúde de Quelimane">
                    @error('local_parto')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label-tw">Bloco / Departamento</label>
                    <input type="text"
                           class="input-tw @error('hospital_unidade') input-error-tw @enderror"
                           name="hospital_unidade"
                           value="{{ old('hospital_unidade', 'Bloco de Partos') }}"
                           placeholder="Ex: Sala de Partos 1">
                    @error('hospital_unidade')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label-tw">Médico Obstetra Responsável</label>
                    <input type="text"
                           class="input-tw @error('profissional_obstetra') input-error-tw @enderror"
                           name="profissional_obstetra"
                           value="{{ old('profissional_obstetra') }}"
                           placeholder="Nome do médico obstetra">
                    @error('profissional_obstetra')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label-tw">Enfermeiro(a) SMI Responsável</label>
                    <input type="text"
                           class="input-tw @error('profissional_enfermeiro') input-error-tw @enderror"
                           name="profissional_enfermeiro"
                           value="{{ old('profissional_enfermeiro', auth()->user()->name) }}"
                           placeholder="Nome do profissional SMI">
                    @error('profissional_enfermeiro')
                        <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- 2. Dados do Recém-Nascido (RN) --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-ocean-100 text-ocean-700 flex items-center justify-center text-sm">
                        <i class="fas fa-baby-carriage"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">2. Dados do Recém-Nascido (RN)</h3>
                </div>
            </div>

            <div class="card-body-tw space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="label-tw">Sexo do Bebê</label>
                        <select class="input-tw @error('sexo_bebe') input-error-tw @enderror" name="sexo_bebe">
                            <option value="">Não informado</option>
                            <option value="masculino" {{ old('sexo_bebe') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="feminino" {{ old('sexo_bebe') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                        </select>
                        @error('sexo_bebe')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Peso ao Nascer (gramas) <span class="text-crimson-500">*</span></label>
                        <input type="number"
                               class="input-tw @error('peso_nascimento') input-error-tw @enderror"
                               name="peso_nascimento"
                               value="{{ old('peso_nascimento') }}"
                               min="300" max="6000" step="10"
                               placeholder="Ex: 3200 (g)"
                               required>
                        @error('peso_nascimento')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Comprimento (cm) <span class="text-crimson-500">*</span></label>
                        <input type="number"
                               class="input-tw @error('altura_nascimento') input-error-tw @enderror"
                               name="altura_nascimento"
                               value="{{ old('altura_nascimento') }}"
                               min="25" max="60" step="0.1"
                               placeholder="Ex: 49.5 (cm)"
                               required>
                        @error('altura_nascimento')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Estado de Saúde do RN <span class="text-crimson-500">*</span></label>
                        <select class="input-tw @error('status_bebe') input-error-tw @enderror" name="status_bebe" required>
                            <option value="">Selecionar status...</option>
                            <option value="vivo_saudavel" {{ old('status_bebe') == 'vivo_saudavel' ? 'selected' : '' }}>Vivo e Saudável</option>
                            <option value="vivo_complicacoes" {{ old('status_bebe') == 'vivo_complicacoes' ? 'selected' : '' }}>Vivo com Complicações</option>
                            <option value="obito_fetal" {{ old('status_bebe') == 'obito_fetal' ? 'selected' : '' }}>Óbito Fetal (Natimorto)</option>
                            <option value="obito_neonatal" {{ old('status_bebe') == 'obito_neonatal' ? 'selected' : '' }}>Óbito Neonatal</option>
                        </select>
                        @error('status_bebe')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Escala APGAR --}}
                <div class="p-4 bg-surface-50 rounded-xl border border-surface-200/60 space-y-3">
                    <h6 class="text-xs font-bold text-surface-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-simple text-brand-600"></i> Escala APGAR (0 a 10)
                    </h6>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="label-tw">APGAR 1º Minuto <span class="text-crimson-500">*</span></label>
                            <input type="number"
                                   class="input-tw @error('apgar_1min') input-error-tw @enderror"
                                   name="apgar_1min"
                                   value="{{ old('apgar_1min') }}"
                                   min="0" max="10" required placeholder="0 - 10">
                            @error('apgar_1min')
                                <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="label-tw">APGAR 5º Minuto <span class="text-crimson-500">*</span></label>
                            <input type="number"
                                   class="input-tw @error('apgar_5min') input-error-tw @enderror"
                                   name="apgar_5min"
                                   value="{{ old('apgar_5min') }}"
                                   min="0" max="10" required placeholder="0 - 10">
                            @error('apgar_5min')
                                <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="label-tw">APGAR 10º Minuto (Opcional)</label>
                            <input type="number"
                                   class="input-tw @error('apgar_10min') input-error-tw @enderror"
                                   name="apgar_10min"
                                   value="{{ old('apgar_10min') }}"
                                   min="0" max="10" placeholder="0 - 10">
                            @error('apgar_10min')
                                <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Parto Múltiplo --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 pt-4">
                        <input type="checkbox"
                               name="parto_multiplo"
                               id="parto_multiplo"
                               value="1"
                               class="rounded border-surface-300 text-brand-600 focus:ring-brand-500"
                               {{ old('parto_multiplo') ? 'checked' : '' }}>
                        <label for="parto_multiplo" class="text-xs font-semibold text-surface-900 cursor-pointer">
                            Parto Múltiplo (Gêmeos, trigêmeos, etc.)
                        </label>
                    </div>

                    <div>
                        <label class="label-tw">Número de Bebês <span class="text-crimson-500">*</span></label>
                        <input type="number"
                               class="input-tw @error('numero_bebes') input-error-tw @enderror"
                               name="numero_bebes"
                               value="{{ old('numero_bebes', 1) }}"
                               min="1" max="5" required>
                        @error('numero_bebes')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Observações, Complicações & Alta --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">3. Observações Médicas & Puerpério MISAU</h3>
                </div>
            </div>

            <div class="card-body-tw space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Complicações Maternas</label>
                        <textarea class="input-tw @error('complicacoes_maternas') input-error-tw @enderror"
                                  name="complicacoes_maternas"
                                  rows="3"
                                  placeholder="Descrever complicações durante o trabalho de parto, lacerações, hemorragia pós-parto, etc.">{{ old('complicacoes_maternas') }}</textarea>
                        @error('complicacoes_maternas')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Observações do Recém-Nascido</label>
                        <textarea class="input-tw @error('observacoes_rn') input-error-tw @enderror"
                                  name="observacoes_rn"
                                  rows="3"
                                  placeholder="Observações sobre reanimação neonatal, profilaxia ocular, vacinação BCG/Pólio 0...">{{ old('observacoes_rn') }}</textarea>
                        @error('observacoes_rn')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Medicamentos Administrados</label>
                        <textarea class="input-tw @error('medicamentos_utilizados') input-error-tw @enderror"
                                  name="medicamentos_utilizados"
                                  rows="2"
                                  placeholder="Ex: Ocitocina 10 UI, Ácido Tranexâmico, antibióticos...">{{ old('medicamentos_utilizados') }}</textarea>
                        @error('medicamentos_utilizados')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Condições Pós-Parto da Puérpera</label>
                        <textarea class="input-tw @error('condicoes_pos_parto') input-error-tw @enderror"
                                  name="condicoes_pos_parto"
                                  rows="2"
                                  placeholder="Estado da puérpera no pós-parto imediato (lochia, involução uterina, deambulação)...">{{ old('condicoes_pos_parto') }}</textarea>
                        @error('condicoes_pos_parto')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label-tw">Data e Hora da Alta Hospitalar</label>
                        <input type="datetime-local"
                               class="input-tw @error('alta_hospitalar') input-error-tw @enderror"
                               name="alta_hospitalar"
                               value="{{ old('alta_hospitalar') }}">
                        @error('alta_hospitalar')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-tw">Observações Gerais</label>
                        <textarea class="input-tw @error('observacoes_gerais') input-error-tw @enderror"
                                  name="observacoes_gerais"
                                  rows="2"
                                  placeholder="Outras observações relevantes">{{ old('observacoes_gerais') }}</textarea>
                        @error('observacoes_gerais')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Aviso de Agendamento do Puerpério MISAU --}}
                <div class="p-3.5 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900 flex items-start gap-3">
                    <i class="fas fa-circle-info text-brand-600 text-base shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Agendamento Automático de Puerpério (MISAU):</span>
                        <p class="text-brand-800 mt-0.5">Ao guardar este registo, o sistema irá atualizar o status da paciente para <strong>Pós-Parto</strong>, encerrar os alertas da gestação e agendar automaticamente as 3 Consultas Puerperais recomendadas (48 horas, 7 dias e 28 dias pós-parto).</p>
                    </div>
                </div>
            </div>

            <div class="card-footer-tw flex items-center justify-end gap-2">
                <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw">
                    <i class="fas fa-times text-xs"></i>
                    <span>Cancelar</span>
                </a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-check-circle text-xs"></i>
                    <span>Registar Parto & Mover para Pós-Parto</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const partoMultiplo = document.getElementById('parto_multiplo');
        const numeroBebes = document.querySelector('input[name="numero_bebes"]');

        if (partoMultiplo && numeroBebes) {
            partoMultiplo.addEventListener('change', function() {
                if (this.checked) {
                    if (numeroBebes.value == 1) numeroBebes.value = 2;
                    numeroBebes.min = 2;
                } else {
                    numeroBebes.value = 1;
                    numeroBebes.min = 1;
                }
            });
        }

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
