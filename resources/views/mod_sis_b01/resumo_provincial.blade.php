@extends('layouts.app-tw')

@section('title', 'Resumo Mensal da Província (MOD-SIS-B01-D)')
@section('page-title', 'Resumo Mensal da Província — SMI CPN')
@section('title-icon', 'fa-map-location-dot')

@section('breadcrumbs')
    <a href="{{ route('mod_sis_b01.index') }}">Livro CPN</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Resumo Provincial (MOD-SIS-B01-D)</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Top Bar & Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                    <i class="fas fa-flag mr-1"></i> MOD-SIS-B01-D
                </span>
                <h2 class="text-xl font-bold text-surface-900">Resumo Mensal da Província — SPS</h2>
            </div>
            <p class="text-sm text-surface-500 mt-1">Consolidação Provincial de Saúde — Província de <strong>{{ $provincia }}</strong></p>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('mod_sis_b01.resumo_provincial') }}" class="flex items-center gap-2">
                <input type="month" name="mes" class="input-tw text-xs" value="{{ $mesAno }}">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-sync-alt text-xs"></i>
                    <span>Filtrar</span>
                </button>
            </form>

            <a href="{{ route('mod_sis_b01.resumo_distrital', ['mes' => $mesAno]) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-city text-xs"></i>
                <span>Ver Distrital (B01-C)</span>
            </a>
        </div>
    </div>

    {{-- Provincial KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
                <i class="fas fa-hospital-user"></i>
            </div>
            <div>
                <p class="stat-card-value text-indigo-700">{{ $totaisProvincia['total_primeiras'] }}</p>
                <p class="stat-card-label">Novas Inscrições CPN (Província)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="stat-card-value text-emerald-700">{{ $totaisProvincia['quatro_ou_mais_consultas'] }}</p>
                <p class="stat-card-label">Total com ≥ 4 CPN na Coorte</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-card-value text-brand-700">{{ $totaisProvincia['primeiras_precoces_12sem'] }}</p>
                <p class="stat-card-label">Captação Precoce (≤ 12 sem)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-purple-500 to-purple-600">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <p class="stat-card-value text-purple-700">{{ count($dadosPorDistrito) }}</p>
                <p class="stat-card-label">Distritos Monitorados</p>
            </div>
        </div>
    </div>

    {{-- Official Provincial Matrix Table --}}
    <div class="card-tw overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-200 bg-surface-50 flex items-center justify-between">
            <h3 class="font-bold text-surface-900 text-sm">
                <i class="fas fa-table-list mr-2 text-indigo-600"></i> Matriz Consolidada de Distritos (MOD-SIS-B01-D)
            </h3>
            <span class="text-xs text-surface-500 font-medium">Serviço Provincial de Saúde / Direcção Nacional de Saúde Pública</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-tw">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Nº</th>
                        <th>Indicadores / Características CPN</th>
                        @foreach($dadosPorDistrito as $distritoNome => $dados)
                            <th class="text-center font-bold text-surface-700">{{ $distritoNome }}</th>
                        @endforeach
                        <th class="text-center bg-indigo-50 text-indigo-900 font-black">Total Provincial</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    <tr class="font-bold bg-surface-50">
                        <td class="text-center text-xs">1</td>
                        <td>Total das 1ªs Consultas</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center">{{ $dados['total_primeiras'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-black text-indigo-700">{{ $totaisProvincia['total_primeiras'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">2</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade entre 10 e 14 anos</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_10_14'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-bold text-xs">{{ $totaisProvincia['idade_10_14'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">3</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade entre 15 e 19 anos (Adolescentes)</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_15_19'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-bold text-xs">{{ $totaisProvincia['idade_15_19'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">4</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade entre 20 e 24 anos</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_20_24'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-bold text-xs">{{ $totaisProvincia['idade_20_24'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs text-surface-400">5</td>
                        <td class="pl-8 text-xs text-surface-600">Mulheres grávidas com idade ≥ 25 anos</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center text-xs">{{ $dados['idade_25_plus'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-bold text-xs">{{ $totaisProvincia['idade_25_plus'] }}</td>
                    </tr>
                    <tr class="bg-amber-50/50">
                        <td class="text-center text-xs font-bold text-amber-700">6</td>
                        <td class="font-semibold text-xs text-amber-900">Mulheres grávidas com ≤ 12 semanas na primeira consulta</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center font-bold text-xs text-amber-800">{{ $dados['primeiras_precoces_12sem'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-black text-amber-700">{{ $totaisProvincia['primeiras_precoces_12sem'] }}</td>
                    </tr>
                    <tr class="bg-surface-100 font-bold text-xs uppercase text-surface-600">
                        <td colspan="{{ count($dadosPorDistrito) + 3 }}" class="py-2 px-4">
                            Indicadores da Coorte de 6 Meses (Inscritas há 6 meses)
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center text-xs">7</td>
                        <td class="font-medium text-xs">Total de mulheres grávidas inscritas no período (Total da COORTE)</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center text-xs font-semibold">{{ $dados['total_coorte_6meses'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 font-bold text-xs">{{ $totaisProvincia['total_coorte_6meses'] }}</td>
                    </tr>
                    <tr class="bg-emerald-50/50 font-bold">
                        <td class="text-center text-xs text-emerald-700">8</td>
                        <td class="text-xs text-emerald-900">Total de mulheres grávidas que fizeram 4 ou mais consultas pré-natais</td>
                        @foreach($dadosPorDistrito as $dados)
                            <td class="text-center text-xs text-emerald-700 font-black">{{ $dados['quatro_ou_mais_consultas'] }}</td>
                        @endforeach
                        <td class="text-center bg-indigo-50 text-emerald-800 font-black">{{ $totaisProvincia['quatro_ou_mais_consultas'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

