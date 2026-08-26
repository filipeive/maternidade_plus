@extends('layouts.app-tw')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')
@section('title-icon', 'fa-sliders')

@section('breadcrumbs')
    <span class="active">Configurações</span>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-6">

    {{-- Unidade Sanitária & Sistema --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Unidade Sanitária & Parâmetros MISAU</h3>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.update-general') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Nome da Unidade Sanitária</label>
                    <input type="text" class="input-tw" value="Centro de Saúde de Quelimane Urbano" disabled>
                </div>

                <div>
                    <label class="label-tw">Província / Distrito</label>
                    <input type="text" class="input-tw" value="Zambézia — Quelimane" disabled>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label-tw">Meta de Contactos ANC (MISAU)</label>
                    <input type="number" class="input-tw" value="8" min="4" max="12">
                </div>

                <div>
                    <label class="label-tw">Dias para Alerta de Gestante Faltosa</label>
                    <input type="number" class="input-tw" value="7" min="1" max="30">
                </div>
            </div>

            <div class="flex items-center justify-end border-t border-surface-100 pt-4">
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-save text-xs"></i>
                    <span>Salvar Configurações</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Notificações --}}
    <div class="card-tw">
        <div class="card-header-tw">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center text-sm">
                    <i class="fas fa-bell"></i>
                </div>
                <h3 class="text-base font-semibold text-surface-900">Notificações e Alertas Automáticos</h3>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.update-notifications') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="space-y-3 text-sm">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" checked class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-surface-800">Alertar automaticamente exames com resultados reagentes (HIV / Sífilis)</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" checked class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-surface-800">Gerar alerta de busca ativa quando a gestante faltar à consulta agendada</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" checked class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-surface-800">Notificar atrasos no calendário de vacinas (VAT) e doses de IPTp-SP</span>
                </label>
            </div>

            <div class="flex items-center justify-end border-t border-surface-100 pt-4">
                <button type="submit" class="btn-primary-tw">
                    <i class="fas fa-check text-xs"></i>
                    <span>Guardar Preferências</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Info do Sistema --}}
    <div class="card-tw p-5 flex items-center justify-between text-xs text-surface-500">
        <div>
            <p><strong class="text-surface-800">Maternidade+</strong> v2.0.0 — Laravel {{ app()->version() }}</p>
            <p class="mt-0.5">Base de dados MySQL · PHP {{ PHP_VERSION }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="badge-success">Sistema Operacional</span>
        </div>
    </div>

</div>
@endsection
