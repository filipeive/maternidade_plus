@extends('layouts.app-tw')

@section('title', 'Leitor de QR Code')
@section('page-title', 'Leitor de QR Code da Gestante')
@section('title-icon', 'fa-qrcode')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Início</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">Leitor de QR Code</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ scanning: false, manualId: '' }">

    {{-- Banner de Instruções --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20">
                <i class="fas fa-qrcode"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Scanner de Cartão da Gestante</h2>
                <p class="text-xs text-white/70">Aponte a câmara do telemóvel ou tablet para o QR Code impresso no Cartão da Paciente para abrir a ficha clínica instantaneamente.</p>
            </div>
        </div>

        <a href="{{ route('patients.index') }}" class="btn-secondary-tw btn-sm-tw">
            <i class="fas fa-users text-xs"></i>
            <span>Lista de Gestantes</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- CÂMARA SCANNER CONTAINER --}}
        <div class="md:col-span-2 card-tw p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-camera text-brand-600"></i> Câmara de Leitura Óptica
                </h3>
                <span class="badge-info text-2xs">HTML5 Scanner</span>
            </div>

            {{-- Leitor de Vídeo --}}
            <div id="scanner-wrapper" class="relative w-full h-80 bg-surface-900 rounded-2xl overflow-hidden flex flex-col items-center justify-center text-white border-2 border-brand-500/30">
                <div id="qr-reader" class="w-full h-full"></div>
                <div id="qr-placeholder" class="text-center p-6 space-y-3">
                    <i class="fas fa-qrcode text-5xl text-brand-400 animate-pulse"></i>
                    <p class="text-xs text-surface-300">Prima no botão abaixo para autorizar o acesso à câmara</p>
                    <button onclick="initScanner()" class="btn-primary-tw font-bold shadow-lg">
                        <i class="fas fa-video text-xs"></i>
                        <span>Iniciar Leitura com Câmara</span>
                    </button>
                </div>
            </div>

            <div class="text-2xs text-surface-500 text-center">
                <i class="fas fa-circle-info text-brand-500 mr-1"></i> O scanner redireciona automaticamente após a leitura bem-sucedida.
            </div>
        </div>

        {{-- LEITURA MANUAL & DICAS --}}
        <div class="space-y-6">
            <div class="card-tw p-6 space-y-4">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2 border-b border-surface-100 pb-3">
                    <i class="fas fa-keyboard text-brand-600"></i> Entrada Manual / NID
                </h3>

                <p class="text-xs text-surface-600">
                    Caso a câmara não esteja disponível, introduza a URL do QR Code ou o número de ID da gestante:
                </p>

                <form @submit.prevent="
                    if (!manualId.trim()) return;
                    let val = manualId.trim();
                    if (val.includes('/patients/')) {
                        window.location.href = val;
                    } else {
                        window.location.href = '{{ url('/patients') }}/' + val.replace(/\D/g, '');
                    }
                " class="space-y-3">
                    <div>
                        <label class="label-tw">Código ou ID da Gestante</label>
                        <input type="text" x-model="manualId" placeholder="Ex: 5 ou http://.../patients/5" class="input-tw text-xs font-mono">
                    </div>

                    <button type="submit" class="btn-primary-tw w-full justify-center text-xs">
                        <i class="fas fa-arrow-right text-xs"></i>
                        <span>Localizar Paciente</span>
                    </button>
                </form>
            </div>

            <div class="card-tw p-4 bg-surface-50 border border-surface-200 text-xs space-y-2">
                <h4 class="font-bold text-surface-900 flex items-center gap-1.5">
                    <i class="fas fa-lightbulb text-gold-500"></i> Dica de Utilização
                </h4>
                <p class="text-surface-600 leading-relaxed text-2xs">
                    Cada Cartão da Gestante possui um QR Code exclusivo com a hiperligação para a sua Ficha Clínica. Basta apontar a câmara para abrir o histórico de consultas, vacinas e exames.
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function initScanner() {
        document.getElementById('qr-placeholder')?.classList.add('hidden');
        
        if (typeof Html5QrcodeScanner === 'undefined') {
            alert('A carregar a biblioteca do leitor...');
            return;
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", 
            { fps: 10, qrbox: { width: 250, height: 250 } }, 
            /* verbose= */ false
        );

        html5QrcodeScanner.render((decodedText) => {
            html5QrcodeScanner.clear();
            if (decodedText.includes('/patients/')) {
                window.location.href = decodedText;
            } else {
                const cleanId = decodedText.replace(/\D/g, '');
                if (cleanId) window.location.href = '{{ url('/patients') }}/' + cleanId;
            }
        });
    }
</script>
@endpush
@endsection
