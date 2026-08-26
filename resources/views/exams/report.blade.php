@extends('layouts.app-tw')

@section('title', 'Relatório de Exames')
@section('page-title', 'Relatório de Exames Laboratoriais')
@section('title-icon', 'fa-file-invoice')

@section('breadcrumbs')
    <a href="{{ route('exams.index') }}">Exames</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Relatório</span>
@endsection

@section('content')
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div>
            <h3 class="text-base font-semibold text-surface-900">Relatório Consolidado de Exames</h3>
            <p class="text-xs text-surface-500">Resumo por período e tipo de exame</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-print text-xs"></i>
                <span>Imprimir</span>
            </button>
            <a href="{{ route('exams.index') }}" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>
        </div>
    </div>

    <div class="card-body-tw">
        <div class="mb-6 p-4 bg-surface-50 rounded-xl border border-surface-200 text-xs">
            <h6 class="font-bold text-surface-700 uppercase tracking-wider text-2xs mb-2">Filtros Aplicados:</h6>
            <div class="flex flex-wrap gap-4">
                <p><strong>Período:</strong> 
                    {{ request('data_inicio') ? \Carbon\Carbon::parse(request('data_inicio'))->format('d/m/Y') : 'Início' }} 
                    até 
                    {{ request('data_fim') ? \Carbon\Carbon::parse(request('data_fim'))->format('d/m/Y') : 'Fim' }}
                </p>
                @if(request('tipo_exame'))
                    <p><strong>Tipo de Exame:</strong> {{ $tiposExames[request('tipo_exame')] ?? request('tipo_exame') }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="card-tw p-4 text-center">
                <p class="text-2xl font-bold text-ocean-600">{{ $exams->where('status', 'solicitado')->count() }}</p>
                <p class="text-xs text-surface-500 uppercase tracking-wider mt-1">Solicitados</p>
            </div>
            <div class="card-tw p-4 text-center">
                <p class="text-2xl font-bold text-brand-600">{{ $exams->where('status', 'realizado')->count() }}</p>
                <p class="text-xs text-surface-500 uppercase tracking-wider mt-1">Realizados</p>
            </div>
            <div class="card-tw p-4 text-center">
                <p class="text-2xl font-bold text-gold-600">{{ $exams->where('status', 'pendente')->count() }}</p>
                <p class="text-xs text-surface-500 uppercase tracking-wider mt-1">Pendentes</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Data Solicitação</th>
                        <th>Data Realização</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td class="font-mono text-xs text-surface-500">#{{ $exam->id }}</td>
                        <td class="font-semibold text-surface-900">{{ $exam->consultation->patient->nome_completo }}</td>
                        <td>{{ $tiposExames[$exam->tipo_exame] ?? $exam->tipo_exame }}</td>
                        <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                        <td>{{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : '--' }}</td>
                        <td>
                            @if($exam->status === 'realizado')
                                <span class="badge-success">Realizado</span>
                            @elseif($exam->status === 'solicitado')
                                <span class="badge-warning">Solicitado</span>
                            @else
                                <span class="badge-neutral">Pendente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-surface-500">
                            <i class="fas fa-exclamation-circle text-2xl text-gold-500 mb-2"></i>
                            <p class="text-sm font-semibold">Nenhum exame encontrado com os filtros selecionados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection