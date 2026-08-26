@extends('layouts.app-tw')

@section('title', 'Detalhes da Vacinação')
@section('page-title', 'Ficha de Imunização')
@section('title-icon', 'fa-syringe')

@section('breadcrumbs')
    <a href="{{ route('vaccines.index') }}">Vacinas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Detalhes</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Details Card --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-syringe"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Registo #{{ $vaccine->id }}</h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('vaccines.edit', $vaccine) }}" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-pen text-xs"></i>
                    <span>Editar</span>
                </a>
                <a href="{{ route('vaccines.index') }}" class="btn-secondary-tw btn-sm-tw">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Voltar</span>
                </a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-2xs font-semibold uppercase text-surface-400">Gestante</p>
                @if($vaccine->patient)
                    <a href="{{ route('patients.show', $vaccine->patient) }}" class="font-bold text-surface-900 text-base hover:text-brand-600">
                        {{ $vaccine->patient->nome_completo }}
                    </a>
                    <p class="text-xs text-surface-500">BI: {{ $vaccine->patient->documento_bi }}</p>
                @else
                    <span class="text-surface-400 italic">N/D</span>
                @endif
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase text-surface-400">Status da Dose</p>
                @php
                    $badgeClass = match($vaccine->status) {
                        'administrada' => 'badge-success',
                        'pendente' => 'badge-warning',
                        'vencida' => 'badge-danger',
                        default => 'badge-neutral'
                    };
                @endphp
                <span class="{{ $badgeClass }} text-xs mt-1 inline-block">{{ ucfirst($vaccine->status) }}</span>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase text-surface-400">Tipo de Vacina / Dose</p>
                <p class="font-semibold text-surface-800">{{ $vaccine->descricao ?? $vaccine->tipo_vacina }} — Dose {{ $vaccine->dose_numero ?? 1 }}</p>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase text-surface-400">Data da Aplicação</p>
                <p class="font-medium text-surface-800">{{ $vaccine->data_administracao ? $vaccine->data_administracao->format('d/m/Y') : 'Pendente' }}</p>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase text-surface-400">Local de Aplicação</p>
                <p class="text-surface-800">{{ ucfirst(str_replace('_', ' ', $vaccine->local_aplicacao ?? 'N/D')) }}</p>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase text-surface-400">Lote / Fabricante</p>
                <p class="text-surface-800">{{ $vaccine->lote ?? 'N/D' }} ({{ $vaccine->fabricante ?? 'N/D' }})</p>
            </div>
        </div>

        @if($vaccine->observacoes)
        <div class="px-6 pb-6 pt-2 border-t border-surface-100">
            <p class="text-2xs font-semibold uppercase text-surface-400 mb-1">Observações</p>
            <p class="text-xs text-surface-700 bg-surface-50 p-3 rounded-lg border border-surface-200/60">{{ $vaccine->observacoes }}</p>
        </div>
        @endif
    </div>

    {{-- Outras Doses da Mesma Gestante --}}
    @if(isset($outrasVacinas) && $outrasVacinas->count() > 0)
    <div class="card-tw">
        <div class="card-header-tw">
            <h3 class="text-sm font-semibold text-surface-900">Histórico de Outras Doses ({{ $vaccine->tipo_vacina }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Dose</th>
                        <th>Data Aplicação</th>
                        <th>Status</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outrasVacinas as $ov)
                    <tr>
                        <td>Dose {{ $ov->dose_numero }}</td>
                        <td>{{ $ov->data_administracao ? $ov->data_administracao->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge-neutral">{{ ucfirst($ov->status) }}</span></td>
                        <td class="text-right">
                            <a href="{{ route('vaccines.show', $ov) }}" class="btn-icon-tw">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
