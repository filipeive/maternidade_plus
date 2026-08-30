@extends('layouts.app-tw')

@section('title', 'Auditoria & Avaliações Clínicas')
@section('page-title', 'Auditoria & Avaliações Clínicas Precoces')
@section('title-icon', 'fa-clipboard-check')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('alertas.index') }}">Alertas Clínicos</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Painel de Avaliações</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- 1. Header Banner & Action Bar --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-800 via-brand-700 to-ocean-800 text-white flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 shadow-md border-none">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20 shrink-0">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                    <h2 class="text-base font-bold text-white">Painel de Auditoria & Triagem Clínica Contínua</h2>
                    <span class="badge-neutral text-3xs uppercase bg-white/10 text-white/90 border border-white/20">Protocolos MISAU</span>
                </div>
                <p class="text-xs text-white/70">Avaliação automatizada de 9 regras obstétricas: Pressão Arterial, BCF, Faltosas, ARO, Vacinas, Exames (HIV/Sífilis/Hb), Peso e Pós-Termo.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <form method="POST" action="{{ route('alertas.avaliar-todos') }}" class="inline">
                @csrf
                <button type="submit" class="btn-tw bg-gold-400 text-surface-950 hover:bg-gold-300 btn-sm-tw font-bold text-xs shadow-sm">
                    <i class="fas fa-rotate text-xs"></i>
                    <span>Executar Avaliação Imediata</span>
                </button>
            </form>

            <a href="{{ route('alertas.avaliacoes.pdf') }}" class="btn-tw bg-white/15 hover:bg-white/25 text-white border border-white/20 btn-sm-tw font-semibold text-xs">
                <i class="fas fa-file-pdf text-xs"></i>
                <span>Exportar Auditoria (PDF)</span>
            </a>

            <a href="{{ route('alertas.index') }}" class="btn-tw bg-white/15 hover:bg-white/25 text-white border border-white/20 btn-sm-tw font-semibold text-xs">
                <i class="fas fa-triangle-exclamation text-xs"></i>
                <span>Central de Alertas</span>
            </a>
        </div>
    </div>

    {{-- 2. Stat Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <a href="{{ route('alertas.avaliacoes', ['filtro' => 'todos']) }}" class="card-tw p-3.5 hover:border-brand-500 transition-all {{ $filtro === 'todos' ? 'ring-2 ring-brand-500 bg-brand-50/20' : '' }}">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">Total Gestantes</span>
            <span class="text-xl font-bold text-surface-900 mt-1 block">{{ $stats['total_avaliadas'] }}</span>
            <span class="text-3xs text-brand-600 font-medium">Inscritas no SMI</span>
        </a>

        <a href="{{ route('alertas.avaliacoes', ['filtro' => 'normais']) }}" class="card-tw p-3.5 hover:border-emerald-500 transition-all {{ $filtro === 'normais' ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
            <span class="text-3xs font-semibold text-emerald-600 uppercase tracking-wider block flex items-center gap-1">
                <i class="fas fa-circle-check text-emerald-600 text-3xs"></i> Normais
            </span>
            <span class="text-xl font-bold text-emerald-700 mt-1 block">{{ $stats['normais'] }}</span>
            <span class="text-3xs text-emerald-600 font-medium">Sem sinais de risco</span>
        </a>

        <a href="{{ route('alertas.avaliacoes', ['filtro' => 'atencao']) }}" class="card-tw p-3.5 hover:border-gold-500 transition-all {{ $filtro === 'atencao' ? 'ring-2 ring-gold-500 bg-gold-50/20' : '' }}">
            <span class="text-3xs font-semibold text-gold-600 uppercase tracking-wider block flex items-center gap-1">
                <i class="fas fa-bell text-gold-600 text-3xs"></i> Atenção
            </span>
            <span class="text-xl font-bold text-gold-700 mt-1 block">{{ $stats['atencao'] }}</span>
            <span class="text-3xs text-gold-600 font-medium">Alerta Médio / Faltosa</span>
        </a>

        <a href="{{ route('alertas.avaliacoes', ['filtro' => 'criticos']) }}" class="card-tw p-3.5 hover:border-crimson-500 transition-all {{ $filtro === 'criticos' ? 'ring-2 ring-crimson-500 bg-crimson-50/20' : '' }}">
            <span class="text-3xs font-semibold text-crimson-600 uppercase tracking-wider block flex items-center gap-1">
                <i class="fas fa-triangle-exclamation text-crimson-600 text-3xs"></i> Críticas / Alto
            </span>
            <span class="text-xl font-bold text-crimson-700 mt-1 block">{{ $stats['criticos'] }}</span>
            <span class="text-3xs text-crimson-600 font-medium">Risco Iminente</span>
        </a>

        <a href="{{ route('alertas.avaliacoes', ['filtro' => 'faltosas']) }}" class="card-tw p-3.5 hover:border-amber-500 transition-all {{ $filtro === 'faltosas' ? 'ring-2 ring-amber-500 bg-amber-50/20' : '' }}">
            <span class="text-3xs font-semibold text-amber-600 uppercase tracking-wider block flex items-center gap-1">
                <i class="fas fa-person-walking-arrow-right text-amber-600 text-3xs"></i> Faltosas
            </span>
            <span class="text-xl font-bold text-amber-700 mt-1 block">{{ $stats['faltosas'] }}</span>
            <span class="text-3xs text-amber-600 font-medium">Busca Ativa (APEs)</span>
        </a>

        <a href="{{ route('alertas.avaliacoes', ['filtro' => 'pos_termo']) }}" class="card-tw p-3.5 hover:border-purple-500 transition-all {{ $filtro === 'pos_termo' ? 'ring-2 ring-purple-500 bg-purple-50/20' : '' }}">
            <span class="text-3xs font-semibold text-purple-600 uppercase tracking-wider block flex items-center gap-1">
                <i class="fas fa-hourglass-half text-purple-600 text-3xs"></i> Pós-Termo
            </span>
            <span class="text-xl font-bold text-purple-700 mt-1 block">{{ $stats['pos_termo'] }}</span>
            <span class="text-3xs text-purple-600 font-medium">&gt; 41 Semanas</span>
        </a>
    </div>

    {{-- 3. Filter Bar & Search --}}
    <div class="card-tw p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'todos', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'todos' ? 'bg-brand-600 text-white shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Todas ({{ $stats['total_avaliadas'] }})
            </a>
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'criticos', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'criticos' ? 'bg-crimson-600 text-white shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Críticas ({{ $stats['criticos'] }})
            </a>
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'atencao', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'atencao' ? 'bg-gold-500 text-surface-950 shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Atenção ({{ $stats['atencao'] }})
            </a>
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'normais', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'normais' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Normais ({{ $stats['normais'] }})
            </a>
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'faltosas', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'faltosas' ? 'bg-amber-500 text-surface-950 shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Faltosas ({{ $stats['faltosas'] }})
            </a>
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'pos_termo', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'pos_termo' ? 'bg-purple-600 text-white shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Pós-Termo ({{ $stats['pos_termo'] }})
            </a>
            <a href="{{ route('alertas.avaliacoes', ['filtro' => 'aro', 'search' => $busca]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $filtro === 'aro' ? 'bg-ocean-600 text-white shadow-xs' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                Alto Risco ARO ({{ $stats['alto_risco'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('alertas.avaliacoes') }}" class="relative sm:w-64">
            <input type="hidden" name="filtro" value="{{ $filtro }}">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
            <input type="text" name="search" value="{{ $busca }}" placeholder="Pesquisar gestante, BI ou NID..." class="input-tw pl-8.5 py-1.5 text-xs w-full">
        </form>
    </div>

    {{-- 4. Main Evaluation Table --}}
    <div class="card-tw overflow-hidden">
        <div class="table-container">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Status</th>
                        <th>Gestante / Identificação</th>
                        <th>Idade Gestacional</th>
                        <th>Assiduidade / Consultas</th>
                        <th>Sinais Vitais (Última CPN)</th>
                        <th>Laboratório & PTV</th>
                        <th>Alertas Disparados</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($avaliacoes as $item)
                        @php $p = $item->patient; @endphp
                        <tr class="hover:bg-surface-50/70 transition-colors {{ $item->status_class === 'critico' ? 'bg-crimson-50/20' : ($item->status_class === 'atencao' ? 'bg-gold-50/15' : '') }}">
                            
                            {{-- Status Icon --}}
                            <td class="text-center">
                                @if($item->status_class === 'critico')
                                    <div class="w-7 h-7 rounded-full bg-crimson-100 text-crimson-700 flex items-center justify-center mx-auto text-xs font-bold shadow-2xs" title="Crítico / Alto Risco">
                                        <i class="fas fa-triangle-exclamation"></i>
                                    </div>
                                @elseif($item->status_class === 'atencao')
                                    <div class="w-7 h-7 rounded-full bg-gold-100 text-gold-800 flex items-center justify-center mx-auto text-xs font-bold shadow-2xs" title="Atenção / Seguimento">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                @else
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-xs font-bold shadow-2xs" title="Normal / Estável">
                                        <i class="fas fa-circle-check"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- Gestante --}}
                            <td>
                                <div class="font-semibold text-surface-900 text-xs flex items-center gap-1.5">
                                    <a href="{{ route('patients.show', $p) }}" class="hover:text-brand-600 hover:underline">
                                        {{ $p->nome_completo }}
                                    </a>
                                    @if($item->is_alto_risco)
                                        <span class="badge-danger text-3xs font-bold uppercase">ARO</span>
                                    @endif
                                </div>
                                <div class="text-3xs text-surface-400 mt-0.5 space-x-1 font-mono">
                                    <span>BI: {{ $p->documento_bi ?? 'N/D' }}</span>
                                    <span>·</span>
                                    <span>Tel: {{ $p->contacto ?? 'S/ Contacto' }}</span>
                                </div>
                                <div class="text-3xs text-surface-500">
                                    {{ $p->bairro ?? 'Bairro N/D' }} ({{ $p->distrito ?? 'Distrito N/D' }})
                                </div>
                            </td>

                            {{-- Idade Gestacional --}}
                            <td>
                                <div class="text-xs font-bold text-surface-800">
                                    {{ $item->idade_gestacional }} Semanas
                                </div>
                                @if($item->is_pos_termo)
                                    <span class="badge-danger text-3xs font-bold uppercase mt-0.5 inline-block">Pós-Termo (&gt;41s)</span>
                                @elseif($item->idade_gestacional >= 37)
                                    <span class="badge-brand text-3xs font-semibold mt-0.5 inline-block">A Termo</span>
                                @else
                                    <span class="badge-neutral text-3xs font-medium mt-0.5 inline-block">Em Evolução</span>
                                @endif
                                <div class="text-3xs text-surface-400 mt-0.5">
                                    DPP: {{ $p->data_provavel_parto ? \Carbon\Carbon::parse($p->data_provavel_parto)->format('d/m/Y') : 'N/D' }}
                                </div>
                            </td>

                            {{-- Assiduidade --}}
                            <td>
                                @if($item->is_faltosa)
                                    <span class="badge-warning text-3xs font-bold block w-fit mb-0.5">
                                        <i class="fas fa-person-walking-dashed-line-arrow-right mr-0.5"></i> Faltosa
                                    </span>
                                    <div class="text-3xs text-crimson-600 font-semibold">
                                        {{ $item->motivo_faltosa }}
                                    </div>
                                @else
                                    <span class="badge-success text-3xs font-semibold block w-fit mb-0.5">
                                        <i class="fas fa-circle-check mr-0.5"></i> Em Dia
                                    </span>
                                    <div class="text-3xs text-surface-500">
                                        {{ $item->dias_sem_consulta }} dias da última CPN
                                    </div>
                                @endif
                                <div class="text-3xs text-surface-400 mt-0.5">
                                    Última: {{ $item->ultima_consulta ? \Carbon\Carbon::parse($item->ultima_consulta->data_consulta)->format('d/m/Y') : 'Nenhuma' }}
                                </div>
                            </td>

                            {{-- Sinais Vitais --}}
                            <td>
                                <div class="space-y-0.5 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-3xs text-surface-400 font-medium">PA:</span>
                                        @if($item->is_pa_grave)
                                            <span class="font-bold text-crimson-600 text-xs bg-crimson-100 px-1.5 py-0.5 rounded flex items-center gap-1">
                                                {{ $item->pressao_arterial }} <i class="fas fa-triangle-exclamation text-3xs"></i>
                                            </span>
                                        @elseif($item->is_pa_alta)
                                            <span class="font-bold text-gold-700 text-xs bg-gold-100 px-1.5 py-0.5 rounded">{{ $item->pressao_arterial }}</span>
                                        @elseif($item->pressao_arterial)
                                            <span class="font-semibold text-surface-800">{{ $item->pressao_arterial }}</span>
                                        @else
                                            <span class="text-surface-400 text-3xs">Não registada</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <span class="text-3xs text-surface-400 font-medium">BCF:</span>
                                        @if($item->is_bcf_anormal)
                                            <span class="font-bold text-crimson-600 text-xs bg-crimson-100 px-1.5 py-0.5 rounded flex items-center gap-1">
                                                {{ $item->bcf }} bpm <i class="fas fa-triangle-exclamation text-3xs"></i>
                                            </span>
                                        @elseif($item->bcf)
                                            <span class="font-semibold text-surface-800">{{ $item->bcf }} bpm</span>
                                        @else
                                            <span class="text-surface-400 text-3xs">—</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Laboratório & PTV --}}
                            <td>
                                @if(count($item->exames_criticos) > 0)
                                    <div class="space-y-0.5">
                                        @foreach($item->exames_criticos as $crit)
                                            <span class="badge-danger text-3xs font-bold block w-fit">{{ $crit }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-3xs text-emerald-700 font-medium bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 inline-block">
                                        <i class="fas fa-shield-virus mr-0.5"></i> Rastreios Normais
                                    </span>
                                @endif

                                @if($item->vacinas_atrasadas > 0)
                                    <div class="text-3xs text-gold-700 font-semibold mt-1">
                                        <i class="fas fa-syringe mr-0.5"></i> {{ $item->vacinas_atrasadas }} vacina(s) em atraso
                                    </div>
                                @endif
                            </td>

                            {{-- Alertas Disparados --}}
                            <td>
                                @if($item->alertas_ativos->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($item->alertas_ativos as $alt)
                                            <div class="flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $alt->nivel === 'alto' ? 'bg-crimson-500' : ($alt->nivel === 'medio' ? 'bg-gold-500' : 'bg-brand-500') }}"></span>
                                                <span class="text-3xs font-semibold {{ $alt->nivel === 'alto' ? 'text-crimson-700' : 'text-surface-700' }}" title="{{ $alt->mensagem }}">
                                                    {{ Str::limit($alt->titulo ?? $alt->tipo_formatado ?? $alt->mensagem, 30) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-3xs text-surface-400 italic">Sem alertas pendentes</span>
                                @endif
                            </td>

                            {{-- Ações --}}
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('patients.show', $p) }}" class="btn-secondary-tw btn-xs-tw" title="Abrir Prontuário">
                                        <i class="fas fa-folder-open text-3xs"></i>
                                        <span>Ficha</span>
                                    </a>

                                    @if($item->is_faltosa)
                                        <a href="{{ route('home_visits.active-search') }}" class="btn-tw bg-gold-400 hover:bg-gold-300 text-surface-950 btn-xs-tw font-bold" title="Encaminhar para Activista">
                                            <i class="fas fa-person-walking text-3xs"></i>
                                            <span>APE</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-surface-400 text-xs">
                                <i class="fas fa-clipboard-check text-2xl mb-2 text-surface-300 block"></i>
                                Nenhuma gestante encontrada com o filtro selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
