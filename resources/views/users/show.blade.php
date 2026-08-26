@extends('layouts.app-tw')

@section('title', 'Perfil de Utilizador')
@section('page-title', 'Perfil de Utilizador Profissional')
@section('title-icon', 'fa-user-tie')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('users.index') }}">Utilizadores</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">{{ $user->name }}</span>
@endsection

@section('content')
<div class="w-full mx-auto space-y-6">

    {{-- HEADER & PERFIL BANNER --}}
    <div class="card-tw p-6 bg-gradient-to-r from-brand-800 via-brand-700 to-ocean-800 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-xl">
        <div class="flex items-center gap-5">
            <div class="relative shrink-0">
                <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white font-black text-3xl border border-white/20 shadow-2xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @if($user->email_verified_at)
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-400 border-2 border-brand-800 flex items-center justify-center text-3xs text-brand-950 font-bold" title="Conta Ativa">
                        <i class="fas fa-check"></i>
                    </span>
                @endif
            </div>

            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-gold-400 text-surface-900 font-extrabold text-3xs uppercase tracking-wider">
                        {{ $user->roles->first()?->name ?? 'Utilizador' }}
                    </span>
                    <span class="text-2xs text-white/70 font-mono">ID #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-white">
                    {{ $user->name }}
                </h2>

                <p class="text-xs text-white/80 flex flex-wrap items-center gap-3">
                    <span><i class="fas fa-envelope text-gold-400 mr-1"></i> {{ $user->email }}</span>
                    @if($user->especialidade)
                        <span>·</span>
                        <span><i class="fas fa-stethoscope text-emerald-400 mr-1"></i> {{ $user->especialidade }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            @if(auth()->id() !== $user->id)
                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-tw {{ $user->email_verified_at ? 'bg-gold-400 hover:bg-gold-300 text-surface-900' : 'bg-emerald-500 hover:bg-emerald-400 text-white' }} font-bold text-xs py-2 px-3.5 shadow-sm">
                        <i class="fas {{ $user->email_verified_at ? 'fa-user-xmark' : 'fa-user-check' }} text-xs"></i>
                        <span>{{ $user->email_verified_at ? 'Inativar Utilizador' : 'Ativar Utilizador' }}</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('users.edit', $user) }}" class="btn-tw bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold py-2 px-3.5 shadow-sm">
                <i class="fas fa-user-pen text-xs"></i>
                <span>Editar Utilizador</span>
            </a>

            <a href="{{ route('users.index') }}" class="btn-secondary-tw text-xs py-2 px-3.5">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Lista de Utilizadores</span>
            </a>
        </div>
    </div>

    {{-- STAT CARDS DO PROFISSIONAL --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $stats['total_consultations'] ?? 0 }}</p>
                <p class="stat-card-label">Total de Consultas Realizadas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-card-value">{{ $stats['this_month'] ?? 0 }}</p>
                <p class="stat-card-label">Atendimentos Este Mês</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
                <i class="fas fa-id-badge"></i>
            </div>
            <div>
                <p class="stat-card-value text-xs font-mono font-bold">{{ $user->crm ?? 'N/A' }}</p>
                <p class="stat-card-label">NID / Ordem Médica</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <p class="stat-card-value text-xs font-bold">{{ $user->email_verified_at ? 'Conta Verificada' : 'Pendente' }}</p>
                <p class="stat-card-label">Status da Conta</p>
            </div>
        </div>
    </div>

    {{-- CONTEÚDO PRINCIPAL EM GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUNA 1: DADOS COMPONENTES --}}
        <div class="card-tw p-6 space-y-4">
            <div class="border-b border-surface-100 pb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-circle-info text-brand-600"></i> Informações do Perfil
                </h3>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Nome Completo</span>
                    <span class="font-bold text-surface-900">{{ $user->name }}</span>
                </div>

                <div>
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Email Profissional</span>
                    <span class="font-bold font-mono text-brand-700">{{ $user->email }}</span>
                </div>

                <div>
                    <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Função / Perfil</span>
                    <span class="badge-success text-3xs uppercase font-bold">{{ $user->roles->first()?->name ?? 'Utilizador' }}</span>
                </div>

                @if($user->telefone)
                    <div>
                        <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Contacto Telefónico</span>
                        <span class="font-bold font-mono text-surface-800">{{ $user->telefone }}</span>
                    </div>
                @endif

                @if($user->crm)
                    <div>
                        <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">NID Profissional / CRM</span>
                        <span class="font-bold font-mono text-surface-800">{{ $user->crm }}</span>
                    </div>
                @endif

                @if($user->especialidade)
                    <div>
                        <span class="text-2xs text-surface-400 font-bold uppercase tracking-wider block">Especialidade Clínica</span>
                        <span class="font-bold text-surface-800">{{ $user->especialidade }}</span>
                    </div>
                @endif

                <div class="pt-3 border-t border-surface-100 space-y-2">
                    <div>
                        <span class="text-3xs text-surface-400 block">Data de Registo no Sistema:</span>
                        <span class="font-mono text-2xs text-surface-600">{{ $user->created_at->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-3xs text-surface-400 block">Última Atualização:</span>
                        <span class="font-mono text-2xs text-surface-600">{{ $user->updated_at->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                </div>

                @if($user->id !== auth()->id())
                    <div class="pt-3 border-t border-surface-100">
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Tem a certeza que deseja remover este utilizador?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-tw btn-sm-tw w-full justify-center">
                                <i class="fas fa-trash-can text-xs"></i>
                                <span>Remover Utilizador</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- COLUNA 2 & 3: HISTÓRICO DE ATENDIMENTOS CLÍNICOS --}}
        <div class="lg:col-span-2 card-tw overflow-hidden space-y-4">
            <div class="p-6 pb-0 flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-history text-brand-600"></i> Consultas & Atendimentos Recentes
                </h3>
                <span class="text-xs text-surface-400 font-semibold">{{ $stats['total_consultations'] ?? 0 }} Consultas</span>
            </div>

            <div class="table-container-tw">
                <table class="table-tw">
                    <thead>
                        <tr>
                            <th>Data & Hora</th>
                            <th>Paciente / Gestante</th>
                            <th>Tipo de Consulta</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->consultations()->with('patient')->latest()->take(10)->get() as $consultation)
                            <tr>
                                <td class="text-xs font-mono text-surface-800">
                                    {{ $consultation->data_consulta?->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @if($consultation->patient)
                                        <a href="{{ route('patients.show', $consultation->patient) }}" class="font-bold text-brand-700 hover:underline block">
                                            {{ $consultation->patient->nome_completo }}
                                        </a>
                                        <span class="text-3xs text-surface-400">BI: {{ $consultation->patient->documento_bi ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-surface-400 italic">Paciente Desconhecida</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-xs font-medium text-surface-800 capitalize">
                                        {{ str_replace('_', ' ', $consultation->tipo_consulta) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-success text-3xs capitalize">
                                        {{ $consultation->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-surface-400">
                                    <i class="fas fa-calendar-xmark text-3xl mb-2"></i>
                                    <p class="text-xs font-semibold">Nenhuma consulta registada por este profissional.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection