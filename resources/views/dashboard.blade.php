@extends('layouts.app-tw')

@section('title', 'Dashboard Clínico')
@section('page-title', 'Painel Geral de Controlo & Gestão Materno-Infantil')
@section('title-icon', 'fa-chart-pie')

@section('breadcrumbs')
    <span class="active">Dashboard Clínico</span>
@endsection

@section('content')

@php
    $diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    $mesesAno = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
    $dataPt = $diasSemana[now()->dayOfWeek] . ', ' . now()->day . ' de ' . $mesesAno[now()->month] . ' de ' . now()->year;
    $usuario = auth()->user();
    $cargoUsuario = $usuario ? ($usuario->getRoleNames()->first() ?? $usuario->especialidade ?? 'Profissional de Saúde') : 'Profissional de Saúde';
    $unidadeSanitaria = \App\Models\Setting::get('unidade_sanitaria', 'Centro de Saúde Urbano & Maternidade');
    $provinciaConfig = \App\Models\Setting::get('provincia', 'Maputo Cidade');
    $distritoConfig = \App\Models\Setting::get('distrito');
@endphp

{{-- ============================================================
     1. BANNER INSTITUCIONAL CLARO & ATENDIMENTO RÁPIDO
     ============================================================ --}}
<div class="card-tw p-5 mb-6 bg-white border border-surface-200 shadow-xs flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 rounded-2xl" x-data="{ quickSearch: '' }">
    <div class="flex items-center gap-4">
        <div class="w-13 h-13 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-xl font-bold border border-brand-100 shrink-0 shadow-xs">
            <i class="fas fa-hospital-user"></i>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <h2 class="text-base font-bold text-surface-900 tracking-tight">{{ $unidadeSanitaria }}</h2>
                <span class="badge-brand text-3xs font-bold uppercase">{{ $provinciaConfig }} @if($distritoConfig) · {{ $distritoConfig }} @endif</span>
                <span class="badge-neutral text-3xs font-medium">{{ $dataPt }}</span>
            </div>
            <p class="text-xs text-surface-600 flex items-center gap-1.5 flex-wrap">
                <span>Bem-vindo(a), <strong class="text-surface-900 font-semibold">{{ $usuario->name ?? 'Utilizador' }}</strong></span>
                <span class="text-surface-300">·</span>
                <span class="text-brand-700 font-medium bg-brand-50 px-2 py-0.5 rounded-full text-3xs border border-brand-100">
                    <i class="fas fa-user-doctor text-3xs mr-0.5"></i> {{ $cargoUsuario }}
                </span>
            </p>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">
        {{-- Campo de Pesquisa Rápida --}}
        <form @submit.prevent="
            if (!quickSearch.trim()) return;
            let val = quickSearch.trim();
            if (val.includes('/patients/')) {
                window.location.href = val;
            } else {
                window.location.href = '{{ url('/patients') }}?search=' + encodeURIComponent(val);
            }
        " class="relative flex-1 sm:w-64">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-surface-400 text-xs"></i>
            <input type="text" x-model="quickSearch" placeholder="Pesquisar gestante, BI ou NID..." class="input-tw pl-9 pr-3 py-2 text-xs w-full">
        </form>

        {{-- Scanner QR Code --}}
        <a href="{{ route('scanner') }}" class="btn-primary-tw btn-sm-tw shrink-0 shadow-xs">
            <i class="fas fa-qrcode text-xs"></i>
            <span>Scanner QR</span>
        </a>

        {{-- Nova Gestante --}}
        <a href="{{ route('patients.create') }}" class="btn-secondary-tw btn-sm-tw shrink-0">
            <i class="fas fa-user-plus text-xs"></i>
            <span>Nova Gestante</span>
        </a>
    </div>
</div>

{{-- ============================================================
     2. GRID DE KPIS & ESTATÍSTICAS PRINCIPAIS (6 CARDS)
     ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    {{-- Total Gestantes Ativas --}}
    <a href="{{ route('patients.index') }}" class="stat-card hover:border-brand-300 transition-all group">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600 group-hover:scale-105 transition-transform">
            <i class="fas fa-person-pregnant text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value text-xl font-bold">{{ $totalGestantes }}</p>
            <p class="stat-card-label text-2xs">Gestantes Ativas</p>
            <span class="inline-block mt-1 text-3xs font-semibold text-crimson-600 bg-crimson-50 px-1.5 py-0.5 rounded">
                {{ $totalGestantesARO }} ARO (Alto Risco)
            </span>
        </div>
    </a>

    {{-- Consultas Hoje / Semana --}}
    <a href="{{ route('consultations.index') }}" class="stat-card hover:border-ocean-300 transition-all group">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600 group-hover:scale-105 transition-transform">
            <i class="fas fa-calendar-check text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value text-xl font-bold">{{ $consultasHoje }}</p>
            <p class="stat-card-label text-2xs">Consultas Hoje</p>
            <span class="inline-block mt-1 text-3xs font-medium text-ocean-700 bg-ocean-50 px-1.5 py-0.5 rounded">
                {{ $consultasEstaSemana }} esta semana
            </span>
        </div>
    </a>

    {{-- Partos no Mês --}}
    <a href="{{ route('births.index') }}" class="stat-card hover:border-gold-300 transition-all group">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600 group-hover:scale-105 transition-transform">
            <i class="fas fa-baby text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value text-xl font-bold">{{ $partosMes }}</p>
            <p class="stat-card-label text-2xs">Partos no Mês</p>
            <span class="inline-block mt-1 text-3xs font-semibold text-gold-800 bg-gold-50 px-1.5 py-0.5 rounded">
                Maternidade SNS
            </span>
        </div>
    </a>

    {{-- Busca Ativa Comunitária (Faltosas) --}}
    <a href="{{ route('home_visits.active-search') }}" class="stat-card hover:border-crimson-300 transition-all group">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600 group-hover:scale-105 transition-transform">
            <i class="fas fa-person-walking text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value text-xl font-bold text-crimson-600">{{ $faltosasCount }}</p>
            <p class="stat-card-label text-2xs">Faltosas CPN</p>
            <span class="inline-block mt-1 text-3xs font-semibold text-crimson-700 bg-crimson-50 px-1.5 py-0.5 rounded">
                Busca Ativa (APEs)
            </span>
        </div>
    </a>

    {{-- Visitas Domiciliárias --}}
    <a href="{{ route('home_visits.index') }}" class="stat-card hover:border-emerald-300 transition-all group">
        <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600 group-hover:scale-105 transition-transform">
            <i class="fas fa-house-medical text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value text-xl font-bold">{{ $visitasMes }}</p>
            <p class="stat-card-label text-2xs">Visitas Domiciliares</p>
            <span class="inline-block mt-1 text-3xs font-medium text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                {{ $visitasRealizadas }} realizadas
            </span>
        </div>
    </a>

    {{-- Transferências Realizadas --}}
    <a href="{{ route('patients.index', ['status' => 'transferidas']) }}" class="stat-card hover:border-purple-300 transition-all group">
        <div class="stat-card-icon bg-gradient-to-br from-purple-500 to-purple-600 group-hover:scale-105 transition-transform">
            <i class="fas fa-arrow-right-from-bracket text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="stat-card-value text-xl font-bold">{{ $totalTransferidas }}</p>
            <p class="stat-card-label text-2xs">Transferidas</p>
            <span class="inline-block mt-1 text-3xs font-medium text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded">
                Outras US / Províncias
            </span>
        </div>
    </a>

</div>

{{-- ============================================================
     3. SEÇÃO DE GRÁFICOS ANALÍTICOS (CHART.JS)
     ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- GRÁFICO 1: Tendência de Consultas CPN & Partos (2 Colunas) --}}
    <div class="card-tw lg:col-span-2">
        <div class="card-header-tw flex-wrap gap-2">
            <div>
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-chart-line text-brand-600"></i>
                    <span>Evolução de Consultas CPN & Partos na Maternidade</span>
                </h3>
                <p class="text-2xs text-surface-400 mt-0.5">Atendimentos clínicos realizados nos últimos 6 meses</p>
            </div>
            <div class="flex items-center gap-3 text-2xs">
                <span class="flex items-center gap-1.5 font-medium text-surface-600">
                    <span class="w-3 h-3 rounded-full bg-brand-500 inline-block"></span> Consultas CPN
                </span>
                <span class="flex items-center gap-1.5 font-medium text-surface-600">
                    <span class="w-3 h-3 rounded-full bg-gold-500 inline-block"></span> Partos Realizados
                </span>
            </div>
        </div>
        <div class="card-body-tw">
            <div class="h-64 relative">
                <canvas id="chartEvolucaoMensal"></canvas>
            </div>
        </div>
    </div>

    {{-- GRÁFICO 2: Distribuição por Trimestre Gestacional (1 Coluna) --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div>
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-chart-pie text-ocean-600"></i>
                    <span>Distribuição Gestacional</span>
                </h3>
                <p class="text-2xs text-surface-400 mt-0.5">Idade gestacional das pacientes ativas</p>
            </div>
        </div>
        <div class="card-body-tw flex flex-col items-center justify-center">
            <div class="h-48 w-48 relative">
                <canvas id="chartTrimestres"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4 w-full text-2xs">
                <div class="p-2 rounded-lg bg-surface-50 border border-surface-100 flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-surface-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span> 1º Trimestre
                    </span>
                    <strong class="text-surface-900">{{ $trimestre1 }}</strong>
                </div>
                <div class="p-2 rounded-lg bg-surface-50 border border-surface-100 flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-surface-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> 2º Trimestre
                    </span>
                    <strong class="text-surface-900">{{ $trimestre2 }}</strong>
                </div>
                <div class="p-2 rounded-lg bg-surface-50 border border-surface-100 flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-surface-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span> 3º Trimestre
                    </span>
                    <strong class="text-surface-900">{{ $trimestre3 }}</strong>
                </div>
                <div class="p-2 rounded-lg bg-surface-50 border border-surface-100 flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-surface-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Pós-Parto
                    </span>
                    <strong class="text-surface-900">{{ $posParto }}</strong>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SEGUNDA LINHA DE GRÁFICOS: Cobertura de Profilaxias & Visitas Domiciliares --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- GRÁFICO 3: Cobertura de Profilaxias MISAU --}}
    <div class="card-tw">
        <div class="card-header-tw flex-wrap gap-2">
            <div>
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-shield-virus text-emerald-600"></i>
                    <span>Cobertura de Profilaxias MISAU</span>
                </h3>
                <p class="text-2xs text-surface-400 mt-0.5">Adesão aos protocolos de prevenção (Malária, Tétano, Anemia)</p>
            </div>
            <a href="{{ route('mod_sis_b01.resumo_mensal') }}" class="btn-secondary-tw btn-xs-tw">
                <span>Livro SIS B01</span>
                <i class="fas fa-arrow-right text-3xs"></i>
            </a>
        </div>
        <div class="card-body-tw">
            <div class="h-56 relative">
                <canvas id="chartProfilaxias"></canvas>
            </div>
        </div>
    </div>

    {{-- GRÁFICO 4: Desfecho das Visitas Domiciliárias & Terreno (APEs) --}}
    <div class="card-tw">
        <div class="card-header-tw flex-wrap gap-2">
            <div>
                <h3 class="font-bold text-surface-900 text-sm flex items-center gap-2">
                    <i class="fas fa-users-viewfinder text-purple-600"></i>
                    <span>Desfecho do Trabalho Comunitário (APEs)</span>
                </h3>
                <p class="text-2xs text-surface-400 mt-0.5">Status das visitas domiciliárias e busca ativa</p>
            </div>
            <a href="{{ route('home_visits.index') }}" class="btn-secondary-tw btn-xs-tw">
                <span>Ver Visitas</span>
                <i class="fas fa-arrow-right text-3xs"></i>
            </a>
        </div>
        <div class="card-body-tw">
            <div class="h-56 relative">
                <canvas id="chartVisitas"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ============================================================
     4. PAINÉIS CLÍNICOS OPERACIONAIS (FEEDS EM TEMPO REAL)
     ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- COLUNA 1: Alertas Clínicos & Próximas Consultas --}}
    <div class="space-y-6">

        {{-- Alertas Clínicos Críticos --}}
        <div class="card-tw border-l-4 border-l-crimson-500">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-surface-900 text-sm">Alertas Precoces & Alto Risco</h4>
                        <p class="text-3xs text-surface-400">Casos que requerem intervenção clínica prioritária</p>
                    </div>
                </div>
                <a href="{{ route('alertas.index') }}" class="btn-secondary-tw btn-xs-tw">
                    <span>Todos os Alertas</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>
            <div class="divide-y divide-surface-100">
                @forelse($alertasPrecoces as $alerta)
                    <div class="p-3.5 flex items-start justify-between gap-3 hover:bg-surface-50/70 transition-colors">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="badge-{{ $alerta->nivel === 'alto' ? 'danger' : ($alerta->nivel === 'medio' ? 'warning' : 'info') }} text-3xs font-bold uppercase">
                                    {{ $alerta->nivel }}
                                </span>
                                <a href="{{ route('patients.show', $alerta->patient) }}" class="font-bold text-surface-900 text-xs hover:text-brand-600 transition-colors">
                                    {{ $alerta->patient->nome_completo }}
                                </a>
                                <span class="text-3xs text-surface-400">({{ $alerta->created_at->diffForHumans() }})</span>
                            </div>
                            <p class="text-xs text-surface-600 leading-snug">{{ $alerta->mensagem }}</p>
                        </div>
                        <a href="{{ route('alertas.index', ['search' => $alerta->patient->nome_completo]) }}" class="btn-danger-tw btn-xs-tw shrink-0">
                            <span>Tratar</span>
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-surface-400">
                        <i class="fas fa-check-circle text-emerald-500 text-2xl mb-2"></i>
                        <p class="text-xs font-medium text-surface-600">Nenhum alerta crítico ativo no momento.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Próximas Consultas Agendadas --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-surface-900 text-sm">Consultas Agendadas (Próximos Dias)</h4>
                        <p class="text-3xs text-surface-400">Fluxo diário de atendimentos CPN e puerpério</p>
                    </div>
                </div>
                <a href="{{ route('consultations.index') }}" class="btn-secondary-tw btn-xs-tw">
                    <span>Ver Agenda</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>
            <div class="divide-y divide-surface-100">
                @forelse($proximasConsultas as $c)
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-surface-50/70 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 font-bold text-xs flex flex-col items-center justify-center shrink-0 border border-brand-200">
                                <span class="leading-none text-2xs uppercase">{{ $c->data_consulta->translatedFormat('M') }}</span>
                                <span class="leading-none text-sm">{{ $c->data_consulta->format('d') }}</span>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('patients.show', $c->patient) }}" class="font-bold text-surface-900 text-xs hover:text-brand-600 transition-colors block truncate">
                                    {{ $c->patient->nome_completo }}
                                </a>
                                <p class="text-3xs text-surface-500">
                                    {{ $c->tipo_consulta_formatado ?? 'CPN de Seguimento' }} · {{ $c->data_consulta->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('consultations.show', $c) }}" class="btn-secondary-tw btn-xs-tw shrink-0">
                            <span>Atender</span>
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-surface-400 text-xs">
                        Nenhuma consulta agendada para os próximos dias.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- COLUNA 2: Busca Ativa Comunitária & Nascimentos Recentes --}}
    <div class="space-y-6">

        {{-- Busca Ativa: Gestantes Faltosas --}}
        <div class="card-tw border-l-4 border-l-gold-500">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gold-100 text-gold-800 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-person-walking-arrow-right"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-surface-900 text-sm">Busca Ativa: Faltosas à CPN</h4>
                        <p class="text-3xs text-surface-400">Gestantes com consultas em atraso para contacto de terreno</p>
                    </div>
                </div>
                <a href="{{ route('home_visits.active-search') }}" class="btn-tw bg-gold-500 hover:bg-gold-600 text-white btn-xs-tw font-bold shadow-xs">
                    <i class="fas fa-plus text-3xs"></i>
                    <span>Encaminhar em Lote</span>
                </a>
            </div>
            <div class="divide-y divide-surface-100">
                @forelse($pacientesFaltosas as $f)
                    @php
                        $ultConsultaFalta = $f->consultations->first();
                        $diasAtraso = $ultConsultaFalta ? (int)now()->diffInDays($ultConsultaFalta->data_consulta) : 0;
                    @endphp
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-surface-50/70 transition-colors">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <a href="{{ route('patients.show', $f) }}" class="font-bold text-surface-900 text-xs hover:text-brand-600 transition-colors">
                                    {{ $f->nome_completo }}
                                </a>
                                <span class="badge-danger text-3xs font-bold">
                                    {{ $diasAtraso }}d em falta
                                </span>
                            </div>
                            <p class="text-3xs text-surface-500 truncate">
                                Tel: {{ $f->contacto ?? 'Sem contacto' }} · Bairro: {{ $f->bairro ?? 'N/D' }}
                            </p>
                        </div>
                        <a href="{{ route('home_visits.active-search') }}" class="btn-secondary-tw btn-xs-tw shrink-0">
                            <i class="fas fa-user-nurse text-3xs text-gold-600"></i>
                            <span>Atribuir APE</span>
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-surface-400 text-xs">
                        <i class="fas fa-check-double text-emerald-500 mb-1"></i>
                        <p>Nenhuma gestante com consulta em atraso.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Nascimentos Recentes na Maternidade --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-baby-carriage"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-surface-900 text-sm">Partos Recentes na Maternidade</h4>
                        <p class="text-3xs text-surface-400">Registo obstétrico e estado neonatal</p>
                    </div>
                </div>
                <a href="{{ route('births.index') }}" class="btn-secondary-tw btn-xs-tw">
                    <span>Ver Livro</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>
            <div class="divide-y divide-surface-100">
                @forelse($ultimosPartos as $p)
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-surface-50/70 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-pink-50 text-pink-600 font-bold text-xs flex items-center justify-center shrink-0 border border-pink-200">
                                <i class="fas fa-baby"></i>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('births.show', $p) }}" class="font-bold text-surface-900 text-xs hover:text-brand-600 transition-colors block truncate">
                                    Mãe: {{ $p->patient->nome_completo ?? 'N/D' }}
                                </a>
                                <p class="text-3xs text-surface-500">
                                    {{ $p->data_hora_parto?->format('d/m/Y H:i') }} · {{ ucfirst(str_replace('_', ' ', $p->tipo_parto ?? 'Eutócico')) }} · Peso: <strong>{{ $p->peso_nascimento ? $p->peso_nascimento . 'g' : 'N/D' }}</strong> · APGAR: {{ $p->apgar_1min ?? '-' }}/{{ $p->apgar_5min ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('births.show', $p) }}" class="btn-icon-tw">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-surface-400 text-xs">
                        Nenhum parto registado recentemente.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

{{-- ============================================================
     5. SCRIPTS DOS GRÁFICOS (CHART.JS)
     ============================================================ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurações globais Chart.js para combinar com o design system
    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.borderRadius = 10;

    // 1. Gráfico de Evolução Mensal (Consultas & Partos)
    const ctxEvolucao = document.getElementById('chartEvolucaoMensal');
    if (ctxEvolucao) {
        new Chart(ctxEvolucao.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($mesesLabels) !!},
                datasets: [
                    {
                        label: 'Consultas CPN Realizadas',
                        data: {!! json_encode($consultasMensais) !!},
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#0f766e',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Partos na Maternidade',
                        data: {!! json_encode($partosMensais) !!},
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217, 119, 6, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#d97706',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Trimestres Gestacionais (Doughnut)
    const ctxTrimestres = document.getElementById('chartTrimestres');
    if (ctxTrimestres) {
        new Chart(ctxTrimestres.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['1º Trimestre', '2º Trimestre', '3º Trimestre', 'Pós-Parto'],
                datasets: [{
                    data: [{{ $trimestre1 }}, {{ $trimestre2 }}, {{ $trimestre3 }}, {{ $posParto }}],
                    backgroundColor: ['#14b8a6', '#0ea5e9', '#f59e0b', '#a855f7'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 3. Gráfico de Profilaxias MISAU (Horizontal Bar)
    const ctxProfilaxias = document.getElementById('chartProfilaxias');
    if (ctxProfilaxias) {
        new Chart(ctxProfilaxias.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($profilaxiasData['labels']) !!},
                datasets: [{
                    label: 'Taxa de Cobertura (%)',
                    data: {!! json_encode($profilaxiasData['percentuais']) !!},
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(14, 165, 233, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(168, 85, 247, 0.85)',
                        'rgba(239, 68, 68, 0.85)'
                    ],
                    borderRadius: 8,
                    barThickness: 16
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + '% das gestantes ativas';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 4. Gráfico de Visitas Domiciliárias (Bar)
    const ctxVisitas = document.getElementById('chartVisitas');
    if (ctxVisitas) {
        new Chart(ctxVisitas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Realizadas', 'Agendadas', 'Não Encontradas', 'Canceladas / US'],
                datasets: [{
                    label: 'Total de Visitas',
                    data: [{{ $visitasRealizadas }}, {{ $visitasAgendadas }}, {{ $visitasNaoEncontrada }}, {{ $visitasCanceladas }}],
                    backgroundColor: ['#10b981', '#0ea5e9', '#f59e0b', '#94a3b8'],
                    borderRadius: 8,
                    barThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush

@endsection
