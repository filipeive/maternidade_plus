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
<div class="max-w-4xl mx-auto space-y-6" x-data="{ manualId: '', scanStatus: '' }">

    {{-- Banner de Instruções --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20">
                <i class="fas fa-qrcode"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Scanner de Cartão da Gestante</h2>
                <p class="text-xs text-white/70">Aponte a câmara do dispositivo ou tire/carregue uma fotografia do QR Code do Cartão para abrir a ficha clínica instantaneamente.</p>
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
                    <i class="fas fa-camera text-brand-600"></i> Leitor Óptico & Fotografia
                </h3>
                <span class="badge-info text-2xs">Universal HTTP/HTTPS</span>
            </div>

            {{-- Área do Vídeo / Leitura --}}
            <div id="scanner-wrapper" class="relative w-full min-h-[260px] bg-surface-900 rounded-2xl overflow-hidden flex flex-col items-center justify-center text-white p-4 border-2 border-brand-500/30">
                <div id="qr-reader" class="w-full h-full min-h-[240px]"></div>
                
                <div id="qr-placeholder" class="text-center p-4 space-y-4">
                    <i class="fas fa-qrcode text-5xl text-brand-400 animate-pulse"></i>
                    <p class="text-xs text-surface-300 max-w-xs mx-auto">Escolha o método de leitura preferido para o Cartão da Gestante:</p>
                    
                    <div class="flex flex-wrap justify-center gap-3">
                        {{-- Botão 1: Ativar Câmara em Tempo Real --}}
                        <button onclick="initScanner()" class="btn-primary-tw font-bold shadow-lg text-xs py-2 px-4">
                            <i class="fas fa-video text-xs"></i>
                            <span>Ativar Vídeo em Tempo Real</span>
                        </button>

                        {{-- Botão 2: Tirar Foto / Carregar Ficheiro (Compatível 100% com HTTP no telemóvel) --}}
                        <label class="btn-secondary-tw bg-white/10 hover:bg-white/20 text-white border-white/20 font-bold text-xs py-2 px-4 cursor-pointer">
                            <i class="fas fa-camera text-xs text-gold-300"></i>
                            <span>Tirar Foto / Carregar Foto QR</span>
                            <input type="file" id="qr-file-input" accept="image/*" capture="environment" class="hidden" onchange="scanQrFile(this)">
                        </label>
                    </div>
                </div>
            </div>

            <div id="scan-status-msg" class="hidden text-xs p-3 rounded-xl bg-brand-50 text-brand-900 border border-brand-200 font-semibold text-center">
                <i class="fas fa-spinner fa-spin mr-1"></i> A processar o QR Code...
            </div>
        </div>

        {{-- LEITURA MANUAL & DICAS --}}
        <div class="space-y-6">
            <div class="card-tw p-6 space-y-4">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2 border-b border-surface-100 pb-3">
                    <i class="fas fa-keyboard text-brand-600"></i> Entrada Manual / NID
                </h3>

                <p class="text-xs text-surface-600">
                    Introduza o NID, BI ou o código ID impresso no cartão da paciente:
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
                        <span>Localizar Ficha Clínica</span>
                    </button>
                </form>
            </div>

            <div class="card-tw p-4 bg-surface-50 border border-surface-200 text-xs space-y-2">
                <h4 class="font-bold text-surface-900 flex items-center gap-1.5">
                    <i class="fas fa-mobile-screen-button text-brand-600"></i> Compatibilidade total
                </h4>
                <p class="text-surface-600 leading-relaxed text-2xs">
                    No telemóvel, prima em <strong>"Tirar Foto / Carregar Foto QR"</strong> para abrir diretamente a câmara nativa do seu dispositivo e fotografar o Cartão da Gestante.
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function processScannedText(decodedText) {
        const statusBox = document.getElementById('scan-status-msg');
        if (statusBox) {
            statusBox.classList.remove('hidden');
            statusBox.innerHTML = '<i class="fas fa-check-circle text-brand-600 mr-1"></i> QR Code validado! A redirecionar para a Ficha...';
        }

        if (decodedText.includes('/patients/')) {
            window.location.href = decodedText;
        } else {
            const cleanId = decodedText.replace(/\D/g, '');
            if (cleanId) {
                window.location.href = '{{ url('/patients') }}/' + cleanId;
            } else {
                alert('Código lido: ' + decodedText);
            }
        }
    }

    function initScanner() {
        document.getElementById('qr-placeholder')?.classList.add('hidden');
        
        if (typeof Html5QrcodeScanner === 'undefined') {
            alert('A carregar biblioteca de leitura...');
            return;
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", 
            { fps: 10, qrbox: { width: 250, height: 250 } }, 
            false
        );

        html5QrcodeScanner.render((decodedText) => {
            html5QrcodeScanner.clear();
            processScannedText(decodedText);
        });
    }

    function scanQrFile(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];

        const statusBox = document.getElementById('scan-status-msg');
        if (statusBox) {
            statusBox.classList.remove('hidden');
            statusBox.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> A analisar foto do QR Code...';
        }

        const html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                processScannedText(decodedText);
            })
            .catch(err => {
                if (statusBox) {
                    statusBox.innerHTML = '<i class="fas fa-circle-exclamation text-crimson-600 mr-1"></i> Não foi possível identificar um QR Code válido na imagem. Tente novamente.';
                }
            });
    }
</script>
@endpush
@endsection
