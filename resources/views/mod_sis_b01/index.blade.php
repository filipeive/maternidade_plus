@extends('layouts.app-tw')

@section('title', 'Livro CPN (MOD-SIS-B01)')
@section('page-title', 'Livro Eletrónico de Registos da Consulta Pré-Natal')
@section('title-icon', 'fa-book-medical')

@section('breadcrumbs')
    <a href="{{ route('consultations.index') }}">Consultas</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Livro CPN (MOD-SIS-B01)</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Top Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-surface-900">Livro de Registos CPN — MOD-SIS-B01 (MISAU)</h2>
            <p class="text-sm text-surface-500">Instrumento oficial de monitoria continuada da gestação, profilaxias e coortes de saúde materna</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('mod_sis_b01.resumo_mensal') }}" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-chart-pie text-xs"></i>
                <span>US (B01-B)</span>
            </a>
            <a href="{{ route('mod_sis_b01.resumo_distrital') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-city text-xs"></i>
                <span>Distrito (B01-C)</span>
            </a>
            <a href="{{ route('mod_sis_b01.resumo_provincial') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-flag text-xs"></i>
                <span>Província (B01-D)</span>
            </a>
            <a href="{{ route('consultations.create') }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-plus text-xs"></i>
                <span>Nova Consulta</span>
            </a>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="card-tw p-4">
        <form method="GET" action="{{ route('mod_sis_b01.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1">
                <label class="label-tw">Pesquisar Utente no Livro CPN</label>
                <input type="text" name="search" class="input-tw" placeholder="Nome da gestante, NID, BI ou Telefone..." value="{{ request('search') }}">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary-tw btn-sm-tw">
                    <i class="fas fa-search text-xs"></i>
                    <span>Pesquisar</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('mod_sis_b01.index') }}" class="btn-secondary-tw btn-sm-tw">
                        <i class="fas fa-times text-xs"></i>
                        <span>Limpar</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Digital Register Table (Grelha Estilo MOD-SIS-B01) --}}
    <div class="card-tw overflow-hidden shadow-lg">
        <div class="card-header-tw flex justify-between items-center bg-gradient-to-r from-surface-800 to-brand-900 text-white p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-gold-300 font-bold">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Livro Digital CPN — Moçambique</h3>
                    <p class="text-2xs text-surface-200">Grelha de acompanhamento de coortes de 6 meses & profilaxias</p>
                </div>
            </div>
            <span class="badge-gold text-2xs uppercase">Formulário Nacional MOD-SIS-B01</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-surface-100 text-surface-800 uppercase font-bold border-b border-surface-200 text-3xs">
                    <tr>
                        <th class="p-3 border-r border-surface-200 w-12 text-center">Nº Livro</th>
                        <th class="p-3 border-r border-surface-200 min-w-[200px]">Identificação & Residência</th>
                        <th class="p-3 border-r border-surface-200 text-center">Faixa Etária</th>
                        <th class="p-3 border-r border-surface-200 text-center">Consultas CPN</th>
                        <th class="p-3 border-r border-surface-200 text-center">Prevenção Malária (TIP SP)</th>
                        <th class="p-3 border-r border-surface-200 text-center">Tétano (VAT)</th>
                        <th class="p-3 border-r border-surface-200 text-center">PTV HIV & Sífilis</th>
                        <th class="p-3 border-r border-surface-200 text-center">Nutrição (Sal Ferr + Ac. Fólico)</th>
                        <th class="p-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($patients as $index => $patient)
                        @php
                            $proph = $patient->prophylaxis;
                            $nConsultas = $patient->consultations->count();
                            $hasAro = $patient->antenatalHistory?->is_aro;
                        @endphp
                        <tr class="hover:bg-brand-50/40 transition-colors">
                            <td class="p-3 border-r border-surface-100 font-bold text-center text-surface-500">
                                #{{ $patients->firstItem() + $index }}
                            </td>
                            <td class="p-3 border-r border-surface-100">
                                <div class="font-bold text-surface-900 flex items-center gap-1.5">
                                    <a href="{{ route('patients.show', $patient) }}" class="hover:text-brand-600">
                                        {{ $patient->nome_completo }}
                                    </a>
                                    @if($hasAro)
                                        <span class="badge-danger text-3xs" title="Alto Risco Obstétrico (ARO)">ARO</span>
                                    @endif
                                </div>
                                <p class="text-3xs text-surface-400">BI: {{ $patient->documento_bi }} · {{ $patient->contacto ?? 'Sem Tel' }}</p>
                            </td>
                            <td class="p-3 border-r border-surface-100 text-center">
                                <span class="badge-neutral text-3xs font-semibold">
                                    {{ $patient->idade }} anos
                                </span>
                            </td>
                            <td class="p-3 border-r border-surface-100 text-center">
                                <span class="{{ $nConsultas >= 4 ? 'badge-success' : 'badge-warning' }} font-bold text-xs">
                                    {{ $nConsultas }} / 4 CPN
                                </span>
                            </td>
                            <td class="p-3 border-r border-surface-100 text-center">
                                <div class="flex items-center justify-center gap-1 text-3xs">
                                    <span class="{{ $proph->sp_1_dose ? 'bg-emerald-100 text-emerald-800' : 'bg-surface-100 text-surface-400' }} px-1.5 py-0.5 rounded font-mono" title="SP 1ª Dose">SP1</span>
                                    <span class="{{ $proph->sp_2_dose ? 'bg-emerald-100 text-emerald-800' : 'bg-surface-100 text-surface-400' }} px-1.5 py-0.5 rounded font-mono" title="SP 2ª Dose">SP2</span>
                                    <span class="{{ $proph->sp_3_dose ? 'bg-emerald-100 text-emerald-800' : 'bg-surface-100 text-surface-400' }} px-1.5 py-0.5 rounded font-mono" title="SP 3ª Dose">SP3</span>
                                    @if($proph->remtil_entregue)
                                        <i class="fas fa-bed text-ocean-600 text-2xs" title="Rede Mosquiteira REMTIL Entregue"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3 border-r border-surface-100 text-center">
                                <span class="{{ !is_null($proph->vat_2_dose) || !is_null($proph->vat_reforco) ? 'badge-success' : 'badge-neutral' }} text-3xs">
                                    {{ !is_null($proph->vat_reforco) ? 'VAT Reforço' : (!is_null($proph->vat_2_dose) ? 'VAT 2ª Dose' : 'Iniciado') }}
                                </span>
                            </td>
                            <td class="p-3 border-r border-surface-100 text-center">
                                <div class="space-y-0.5 text-3xs">
                                    <p class="font-medium text-surface-700">HIV: <span class="font-bold uppercase">{{ $proph->hiv_status_entrada ?? 'Negativo' }}</span></p>
                                    <p class="text-surface-500">Sífilis: <span class="font-bold">{{ $proph->sifilis_resultado ?? 'Negativo' }}</span></p>
                                </div>
                            </td>
                            <td class="p-3 border-r border-surface-100 text-center">
                                <span class="{{ $proph->sal_ferroso_folico_3doses ? 'badge-success' : 'badge-warning' }} text-3xs">
                                    {{ $proph->sal_ferroso_folico_3doses ? '≥3 Doses Sal Fer.' : 'Em curso' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('patients.show', $patient) }}" class="btn-icon-tw" title="Ver Cartão Clínico FPN">
                                    <i class="fas fa-id-card text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-surface-400">
                                <i class="fas fa-folder-open text-3xl mb-2"></i>
                                <p>Nenhuma gestante registada no Livro CPN.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer-tw p-4 border-t border-surface-100 flex items-center justify-between">
            <p class="text-xs text-surface-500">
                Mostrando {{ $patients->firstItem() ?? 0 }} a {{ $patients->lastItem() ?? 0 }} de {{ $patients->total() }} registos
            </p>
            {{ $patients->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
