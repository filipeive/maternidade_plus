@extends('layouts.app-tw')

@section('title', 'Detalhes da Consulta')
@section('page-title', 'Consulta — ' . $consultation->patient->nome_completo)
@section('title-icon', 'fa-calendar-check')

@section('breadcrumbs')
    <a href="{{ route('consultations.index') }}">Consultas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Detalhes</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Column (2/3) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Main Details Card --}}
        <div class="card-tw">
            <div class="card-header-tw flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-surface-900">Consulta ANC — {{ $consultation->data_consulta->format('d/m/Y H:i') }}</h3>
                        <p class="text-xs text-surface-500">Gestante: <strong>{{ $consultation->patient->nome_completo }}</strong></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @if($consultation->status !== 'realizada')
                        <a href="{{ route('consultations.edit', $consultation) }}" class="btn-secondary-tw btn-sm-tw">
                            <i class="fas fa-edit text-xs"></i>
                            <span>Editar</span>
                        </a>
                    @endif
                    @if($consultation->status === 'agendada')
                        <form method="POST" action="{{ route('consultations.confirm', $consultation) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-primary-tw btn-sm-tw">
                                <i class="fas fa-check text-xs"></i>
                                <span>Confirmar</span>
                            </button>
                        </form>
                    @endif
                    @if($consultation->status === 'confirmada')
                        <form method="POST" action="{{ route('consultations.complete', $consultation) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-primary-tw btn-sm-tw">
                                <i class="fas fa-check-double text-xs"></i>
                                <span>Marcar Realizada</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card-body-tw">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Left Column: Info Básica --}}
                    <div class="space-y-3 text-xs">
                        <h6 class="font-bold text-brand-700 uppercase tracking-wider text-2xs border-b border-surface-100 pb-1">
                            Informações Gerais
                        </h6>
                        <p class="flex justify-between">
                            <span class="text-surface-500">Data e Hora:</span>
                            <span class="font-semibold text-surface-900">{{ $consultation->data_consulta->format('d/m/Y H:i') }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-surface-500">Tipo de Consulta:</span>
                            <span class="badge-info">{{ $consultation->tipo_consulta_label }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-surface-500">Status:</span>
                            @php
                                $badgeStatus = match($consultation->status) {
                                    'realizada' => 'badge-success',
                                    'confirmada' => 'badge-info',
                                    'agendada' => 'badge-warning',
                                    'cancelada' => 'badge-danger',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeStatus }}">{{ ucfirst($consultation->status) }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-surface-500">Profissional Responsável:</span>
                            <span class="font-semibold text-surface-900">{{ $consultation->user->name ?? 'Sistema' }}</span>
                        </p>
                        @if($consultation->proxima_consulta)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Próxima Consulta:</span>
                                <span class="font-semibold text-brand-600">{{ $consultation->proxima_consulta->format('d/m/Y') }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Right Column: Medições --}}
                    <div class="space-y-3 text-xs">
                        <h6 class="font-bold text-brand-700 uppercase tracking-wider text-2xs border-b border-surface-100 pb-1">
                            Medições & Vitais
                        </h6>
                        @if($consultation->semanas_gestacao)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Semanas de Gestação:</span>
                                <span class="badge-info">{{ $consultation->semanas_gestacao }}ª semana</span>
                            </p>
                        @endif
                        @if($consultation->peso)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Peso:</span>
                                <span class="font-semibold text-surface-900">{{ $consultation->peso }} kg</span>
                            </p>
                        @endif
                        @if($consultation->pressao_arterial)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Pressão Arterial:</span>
                                <span class="font-semibold text-surface-900">{{ $consultation->pressao_arterial }} mmHg</span>
                            </p>
                        @endif
                        @if($consultation->batimentos_fetais)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Batimentos Fetais (BCF):</span>
                                <span class="font-semibold text-surface-900">{{ $consultation->batimentos_fetais }} bpm</span>
                            </p>
                        @endif
                        @if($consultation->altura_uterina)
                            <p class="flex justify-between">
                                <span class="text-surface-500">Altura Uterina:</span>
                                <span class="font-semibold text-surface-900">{{ $consultation->altura_uterina }} cm</span>
                            </p>
                        @endif
                    </div>
                </div>

                @if($consultation->observacoes)
                    <div class="mt-6 pt-4 border-t border-surface-100">
                        <h6 class="font-bold text-surface-800 text-xs mb-2">Observações / Achados Clínicos</h6>
                        <div class="p-3 bg-surface-50 rounded-lg text-xs text-surface-700 leading-relaxed border border-surface-100">
                            {{ $consultation->observacoes }}
                        </div>
                    </div>
                @endif

                @if($consultation->orientacoes)
                    <div class="mt-4">
                        <h6 class="font-bold text-brand-800 text-xs mb-2">Orientações e Recomendações</h6>
                        <div class="p-3 bg-brand-50/50 border-l-4 border-brand-500 rounded-r-lg text-xs text-brand-900 leading-relaxed">
                            {{ $consultation->orientacoes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Exames Relacionados --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <h5 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-microscope text-brand-500"></i> Exames Solicitados nesta Consulta
                </h5>
                <a href="{{ route('exams.create', ['consultation_id' => $consultation->id]) }}" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Solicitar Exame</span>
                </a>
            </div>
            <div class="p-4">
                @if($consultation->exams->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table-tw">
                            <thead>
                                <tr>
                                    <th>Tipo de Exame</th>
                                    <th>Solicitação</th>
                                    <th>Realização</th>
                                    <th>Status</th>
                                    <th class="text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultation->exams as $exam)
                                <tr>
                                    <td>
                                        <strong class="text-surface-900">{{ $exam->tipo_exame_label }}</strong>
                                        @if($exam->descricao_exame)
                                            <p class="text-2xs text-surface-400">{{ $exam->descricao_exame }}</p>
                                        @endif
                                    </td>
                                    <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                                    <td>
                                        @if($exam->data_realizacao)
                                            {{ $exam->data_realizacao->format('d/m/Y') }}
                                        @else
                                            <span class="text-surface-400 text-xs italic">Pendente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $exBadge = match($exam->status) {
                                                'realizado' => 'badge-success',
                                                'solicitado' => 'badge-warning',
                                                default => 'badge-neutral'
                                            };
                                        @endphp
                                        <span class="{{ $exBadge }}">{{ ucfirst($exam->status) }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('exams.show', $exam) }}" class="btn-icon-tw" title="Ver Exame">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-surface-500">
                        <i class="fas fa-flask text-2xl text-surface-300 mb-2"></i>
                        <p class="text-xs">Nenhum exame solicitado nesta consulta.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Sidebar (1/3) --}}
    <div class="space-y-6">

        {{-- Gestante Info Card --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Perfil da Gestante</h6>
            </div>
            <div class="card-body-tw space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($consultation->patient->nome_completo ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="font-bold text-surface-900 text-sm">{{ $consultation->patient->nome_completo }}</h6>
                        <p class="text-2xs text-surface-500">BI: {{ $consultation->patient->documento_bi }}</p>
                    </div>
                </div>

                <div class="border-t border-surface-100 pt-3 space-y-1.5 text-xs">
                    <p class="flex justify-between">
                        <span class="text-surface-500">Idade:</span>
                        <span class="font-semibold text-surface-800">{{ $consultation->patient->idade }} anos</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-surface-500">Contacto:</span>
                        <span class="font-semibold text-surface-800">{{ $consultation->patient->contacto }}</span>
                    </p>
                    @if($consultation->patient->semanas_gestacao)
                        <p class="flex justify-between">
                            <span class="text-surface-500">Idade Gestacional:</span>
                            <span class="badge-info">{{ $consultation->patient->semanas_gestacao }}ª semana</span>
                        </p>
                    @endif
                    @if($consultation->patient->tipo_sanguineo)
                        <p class="flex justify-between">
                            <span class="text-surface-500">Tipo Sanguíneo:</span>
                            <span class="badge-danger">{{ $consultation->patient->tipo_sanguineo }}</span>
                        </p>
                    @endif
                </div>

                @if($consultation->patient->alergias)
                    <div class="bg-gold-50 border border-gold-200 text-gold-900 p-2.5 rounded-lg text-2xs">
                        <strong>⚠️ Alergias:</strong> {{ $consultation->patient->alergias }}
                    </div>
                @endif

                <div class="border-t border-surface-100 pt-3 flex flex-col gap-2">
                    <a href="{{ route('patients.show', $consultation->patient) }}" class="btn-secondary-tw btn-sm-tw w-full">
                        <i class="fas fa-user text-xs"></i>
                        <span>Ver Perfil Completo</span>
                    </a>
                    <a href="{{ route('consultations.create', ['patient_id' => $consultation->patient->id]) }}" class="btn-primary-tw btn-sm-tw w-full">
                        <i class="fas fa-calendar-plus text-xs"></i>
                        <span>Agendar Nova Consulta</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection