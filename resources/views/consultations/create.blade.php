@extends('layouts.app-tw')

@section('title', 'Nova Consulta')
@section('page-title', 'Agendar Nova Consulta')
@section('title-icon', 'fa-calendar-plus')

@section('breadcrumbs')
    <a href="{{ route('consultations.index') }}">Consultas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Nova</span>
@endsection

@section('content')
<div class="max-w-full mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Agendamento de Consulta ANC</h3>
            </div>
            <a href="{{ route('consultations.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <form action="{{ route('consultations.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Seleção da Gestante --}}
            <div>
                <label for="patient_id" class="label-tw">Gestante <span class="text-crimson-500">*</span></label>
                <select class="input-tw @error('patient_id') input-error-tw @enderror"
                        id="patient_id"
                        name="patient_id"
                        required
                        onchange="updatePatientInfo(this)">
                    <option value="">Selecione a gestante</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}"
                                {{ (old('patient_id') == $p->id || ($patient && $patient->id == $p->id)) ? 'selected' : '' }}
                                data-semanas="{{ $p->semanas_gestacao }}"
                                data-tipo-sanguineo="{{ $p->tipo_sanguineo }}"
                                data-alergias="{{ $p->alergias }}"
                                data-tem-parto="{{ \App\Models\Birth::where('patient_id', $p->id)->exists() ? '1' : '0' }}">
                            {{ $p->nome_completo }} — BI: {{ $p->documento_bi }}
                            @if($p->semanas_gestacao)
                                ({{ $p->semanas_gestacao }}ª semana)
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('patient_id')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Box --}}
            <div id="patient-info" class="alert-info-tw hidden">
                <i class="fas fa-info-circle text-ocean-600 mt-0.5 shrink-0"></i>
                <div id="patient-details" class="text-xs"></div>
            </div>

            @if(isset($latestBirth) && $latestBirth)
                <div class="p-4 bg-brand-50 border border-brand-200 rounded-xl flex items-start gap-3 text-brand-900 shadow-xs">
                    <div class="w-10 h-10 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center shrink-0 text-lg shadow-xs">
                        <i class="fas fa-baby"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-sm text-brand-900 flex items-center gap-2">
                            <span>Registo de Parto Detectado</span>
                            <span class="badge-success text-3xs uppercase">{{ $etapaPuerperio ?? 'Consulta de Puerpério' }}</span>
                        </h4>
                        <p class="text-xs text-brand-800">
                            A paciente <strong>{{ $patient->nome_completo }}</strong> registou o parto em <strong>{{ $latestBirth->data_parto?->format('d/m/Y \à\s H:i') }}</strong> (há {{ $diasPosParto }} {{ $diasPosParto == 1 ? 'dia' : 'dias' }}).
                        </p>
                        <p class="text-2xs text-brand-700 font-semibold">
                            <i class="fas fa-magic mr-1"></i> O tipo de consulta foi automaticamente definido para <strong>Pós-Parto / Puerpério</strong>.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Data & Tipo --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="data_consulta" class="label-tw">Data e Hora <span class="text-crimson-500">*</span></label>
                    <input type="datetime-local"
                           class="input-tw @error('data_consulta') input-error-tw @enderror"
                           id="data_consulta"
                           name="data_consulta"
                           value="{{ old('data_consulta', now()->format('Y-m-d\TH:i')) }}"
                           required>
                    @error('data_consulta')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_consulta" class="label-tw">Tipo de Consulta <span class="text-crimson-500">*</span></label>
                    <select class="input-tw @error('tipo_consulta') input-error-tw @enderror"
                            id="tipo_consulta"
                            name="tipo_consulta"
                            required>
                        <option value="">Selecione o tipo</option>
                        <option value="1_trimestre" {{ (old('tipo_consulta', $sugeridoTipoConsulta ?? '') === '1_trimestre') ? 'selected' : '' }}>1º Trimestre (Até 12 semanas)</option>
                        <option value="2_trimestre" {{ (old('tipo_consulta', $sugeridoTipoConsulta ?? '') === '2_trimestre') ? 'selected' : '' }}>2º Trimestre (13 a 27 semanas)</option>
                        <option value="3_trimestre" {{ (old('tipo_consulta', $sugeridoTipoConsulta ?? '') === '3_trimestre') ? 'selected' : '' }}>3º Trimestre (28+ semanas)</option>
                        <option value="pos_parto" {{ (old('tipo_consulta', $sugeridoTipoConsulta ?? '') === 'pos_parto') ? 'selected' : '' }}>👶 Pós-parto / Puerpério</option>
                        <option value="emergencia" {{ (old('tipo_consulta') === 'emergencia') ? 'selected' : '' }}>Emergência / Não agendada</option>
                    </select>
                    @error('tipo_consulta')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Vitais & Biometria --}}
            <div class="border-t border-surface-100 pt-6">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-heart-pulse text-brand-500"></i> Sinais Vitais & Biometria (Opcional no Agendamento)
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="semanas_gestacao" class="label-tw">Semanas de Gestação</label>
                        <input type="number"
                               min="1"
                               max="45"
                               class="input-tw @error('semanas_gestacao') input-error-tw @enderror"
                               id="semanas_gestacao"
                               name="semanas_gestacao"
                               value="{{ old('semanas_gestacao') }}"
                               placeholder="Ex: 24">
                        @error('semanas_gestacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="peso" class="label-tw">Peso (kg)</label>
                        <input type="number"
                               step="0.1"
                               min="30"
                               max="200"
                               class="input-tw @error('peso') input-error-tw @enderror"
                               id="peso"
                               name="peso"
                               value="{{ old('peso') }}"
                               placeholder="Ex: 65.5">
                        @error('peso')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pressao_arterial" class="label-tw">Pressão Arterial (mmHg)</label>
                        <input type="text"
                               class="input-tw @error('pressao_arterial') input-error-tw @enderror"
                               id="pressao_arterial"
                               name="pressao_arterial"
                               value="{{ old('pressao_arterial') }}"
                               placeholder="Ex: 120/80">
                        @error('pressao_arterial')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="altura_uterina" class="label-tw">Altura Uterina (cm)</label>
                        <input type="number"
                               step="0.5"
                               min="10"
                               max="50"
                               class="input-tw @error('altura_uterina') input-error-tw @enderror"
                               id="altura_uterina"
                               name="altura_uterina"
                               value="{{ old('altura_uterina') }}"
                               placeholder="Ex: 28">
                        @error('altura_uterina')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="batimentos_fetais" class="label-tw">FC Fetal (FCF bpm)</label>
                        <input type="number"
                               min="110"
                               max="180"
                               class="input-tw @error('batimentos_fetais') input-error-tw @enderror"
                               id="batimentos_fetais"
                               name="batimentos_fetais"
                               value="{{ old('batimentos_fetais') }}"
                               placeholder="Ex: 140">
                        @error('batimentos_fetais')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Observações --}}
            <div class="border-t border-surface-100 pt-6">
                <label for="observacoes" class="label-tw">Observações / Motivo da Consulta</label>
                <textarea class="input-tw @error('observacoes') input-error-tw @enderror"
                          id="observacoes"
                          name="observacoes"
                          rows="3"
                          placeholder="Queixas da gestante, notas prévias...">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <p class="error-text-tw">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                <a href="{{ route('consultations.index') }}" class="btn-secondary-tw">Cancelar</a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-calendar-check text-xs"></i>
                    <span>Confirmar Agendamento</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updatePatientInfo(select) {
        const option = select.options[select.selectedIndex];
        const infoDiv = document.getElementById('patient-info');
        const detailsDiv = document.getElementById('patient-details');

        if (option.value) {
            const semanas = option.getAttribute('data-semanas');
            const tipoSanguineo = option.getAttribute('data-tipo-sanguineo');
            const alergias = option.getAttribute('data-alergias');
            const temParto = option.getAttribute('data-tem-parto') === '1';

            let html = `<p class="font-semibold mb-1">${option.text}</p>`;
            if (temParto) {
                html += `<p class="text-brand-700 font-bold"><i class="fas fa-baby mr-1"></i>Paciente com Registo de Parto (Pós-Parto / Puerpério)</p>`;
            } else if (semanas) {
                html += `<p>Idade Gestacional: <strong>${semanas}ª semana</strong></p>`;
            }
            if (tipoSanguineo) html += `<p>Tipo Sanguíneo: <strong>${tipoSanguineo}</strong></p>`;
            if (alergias) html += `<p class="text-crimson-600 font-semibold mt-1"><i class="fas fa-triangle-exclamation mr-1"></i>Alergias: ${alergias}</p>`;

            detailsDiv.innerHTML = html;
            infoDiv.classList.remove('hidden');

            if (semanas && !document.getElementById('semanas_gestacao').value) {
                document.getElementById('semanas_gestacao').value = semanas;
            }

            // Seleção automática do tipo de consulta (Trimesters ou Pós-Parto)
            const tipoSelect = document.getElementById('tipo_consulta');
            if (tipoSelect) {
                if (temParto) {
                    tipoSelect.value = 'pos_parto';
                } else if (semanas) {
                    const s = parseInt(semanas);
                    if (s <= 12) {
                        tipoSelect.value = '1_trimestre';
                    } else if (s <= 27) {
                        tipoSelect.value = '2_trimestre';
                    } else {
                        tipoSelect.value = '3_trimestre';
                    }
                }
            }
        } else {
            infoDiv.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('patient_id');
        if (select && select.value) {
            updatePatientInfo(select);
        }
    });
</script>
@endpush
@endsection