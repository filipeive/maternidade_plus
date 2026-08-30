@extends('layouts.app-tw')

@section('title', 'Relatórios & Estatísticas')
@section('page-title', 'Central de Relatórios Estatísticos & Indicadores MISAU')
@section('title-icon', 'fa-chart-pie')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Relatórios MISAU</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- 1. Header Banner & Filtros de Período --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-800 via-brand-700 to-ocean-800 text-white flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 shadow-md border-none">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20 shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                    <h2 class="text-base font-bold text-white">Central de Inteligência Clínica & Relatórios SISMA</h2>
                    <span class="badge-neutral text-3xs uppercase bg-white/10 text-white/90 border border-white/20">MISAU Moçambique</span>
                </div>
                <p class="text-xs text-white/70">Consolidação estatística de CPN, Nascimentos, Profilaxias (IPTp/REMTIL), PTV/HIV, Busca Ativa e Alertas Precoces.</p>
            </div>
        </div>

        {{-- Filtro de Mês / Ano --}}
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
            <select name="year" class="input-tw bg-white/10 border-white/20 text-white text-xs py-1.5 focus:bg-white focus:text-surface-900">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} class="text-surface-900">{{ $y }}</option>
                @endfor
            </select>

            <select name="month" class="input-tw bg-white/10 border-white/20 text-white text-xs py-1.5 focus:bg-white focus:text-surface-900">
                <option value="all" {{ $month === 'all' ? 'selected' : '' }} class="text-surface-900">Ano Inteiro</option>
                @foreach([
                    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                ] as $val => $nome)
                    <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }} class="text-surface-900">{{ $nome }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-tw bg-gold-400 text-surface-950 hover:bg-gold-300 btn-sm-tw font-bold text-xs shadow-sm">
                <i class="fas fa-filter text-xs"></i>
                <span>Atualizar</span>
            </button>
        </form>
    </div>

    {{-- 2. KPIs Macro do Período --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="card-tw p-3.5 border-l-4 border-l-brand-600">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">Novas Gestantes</span>
            <span class="text-xl font-bold text-brand-700 mt-1 block">{{ $stats['novasGestantesCount'] }}</span>
            <span class="text-3xs text-surface-500 font-medium">{{ $stats['inscricoesPrecoces'] }} precoces (&le;12 sem)</span>
        </div>

        <div class="card-tw p-3.5 border-l-4 border-l-ocean-600">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">Consultas ANC</span>
            <span class="text-xl font-bold text-ocean-700 mt-1 block">{{ $stats['consultasRealizadas'] }}</span>
            <span class="text-3xs text-surface-500 font-medium">{{ $stats['consultasQuarta'] }} de 4ª+ CPN</span>
        </div>

        <div class="card-tw p-3.5 border-l-4 border-l-crimson-600">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">Partos no Período</span>
            <span class="text-xl font-bold text-crimson-700 mt-1 block">{{ $stats['totalPartos'] }}</span>
            <span class="text-3xs text-surface-500 font-medium">{{ $stats['partosNormais'] }} normais / {{ $stats['cesarianas'] }} cesár.</span>
        </div>

        <div class="card-tw p-3.5 border-l-4 border-l-gold-500">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">IPTp-SP (Malária)</span>
            <span class="text-xl font-bold text-gold-700 mt-1 block">{{ $stats['iptp3MaisDoses'] }}</span>
            <span class="text-3xs text-surface-500 font-medium">{{ $stats['iptp1Dose'] }} com 1ª dose</span>
        </div>

        <div class="card-tw p-3.5 border-l-4 border-l-emerald-600">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">Busca Ativa (APEs)</span>
            <span class="text-xl font-bold text-emerald-700 mt-1 block">{{ $stats['visitasRealizadas'] }}</span>
            <span class="text-3xs text-surface-500 font-medium">de {{ $stats['visitasTotal'] }} agendadas</span>
        </div>

        <div class="card-tw p-3.5 border-l-4 border-l-purple-600">
            <span class="text-3xs font-semibold text-surface-400 uppercase tracking-wider block">Alertas Resolvidos</span>
            <span class="text-xl font-bold text-purple-700 mt-1 block">{{ $stats['taxaResolubilidade'] }}%</span>
            <span class="text-3xs text-surface-500 font-medium">{{ $stats['alertasResolvidos'] }} de {{ $stats['alertasTotal'] }}</span>
        </div>
    </div>

    {{-- 3. Detalhamento Modular em 2 Colunas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Bloco A: Saúde Materna & Consulta Pré-Natal (CPN) --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                        <i class="fas fa-person-pregnant"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">1. Saúde Materna & Consulta Pré-Natal (CPN)</h3>
                </div>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Total de Gestantes Ativas em Acompanhamento:</span>
                    <strong class="text-surface-900 font-mono text-sm">{{ $stats['totalAtivas'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Novas Inscrições no Período Selecionado:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['novasGestantesCount'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Inscrições Precoces na 1ª CPN (&le; 12 semanas):</span>
                    <strong class="text-brand-700 font-mono">{{ $stats['inscricoesPrecoces'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Adolescentes Inscritas (10 a 19 anos):</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['adolescentesSMI'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Gestantes Classificadas como Alto Risco (ARO):</span>
                    <strong class="text-crimson-600 font-mono font-bold">{{ $stats['altoRiscoCount'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Transferências Inter-Hospitalares / Província:</span>
                    <strong class="text-ocean-700 font-mono">{{ $stats['transferenciasPeriodo'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-surface-600">Consultas de 4ª CPN ou superior:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['consultasQuarta'] }}</strong>
                </div>
            </div>
        </div>

        {{-- Bloco B: Maternidade, Partos & Neonatal --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm">
                        <i class="fas fa-baby"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">2. Maternidade, Partos & Saúde Neonatal</h3>
                </div>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Total de Partos Institucionais:</span>
                    <strong class="text-surface-900 font-mono text-sm">{{ $stats['totalPartos'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Partos Eutócicos (Vaginais / Normais):</span>
                    <strong class="text-emerald-700 font-mono">{{ $stats['partosNormais'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Cesarianas Realizadas:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['cesarianas'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Nados Vivos:</span>
                    <strong class="text-emerald-700 font-mono font-bold">{{ $stats['nadosVivos'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Nados Mortos (Natimortos):</span>
                    <strong class="text-crimson-600 font-mono">{{ $stats['nadosMortos'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Recém-nascidos com Baixo Peso (&lt; 2.500g):</span>
                    <strong class="text-gold-700 font-mono">{{ $stats['baixoPeso'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-surface-600">Asfixia / APGAR no 5º minuto &lt; 7:</span>
                    <strong class="text-crimson-600 font-mono">{{ $stats['apgarBaixo'] }}</strong>
                </div>
            </div>
        </div>

        {{-- Bloco C: Profilaxias MISAU (IPTp, REMTIL, Nutrição) --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-800 flex items-center justify-center text-sm">
                        <i class="fas fa-mosquito"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">3. Profilaxias Maternas & Nutrição (MISAU)</h3>
                </div>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">IPTp-SP 1ª Dose (Malária):</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['iptp1Dose'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">IPTp-SP 2ª Dose (Malária):</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['iptp2Dose'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">IPTp-SP 3ª+ Doses (Proteção Completa):</span>
                    <strong class="text-emerald-700 font-mono font-bold">{{ $stats['iptp3MaisDoses'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Redes Mosquiteiras Tratadas (REMTIL) Distribuídas:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['remtilEntregues'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Suplementação de Sulfato Ferroso / Ácido Fólico:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['ferroFolatoEntregues'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Desparasitação com Mebendazol (&ge; 12 semanas):</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['mebendazolEntregues'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-surface-600">Prevenção HPP com Misoprostol Comunitário:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['misoprostolEntregues'] }}</strong>
                </div>
            </div>
        </div>

        {{-- Bloco D: Triagem PTV (HIV/Sífilis) & Busca Ativa --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                        <i class="fas fa-virus-covid"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-surface-900">4. Triagem PTV (HIV/Sífilis) & Saúde Comunitária</h3>
                </div>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Mulheres Testadas para HIV na CPN:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['hivTestadas'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">HIV Positivas / Início de TARV Imediato:</span>
                    <strong class="text-crimson-600 font-mono font-bold">{{ $stats['hivPositivas'] }} ({{ $stats['tarvIniciadas'] }} em TARV)</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Parceiros Masculinos Testados para HIV:</span>
                    <strong class="text-brand-700 font-mono">{{ $stats['parceiroHivTestados'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Rastreio de Sífilis (VDRL) / Tratadas c/ Penicilina:</span>
                    <strong class="text-surface-900 font-mono">{{ $stats['sifilisTestadas'] }} ({{ $stats['sifilisTratadas'] }} tratadas)</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-surface-100">
                    <span class="text-surface-600">Visitas de Busca Ativa Realizadas (APEs):</span>
                    <strong class="text-emerald-700 font-mono">{{ $stats['visitasRealizadas'] }}</strong>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-surface-600">Visitas com Desfecho "Não Encontrada":</span>
                    <strong class="text-gold-700 font-mono">{{ $stats['visitasNaoEncontrada'] }}</strong>
                </div>
            </div>
        </div>

    </div>

    {{-- 4. Hub de Relatórios Oficiais & Livros de Registo --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-surface-100 text-surface-700 flex items-center justify-center text-sm">
                    <i class="fas fa-book-medical"></i>
                </div>
                <h3 class="text-sm font-semibold text-surface-900">Mapas & Livros de Registo Oficiais do MISAU</h3>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- MOD-SIS-B01 Livro de CPN --}}
            <div class="p-4 bg-surface-50 rounded-xl border border-surface-200 flex flex-col justify-between space-y-3">
                <div>
                    <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm mb-2">
                        <i class="fas fa-book"></i>
                    </span>
                    <h4 class="text-xs font-bold text-surface-900">Livro de CPN (MOD-SIS-B01)</h4>
                    <p class="text-3xs text-surface-500 mt-1">Registo nominal oficial de consultas pré-natais segundo o modelo do MISAU.</p>
                </div>
                <a href="{{ route('mod_sis_b01.index') }}" class="btn-primary-tw btn-xs-tw justify-center">
                    <span>Abrir Livro B01</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>

            {{-- Resumo Mensal B01-B --}}
            <div class="p-4 bg-surface-50 rounded-xl border border-surface-200 flex flex-col justify-between space-y-3">
                <div>
                    <span class="w-8 h-8 rounded-lg bg-ocean-100 text-ocean-700 flex items-center justify-center text-sm mb-2">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                    <h4 class="text-xs font-bold text-surface-900">Resumo Mensal (MOD-SIS-B01-B)</h4>
                    <p class="text-3xs text-surface-500 mt-1">Mapa mensal agregado para envio à Direcção Distrital de Saúde (DDS / SISMA).</p>
                </div>
                <a href="{{ route('mod_sis_b01.resumo-mensal') }}" class="btn-secondary-tw btn-xs-tw justify-center">
                    <span>Gerar Resumo B01-B</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>

            {{-- Auditoria de Alertas Precoces --}}
            <div class="p-4 bg-surface-50 rounded-xl border border-surface-200 flex flex-col justify-between space-y-3">
                <div>
                    <span class="w-8 h-8 rounded-lg bg-crimson-100 text-crimson-700 flex items-center justify-center text-sm mb-2">
                        <i class="fas fa-clipboard-check"></i>
                    </span>
                    <h4 class="text-xs font-bold text-surface-900">Painel de Avaliações Clínicas</h4>
                    <p class="text-3xs text-surface-500 mt-1">Auditoria nominal contínua de risco obstétrico e sinais de alarme das gestantes.</p>
                </div>
                <a href="{{ route('alertas.avaliacoes') }}" class="btn-secondary-tw btn-xs-tw justify-center">
                    <span>Ver Avaliações</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>

            {{-- Métricas M&E de Alertas --}}
            <div class="p-4 bg-surface-50 rounded-xl border border-surface-200 flex flex-col justify-between space-y-3">
                <div>
                    <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm mb-2">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <h4 class="text-xs font-bold text-surface-900">Métricas de Alertas (M&E)</h4>
                    <p class="text-3xs text-surface-500 mt-1">Tempos de resposta clínica, resolubilidade e impacto em saúde materno-infantil.</p>
                </div>
                <a href="{{ route('alertas.metricas') }}" class="btn-secondary-tw btn-xs-tw justify-center">
                    <span>Dashboard M&E</span>
                    <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
