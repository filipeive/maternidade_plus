@extends('layouts.app-tw')

@section('title', 'Lista de Partos')
@section('page-title', 'Partos Registrados')
@section('title-icon', 'fa-baby')

@section('breadcrumbs')
    <span class="active">Partos</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Registos de Partos</h2>
        <p class="text-sm text-surface-500">Histórico de nascimentos e partos assistidos</p>
    </div>

    <div class="flex items-center gap-2" x-data="{open: false}" @click.outside="open = false">
        <div class="relative">
            <button @click="open = !open" class="btn-secondary-tw">
                <i class="fas fa-filter text-xs text-brand-600"></i>
                <span>{{ request()->has('tipo') ? ucfirst(request()->tipo) : 'Todos os Partos' }}</span>
                <i class="fas fa-chevron-down text-2xs text-surface-400"></i>
            </button>
            <div x-show="open" x-transition class="dropdown-tw w-48 right-0" x-cloak>
                <a href="{{ route('births.index') }}" class="dropdown-item-tw">Todos os Partos</a>
                <div class="dropdown-divider-tw"></div>
                <a href="{{ route('births.index', ['tipo' => 'normal']) }}" class="dropdown-item-tw">Partos Normais</a>
                <a href="{{ route('births.index', ['tipo' => 'cesariana']) }}" class="dropdown-item-tw">Cesarianas</a>
            </div>
        </div>
    </div>
</div>

<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                <i class="fas fa-baby"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Partos Registrados no Sistema</h3>
        </div>
        <span class="badge-neutral font-medium">{{ $births->total() }} registos</span>
    </div>

    @if($births->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Data / Hora</th>
                        <th>Paciente / Mãe</th>
                        <th>Tipo de Parto</th>
                        <th>Dados do Bebê</th>
                        <th>Profissional Responsável</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($births as $birth)
                    <tr>
                        <td>
                            <p class="font-medium text-surface-800">{{ $birth->data_hora_parto->format('d/m/Y') }}</p>
                            <p class="text-2xs text-surface-400">{{ $birth->data_hora_parto->format('H:i') }}</p>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($birth->patient->nome_completo ?? 'G', 0, 1)) }}
                                </div>
                                <a href="{{ route('patients.show', $birth->patient) }}"
                                   class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                    {{ $birth->patient->nome_completo }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <span class="{{ $birth->tipo_parto === 'normal' ? 'badge-success' : 'badge-warning' }}">
                                {{ $birth->tipo_parto_formatado }}
                            </span>
                        </td>
                        <td>
                            @if($birth->peso_nascimento)
                                <p class="font-medium text-surface-800 text-xs">{{ $birth->peso_formatado }}</p>
                                <p class="text-2xs text-surface-400">{{ $birth->sexo_bebe ? ucfirst($birth->sexo_bebe) : 'N/A' }}</p>
                            @else
                                <span class="text-2xs text-surface-400 italic">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-xs text-surface-600">{{ $birth->user->name ?? 'Sistema' }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('births.show', $birth) }}"
                               class="btn-icon-tw"
                               title="Ver Detalhes">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $births->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $births->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $births->total() }}</span> partos
            </p>
            <div>
                {{ $births->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-baby text-3xl text-surface-400"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhum parto registrado</h3>
            <p class="text-sm text-surface-500">Os partos são vinculados diretamente ao cadastro das gestantes.</p>
        </div>
    @endif
</div>
@endsection