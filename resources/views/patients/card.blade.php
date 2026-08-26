@extends('layouts.app-tw')

@section('title', 'Cartão da Gestante — ' . $patient->nome_completo)
@section('page-title', 'Cartão de Identificação da Gestante')
@section('title-icon', 'fa-id-card')

@section('breadcrumbs')
    <a href="{{ route('patients.index') }}">Gestantes</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('patients.show', $patient) }}">{{ $patient->nome_completo }}</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Cartão de Identificação</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Botões de Ação na Tela --}}
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('patients.show', $patient) }}" class="btn-secondary-tw btn-sm-tw">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Voltar à Ficha da Paciente</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('patients.card.pdf', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                <i class="fas fa-file-pdf text-crimson-600 text-xs"></i>
                <span>Descarregar PDF</span>
            </a>
            <button onclick="window.print()" class="btn-primary-tw btn-sm-tw">
                <i class="fas fa-print text-xs"></i>
                <span>Imprimir Cartão</span>
            </button>
        </div>
    </div>

    {{-- CARTÃO DA GESTANTE — DESIGN ESTILIZADO DE SAÚDE --}}
    <div class="print-card-wrapper flex justify-center">
        <div class="w-full max-w-2xl bg-white rounded-2xl border-2 border-brand-500 shadow-xl overflow-hidden print:shadow-none print:border-2 print:border-black print:w-[100%] print:max-w-none">
            
            {{-- Topo do Cartão com Cores de Moçambique & MISAU --}}
            <div class="bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white p-4 flex items-center justify-between border-b-4 border-gold-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-xs flex items-center justify-center border border-white/20 text-gold-300 text-xl font-black">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-wider text-white">REPÚBLICA DE MOÇAMBIQUE</h2>
                        <h3 class="text-2xs font-semibold text-gold-300 uppercase tracking-widest">MINISTÉRIO DA SAÚDE · SERVIÇO DE SAÚDE MATERNO-INFANTIL</h3>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-white/20 rounded-md text-2xs font-bold uppercase tracking-wider border border-white/30 text-white">
                    CARTÃO DA GESTANTE
                </span>
            </div>

            {{-- Corpo do Cartão --}}
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 bg-gradient-to-b from-white to-surface-50/50">
                
                {{-- Coluna 1 & 2: Dados Principais da Paciente --}}
                <div class="sm:col-span-2 space-y-4">
                    <div>
                        <span class="text-3xs uppercase font-extrabold tracking-widest text-surface-400 block">NOME COMPLETO DA PACIENTE</span>
                        <h1 class="text-lg font-black text-surface-900 leading-tight">{{ $patient->nome_completo }}</h1>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-surface-100/60 p-2 rounded-lg border border-surface-200">
                            <span class="text-3xs uppercase font-bold text-surface-500 block">NID / BI</span>
                            <span class="font-bold text-surface-900">{{ $patient->documento_bi ?? 'N/A' }}</span>
                        </div>

                        <div class="bg-surface-100/60 p-2 rounded-lg border border-surface-200">
                            <span class="text-3xs uppercase font-bold text-surface-500 block">IDADE / TIPO SANGUÍNEO</span>
                            <span class="font-bold text-surface-900">{{ $patient->idade }} anos · <span class="text-crimson-600">{{ $patient->tipo_sanguineo ?? 'S/T' }}</span></span>
                        </div>

                        <div class="bg-surface-100/60 p-2 rounded-lg border border-surface-200">
                            <span class="text-3xs uppercase font-bold text-surface-500 block">DATA ÚLTIMA MENSTRUAÇÃO (DUM)</span>
                            <span class="font-bold text-brand-700">{{ $patient->data_ultima_menstruacao?->format('d/m/Y') ?? 'N/A' }}</span>
                        </div>

                        <div class="bg-surface-100/60 p-2 rounded-lg border border-surface-200">
                            <span class="text-3xs uppercase font-bold text-surface-500 block">DATA PROVÁVEL DO PARTO (DPP)</span>
                            <span class="font-bold text-crimson-600">{{ $patient->data_provavel_parto?->format('d/m/Y') ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="bg-brand-50/60 border border-brand-200/80 p-2.5 rounded-lg text-xs flex justify-between items-center">
                        <div>
                            <span class="text-3xs uppercase font-bold text-brand-800 block">UNIDADE SANITÁRIA DE REFERÊNCIA</span>
                            <span class="font-bold text-surface-900">Centro de Saúde de Quelimane Urbano</span>
                        </div>
                        <div class="text-right">
                            <span class="text-3xs uppercase font-bold text-brand-800 block">CONTACTO EMERGÊNCIA</span>
                            <span class="font-bold text-surface-900">{{ $patient->contacto_emergencia ?? $patient->contacto }}</span>
                        </div>
                    </div>
                </div>

                {{-- Coluna 3: QR Code Destacado para Leitura da Enfermeira --}}
                <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl border border-surface-200 shadow-2xs text-center space-y-2">
                    <div class="p-2 bg-white rounded-lg border-2 border-brand-500">
                        <img src="{{ $qrCode }}" alt="QR Code da Gestante" class="w-36 h-36 object-contain">
                    </div>
                    <span class="text-3xs uppercase font-extrabold tracking-wider text-brand-700">SCAN PARA FICHA CLÍNICA</span>
                    <span class="text-4xs text-surface-400 font-mono">ID: #{{ str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>

            </div>

            {{-- Rodapé do Cartão --}}
            <div class="px-6 py-2.5 bg-surface-100/80 border-t border-surface-200 flex items-center justify-between text-3xs text-surface-500 font-medium">
                <span>Maternidade+ · Cuidados Pré-Natais MISAU</span>
                <span>Apresente este cartão em cada consulta pré-natal</span>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-card-wrapper, .print-card-wrapper * {
        visibility: visible;
    }
    .print-card-wrapper {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection
