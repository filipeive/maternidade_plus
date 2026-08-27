@extends('layouts.app-tw')

@section('title', 'Resumo Mensal SMI (MOD-SIS-B01-B)')
@section('page-title', 'Resumo Mensal da Unidade Sanitária — Consulta Pré-Natal')
@section('title-icon', 'fa-file-invoice')

@section('breadcrumbs')
    <a href="{{ route('mod_sis_b01.index') }}">Livro CPN</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Resumo Mensal (MOD-SIS-B01-B)</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Top Bar & Date Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-surface-900">Formulário Estatístico Mensal — MOD-SIS-B01-B</h2>
            <p class="text-sm text-surface-500">Relatório consolidado para Direcção Distrital de Saúde (DDS) & MISAU</p>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('mod_sis_b01.resumo_mensal') }}" class="flex items-center gap-2">
                <input type="month" name="mes" class="input-tw text-xs" value="{{ $mesAno }}">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-sync-alt text-xs"></i>
                    <span>Atualizar</span>
                </button>
            </form>

            <a href="{{ route('mod_sis_b01.resumo_mensal.pdf', ['mes' => $mesAno]) }}" class="btn-danger-tw btn-sm-tw">
                <i class="fas fa-file-pdf text-xs"></i>
                <span>Exportar PDF Oficial</span>
            </a>
        </div>
    </div>

    {{-- Metric Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-brand-500 to-brand-600">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="stat-card-value text-brand-700">{{ $indicadores['total_primeiras'] }}</p>
                <p class="stat-card-label">Novas Gestantes (1ª CPN)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <p class="stat-card-value text-emerald-700">{{ $indicadores['quatro_ou_mais_consultas'] }}</p>
                <p class="stat-card-label">Coorte com ≥ 4 Consultas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-ocean-500 to-ocean-600">
                <i class="fas fa-mosquito"></i>
            </div>
            <div>
                <p class="stat-card-value text-ocean-700">{{ $indicadores['sp2_doses'] }}</p>
                <p class="stat-card-label">Gestantes com SP2 (Fansidar)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon bg-gradient-to-br from-gold-500 to-gold-600">
                <i class="fas fa-syringe"></i>
            </div>
            <div>
                <p class="stat-card-value text-gold-700">{{ $indicadores['vat_concluido'] }}</p>
                <p class="stat-card-label">Vacinação VAT Imunizadas</p>
            </div>
        </div>
    </div>

    {{-- Report Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Section 1: Primeiras Consultas por Faixa Etária --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <h3 class="text-sm font-semibold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-user-clock text-brand-500"></i> 1. Primeiras Consultas CPN no Mês
                </h3>
            </div>
            <div class="p-4 space-y-3 text-xs">
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Mulheres grávidas entre 10 a 14 anos:</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['idade_10_14'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Mulheres grávidas entre 15 a 19 anos (Adolescentes):</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['idade_15_19'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Mulheres grávidas entre 20 a 24 anos:</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['idade_20_24'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Mulheres grávidas com idade ≥ 25 anos:</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['idade_25_plus'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 bg-brand-50/60 rounded-lg px-2">
                    <span class="font-semibold text-brand-900">Captação Precoce (≤ 12 Semanas na 1ª CPN):</span>
                    <span class="font-bold text-brand-700 text-sm">{{ $indicadores['primeiras_precoces_12sem'] }}</span>
                </div>
            </div>
        </div>

        {{-- Section 2: Coorte de 6 Meses & Profilaxias --}}
        <div class="card-tw">
            <div class="card-header-tw">
                <h3 class="text-sm font-semibold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-shield-virus text-ocean-500"></i> 2. Avaliação de Coorte de 6 Meses
                </h3>
            </div>
            <div class="p-4 space-y-3 text-xs">
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Total de gestantes na coorte de 6 meses:</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['total_coorte_6meses'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Com 4 ou mais consultas CPN efetuadas:</span>
                    <span class="font-bold text-emerald-700">{{ $indicadores['quatro_ou_mais_consultas'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Receberam 2 Doses de SP (Fansidar Malária):</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['sp2_doses'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Receberam 4 ou mais Doses de SP (Fansidar):</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['sp4_doses'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Receberam Rede Mosquiteira (REMTIL):</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['remtil_entregue'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-surface-100">
                    <span class="text-surface-600">Receberam ≥3 Doses de Sal Ferroso + Ácido Fólico:</span>
                    <span class="font-bold text-surface-900">{{ $indicadores['sal_ferroso_3doses'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-surface-600">Gestantes HIV+ em TARV à entrada ou iniciado:</span>
                    <span class="font-bold text-crimson-600">{{ $indicadores['hiv_tarv_entrada'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
