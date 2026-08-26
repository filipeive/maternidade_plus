@extends('layouts.app-tw')

@section('title', 'Novo Utilizador')
@section('page-title', 'Registar Novo Utilizador')
@section('title-icon', 'fa-user-plus')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('users.index') }}">Utilizadores</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Novo Utilizador</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER BANNER --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Criar Novo Utilizador Profissional</h2>
                <p class="text-xs text-white/70">Cadastre médicos, enfermeiros, laboratoristas e administradores do Centro de Saúde</p>
            </div>
        </div>

        <a href="{{ route('users.index') }}" class="btn-secondary-tw btn-sm-tw">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Voltar à Lista</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- FORMULÁRIO PRINCIPAL --}}
        <div class="lg:col-span-2 card-tw p-6 space-y-6">
            <div class="border-b border-surface-100 pb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-id-card text-brand-600"></i> Dados de Acesso & Perfil Profissional
                </h3>
                <span class="text-3xs font-semibold text-surface-400 uppercase">MISAU CS</span>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    {{-- Nome Completo --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="label-tw">Nome Completo do Profissional <span class="text-crimson-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Dra. Maria Eugenia Simbine" class="input-tw pl-10 text-xs @error('name') input-error-tw @enderror">
                        </div>
                        @error('name')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="label-tw">Endereço de Email <span class="text-crimson-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="enfermeira@maternidade.mz" class="input-tw pl-10 text-xs @error('email') input-error-tw @enderror">
                        </div>
                        @error('email')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Função / Perfil de Acesso --}}
                    <div>
                        <label for="role" class="label-tw">Função / Perfil de Acesso <span class="text-crimson-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user-shield absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <select id="role" name="role" required class="input-tw pl-10 text-xs @error('role') input-error-tw @enderror">
                                <option value="">Selecione a Função...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('role')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Telefone / Contacto --}}
                    <div>
                        <label for="telefone" class="label-tw">Número de Telemóvel</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="telefone" type="text" name="telefone" value="{{ old('telefone') }}" placeholder="+258 84 123 4567" class="input-tw pl-10 text-xs font-mono">
                        </div>
                        @error('telefone')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NID Profissional / CRM --}}
                    <div>
                        <label for="crm" class="label-tw">NID Profissional / Ordem Médica</label>
                        <div class="relative">
                            <i class="fas fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="crm" type="text" name="crm" value="{{ old('crm') }}" placeholder="Ex: ORDEM-MZ-9842" class="input-tw pl-10 text-xs font-mono">
                        </div>
                        @error('crm')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Especialidade --}}
                    <div class="sm:col-span-2">
                        <label for="especialidade" class="label-tw">Especialidade Clínica / Serviço</label>
                        <div class="relative">
                            <i class="fas fa-stethoscope absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="especialidade" type="text" name="especialidade" value="{{ old('especialidade') }}" placeholder="Ex: Obstetrícia e Ginecologia, Enfermagem de Saúde Materna" class="input-tw pl-10 text-xs">
                        </div>
                        @error('especialidade')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Palavra-passe --}}
                    <div>
                        <label for="password" class="label-tw">Palavra-passe <span class="text-crimson-500">*</span></label>
                        <div class="relative" x-data="{ showPass: false }">
                            <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="password" :type="showPass ? 'text' : 'password'" name="password" required placeholder="Mínimo 8 caracteres" class="input-tw pl-10 pr-10 text-xs @error('password') input-error-tw @enderror">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600 text-xs">
                                <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmação da Palavra-passe --}}
                    <div>
                        <label for="password_confirmation" class="label-tw">Confirmar Palavra-passe <span class="text-crimson-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repita a palavra-passe" class="input-tw pl-10 text-xs">
                        </div>
                    </div>

                </div>

                <div class="pt-4 border-t border-surface-100 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="btn-secondary-tw btn-sm-tw">Cancelar</a>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-save text-xs"></i>
                        <span>Registar Utilizador</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- COLUNA LATERAL: PERMISSÕES & ESTATÍSTICAS --}}
        <div class="space-y-6">
            
            {{-- Funções e Permissões --}}
            <div class="card-tw p-5 space-y-4">
                <h3 class="text-sm font-bold text-surface-900 border-b border-surface-100 pb-2 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-brand-600"></i> Funções no Sistema MISAU
                </h3>

                <div class="space-y-2.5 text-xs">
                    <div class="p-2.5 bg-crimson-50 border border-crimson-200 rounded-xl space-y-0.5">
                        <div class="flex items-center justify-between font-bold text-crimson-900">
                            <span>Administrador</span>
                            <span class="badge-danger text-3xs">Acesso Total</span>
                        </div>
                        <p class="text-3xs text-crimson-800">Gestão de utilizadores, configurações e relatórios globais.</p>
                    </div>

                    <div class="p-2.5 bg-brand-50 border border-brand-200 rounded-xl space-y-0.5">
                        <div class="flex items-center justify-between font-bold text-brand-900">
                            <span>Médico Obstetra</span>
                            <span class="badge-success text-3xs">Consultas & Partos</span>
                        </div>
                        <p class="text-3xs text-brand-800">Atendimento clínico, prescrições e registo de partos.</p>
                    </div>

                    <div class="p-2.5 bg-ocean-50 border border-ocean-200 rounded-xl space-y-0.5">
                        <div class="flex items-center justify-between font-bold text-ocean-900">
                            <span>Enfermeira SMI</span>
                            <span class="badge-info text-3xs">ANC & Puerpério</span>
                        </div>
                        <p class="text-3xs text-ocean-800">Consultas pré-natais, vacinas IPTp e visitas domiciliárias.</p>
                    </div>

                    <div class="p-2.5 bg-gold-50 border border-gold-200 rounded-xl space-y-0.5">
                        <div class="flex items-center justify-between font-bold text-gold-900">
                            <span>Laboratorista</span>
                            <span class="badge-warning text-3xs">Exames</span>
                        </div>
                        <p class="text-3xs text-gold-800">Processamento de exames clínicos e alertas de laboratório.</p>
                    </div>
                </div>
            </div>

            {{-- Dica de Segurança --}}
            <div class="card-tw p-4 bg-surface-50 border border-surface-200 text-xs space-y-2">
                <h4 class="font-bold text-surface-900 flex items-center gap-1.5">
                    <i class="fas fa-lock text-brand-600"></i> Segurança das Credenciais
                </h4>
                <p class="text-surface-600 leading-relaxed text-2xs">
                    Certifique-se de que a palavra-passe possui no mínimo 8 caracteres. O utilizador terá a sua conta ativada automaticamente.
                </p>
            </div>

        </div>

    </div>
</div>
@endsection