@extends('layouts.app-tw')

@section('title', 'Detalhes da Visita Domiciliária')
@section('page-title', 'Visita Domiciliária #' . $homeVisit->id)
@section('title-icon', 'fa-house-medical')

@section('breadcrumbs')
    <a href="{{ route('home_visits.index') }}">Visitas Domiciliárias</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Visita #{{ $homeVisit->id }}</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6" x-data="{ 
    completeModalOpen: false,
    rescheduleModalOpen: false,
    resolveModalOpen: false
}">

    {{-- CABEÇALHO & BARRA DE AÇÕES --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-surface-900">Visita Domiciliária #{{ $homeVisit->id }}</h2>
                @php
                    $statusBadge = match($homeVisit->status) {
                        'realizada' => 'badge-success',
                        'agendada' => 'badge-warning',
                        'nao_encontrada' => 'badge-danger',
                        'reagendada' => 'badge-info',
                        'cancelada' => 'badge-neutral',
                        default => 'badge-neutral'
                    };
                @endphp
                <span class="{{ $statusBadge }} text-xs uppercase font-bold">
                    {{ ucfirst(str_replace('_', ' ', $homeVisit->status)) }}
                </span>
            </div>
            <p class="text-xs text-surface-500 mt-0.5">
                Programada para {{ $homeVisit->data_visita ? $homeVisit->data_visita->format('d/m/Y \à\s H:i') : 'Data N/D' }} · Responsável: <span class="font-medium text-surface-700">{{ $homeVisit->user->name ?? 'Agente Comunitário' }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('home_visits.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Voltar</span>
            </a>

            @if($homeVisit->status === 'agendada' || $homeVisit->status === 'reagendada')
                <button type="button" 
                        @click="completeModalOpen = true" 
                        class="btn-primary-tw btn-sm-tw bg-brand-600 hover:bg-brand-700">
                    <i class="fas fa-clipboard-check text-xs"></i>
                    <span>Registar Desfecho / Completar</span>
                </button>

                <button type="button" 
                        @click="rescheduleModalOpen = true" 
                        class="btn-secondary-tw btn-sm-tw text-gold-700 bg-gold-50 border-gold-200 hover:bg-gold-100">
                    <i class="fas fa-calendar-days text-xs text-gold-600"></i>
                    <span>Reagendar</span>
                </button>

                <form method="POST" action="{{ route('home_visits.mark-not-found', $homeVisit) }}" onsubmit="return confirm('Marcar gestante como não encontrada no endereço? Uma nova tentativa será agendada automaticamente.');" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn-secondary-tw btn-sm-tw text-crimson-700 bg-crimson-50 border-crimson-200 hover:bg-crimson-100" title="Marcar como Não Encontrada">
                        <i class="fas fa-user-slash text-xs text-crimson-600"></i>
                        <span>Não Encontrada</span>
                    </button>
                </form>

                <button type="button" 
                        @click="resolveModalOpen = true" 
                        class="btn-secondary-tw btn-sm-tw text-brand-700 bg-brand-50 border-brand-200 hover:bg-brand-100">
                    <i class="fas fa-circle-check text-xs text-brand-600"></i>
                    <span>Atendida na US</span>
                </button>
            @endif

            <a href="{{ route('home_visits.edit', $homeVisit) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-pen text-xs"></i>
                <span>Editar</span>
            </a>
        </div>
    </div>

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUNA PRINCIPAL (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- CARTÃO 1: DADOS DA VISITA --}}
            <div class="card-tw p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="font-bold text-surface-900 text-sm">Dados da Visita Domiciliária</h3>
                    </div>
                    <span class="badge-neutral text-2xs">{{ $homeVisit->tipo_visita_formatada }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-surface-400 font-medium">Data e Hora Programada:</span>
                        <p class="font-bold text-surface-900 text-sm mt-0.5">
                            {{ $homeVisit->data_visita ? $homeVisit->data_visita->format('d/m/Y \à\s H:i') : '-' }}
                        </p>
                        @if($homeVisit->isOverdue())
                            <span class="text-crimson-600 font-bold text-2xs flex items-center gap-1 mt-1">
                                <i class="fas fa-triangle-exclamation"></i> Visita atrasada em relação à data agendada
                            </span>
                        @endif
                    </div>

                    <div>
                        <span class="text-surface-400 font-medium">Responsável pelo Acompanhamento:</span>
                        <p class="font-bold text-surface-900 text-sm mt-0.5 flex items-center gap-1.5">
                            <i class="fas fa-user-nurse text-brand-500"></i>
                            <span>{{ $homeVisit->user->name ?? 'Agente Comunitário' }}</span>
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-surface-400 font-medium">Motivo do Acompanhamento:</span>
                        <p class="font-medium text-surface-800 bg-surface-50 p-2.5 rounded-xl border border-surface-200 mt-1 leading-relaxed">
                            {{ $homeVisit->motivo_visita }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-surface-400 font-medium">Endereço da Visita no Terreno:</span>
                        <p class="font-medium text-surface-800 flex items-start gap-1.5 mt-1">
                            <i class="fas fa-location-dot text-crimson-500 mt-0.5 shrink-0"></i>
                            <span>{{ $homeVisit->endereco_visita ?? $homeVisit->patient->endereco ?? 'Não especificado' }}</span>
                        </p>
                    </div>

                    @if($homeVisit->observacoes_gerais)
                        <div class="sm:col-span-2">
                            <span class="text-surface-400 font-medium">Observações & Histórico:</span>
                            <div class="font-mono text-xs text-surface-700 bg-surface-50 p-3 rounded-xl border border-surface-200 mt-1 whitespace-pre-line">
                                {{ $homeVisit->observacoes_gerais }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CARTÃO 2: RELATÓRIO DA VISITA REALIZADA --}}
            @if($homeVisit->status === 'realizada')
                <div class="card-tw p-5 space-y-5 border-l-4 border-brand-500">
                    <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <h3 class="font-bold text-surface-900 text-sm">Desfecho e Relatório da Visita Realizada</h3>
                        </div>
                        <span class="badge-success text-2xs">Realizada</span>
                    </div>

                    {{-- Alertas Especiais --}}
                    @if($homeVisit->necessita_referencia)
                        <div class="p-3.5 bg-crimson-50 border border-crimson-200 rounded-xl text-xs text-crimson-900 flex items-start gap-3">
                            <i class="fas fa-triangle-exclamation text-crimson-600 text-lg mt-0.5 shrink-0"></i>
                            <div>
                                <h4 class="font-bold text-crimson-800">Necessidade de Referência Médica Identificada!</h4>
                                <p class="text-2xs text-crimson-700 mt-0.5">Durante a visita foi detetado que a paciente precisa de avaliação na Unidade Sanitária.</p>
                            </div>
                        </div>
                    @endif

                    @if($homeVisit->acompanhante_presente)
                        <div class="p-3 bg-ocean-50 border border-ocean-200 rounded-xl text-xs text-ocean-900 flex items-center gap-2">
                            <i class="fas fa-user-group text-ocean-600"></i>
                            <span>Acompanhante / Parceiro esteve presente e participou na orientação comunitária.</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-surface-400 font-medium">Condições de Higiene:</span>
                            <p class="font-bold text-surface-900 mt-0.5">
                                @php
                                    $higieneBadge = match($homeVisit->condicoes_higiene) {
                                        'bom' => 'badge-success',
                                        'regular' => 'badge-warning',
                                        'ruim' => 'badge-danger',
                                        default => 'badge-neutral'
                                    };
                                @endphp
                                <span class="{{ $higieneBadge }}">{{ ucfirst($homeVisit->condicoes_higiene ?? 'N/D') }}</span>
                            </p>
                        </div>

                        <div>
                            <span class="text-surface-400 font-medium">Apoio Familiar:</span>
                            <p class="font-bold text-surface-900 mt-0.5">
                                @php
                                    $apoioBadge = match($homeVisit->apoio_familiar) {
                                        'adequado' => 'badge-success',
                                        'parcial' => 'badge-warning',
                                        'inadequado' => 'badge-danger',
                                        default => 'badge-neutral'
                                    };
                                @endphp
                                <span class="{{ $apoioBadge }}">{{ ucfirst($homeVisit->apoio_familiar ?? 'N/D') }}</span>
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="text-surface-400 font-medium">Observações do Ambiente Domiciliário:</span>
                            <p class="font-medium text-surface-800 bg-surface-50 p-2.5 rounded-xl border border-surface-200 mt-1 leading-relaxed">
                                {{ $homeVisit->observacoes_ambiente ?? 'Sem observações' }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="text-surface-400 font-medium">Orientações Transmitidas (Normas MISAU):</span>
                            <p class="font-medium text-surface-800 bg-brand-50/50 p-3 rounded-xl border border-brand-200 mt-1 leading-relaxed">
                                {{ $homeVisit->orientacoes_dadas }}
                            </p>
                        </div>

                        @if($homeVisit->queixas_principais)
                            <div class="sm:col-span-2">
                                <span class="text-surface-400 font-medium">Queixas Relatadas pela Gestante:</span>
                                <p class="font-medium text-surface-800 bg-surface-50 p-2.5 rounded-xl border border-surface-200 mt-1">
                                    {{ $homeVisit->queixas_principais }}
                                </p>
                            </div>
                        @endif

                        @if($homeVisit->estado_nutricional)
                            <div class="sm:col-span-2">
                                <span class="text-surface-400 font-medium">Avaliação do Estado Nutricional:</span>
                                <p class="font-medium text-surface-800 bg-surface-50 p-2.5 rounded-xl border border-surface-200 mt-1">
                                    {{ $homeVisit->estado_nutricional }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Sinais Vitais --}}
                    @if($homeVisit->sinais_vitais && is_array($homeVisit->sinais_vitais) && count(array_filter($homeVisit->sinais_vitais)) > 0)
                        <div class="border-t border-surface-100 pt-4 space-y-2">
                            <h4 class="text-xs font-bold text-surface-700 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-heart-pulse text-crimson-500"></i> Sinais Vitais Medidos
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($homeVisit->sinais_vitais as $sinal => $valor)
                                    @if(!empty($valor))
                                        <div class="bg-surface-50 p-2.5 rounded-xl border border-surface-200 text-center">
                                            <span class="text-3xs uppercase font-bold text-surface-400">{{ ucfirst(str_replace('_', ' ', $sinal)) }}</span>
                                            <p class="font-extrabold text-surface-900 text-sm font-mono mt-0.5">{{ $valor }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Materiais Entregues --}}
                    @if($homeVisit->materiais_entregues && is_array($homeVisit->materiais_entregues) && count($homeVisit->materiais_entregues) > 0)
                        <div class="border-t border-surface-100 pt-4 space-y-2">
                            <h4 class="text-xs font-bold text-surface-700 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-boxes-packing text-brand-600"></i> Insumos & Materiais Entregues
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($homeVisit->materiais_entregues as $mat)
                                    <span class="badge-brand text-xs px-2.5 py-1 flex items-center gap-1">
                                        <i class="fas fa-check text-3xs"></i> {{ $mat }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($homeVisit->proxima_visita)
                        <div class="border-t border-surface-100 pt-3 text-xs flex items-center gap-2 text-gold-700 font-medium">
                            <i class="fas fa-calendar-plus text-gold-600"></i>
                            <span>Próxima visita de seguimento agendada para: <strong class="text-surface-900 font-mono">{{ $homeVisit->proxima_visita->format('d/m/Y') }}</strong></span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- CARTÃO 3: OUTRAS VISITAS DESTA PACIENTE --}}
            @if(isset($outrasVisitas) && $outrasVisitas->count() > 0)
                <div class="card-tw overflow-hidden">
                    <div class="card-header-tw">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-surface-100 text-surface-700 flex items-center justify-center text-sm">
                                <i class="fas fa-history"></i>
                            </div>
                            <h3 class="font-bold text-surface-900 text-sm">Histórico de Visitas Domiciliárias da Gestante</h3>
                        </div>
                        <span class="badge-neutral text-2xs">{{ $outrasVisitas->count() }} anteriores</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-tw">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Agente</th>
                                    <th class="text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outrasVisitas as $v)
                                    <tr>
                                        <td>
                                            <span class="font-bold text-surface-800 text-xs">{{ $v->data_visita ? $v->data_visita->format('d/m/Y') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-neutral text-3xs">{{ $v->tipo_visita_formatada }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $bClass = match($v->status) {
                                                    'realizada' => 'badge-success',
                                                    'agendada' => 'badge-warning',
                                                    'nao_encontrada' => 'badge-danger',
                                                    'reagendada' => 'badge-info',
                                                    default => 'badge-neutral'
                                                };
                                            @endphp
                                            <span class="{{ $bClass }} text-3xs">{{ ucfirst(str_replace('_', ' ', $v->status)) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-surface-600">{{ $v->user->name ?? 'Agente' }}</span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('home_visits.show', $v) }}" class="btn-icon-tw" title="Ver Visita">
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

        {{-- COLUNA LATERAL (1/3): FICHA DA GESTANTE & AÇÕES --}}
        <div class="space-y-6">

            {{-- CARTÃO DA GESTANTE --}}
            <div class="card-tw p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                    <h3 class="font-bold text-surface-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-person-pregnant text-brand-600"></i> Ficha da Gestante
                    </h3>
                    <a href="{{ route('patients.show', $homeVisit->patient) }}" class="btn-secondary-tw btn-xs-tw">
                        <i class="fas fa-folder-open text-3xs"></i>
                        <span>Ver Ficha</span>
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-brand-100 text-brand-700 font-bold text-base flex items-center justify-center shrink-0 shadow-xs">
                        {{ strtoupper(substr($homeVisit->patient->nome_completo ?? 'G', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-surface-900 text-sm truncate">{{ $homeVisit->patient->nome_completo }}</h4>
                        <p class="text-2xs text-surface-500 font-mono">BI: {{ $homeVisit->patient->documento_bi ?? 'N/D' }}</p>
                        @if($homeVisit->patient->data_nascimento)
                            <p class="text-2xs text-surface-400">{{ (int)$homeVisit->patient->data_nascimento->age }} anos</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 text-xs border-t border-surface-100 pt-3">
                    <div>
                        <span class="text-surface-400">Contacto Direto:</span>
                        <p class="font-semibold text-surface-800 flex items-center gap-1.5 mt-0.5">
                            <i class="fas fa-phone text-brand-500 text-2xs"></i>
                            <span>{{ $homeVisit->patient->contacto ?? 'Não informado' }}</span>
                        </p>
                    </div>

                    @if($homeVisit->patient->parceiro_nome || $homeVisit->patient->parceiro_contacto)
                        <div>
                            <span class="text-surface-400">Parceiro / Marido:</span>
                            <p class="font-medium text-surface-800 mt-0.5">
                                {{ $homeVisit->patient->parceiro_nome ?? 'Nome N/D' }} · {{ $homeVisit->patient->parceiro_contacto ?? 'Sem telefone' }}
                            </p>
                        </div>
                    @endif

                    @if($homeVisit->patient->acompanhante_nome || $homeVisit->patient->acompanhante_contacto)
                        <div>
                            <span class="text-surface-400">Acompanhante / Familiar:</span>
                            <p class="font-medium text-surface-800 mt-0.5">
                                {{ $homeVisit->patient->acompanhante_nome }} ({{ $homeVisit->patient->acompanhante_parentesco ?? 'Familiar' }}) · {{ $homeVisit->patient->acompanhante_contacto ?? 'Sem telefone' }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <span class="text-surface-400">Endereço Residencial:</span>
                        <p class="font-medium text-surface-800 flex items-start gap-1.5 mt-0.5">
                            <i class="fas fa-map-pin text-crimson-500 text-2xs mt-0.5 shrink-0"></i>
                            <span>{{ $homeVisit->patient->endereco ?? 'Não informado' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- CARTÃO: BOAS PRÁTICAS MISAU NA VISITA --}}
            <div class="card-tw p-5 space-y-3 bg-gradient-to-br from-brand-50/40 to-surface-50">
                <h4 class="font-bold text-surface-900 text-xs uppercase tracking-wider text-brand-800 flex items-center gap-1.5">
                    <i class="fas fa-book-medical text-brand-600"></i> Protocolos MISAU na Comunidade
                </h4>
                <ul class="space-y-2 text-xs text-surface-700">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-600 mt-0.5 shrink-0 text-3xs"></i>
                        <span>Relembrar o cumprimento das 4 a 8 consultas de CPN.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-600 mt-0.5 shrink-0 text-3xs"></i>
                        <span>Incentivar a toma do IPTp-SP (Malária) a cada 4 semanas a partir da 13ª sem.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-600 mt-0.5 shrink-0 text-3xs"></i>
                        <span>Alertar sobre sinais de perigo: hemorragia, febre, cefaleia forte, edema.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-brand-600 mt-0.5 shrink-0 text-3xs"></i>
                        <span>Promover o parto institucional e preparar acompanhante jovem para apoio.</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    {{-- MODAL 1: COMPLETAR VISITA / REGISTAR DESFECHO --}}
    <div x-show="completeModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/50 backdrop-blur-xs overflow-y-auto"
         @keydown.escape.window="completeModalOpen = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 space-y-5 border border-surface-200 my-8 max-h-[90vh] overflow-y-auto" @click.away="completeModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="font-bold text-surface-900 text-base">Registar Desfecho da Visita Domiciliária</h3>
                </div>
                <button type="button" @click="completeModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('home_visits.complete', $homeVisit) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="p-3 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900">
                    <p class="font-bold">Paciente: {{ $homeVisit->patient->nome_completo }}</p>
                    <p class="text-2xs text-brand-700">Preencha o relatório da visita realizada no terreno para atualizar o registo clínico.</p>
                </div>

                {{-- Ambiente e Higiene --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label-tw">Condições de Higiene <span class="text-crimson-500">*</span></label>
                        <select name="condicoes_higiene" required class="input-tw text-xs">
                            <option value="bom">Bom</option>
                            <option value="regular">Regular</option>
                            <option value="ruim">Ruim</option>
                        </select>
                    </div>

                    <div>
                        <label class="label-tw">Apoio Familiar <span class="text-crimson-500">*</span></label>
                        <select name="apoio_familiar" required class="input-tw text-xs">
                            <option value="adequado">Adequado</option>
                            <option value="parcial">Parcial</option>
                            <option value="inadequado">Inadequado</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label-tw">Observações do Ambiente Domiciliário <span class="text-crimson-500">*</span></label>
                    <textarea name="observacoes_ambiente" rows="2" required placeholder="Descreva as condições habitacionais, saneamento, apoio do agregado familiar..." class="input-tw text-xs">{{ old('observacoes_ambiente', 'Ambiente familiar acolhedor e com saneamento adequado.') }}</textarea>
                </div>

                <div>
                    <label class="label-tw">Orientações Transmitidas (MISAU) <span class="text-crimson-500">*</span></label>
                    <textarea name="orientacoes_dadas" rows="3" required placeholder="Orientações sobre consultas de rotina, vacinas, sinais de perigo, nutrição e planeamento..." class="input-tw text-xs">{{ old('orientacoes_dadas') }}</textarea>
                </div>

                {{-- Sinais Vitais Opcionais --}}
                <div class="border-t border-surface-100 pt-3 space-y-2">
                    <label class="label-tw">Sinais Vitais (Opcional - se medidos)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <input type="text" name="sinais_vitais[pressao_arterial]" placeholder="PA (Ex: 120/80)" class="input-tw text-xs">
                        <input type="text" name="sinais_vitais[frequencia_cardiaca]" placeholder="FC (Ex: 78 bpm)" class="input-tw text-xs">
                        <input type="text" name="sinais_vitais[temperatura]" placeholder="Temp (Ex: 36.5°C)" class="input-tw text-xs">
                        <input type="text" name="sinais_vitais[peso]" placeholder="Peso (Ex: 62 kg)" class="input-tw text-xs">
                    </div>
                </div>

                {{-- Materiais Entregues --}}
                <div class="border-t border-surface-100 pt-3 space-y-2">
                    <label class="label-tw">Materiais / Insumos Entregues</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="materiais_entregues[]" value="Material Educativo" class="rounded border-surface-300 text-brand-600">
                            <span>Folheto Educativo</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="materiais_entregues[]" value="Suplementos de Ferro/Folato" class="rounded border-surface-300 text-brand-600">
                            <span>Ferro / Ácido Fólico</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="materiais_entregues[]" value="Preservativos" class="rounded border-surface-300 text-brand-600">
                            <span>Preservativos</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="materiais_entregues[]" value="Rede Mosquiteira" class="rounded border-surface-300 text-brand-600">
                            <span>Rede Mosquiteira</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="materiais_entregues[]" value="Kit Higiene" class="rounded border-surface-300 text-brand-600">
                            <span>Kit de Higiene</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="materiais_entregues[]" value="Cartão de Saúde" class="rounded border-surface-300 text-brand-600">
                            <span>Cartão da Gestante</span>
                        </label>
                    </div>
                </div>

                {{-- Checkboxes Adicionais --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 border-t border-surface-100 pt-3 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer p-2 bg-surface-50 rounded-xl border border-surface-200">
                        <input type="checkbox" name="acompanhante_presente" value="1" class="rounded border-surface-300 text-brand-600">
                        <span>Acompanhante presente na visita</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer p-2 bg-crimson-50 rounded-xl border border-crimson-200 text-crimson-800 font-semibold">
                        <input type="checkbox" name="necessita_referencia" value="1" class="rounded border-crimson-300 text-crimson-600">
                        <span>Necessita Referência à US</span>
                    </label>
                </div>

                <div>
                    <label class="label-tw">Agendar Próxima Visita (Opcional)</label>
                    <input type="date" name="proxima_visita" class="input-tw text-xs">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                    <button type="button" @click="completeModalOpen = false" class="btn-secondary-tw btn-sm-tw">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw bg-brand-600 hover:bg-brand-700">
                        <i class="fas fa-check-circle text-xs"></i>
                        <span>Salvar e Concluir Visita</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: REAGENDAR VISITA --}}
    <div x-show="rescheduleModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/50 backdrop-blur-xs"
         @keydown.escape.window="rescheduleModalOpen = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-5 border border-surface-200" @click.away="rescheduleModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-calendar-days"></i>
                    </div>
                    <h3 class="font-bold text-surface-900 text-sm">Reagendar Visita Domiciliária</h3>
                </div>
                <button type="button" @click="rescheduleModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('home_visits.reschedule', $homeVisit) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label-tw">Nova Data <span class="text-crimson-500">*</span></label>
                        <input type="date" name="nova_data_visita" value="{{ now()->addDays(2)->format('Y-m-d') }}" required class="input-tw text-xs">
                    </div>
                    <div>
                        <label class="label-tw">Nova Hora <span class="text-crimson-500">*</span></label>
                        <input type="time" name="nova_hora_visita" value="09:00" required class="input-tw text-xs">
                    </div>
                </div>

                <div>
                    <label class="label-tw">Motivo do Reagendamento <span class="text-crimson-500">*</span></label>
                    <textarea name="motivo_reagendamento" rows="3" required placeholder="Ex: Gestante ausente em consulta prévia, solicitou remarcação..." class="input-tw text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                    <button type="button" @click="rescheduleModalOpen = false" class="btn-secondary-tw btn-sm-tw">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw bg-gold-600 hover:bg-gold-700">
                        <i class="fas fa-calendar-check text-xs"></i>
                        <span>Confirmar Reagendamento</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 3: ATENDIDA NA UNIDADE SANITÁRIA (DISPENSAR VISITA) --}}
    <div x-show="resolveModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-950/50 backdrop-blur-xs"
         @keydown.escape.window="resolveModalOpen = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-5 border border-surface-200" @click.away="resolveModalOpen = false">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <h3 class="font-bold text-surface-900 text-sm">Dispensar Visita (Paciente Atendida na US)</h3>
                </div>
                <button type="button" @click="resolveModalOpen = false" class="text-surface-400 hover:text-surface-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('home_visits.resolve-at-facility', $homeVisit) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="p-3 bg-brand-50 border border-brand-200 rounded-xl text-xs text-brand-900">
                    <p class="font-bold">A paciente compareceu à Unidade Sanitária?</p>
                    <p class="text-2xs text-brand-700 mt-0.5">Ao marcar como resolvida, a activista comunitária é notificada de que a visita de busca ativa não é mais necessária.</p>
                </div>

                <div>
                    <label class="label-tw">Justificação / Nota de Atendimento</label>
                    <input type="text" name="motivo_resolucao" value="Paciente compareceu espontaneamente à consulta na Unidade Sanitária." class="input-tw text-xs" required>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-100">
                    <button type="button" @click="resolveModalOpen = false" class="btn-secondary-tw btn-sm-tw">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary-tw btn-sm-tw">
                        <i class="fas fa-check text-xs"></i>
                        <span>Confirmar Resolução na US</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
