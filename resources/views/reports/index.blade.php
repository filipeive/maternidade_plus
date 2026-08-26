@extends('layouts.app-tw')

@section('title', 'Relatórios MISAU')
@section('page-title', 'Relatórios Estatísticos e Indicadores (MISAU)')
@section('title-icon', 'fa-chart-pie')

@section('breadcrumbs')
    <span class="active">Relatórios MISAU</span>
@endsection

@section('content')

{{-- Filter Card --}}
<div class="card-tw p-4 mb-6">
    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
        <div>
            <label class="label-tw">Ano de Referência</label>
            <select name="year" class="input-tw">
                @for($y = date('Y'); $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label class="label-tw">Mês de Referência</label>
            <select name="month" class="input-tw">
                @foreach([
                    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                ] as $val => $nome)
                    <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>{{ $nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-filter text-xs"></i>
                <span>Atualizar Relatório</span>
            </button>
        </div>
    </form>
</div>

{{-- Stat Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
            <i class="fas fa-person-pregnant"></i>
        </div>
        <div>
            <p class="stat-card-value text-brand-700">{{ $stats['novas_gestantes_mes'] }}</p>
            <p class="stat-card-label">Novas Gestantes (Mês)</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div>
            <p class="stat-card-value text-gold-700">{{ $stats['consultas_realizadas_mes'] }}</p>
            <p class="stat-card-label">Consultas ANC Realizadas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
            <i class="fas fa-flask"></i>
        </div>
        <div>
            <p class="stat-card-value text-ocean-700">{{ $stats['exames_realizados_mes'] }}</p>
            <p class="stat-card-label">Exames Concluídos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon bg-gradient-to-br from-crimson-500 to-crimson-600">
            <i class="fas fa-baby"></i>
        </div>
        <div>
            <p class="stat-card-value text-crimson-600">{{ $stats['partos_mes'] }}</p>
            <p class="stat-card-label">Partos Registados</p>
        </div>
    </div>
</div>

{{-- Report Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Relatório Pré-Natal --}}
    <div class="card-tw p-5 space-y-3">
        <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-lg">
            <i class="fas fa-file-medical-alt"></i>
        </div>
        <h3 class="text-base font-semibold text-surface-900">Relatório Mensal Pré-Natal (SIS-MA)</h3>
        <p class="text-xs text-surface-500">Indicadores de primeira consulta, 4ª e 8ª consulta ANC segundo norma do MISAU.</p>
        <div class="pt-2">
            <a href="#" onclick="alert('Exportação de mapa mensal em desenvolvimento'); return false;" class="btn-primary-tw btn-sm-tw w-full justify-center">
                <i class="fas fa-download text-xs"></i>
                <span>Exportar Mapa SIS-MA</span>
            </a>
        </div>
    </div>

    {{-- Relatório de Imunização --}}
    <div class="card-tw p-5 space-y-3">
        <div class="w-10 h-10 rounded-xl bg-gold-100 text-gold-700 flex items-center justify-center text-lg">
            <i class="fas fa-syringe"></i>
        </div>
        <h3 class="text-base font-semibold text-surface-900">Relatório de Cobertura Vacinal & IPTp</h3>
        <p class="text-xs text-surface-500">Imunização por VAT (Tétano) e 3+ doses de sulfadoxina-pirimetamina para malária.</p>
        <div class="pt-2">
            <a href="{{ route('vaccines.generate-report') }}" class="btn-secondary-tw btn-sm-tw w-full justify-center">
                <i class="fas fa-file-alt text-xs"></i>
                <span>Gerar Relatório Imunização</span>
            </a>
        </div>
    </div>

    {{-- Relatório de Alertas & M&E --}}
    <div class="card-tw p-5 space-y-3">
        <div class="w-10 h-10 rounded-xl bg-crimson-100 text-crimson-700 flex items-center justify-center text-lg">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3 class="text-base font-semibold text-surface-900">Métricas & Indicadores de Alertas</h3>
        <p class="text-xs text-surface-500">Desempenho de seguimento de alto risco, tempos de resposta e resolubilidade.</p>
        <div class="pt-2">
            <a href="{{ route('alertas.metricas') }}" class="btn-secondary-tw btn-sm-tw w-full justify-center">
                <i class="fas fa-chart-line text-xs"></i>
                <span>Abrir Dashboard M&E</span>
            </a>
        </div>
    </div>

</div>

@endsection
