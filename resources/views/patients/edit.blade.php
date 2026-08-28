@extends('layouts.app-tw')

@section('title', 'Editar Gestante')
@section('page-title', 'Editar Cadastro de Gestante')
@section('title-icon', 'fa-user-pen')

@section('breadcrumbs')
    <a href="{{ route('patients.index') }}">Gestantes</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('patients.show', $patient) }}">{{ $patient->nome_completo }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar</span>
@endsection

@section('content')
<div class="w-full mx-auto">
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-user-pen"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Editar Cadastro: {{ $patient->nome_completo }}</h3>
            </div>
            <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>

        <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Informações Pessoais --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card text-brand-500"></i> Informações Pessoais
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="nome_completo" class="label-tw">Nome Completo <span class="text-crimson-500">*</span></label>
                        <input type="text"
                               class="input-tw @error('nome_completo') input-error-tw @enderror"
                               id="nome_completo"
                               name="nome_completo"
                               value="{{ old('nome_completo', $patient->nome_completo) }}"
                               required>
                        @error('nome_completo')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="filiacao" class="label-tw">Filiação (Pais)</label>
                        <input type="text"
                               class="input-tw @error('filiacao') input-error-tw @enderror"
                               id="filiacao"
                               name="filiacao"
                               value="{{ old('filiacao', $patient->filiacao) }}"
                               placeholder="Filha de ... e de ...">
                        @error('filiacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="data_nascimento" class="label-tw">Data de Nascimento <span class="text-crimson-500">*</span></label>
                        <input type="date"
                               class="input-tw @error('data_nascimento') input-error-tw @enderror"
                               id="data_nascimento"
                               name="data_nascimento"
                               value="{{ old('data_nascimento', $patient->data_nascimento?->format('Y-m-d')) }}"
                               required>
                        @error('data_nascimento')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estado_civil" class="label-tw">Estado Civil</label>
                        <select class="input-tw @error('estado_civil') input-error-tw @enderror" id="estado_civil" name="estado_civil">
                            <option value="solteira" {{ old('estado_civil', $patient->estado_civil) === 'solteira' ? 'selected' : '' }}>Solteira</option>
                            <option value="casada" {{ old('estado_civil', $patient->estado_civil) === 'casada' ? 'selected' : '' }}>Casada</option>
                            <option value="uniao_de_facto" {{ old('estado_civil', $patient->estado_civil) === 'uniao_de_facto' ? 'selected' : '' }}>União de Facto</option>
                            <option value="viuva" {{ old('estado_civil', $patient->estado_civil) === 'viuva' ? 'selected' : '' }}>Viúva</option>
                            <option value="divorciada" {{ old('estado_civil', $patient->estado_civil) === 'divorciada' ? 'selected' : '' }}>Divorciada</option>
                        </select>
                        @error('estado_civil')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="local_trabalho" class="label-tw">Profissão / Trabalho</label>
                        <input type="text"
                               class="input-tw @error('local_trabalho') input-error-tw @enderror"
                               id="local_trabalho"
                               name="local_trabalho"
                               value="{{ old('local_trabalho', $patient->local_trabalho) }}"
                               placeholder="Ex: Camponesa, Comerciante...">
                        @error('local_trabalho')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="documento_bi" class="label-tw">Documento BI / NID <span class="text-crimson-500">*</span></label>
                        <input type="text"
                               class="input-tw @error('documento_bi') input-error-tw @enderror"
                               id="documento_bi"
                               name="documento_bi"
                               value="{{ old('documento_bi', $patient->documento_bi) }}"
                               required>
                        @error('documento_bi')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="contacto" class="label-tw">Contacto Telefónico <span class="text-crimson-500">*</span></label>
                        <input type="tel"
                               class="input-tw @error('contacto') input-error-tw @enderror"
                               id="contacto"
                               name="contacto"
                               value="{{ old('contacto', $patient->contacto) }}"
                               required>
                        @error('contacto')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contacto_emergencia" class="label-tw">Contacto Emergência</label>
                        <input type="tel"
                               class="input-tw @error('contacto_emergencia') input-error-tw @enderror"
                               id="contacto_emergencia"
                               name="contacto_emergencia"
                               value="{{ old('contacto_emergencia', $patient->contacto_emergencia) }}">
                        @error('contacto_emergencia')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="codigo_ptv" class="label-tw">Código PTV (Se aplicável)</label>
                        <input type="text"
                               class="input-tw @error('codigo_ptv') input-error-tw @enderror"
                               id="codigo_ptv"
                               name="codigo_ptv"
                               value="{{ old('codigo_ptv', $patient->codigo_ptv) }}"
                               placeholder="Ex: PTV-2026-045">
                        @error('codigo_ptv')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Rede de Apoio Familiar & Notificações SMS de Suporte --}}
                <div class="mt-5 p-4 bg-surface-50 rounded-xl border border-surface-200/80 space-y-4"
                     x-data="{ temParceiro: '{{ old('tem_parceiro', $patient->tem_parceiro ? '1' : '0') }}' }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-surface-200/60 pb-2">
                        <div>
                            <h5 class="text-xs font-bold text-surface-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-users-line text-brand-600"></i> Rede de Apoio Familiar & Notificações de Retenção
                            </h5>
                            <p class="text-2xs text-surface-500">Registo do parceiro ou familiar de suporte para reforço e busca ativa comunitária via SMS</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="tem_parceiro" value="1" x-model="temParceiro" class="text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-surface-800">Tem Parceiro</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="tem_parceiro" value="0" x-model="temParceiro" class="text-amber-600 focus:ring-amber-500">
                                <span class="font-medium text-surface-800">Sem Parceiro / Apoio</span>
                            </label>
                        </div>
                    </div>

                    {{-- Bloco do Parceiro --}}
                    <div x-show="temParceiro == '1'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label for="parceiro_nome" class="label-tw">Nome do Parceiro / Pai do Bebê</label>
                            <input type="text"
                                   class="input-tw @error('parceiro_nome') input-error-tw @enderror"
                                   id="parceiro_nome"
                                   name="parceiro_nome"
                                   value="{{ old('parceiro_nome', $patient->parceiro_nome) }}"
                                   placeholder="Nome completo do parceiro">
                            @error('parceiro_nome')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="parceiro_contacto" class="label-tw">Contacto Telefónico do Parceiro</label>
                            <input type="tel"
                                   class="input-tw @error('parceiro_contacto') input-error-tw @enderror"
                                   id="parceiro_contacto"
                                   name="parceiro_contacto"
                                   value="{{ old('parceiro_contacto', $patient->parceiro_contacto) }}"
                                   placeholder="Ex: +258 84 987 6543">
                            @error('parceiro_contacto')
                                <p class="error-text-tw">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center pt-5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs">
                                <input type="checkbox" name="parceiro_notificar_sms" value="1" {{ old('parceiro_notificar_sms', $patient->parceiro_notificar_sms) ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-surface-800">Enviar SMS ao Parceiro se a gestante faltar</span>
                            </label>
                        </div>
                    </div>

                    {{-- Bloco do Acompanhante / Familiar de Apoio --}}
                    <div class="pt-3 border-t border-surface-200/60">
                        <span class="text-2xs font-bold uppercase text-surface-600 tracking-wider block mb-2">
                            <i class="fas fa-hand-holding-heart text-gold-600 mr-1"></i> Acompanhante / Familiar de Apoio (Mãe, Tia, Irmã, Sogra ou Vizinha):
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label for="acompanhante_nome" class="label-tw">Nome do Acompanhante</label>
                                <input type="text"
                                       class="input-tw @error('acompanhante_nome') input-error-tw @enderror"
                                       id="acompanhante_nome"
                                       name="acompanhante_nome"
                                       value="{{ old('acompanhante_nome', $patient->acompanhante_nome) }}"
                                       placeholder="Nome do familiar">
                                @error('acompanhante_nome')
                                    <p class="error-text-tw">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="acompanhante_parentesco" class="label-tw">Grau de Parentesco</label>
                                <select class="input-tw @error('acompanhante_parentesco') input-error-tw @enderror" id="acompanhante_parentesco" name="acompanhante_parentesco">
                                    <option value="">Selecione...</option>
                                    <option value="Mae" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Mae' ? 'selected' : '' }}>Mãe</option>
                                    <option value="Tia" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Tia' ? 'selected' : '' }}>Tia</option>
                                    <option value="Irma" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Irma' ? 'selected' : '' }}>Irmã</option>
                                    <option value="Sogra" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Sogra' ? 'selected' : '' }}>Sogra</option>
                                    <option value="Avo" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Avo' ? 'selected' : '' }}>Avó</option>
                                    <option value="Vizinha_APE" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Vizinha_APE' ? 'selected' : '' }}>Vizinha / APE</option>
                                    <option value="Amiga" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Amiga' ? 'selected' : '' }}>Amiga</option>
                                    <option value="Outro" {{ old('acompanhante_parentesco', $patient->acompanhante_parentesco) == 'Outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('acompanhante_parentesco')
                                    <p class="error-text-tw">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="acompanhante_contacto" class="label-tw">Contacto do Acompanhante</label>
                                <input type="tel"
                                       class="input-tw @error('acompanhante_contacto') input-error-tw @enderror"
                                       id="acompanhante_contacto"
                                       name="acompanhante_contacto"
                                       value="{{ old('acompanhante_contacto', $patient->acompanhante_contacto) }}"
                                       placeholder="Ex: +258 82 123 4567">
                                @error('acompanhante_contacto')
                                    <p class="error-text-tw">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer text-xs">
                                    <input type="checkbox" name="acompanhante_notificar_sms" value="1" {{ old('acompanhante_notificar_sms', $patient->acompanhante_notificar_sms) ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                                    <span class="font-medium text-surface-800">Enviar SMS de apoio/lembrete ao familiar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label for="distrito" class="label-tw">Distrito</label>
                        <input type="text"
                               class="input-tw @error('distrito') input-error-tw @enderror"
                               id="distrito"
                               name="distrito"
                               value="{{ old('distrito', $patient->distrito ?? 'Quelimane') }}">
                        @error('distrito')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bairro" class="label-tw">Bairro</label>
                        <input type="text"
                               class="input-tw @error('bairro') input-error-tw @enderror"
                               id="bairro"
                               name="bairro"
                               value="{{ old('bairro', $patient->bairro) }}"
                               placeholder="Ex: Coalane, Saguar, 17 Setembro...">
                        @error('bairro')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ponto_referencia_residencia" class="label-tw">Ponto de Referência da Residência</label>
                        <input type="text"
                               class="input-tw @error('ponto_referencia_residencia') input-error-tw @enderror"
                               id="ponto_referencia_residencia"
                               name="ponto_referencia_residencia"
                               value="{{ old('ponto_referencia_residencia', $patient->ponto_referencia_residencia) }}"
                               placeholder="Próximo à Escola, Mercado...">
                        @error('ponto_referencia_residencia')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="endereco" class="label-tw">Endereço Completo (Rua / Av / Casa) <span class="text-crimson-500">*</span></label>
                    <textarea class="input-tw @error('endereco') input-error-tw @enderror"
                              id="endereco"
                              name="endereco"
                              rows="2"
                              required>{{ old('endereco', $patient->endereco) }}</textarea>
                    @error('endereco')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-surface-100 pt-6"></div>

            {{-- Histórico Obstétrico & Triagem de Risco MISAU --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-stethoscope text-brand-500"></i> Histórico Obstétrico & Triagem de Risco (FPN MISAU)
                </h4>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    <div>
                        <label for="tipo_sanguineo" class="label-tw">Sangue Mãe</label>
                        <select class="input-tw @error('tipo_sanguineo') input-error-tw @enderror" id="tipo_sanguineo" name="tipo_sanguineo">
                            <option value="">Selecione</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo_sanguineo', $patient->tipo_sanguineo) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo_sanguineo')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tipo_sanguineo_parceiro" class="label-tw">Sangue Parceiro</label>
                        <select class="input-tw @error('tipo_sanguineo_parceiro') input-error-tw @enderror" id="tipo_sanguineo_parceiro" name="tipo_sanguineo_parceiro">
                            <option value="">Selecione</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo_sanguineo_parceiro', $patient->tipo_sanguineo_parceiro) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo_sanguineo_parceiro')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="altura_cm" class="label-tw">Altura (cm)</label>
                        <input type="number"
                               min="100" max="220"
                               class="input-tw @error('altura_cm') input-error-tw @enderror"
                               id="altura_cm"
                               name="altura_cm"
                               value="{{ old('altura_cm', $patient->altura_cm) }}"
                               placeholder="Ex: 155">
                        @error('altura_cm')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_gestacoes" class="label-tw">Gestações (G)</label>
                        <input type="number"
                               min="1"
                               class="input-tw @error('numero_gestacoes') input-error-tw @enderror"
                               id="numero_gestacoes"
                               name="numero_gestacoes"
                               value="{{ old('numero_gestacoes', $patient->numero_gestacoes) }}"
                               required>
                        @error('numero_gestacoes')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_partos" class="label-tw">Partos (P)</label>
                        <input type="number"
                               min="0"
                               class="input-tw @error('numero_partos') input-error-tw @enderror"
                               id="numero_partos"
                               name="numero_partos"
                               value="{{ old('numero_partos', $patient->numero_partos) }}"
                               required>
                        @error('numero_partos')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="numero_abortos" class="label-tw">Abortos (A)</label>
                        <input type="number"
                               min="0"
                               class="input-tw @error('numero_abortos') input-error-tw @enderror"
                               id="numero_abortos"
                               name="numero_abortos"
                               value="{{ old('numero_abortos', $patient->numero_abortos) }}"
                               required>
                        @error('numero_abortos')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Triagem de Alergias & Prevenção --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-xs">
                    <label class="flex items-center gap-2 p-2.5 bg-surface-50 rounded-lg border border-surface-200 cursor-pointer">
                        <input type="checkbox" name="uso_rede_mosquiteira" value="1" {{ old('uso_rede_mosquiteira', $patient->uso_rede_mosquiteira) ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                        <span class="font-medium text-surface-800">Dorme sob Rede Mosquiteira</span>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 bg-crimson-50/50 rounded-lg border border-crimson-200 cursor-pointer">
                        <input type="checkbox" name="alergia_penicilina" value="1" {{ old('alergia_penicilina', $patient->alergia_penicilina) ? 'checked' : '' }} class="rounded text-crimson-600 focus:ring-crimson-500">
                        <span class="font-medium text-crimson-900">Alergia à Penicilina</span>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 bg-crimson-50/50 rounded-lg border border-crimson-200 cursor-pointer">
                        <input type="checkbox" name="alergia_cotrimoxazol" value="1" {{ old('alergia_cotrimoxazol', $patient->alergia_cotrimoxazol) ? 'checked' : '' }} class="rounded text-crimson-600 focus:ring-crimson-500">
                        <span class="font-medium text-crimson-900">Alergia ao Cotrimoxazol</span>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 bg-crimson-50/50 rounded-lg border border-crimson-200 cursor-pointer">
                        <input type="checkbox" name="alergia_sp" value="1" {{ old('alergia_sp', $patient->alergia_sp) ? 'checked' : '' }} class="rounded text-crimson-600 focus:ring-crimson-500">
                        <span class="font-medium text-crimson-900">Alergia a SP / Fansidar</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="data_ultima_menstruacao" class="label-tw">Data da Última Menstruação (DUM)</label>
                        <input type="date"
                               class="input-tw @error('data_ultima_menstruacao') input-error-tw @enderror"
                               id="data_ultima_menstruacao"
                               name="data_ultima_menstruacao"
                               value="{{ old('data_ultima_menstruacao', $patient->data_ultima_menstruacao?->format('Y-m-d')) }}">
                        @error('data_ultima_menstruacao')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="alergias" class="label-tw">Outras Alergias / Reações</label>
                        <input type="text"
                               class="input-tw @error('alergias') input-error-tw @enderror"
                               id="alergias"
                               name="alergias"
                               value="{{ old('alergias', $patient->alergias) }}"
                               placeholder="Ex: Alergia alimentar, dipirona...">
                        @error('alergias')
                            <p class="error-text-tw">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="historico_medico" class="label-tw">Histórico Médico / Sintomas de Alarme</label>
                    <textarea class="input-tw @error('historico_medico') input-error-tw @enderror"
                              id="historico_medico"
                              name="historico_medico"
                              rows="2"
                              placeholder="Diabetes, hipertensão crônica, convulsões, tosse > 3 semanas, infecções prévias...">{{ old('historico_medico', $patient->historico_medico) }}</textarea>
                    @error('historico_medico')
                        <p class="error-text-tw">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 border-t border-surface-100 pt-6">
                <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw">Cancelar</a>
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
