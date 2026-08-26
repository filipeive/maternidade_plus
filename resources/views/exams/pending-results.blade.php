@extends('layouts.app-tw')

@section('title', 'Exames Pendentes')
@section('page-title', 'Exames Pendentes de Resultado')
@section('title-icon', 'fa-clock')

@section('breadcrumbs')
    <a href="{{ route('exams.index') }}">Exames</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Pendentes</span>
@endsection

@section('content')
<div class="card-tw overflow-hidden">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Exames Solicitados Aguardando Resultado</h3>
        </div>
        <span class="badge-warning">
            <i class="fas fa-flask mr-1 text-2xs"></i>{{ $exams->total() }} Pendentes
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-tw">
            <thead>
                <tr>
                    <th># ID</th>
                    <th>Gestante</th>
                    <th>Tipo de Exame</th>
                    <th>Data Solicitação</th>
                    <th>Profissional Solicitante</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr>
                    <td class="font-mono text-xs text-surface-500">#{{ $exam->id }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($exam->consultation->patient->nome_completo ?? 'G', 0, 1)) }}
                            </div>
                            <a href="{{ route('patients.show', $exam->consultation->patient) }}"
                               class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                {{ $exam->consultation->patient->nome_completo }}
                            </a>
                        </div>
                    </td>
                    <td>
                        <span class="font-medium text-surface-900">{{ $tiposExames[$exam->tipo_exame] ?? $exam->tipo_exame }}</span>
                    </td>
                    <td>
                        <span class="text-xs text-surface-700">{{ $exam->data_solicitacao?->format('d/m/Y') ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="text-xs text-surface-600">{{ $exam->consultation->user->name ?? 'Sistema' }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('exams.result-form', $exam) }}"
                               class="btn-primary-tw btn-sm-tw"
                               title="Registrar Resultado">
                                <i class="fas fa-file-signature text-xs"></i>
                                <span>Lançar Resultado</span>
                            </a>
                            <a href="{{ route('exams.show', $exam) }}"
                               class="btn-icon-tw"
                               title="Ver Detalhes">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center">
                            <i class="fas fa-check-circle text-3xl text-brand-500"></i>
                        </div>
                        <h3 class="text-base font-semibold text-surface-800 mb-1">Todos os exames estão em dia!</h3>
                        <p class="text-sm text-surface-500">Nenhum exame pendente de resultado no momento.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($exams->count() > 0)
        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $exams->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $exams->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $exams->total() }}</span> exames
            </p>
            <div>
                {{ $exams->links() }}
            </div>
        </div>
    @endif
</div>
@endsection