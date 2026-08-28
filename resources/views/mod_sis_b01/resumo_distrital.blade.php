@extends('layouts.app-tw')

@section('title', 'Resumo Mensal do Distrito (MOD-SIS-B01-C)')
@section('page-title', 'Resumo Mensal do Distrito — SMI CPN')
@section('title-icon', 'fa-city')

@section('breadcrumbs')
    <a href="{{ route('mod_sis_b01.index') }}">Livro CPN</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Resumo Distrital (MOD-SIS-B01-C)</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Top Bar & Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <i class="fas fa-landmark mr-1"></i> MOD-SIS-B01-C
                </span>
                <h2 class="text-xl font-bold text-surface-900">Resumo Mensal do Distrito — SDSMAS</h2>
            </div>
            <p class="text-sm text-surface-500 mt-1">Consolidação de Unidades Sanitárias do Distrito de <strong>{{ $distrito }}</strong></p>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('mod_sis_b01.resumo_distrital') }}" class="flex items-center gap-2">
                <input type="month" name="mes" class="input-tw text-xs" value="{{ $mesAno }}">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-sync-alt text-xs"></i>
                    <span>Filtrar</span>
                </button>
            </form>

            <a href="{{ route('mod_sis_b01.resumo_provincial', ['mes' => $mesAno]) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-globe-africa text-xs"></i>
                <span>Ver Provincial (B01-D)</span>
            </a>
        </div>
    </div>

    {{-- Summary KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-hospital-user"></i>
            </div>
            <div>
                <p class="stat-card-value text-brand-700">{{ $totaisDistrito['total_primeiras'] }}</p>
                <p class="stat-card-label">Total 1ªs Consultas (Distrito)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="stat-card-value text-emerald-700">{{ $totaisDistrito['quatro_ou_mais_consultas'] }}</p>
                <p class="stat-card-label">Coorte Retida (≥ 4 CPN)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-card-value text-indigo-700">{{ $totaisDistrito['primeiras_precoces_12sem'] }}</p>
                <p class="stat-card-label">1ª CPN Precoce (≤ 12 sem)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-amber-500 to-amber-600">
                <i class="fas fa-users-viewfinder"></i>
            </div>
            <div>
                <p class="stat-card-value text-amber-700">{{ $totaisDistrito['total_coorte_6meses'] }}</p>
                <p class="stat-card-label">Total Inscritas na Coorte 6m</p>
            </div>
        </div>
    </div>

    {{-- Official Matrix Table --}}
    <div class="card-tw overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-200 bg-surface-50 flex items-center justify-between">
            <h3 class="font-bold text-surface-900 text-sm">
                <i class="fas fa-table mr-2 text-brand-600"></i> Matriz de Indicadores por Unidade Sanitária (MOD-SIS-B01-C)
            </h3>
            <span class="text-xs text-surface-500 font-medium">República de Moçambique — Ministério da Saúde</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Nº</th>
                        <th>Indicadores / Características CPN</th>
                        @foreach($dadosPorUnidade as $unidadeNome => $dados)
                            <th class="text-center font-bold text-surface-700">{{ $unidadeNome }}</th>
                        @endforeach
                        <th class="text-center bg-brand-50 text-brand-900 font-black">Total a Transportar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    <tr class="font-bold bg-surface-50">
                        <td class="text-center text-xs">1</td>
                        <td>Total das 1ªs Consultas</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center">{{ $dados['total_primeiras'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-black text-brand-700">{{ $totaisDistrito['total_primeiras'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">2</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade entre 10 e 14 anos</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_10_14'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-bold text-xs">{{ $totaisDistrito['idade_10_14'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">3</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade entre 15 e 19 anos (Adolescentes)</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_15_19'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-bold text-xs">{{ $totaisDistrito['idade_15_19'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">4</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade entre 20 e 24 anos</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_20_24'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-bold text-xs">{{ $totaisDistrito['idade_20_24'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">5</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade ≥ 25 anos</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_25_plus'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-bold text-xs">{{ $totaisDistrito['idade_25_plus'] }}</td>
                    </tr>
                    <tr class="bg-amber-50/50">
                        <td class="text-center text-xs font-bold text-amber-700">6</td>
                        <td class="font-semibold text-xs text-amber-900">Mulheres grávidas com ≤ 12 semanas na primeira consulta</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center font-bold text-xs text-amber-800">{{ $dados['primeiras_precoces_12sem'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-black text-amber-700">{{ $totaisDistrito['primeiras_precoces_12sem'] }}</td>
                    </tr>
                    <tr class="bg-surface-100 font-bold text-xs uppercase text-surface-600">
                        <td colspan="{{ count($dadosPorUnidade) + 3 }}" class="py-2 px-4">
                            Indicadores da Coorte de 6 Meses (Inscritas há 6 meses)
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs">7</td>
                        <td class="font-medium text-xs">Total de mulheres grávidas inscritas no período (Total da COORTE)</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center text-xs font-semibold">{{ $dados['total_coorte_6meses'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 font-bold text-xs">{{ $totaisDistrito['total_coorte_6meses'] }}</td>
                    </tr>
                    <tr class="bg-emerald-50/50 font-bold">
                        <td class="text-center text-xs text-emerald-700">8</td>
                        <td class="text-xs text-emerald-900">Total de mulheres grávidas que fizeram 4 ou mais consultas pré-natais</td>
                        @foreach($dadosPorUnidade as $dados)
                            <td class="text-center text-xs text-emerald-700 font-black">{{ $dados['quatro_ou_mais_consultas'] }}</td>
                        @endforeach
                        <td class="text-center bg-brand-50 text-emerald-800 font-black">{{ $totaisDistrito['quatro_ou_mais_consultas'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

