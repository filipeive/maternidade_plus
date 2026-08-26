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
<div class="max-w-4xl mx-auto space-y-6" x-data="{ manualId: '', cameraMode: 'camera', selectedCameraId: '' }">

    {{-- Banner de Instruções --}}
    <div class="card-tw p-5 bg-gradient-to-r from-brand-700 via-brand-600 to-ocean-700 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center text-gold-300 text-xl font-bold border border-white/20 shrink-0">
                <i class="fas fa-qrcode"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Scanner de Cartão da Gestante</h2>
                <p class="text-xs text-white/70">Aponte a câmara do telemóvel/tablet ou fotografe o QR Code do Cartão para abrir a ficha clínica instantaneamente.</p>
            </div>
        </div>

        <a href="{{ route('patients.index') }}" class="btn-secondary-tw btn-sm-tw shrink-0">
            <i class="fas fa-users text-xs"></i>
            <span>Lista de Gestantes</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- CÂMARA SCANNER CONTAINER --}}
        <div class="md:col-span-2 card-tw p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-camera text-brand-600"></i> Leitor de QR Code
                </h3>
                <span class="badge-success text-2xs">Pronto para Leitura</span>
            </div>

            {{-- Caixa de Diagnóstico HTTP caso o navegador bloqueie o vídeo direto --}}
            <div id="http-warning" class="hidden p-3 rounded-xl bg-gold-50 border border-gold-200 text-gold-900 text-xs flex items-start gap-2.5">
                <i class="fas fa-circle-info text-gold-600 text-sm mt-0.5 shrink-0"></i>
                <p>O seu navegador requer permissão para o fluxo de vídeo contínuo em endereços HTTP. <strong>No telemóvel/tablet, prima no botão amarelo "Tirar Foto ao QR Code" abaixo para abrir a câmara nativa!</strong></p>
            </div>

            {{-- Seletor de Câmaras --}}
            <div id="camera-select-container" class="hidden space-y-1">
                <label class="label-tw">Selecione a Câmara do Dispositivo:</label>
                <select id="camera-select" onchange="switchCamera(this.value)" class="input-tw text-xs font-mono">
                    <option value="">A carregar câmaras disponíveis...</option>
                </select>
            </div>

            {{-- Container do Leitor Óptico --}}
            <div id="scanner-wrapper" class="relative w-full min-h-[280px] bg-surface-900 rounded-2xl overflow-hidden flex flex-col items-center justify-center text-white p-4 border-2 border-brand-500/30">
                
                {{-- Elemento onde o Html5Qrcode injeta a câmara --}}
                <div id="qr-reader" class="w-full h-full min-h-[250px]"></div>
                
                {{-- Placeholder com Ações Rápidas --}}
                <div id="qr-placeholder" class="text-center p-4 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-brand-500/20 text-brand-400 flex items-center justify-center mx-auto text-3xl">
                        <i class="fas fa-qrcode animate-pulse"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">Digitalizar Cartão da Paciente</h4>
                        <p class="text-xs text-surface-300 max-w-xs mx-auto mt-1">Escolha a opção de leitura para abrir a Ficha da Gestante:</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-3 pt-2">
                        {{-- Botão 1: Fotografar com Câmara Nativa (Ideal para Telemóveis/Tablets) --}}
                        <label class="btn-tw bg-gold-400 hover:bg-gold-300 text-surface-900 font-extrabold text-xs py-2.5 px-4 cursor-pointer shadow-lg">
                            <i class="fas fa-camera text-sm mr-1"></i>
                            <span>Tirar Foto ao QR Code</span>
                            <input type="file" id="qr-file-input" accept="image/*" capture="environment" class="hidden" onchange="scanQrFile(this)">
                        </label>

                        {{-- Botão 2: Vídeo Contínuo em Tempo Real --}}
                        <button onclick="initLiveStreamCamera()" class="btn-primary-tw font-bold text-xs py-2.5 px-4">
                            <i class="fas fa-video text-xs"></i>
                            <span>Ativar Vídeo em Tempo Real</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Caixa de Feedback do Scan --}}
            <div id="scan-status-msg" class="hidden text-xs p-3 rounded-xl bg-brand-50 text-brand-900 border border-brand-200 font-semibold text-center">
                <i class="fas fa-spinner fa-spin mr-1"></i> A analisar código...
            </div>
        </div>

        {{-- LEITURA MANUAL & INSTRUÇÕES --}}
        <div class="space-y-6">
            <div class="card-tw p-6 space-y-4">
                <h3 class="text-sm font-bold text-surface-900 flex items-center gap-2 border-b border-surface-100 pb-3">
                    <i class="fas fa-keyboard text-brand-600"></i> Entrada Manual / NID
                </h3>

                <p class="text-xs text-surface-600">
                    Introduza o número de NID, BI ou ID da gestante impresso no cartão:
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

                    <button type="submit" class="btn-primary-tw w-full justify-center text-xs py-2.5">
                        <i class="fas fa-arrow-right text-xs"></i>
                        <span>Localizar Ficha Clínica</span>
                    </button>
                </form>
            </div>

            <div class="card-tw p-4 bg-surface-50 border border-surface-200 text-xs space-y-2">
                <h4 class="font-bold text-surface-900 flex items-center gap-1.5">
                    <i class="fas fa-lightbulb text-gold-500"></i> Dica de Utilização no Telemóvel
                </h4>
                <p class="text-surface-600 leading-relaxed text-2xs">
                    Prima no botão amarelo <strong>"Tirar Foto ao QR Code"</strong>. O telemóvel abrirá automaticamente a câmara para tirar uma fotografia ao Cartão da Paciente e redirecionar para a Ficha Clínica.
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCodeScannerInstance = null;

    function processScannedText(decodedText) {
        const statusBox = document.getElementById('scan-status-msg');
        if (statusBox) {
            statusBox.classList.remove('hidden');
            statusBox.className = 'text-xs p-3 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-200 font-semibold text-center';
            statusBox.innerHTML = '<i class="fas fa-check-circle text-emerald-600 mr-1"></i> QR Code validado! A abrir Ficha Clínica...';
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

    function initLiveStreamCamera() {
        document.getElementById('qr-placeholder')?.classList.add('hidden');
        const statusBox = document.getElementById('scan-status-msg');

        if (typeof Html5Qrcode === 'undefined') {
            alert('A carregar biblioteca de leitura...');
            return;
        }

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 0) {
                const selectContainer = document.getElementById('camera-select-container');
                const select = document.getElementById('camera-select');
                if (selectContainer && select) {
                    selectContainer.classList.remove('hidden');
                    select.innerHTML = '';
                    devices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.text = device.label || `Câmara ${index + 1}`;
                        select.appendChild(option);
                    });
                }

                // Iniciar com a câmara traseira por defeito
                const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment') || d.label.toLowerCase().includes('traseira')) || devices[devices.length - 1];
                startCameraDevice(backCamera.id);
            } else {
                startCameraDevice({ facingMode: "environment" });
            }
        }).catch(err => {
            console.warn("Erro ao listar câmaras:", err);
            document.getElementById('http-warning')?.classList.remove('hidden');
            startCameraDevice({ facingMode: "environment" });
        });
    }

    function startCameraDevice(cameraIdOrConfig) {
        if (html5QrCodeScannerInstance) {
            html5QrCodeScannerInstance.stop().catch(() => {}).then(() => {
                runCamera(cameraIdOrConfig);
            });
        } else {
            runCamera(cameraIdOrConfig);
        }
    }

    function runCamera(cameraIdOrConfig) {
        html5QrCodeScannerInstance = new Html5Qrcode("qr-reader");
        html5QrCodeScannerInstance.start(
            cameraIdOrConfig, 
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                html5QrCodeScannerInstance.stop().then(() => {
                    processScannedText(decodedText);
                });
            },
            (errorMessage) => {
                // Leitura contínua em progresso
            }
        ).catch(err => {
            console.error("Erro ao iniciar câmara:", err);
            document.getElementById('http-warning')?.classList.remove('hidden');
            document.getElementById('qr-placeholder')?.classList.remove('hidden');
        });
    }

    function switchCamera(cameraId) {
        if (cameraId) {
            startCameraDevice(cameraId);
        }
    }

    function scanQrFile(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];

        const statusBox = document.getElementById('scan-status-msg');
        if (statusBox) {
            statusBox.classList.remove('hidden');
            statusBox.className = 'text-xs p-3 rounded-xl bg-brand-50 text-brand-900 border border-brand-200 font-semibold text-center';
            statusBox.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> A analisar fotografia do QR Code...';
        }

        const html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                processScannedText(decodedText);
            })
            .catch(err => {
                if (statusBox) {
                    statusBox.className = 'text-xs p-3 rounded-xl bg-crimson-50 text-crimson-900 border border-crimson-200 font-semibold text-center';
                    statusBox.innerHTML = '<i class="fas fa-circle-exclamation text-crimson-600 mr-1"></i> Não foi possível identificar um QR Code válido na imagem. Certifique-se que o código está nítido e tente novamente.';
                }
            });
    }
</script>
@endpush
@endsection
