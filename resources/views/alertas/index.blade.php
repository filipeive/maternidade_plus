@extends('layouts.app-tw')

@section('title', 'Alertas Precoces')
@section('page-title', 'Módulo de Alerta Precoce')
@section('title-icon', 'fa-triangle-exclamation')

@section('breadcrumbs')
    <span class="active">Alertas Precoces</span>
@endsection

@section('content')

{{-- Header & Action Buttons --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-surface-900">Painel de Alertas Clínicos</h2>
        <p class="text-sm text-surface-500">Monitoria e triagem de sinais de risco materno-fetal baseada em evidência</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('alertas.metricas') }}" class="btn-secondary-tw">
            <i class="fas fa-chart-line text-xs text-brand-600"></i>
            <span>Métricas de Impacto (M&E)</span>
        </a>
        <a href="{{ route('dashboard') }}" class="btn-secondary-tw">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Dashboard</span>
        </a>
    </div>
</div>

{{-- Stat Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-bolt"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $estatisticas['altos_ativos'] ?? 0 }}</p>
            <p class="stat-card-label">Alertas Altos Ativos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-bell"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $estatisticas['total_ativos'] ?? 0 }}</p>
            <p class="stat-card-label">Total Ativos / Pendentes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-user-clock"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $estatisticas['em_seguimento'] ?? 0 }}</p>
            <p class="stat-card-label">Em Seguimento</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $estatisticas['resolvidos'] ?? 0 }}</p>
            <p class="stat-card-label">Resolvidos</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card-tw p-4 mb-6" x-data="{expanded: false}">
    <div class="flex items-center justify-between mb-3 lg:mb-0">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 flex items-center gap-2">
            <i class="fas fa-filter text-brand-500"></i> Filtros de Pesquisa
        </h3>
    </div>

    <form method="GET" action="{{ route('alertas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end mt-3">
        <div>
            <label class="label-tw">Pesquisar Paciente / BI</label>
            <input type="text"
                   name="search"
                   class="input-tw"
                   placeholder="Nome ou BI..."
                   value="{{ request('search') ?? request('paciente') }}">
        </div>

        <div>
            <label class="label-tw">Nível de Severidade</label>
            <select name="nivel" class="input-tw">
                <option value="">Todos os Níveis</option>
                <option value="alto" {{ request('nivel') === 'alto' ? 'selected' : '' }}>Alto</option>
                <option value="medio" {{ request('nivel') === 'medio' ? 'selected' : '' }}>Médio</option>
                <option value="baixo" {{ request('nivel') === 'baixo' ? 'selected' : '' }}>Baixo</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Status do Alerta</label>
            <select name="status" class="input-tw">
                <option value="">Todos os Status</option>
                <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="em_seguimento" {{ request('status') === 'em_seguimento' ? 'selected' : '' }}>Em Seguimento</option>
                <option value="resolvido" {{ request('status') === 'resolvido' ? 'selected' : '' }}>Resolvido</option>
                <option value="ignorado" {{ request('status') === 'ignorado' ? 'selected' : '' }}>Ignorado</option>
            </select>
        </div>

        <div>
            <label class="label-tw">Regra Clínica / Tipo</label>
            <select name="tipo" class="input-tw">
                <option value="">Todas as Regras</option>
                <option value="pressao_arterial_grave" {{ request('tipo') === 'pressao_arterial_grave' ? 'selected' : '' }}>PA Grave (>= 160/110)</option>
                <option value="pressao_arterial_alta" {{ request('tipo') === 'pressao_arterial_alta' ? 'selected' : '' }}>PA Elevada (>= 140/90)</option>
                <option value="bcf_anormal" {{ request('tipo') === 'bcf_anormal' ? 'selected' : '' }}>BCF Anormal (<110 ou >160)</option>
                <option value="gestante_faltosa" {{ request('tipo') === 'gestante_faltosa' ? 'selected' : '' }}>Gestante Faltosa</option>
                <option value="alto_risco_sem_seguimento" {{ request('tipo') === 'alto_risco_sem_seguimento' ? 'selected' : '' }}>Alto Risco Sem Seguimento</option>
                <option value="vacinas_em_atraso" {{ request('tipo') === 'vacinas_em_atraso' ? 'selected' : '' }}>Vacinas em Atraso</option>
                <option value="exames_criticos" {{ request('tipo') === 'exames_criticos' ? 'selected' : '' }}>Exames Críticos (HIV/Anemia)</option>
                <option value="ganho_peso_anormal" {{ request('tipo') === 'ganho_peso_anormal' ? 'selected' : '' }}>Ganho/Perda de Peso</option>
                <option value="pos_termo" {{ request('tipo') === 'pos_termo' ? 'selected' : '' }}>Gestação Pós-Termo (>41 sem)</option>
                <option value="sangramento_reportado" {{ request('tipo') === 'sangramento_reportado' ? 'selected' : '' }}>Sangramento Reportado</option>
            </select>
        </div>

        <div class="col-span-full flex items-center justify-end gap-2 pt-2 border-t border-surface-100">
            <button type="submit" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-search text-xs"></i>
                <span>Aplicar Filtros</span>
            </button>
            <a href="{{ route('alertas.index') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-times text-xs"></i>
                <span>Limpar</span>
            </a>
        </div>
    </form>
</div>

{{-- Alerts Table Card --}}
<div class="card-tw overflow-hidden" x-data="{activeModal: null}">
    <div class="card-header-tw">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-900">Lista de Alertas Clínicos</h3>
        </div>
        <span class="badge-neutral font-medium">{{ $alertas->total() }} alertas</span>
    </div>

    @if($alertas->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th>Severidade</th>
                        <th>Gestante</th>
                        <th>Tipo de Alerta</th>
                        <th>Mensagem / Detalhes</th>
                        <th>Data Emissão</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alertas as $alerta)
                    <tr>
                        <td>
                            @if($alerta->nivel === 'alto')
                                <span class="badge-danger">
                                    <i class="fas fa-bolt mr-1 text-2xs animate-pulse"></i>Alto
                                </span>
                            @elseif($alerta->nivel === 'medio')
                                <span class="badge-warning">
                                    <i class="fas fa-exclamation mr-1 text-2xs"></i>Médio
                                </span>
                            @else
                                <span class="badge-info">
                                    <i class="fas fa-info-circle mr-1 text-2xs"></i>Baixo
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($alerta->patient)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($alerta->patient->nome_completo ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('patients.show', $alerta->patient) }}" class="font-semibold text-surface-900 hover:text-brand-600 transition-colors">
                                            {{ $alerta->patient->nome_completo }}
                                        </a>
                                        <p class="text-2xs text-surface-400">BI: {{ $alerta->patient->documento_bi ?? 'N/D' }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-surface-400 italic">Paciente N/D</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-medium text-surface-900">{{ $alerta->tipo_label }}</span>
                        </td>
                        <td class="max-w-xs">
                            <p class="text-xs text-surface-700 leading-relaxed">{{ $alerta->mensagem }}</p>
                            @if($alerta->nota_resolucao)
                                <div class="text-2xs text-surface-500 mt-1 bg-surface-50 p-2 rounded border border-surface-200/60">
                                    <i class="fas fa-comment-medical text-brand-500 mr-1"></i>{{ $alerta->nota_resolucao }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <p class="font-medium text-surface-800 text-xs">{{ $alerta->created_at->format('d/m/Y') }}</p>
                            <p class="text-2xs text-surface-400">{{ $alerta->created_at->format('H:i') }}</p>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($alerta->status) {
                                    'ativo' => 'badge-danger',
                                    'em_seguimento' => 'badge-warning',
                                    'resolvido' => 'badge-success',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $alerta->status)) }}</span>
                        </td>
                        <td class="text-right">
                            <button type="button"
                                    @click="activeModal = {{ $alerta->id }}"
                                    class="btn-primary-tw btn-sm-tw">
                                <i class="fas fa-stethoscope text-xs"></i>
                                <span>Tratar</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modais Alpine.js para Resolução --}}
        @foreach($alertas as $alerta)
        <div x-show="activeModal === {{ $alerta->id }}"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-cloak>
            <div @click.outside="activeModal = null"
                 class="bg-white rounded-xl shadow-toast border border-surface-200 w-full max-w-lg overflow-hidden animate-fade-in-up">

                <form method="POST" action="{{ route('alertas.transitar', $alerta) }}">
                    @csrf
                    <div class="px-5 py-4 bg-gradient-to-r from-brand-600 to-brand-700 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-notes-medical"></i>
                            <h4 class="font-semibold text-sm">Tratar Alerta Clínico</h4>
                        </div>
                        <button type="button" @click="activeModal = null" class="text-white/70 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="p-3 bg-surface-50 rounded-lg border border-surface-200/60 space-y-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-surface-900 text-sm">{{ $alerta->patient->nome_completo ?? 'Paciente' }}</span>
                                @if($alerta->nivel === 'alto')
                                    <span class="badge-danger text-2xs">Alto</span>
                                @elseif($alerta->nivel === 'medio')
                                    <span class="badge-warning text-2xs">Médio</span>
                                @else
                                    <span class="badge-info text-2xs">Baixo</span>
                                @endif
                            </div>
                            <p class="text-xs text-surface-600">{{ $alerta->mensagem }}</p>

                            @if($alerta->patient && $alerta->patient->podeRegistrarParto())
                                <div class="pt-2 border-t border-surface-200/60">
                                    <a href="{{ route('births.create', $alerta->patient) }}"
                                       class="w-full btn-secondary-tw py-2 text-xs flex items-center justify-center gap-2 bg-brand-50 hover:bg-brand-100 text-brand-700 border-brand-300 font-semibold shadow-2xs transition-all">
                                        <i class="fas fa-baby text-brand-600"></i>
                                        <span>A paciente já deu à luz? Registar Parto Agora →</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="label-tw">Novo Status <span class="text-crimson-500">*</span></label>
                            <select name="status" class="input-tw" required>
                                <option value="em_seguimento" {{ $alerta->status === 'em_seguimento' ? 'selected' : '' }}>
                                    Em Seguimento (Contacto realizado / Consulta marcada)
                                </option>
                                <option value="resolvido" {{ $alerta->status === 'resolvido' ? 'selected' : '' }}>
                                    Resolvido (Conduta executada / Sinais normalizados)
                                </option>
                                <option value="ignorado" {{ $alerta->status === 'ignorado' ? 'selected' : '' }}>
                                    Ignorado (Falso positivo verificado)
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="label-tw">Nota Clínica de Resolução / Conduta <span class="text-crimson-500">*</span></label>
                            <textarea name="nota"
                                      class="input-tw"
                                      rows="3"
                                      required
                                      maxlength="1000"
                                      placeholder="Descreva detalhadamente a conduta tomada, medicação prescrita ou motivo do encerramento...">{{ old('nota', $alerta->nota_resolucao) }}</textarea>
                            <p class="text-2xs text-surface-400 mt-1">Obrigatório para fins de auditoria clínica e histórico do MISAU.</p>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-2">
                        <button type="button" @click="activeModal = null" class="btn-secondary-tw btn-sm-tw">Cancelar</button>
                        <button type="submit" class="btn-primary-tw btn-sm-tw">
                            <i class="fas fa-save text-xs"></i>
                            <span>Registar Ação</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        <div class="card-footer-tw flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-surface-500">
                Mostrando <span class="font-medium text-surface-800">{{ $alertas->firstItem() ?? 0 }}</span> a
                <span class="font-medium text-surface-800">{{ $alertas->lastItem() ?? 0 }}</span> de
                <span class="font-medium text-surface-800">{{ $alertas->total() }}</span> alertas
            </p>
            <div>
                {{ $alertas->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center">
                <i class="fas fa-shield-check text-3xl text-brand-500"></i>
            </div>
            <h3 class="text-base font-semibold text-surface-800 mb-1">Nenhum alerta clínico encontrado</h3>
            <p class="text-sm text-surface-500">Não existem alertas para os filtros selecionados.</p>
        </div>
    @endif
</div>
@endsection
