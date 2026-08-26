@extends('layouts.app-tw')

@section('title', 'Gestantes')
@section('page-title', 'Gestão de Gestantes')
@section('title-icon', 'fa-person-pregnant')

@section('breadcrumbs')
    <span class="active">Gestantes</span>
@endsection

@section('content')

{{-- Header & Action Button --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Lista de Gestantes</h2>
        <p class="text-sm text-surface-500">Gerencie o cadastro e acompanhamento das gestantes</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn-primary-tw shrink-0">
        <i class="fas fa-user-plus text-xs"></i>
        <span>Nova Gestante</span>
    </a>
</div>

{{-- Filter Card --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('patients.index') }}" class="flex flex-col sm:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Pesquisar por nome, BI ou contacto..."
                   class="input-tw pl-9">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit" class="btn-primary-tw btn-sm-tw flex-1 sm:flex-none">
                <i class="fas fa-search text-xs"></i>
                <span>Pesquisar</span>
            </button>
            @if(request('search'))
                <a href="{{ route('patients.index') }}" class="btn-secondary-tw btn-sm-tw flex-1 sm:flex-none">
                    <i class="fas fa-times text-xs"></i>
                    <span>Limpar</span>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Main Patients Table Card --}}
<div class="card-tw overflow-hidden">
    @if($patients->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Idade</th>
                        <th>Documento BI</th>
                        <th>Contacto</th>
                        <th>Idade Gestacional</th>
                        <th>Status</th>
                        <th>Próxima Consulta</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                        {{ $patient->nome_completo }}
                                    </a>
                                    <p class="text-2xs text-surface-400">
                                        GPA: G{{ $patient->numero_gestacoes }}P{{ $patient->numero_partos }}A{{ $patient->numero_abortos }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-medium text-surface-800">{{ $patient->idade }} anos</span>
                        </td>
                        <td>
                            <span class="font-mono text-xs text-surface-600">{{ $patient->documento_bi ?? 'N/D' }}</span>
                        </td>
                        <td>
                            <span class="text-surface-700">{{ $patient->contacto ?? 'N/D' }}</span>
                        </td>
                        <td>
                            @if($patient->idade_gestacional)
                                <span class="badge-info">
                                    <i class="fas fa-calendar-week mr-1 text-2xs"></i>{{ $patient->idade_gestacional }}ª semana
                                </span>
                                <p class="text-2xs text-surface-400 mt-0.5">{{ $patient->trimestre }}</p>
                            @else
                                <span class="text-surface-400">N/A</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $status = $patient->status_gravidez;
                                $badgeClass = match($status) {
                                    'Gestante' => 'badge-success',
                                    'A termo' => 'badge-warning',
                                    'Pós-parto' => 'badge-neutral',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $status }}</span>
                        </td>
                        <td>
                            @if($patient->consultations->count() > 0)
                                @php $proximaConsulta = $patient->consultations->first(); @endphp
                                <div class="text-xs">
                                    <p class="font-medium text-surface-800">{{ $proximaConsulta->data_consulta->format('d/m/Y') }}</p>
                                    <p class="text-2xs text-surface-400">{{ $proximaConsulta->data_consulta->format('H:i') }}</p>
                                </div>
                            @else
                                <span class="text-2xs text-surface-400 italic">Nenhuma agendada</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('patients.show', $patient) }}"
                                   class="btn-icon-tw"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}"
                                   class="btn-icon-tw"
                                   title="Editar">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <a href="{{ route('consultations.create', $patient) }}"
                                   class="btn-icon-tw text-brand-600 hover:bg-brand-50"
                                   title="Nova Consulta">
                                    <i class="fas fa-calendar-plus text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer & Pagination --}}
        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $patients->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $patients->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $patients->total() }}</span> gestantes
                @if(request('search'))
                    (filtrado por "{{ request('search') }}")
                @endif
            </p>
            <div>
                {{ $patients->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-person-pregnant text-3xl text-surface-400"></i>
            </div>
            @if(request('search'))
                <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhuma gestante encontrada</h3>
                <p class="text-sm text-surface-500 mb-4">
                    Não foram encontradas gestantes com o termo "{{ request('search') }}".
                </p>
                <a href="{{ route('patients.index') }}" class="btn-secondary-tw">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Ver Todas as Gestantes</span>
                </a>
            @else
                <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhuma gestante cadastrada</h3>
                <p class="text-sm text-surface-500 mb-4">Comece adicionando a primeira gestante ao sistema.</p>
                <a href="{{ route('patients.create') }}" class="btn-primary-tw">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Cadastrar Primeira Gestante</span>
                </a>
            @endif
        </div>
    @endif
</div>

@endsection