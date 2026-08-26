@extends('layouts.app-tw')

@section('title', 'Consultas')
@section('page-title', 'Gestão de Consultas')
@section('title-icon', 'fa-calendar-check')

@section('breadcrumbs')
    <span class="active">Consultas</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Lista de Consultas ANC</h2>
        <p class="text-sm text-surface-500">Gerencie as consultas pré-natais agendadas e realizadas</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('consultations.create') }}" class="btn-primary-tw">
            <i class="fas fa-plus text-xs"></i>
            <span>Nova Consulta</span>
        </a>
        <a href="{{ route('exams.pending-results') }}" class="btn-secondary-tw">
            <i class="fas fa-flask text-xs text-gold-600"></i>
            <span>Exames Pendentes</span>
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label-tw">Status</label>
            <select name="status" class="input-tw">
                <option value="">Todos os status</option>
                <option value="agendada" {{ request('status') === 'agendada' ? 'selected' : '' }}>Agendada</option>
                <option value="confirmada" {{ request('status') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                <option value="realizada" {{ request('status') === 'realizada' ? 'selected' : '' }}>Realizada</option>
                <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Tipo de Consulta</label>
            <select name="tipo" class="input-tw">
                <option value="">Todos os tipos</option>
                <option value="1_trimestre" {{ request('tipo') === '1_trimestre' ? 'selected' : '' }}>1º Trimestre</option>
                <option value="2_trimestre" {{ request('tipo') === '2_trimestre' ? 'selected' : '' }}>2º Trimestre</option>
                <option value="3_trimestre" {{ request('tipo') === '3_trimestre' ? 'selected' : '' }}>3º Trimestre</option>
                <option value="pos_parto" {{ request('tipo') === 'pos_parto' ? 'selected' : '' }}>Pós-parto</option>
                <option value="emergencia" {{ request('tipo') === 'emergencia' ? 'selected' : '' }}>Emergência</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Data da Consulta</label>
            <input type="date" name="data" class="input-tw" value="{{ request('data') }}">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw flex-1">
                <i class="fas fa-search text-xs"></i>
                <span>Filtrar</span>
            </button>
            <a href="{{ route('consultations.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Consultations Table --}}
<div class="card-tw overflow-hidden">
    @if($consultations->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Gestante</th>
                        <th>Data / Hora</th>
                        <th>Tipo</th>
                        <th>Semanas</th>
                        <th>Status</th>
                        <th>Profissional</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultations as $consultation)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($consultation->patient->nome_completo ?? 'G', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('patients.show', $consultation->patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                        {{ $consultation->patient->nome_completo }}
                                    </a>
                                    <p class="text-2xs text-surface-400">BI: {{ $consultation->patient->documento_bi }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="font-medium text-surface-800">{{ $consultation->data_consulta->format('d/m/Y') }}</p>
                            <p class="text-2xs text-surface-400">{{ $consultation->data_consulta->format('H:i') }}</p>
                        </td>
                        <td>
                            <span class="badge-info">{{ $consultation->tipo_consulta_label }}</span>
                        </td>
                        <td>
                            <span class="font-medium text-surface-800">{{ $consultation->semanas_gestacao ?? 'N/A' }}ª</span>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($consultation->status) {
                                    'realizada' => 'badge-success',
                                    'confirmada' => 'badge-info',
                                    'agendada' => 'badge-warning',
                                    'cancelada' => 'badge-danger',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst($consultation->status) }}</span>
                        </td>
                        <td>
                            <span class="text-xs text-surface-600">{{ $consultation->user->name ?? 'Sistema' }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('consultations.show', $consultation) }}"
                                   class="btn-icon-tw"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('consultations.edit', $consultation) }}"
                                   class="btn-icon-tw"
                                   title="Editar">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @if($consultation->status !== 'realizada')
                                    <form method="POST" action="{{ route('consultations.complete', $consultation) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-icon-tw text-brand-600 hover:bg-brand-50" title="Marcar como Realizada">
                                            <i class="fas fa-check text-xs"></i>
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
                Mostrando <span class="font-medium text-surface-800">{{ $consultations->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $consultations->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $consultations->total() }}</span> consultas
            </p>
            <div>
                {{ $consultations->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-surface-100 flex items-center justify-center">
                <i class="fas fa-calendar-xmark text-3xl text-surface-400"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhuma consulta encontrada</h3>
            <p class="text-sm text-surface-500 mb-4">Ajuste os filtros ou agende uma nova consulta pré-natal.</p>
            <a href="{{ route('consultations.create') }}" class="btn-primary-tw">
                <i class="fas fa-plus text-xs"></i>
                <span>Agendar Consulta</span>
            </a>
        </div>
    @endif
</div>
@endsection