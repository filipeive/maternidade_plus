@extends('layouts.app-tw')

@section('title', 'Editar Utilizador')
@section('page-title', 'Editar Utilizador')
@section('title-icon', 'fa-user-edit')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('users.index') }}">Utilizadores</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Editar Utilizador</span>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6">

    {{-- HEADER BANNER --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Editar Perfil de Utilizador — {{ $user->name }}</h2>
                <p class="text-xs text-white/70">Atualize as informações de acesso, função e especialidade clínica</p>
            </div>
        </div>

        <a href="{{ route('users.index') }}" class="btn-secondary-tw btn-sm-tw">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Voltar à Lista</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- FORMULÁRIO PRINCIPAL DE EDIÇÃO --}}
        <div class="lg:col-span-2 card-tw p-6 space-y-6">
            <div class="border-b border-surface-100 pb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-pen-to-square text-brand-600"></i> Atualizar Credenciais & Dados
                </h3>
                <span class="badge-neutral text-2xs uppercase">ID #{{ $user->id }}</span>
            </div>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    {{-- Nome Completo --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="label-tw">Nome Completo do Profissional <span class="text-crimson-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-tw pl-10 text-xs @error('name') input-error-tw @enderror">
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
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-tw pl-10 text-xs @error('email') input-error-tw @enderror">
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
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('role')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado da Conta (Ativo / Inativo) --}}
                    <div>
                        <label for="active" class="label-tw">Estado da Conta <span class="text-crimson-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <select id="active" name="active" class="input-tw pl-10 text-xs">
                                <option value="1" {{ old('active', $user->email_verified_at ? '1' : '0') == '1' ? 'selected' : '' }}>
                                    Ativo (Acesso Permitido)
                                </option>
                                <option value="0" {{ old('active', $user->email_verified_at ? '1' : '0') == '0' ? 'selected' : '' }}>
                                    Inativo (Acesso Suspenso)
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- Telefone / Contacto --}}
                    <div>
                        <label for="telefone" class="label-tw">Número de Telemóvel</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                            <input id="telefone" type="text" name="telefone" value="{{ old('telefone', $user->telefone) }}" class="input-tw pl-10 text-xs font-mono">
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
                            <input id="crm" type="text" name="crm" value="{{ old('crm', $user->crm) }}" class="input-tw pl-10 text-xs font-mono">
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
                            <input id="especialidade" type="text" name="especialidade" value="{{ old('especialidade', $user->especialidade) }}" class="input-tw pl-10 text-xs">
                        </div>
                        @error('especialidade')
                            <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alterar Palavra-passe (Opcional) --}}
                    <div class="sm:col-span-2 pt-2 border-t border-surface-100 space-y-3">
                        <span class="text-xs font-bold text-surface-800 block">Alterar Palavra-passe (Deixe em branco para manter a atual)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="label-tw">Nova Palavra-passe</label>
                                <div class="relative" x-data="{ showPass: false }">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                                    <input id="password" :type="showPass ? 'text' : 'password'" name="password" placeholder="Nova palavra-passe" class="input-tw pl-10 pr-10 text-xs @error('password') input-error-tw @enderror">
                                    <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600 text-xs">
                                        <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-2xs text-crimson-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="label-tw">Confirmar Nova Palavra-passe</label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
                                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repita a nova palavra-passe" class="input-tw pl-10 text-xs">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pt-4 border-t border-surface-100 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="btn-secondary-tw btn-sm-tw">Cancelar</a>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-save text-xs"></i>
                        <span>Guardar Alterações</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- PERFIL & RESUMO DO UTILIZADOR --}}
        <div class="space-y-6">
            <div class="card-tw p-6 text-center space-y-4">
                <div class="w-20 h-20 rounded-full bg-brand-600 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div>
                    <h3 class="text-base font-bold text-surface-900">{{ $user->name }}</h3>
                    <p class="text-xs text-surface-500 font-mono">{{ $user->email }}</p>
                </div>

                <div class="pt-2">
                    <span class="badge-success text-2xs uppercase px-3 py-1">
                        {{ $user->roles->first()?->name ?? 'Utilizador' }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection