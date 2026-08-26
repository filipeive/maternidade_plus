@extends('layouts.app-tw')

@section('title', 'Utilizadores')
@section('page-title', 'Gestão de Utilizadores do Sistema')
@section('title-icon', 'fa-users-gear')

@section('breadcrumbs')
    <span class="active">Utilizadores</span>
@endsection

@section('content')
@php
    $stats = [
        'total' => $users->total(),
        'active' => \App\Models\User::whereNotNull('email_verified_at')->count(),
        'doctors' => \App\Models\User::role('Médico')->count(),
        'nurses' => \App\Models\User::role('Enfermeiro')->count(),
    ];
@endphp

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $stats['total'] }}</p>
            <p class="stat-card-label">Total de Utilizadores</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-user-check"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $stats['active'] }}</p>
            <p class="stat-card-label">Utilizadores Ativos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-user-md"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $stats['doctors'] }}</p>
            <p class="stat-card-label">Médicos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-user-nurse"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $stats['nurses'] }}</p>
            <p class="stat-card-label">Enfermeiros</p>
        </div>
    </div>
</div>

{{-- Header Action --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Lista de Utilizadores</h2>
        <p class="text-sm text-surface-500">Gerencie contas, permissões e perfis de acesso dos profissionais</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn-primary-tw">
        <i class="fas fa-user-plus text-xs"></i>
        <span>Novo Utilizador</span>
    </a>
</div>

{{-- Filters --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label-tw">Pesquisar Utilizador</label>
            <input type="text"
                   name="search"
                   class="input-tw"
                   placeholder="Nome ou email..."
                   value="{{ request('search') }}">
        </div>

        <div>
            <label class="label-tw">Perfil / Role</label>
            <select name="role" class="input-tw">
                <option value="">Todos os Perfis</option>
                <option value="Administrador" {{ request('role') === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="Médico" {{ request('role') === 'Médico' ? 'selected' : '' }}>Médico</option>
                <option value="Enfermeiro" {{ request('role') === 'Enfermeiro' ? 'selected' : '' }}>Enfermeiro</option>
                <option value="Técnico de Laboratório" {{ request('role') === 'Técnico de Laboratório' ? 'selected' : '' }}>Técnico de Laboratório</option>
            </select>
        </div>

        <div class="col-span-2 flex items-center justify-end gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-search text-xs"></i>
                <span>Filtrar</span>
            </button>
            <a href="{{ route('users.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Users Table Card --}}
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Utilizadores Registados</h3>
        </div>
        <span class="badge-neutral font-medium">{{ $users->total() }} utilizadores</span>
    </div>

    @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Utilizador</th>
                        <th>Email</th>
                        <th>Perfil / Função</th>
                        <th>Data de Registo</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-brand-500 text-white font-bold text-sm flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-surface-900">{{ $user->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-surface-700 font-mono text-xs">{{ $user->email }}</span>
                        </td>
                        <td>
                            @php
                                $role = method_exists($user, 'getRoleNames') ? ($user->getRoleNames()->first() ?? 'Sem Perfil') : 'Utilizador';
                                $badgeClass = match($role) {
                                    'Administrador' => 'badge-danger',
                                    'Médico' => 'badge-info',
                                    'Enfermeiro' => 'badge-success',
                                    'Técnico de Laboratório' => 'badge-warning',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $role }}</span>
                        </td>
                        <td>
                            <span class="text-xs text-surface-500">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td>
                            @if ($user->email_verified_at)
                                <span class="badge-success">Ativo</span>
                            @else
                                <span class="badge-danger">Inativo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('users.show', $user) }}"
                                   class="btn-icon-tw"
                                   title="Ver">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="btn-icon-tw"
                                   title="Editar">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>

                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn-icon-tw {{ $user->email_verified_at ? 'text-gold-600 hover:bg-gold-50' : 'text-emerald-600 hover:bg-emerald-50' }}" 
                                                title="{{ $user->email_verified_at ? 'Inativar Utilizador' : 'Ativar Utilizador' }}">
                                            <i class="fas {{ $user->email_verified_at ? 'fa-user-xmark' : 'fa-user-check' }} text-xs"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Tem certeza que deseja remover este utilizador?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-tw text-crimson-600 hover:bg-crimson-50" title="Eliminar">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $users->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $users->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $users->total() }}</span> utilizadores
            </p>
            <div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-users-slash text-3xl text-surface-400"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhum utilizador encontrado</h3>
            <p class="text-sm text-surface-500">Ajuste os filtros de pesquisa ou adicione um novo utilizador.</p>
        </div>
    @endif
</div>
@endsection