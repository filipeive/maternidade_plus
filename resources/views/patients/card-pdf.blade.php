<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cartão da Gestante — {{ $patient->nome_completo }}</title>
    <style>
        @page { margin: 10px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; background: #fff; }
        .card { border: 2px solid #0d9488; border-radius: 8px; overflow: hidden; }
        .header { background: #0f766e; color: #fff; padding: 8px 12px; border-bottom: 3px solid #f59e0b; }
        .header h1 { font-size: 10px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .header h2 { font-size: 8px; color: #fde68a; margin: 2px 0 0 0; text-transform: uppercase; }
        .body { padding: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { vertical-align: top; padding: 4px; }
        .patient-name { font-size: 14px; font-weight: bold; color: #0f172a; margin-bottom: 6px; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 6px; border-radius: 4px; margin-bottom: 4px; }
        .label { font-size: 7px; color: #64748b; font-weight: bold; text-transform: uppercase; display: block; }
        .val { font-size: 9px; font-weight: bold; color: #0f172a; }
        .qr-box { text-align: center; border: 1px solid #cbd5e1; padding: 6px; border-radius: 6px; }
        .qr-box img { width: 110px; height: 110px; }
        .footer { background: #f1f5f9; padding: 4px 10px; font-size: 7px; color: #64748b; border-top: 1px solid #e2e8f0; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>REPÚBLICA DE MOÇAMBIQUE — MINISTÉRIO DA SAÚDE</h1>
            <h2>CARTÃO DE IDENTIFICAÇÃO DA GESTANTE (SMI)</h2>
        </div>
        <div class="body">
            <table class="table">
                <tr>
                    <td style="width: 65%;">
                        <div class="patient-name">{{ $patient->nome_completo }}</div>
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <div class="box">
                                        <span class="label">NID / BI</span>
                                        <span class="val">{{ $patient->documento_bi ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td style="width: 50%;">
                                    <div class="box">
                                        <span class="label">Idade / Sangue</span>
                                        <span class="val">{{ $patient->idade }} anos ({{ $patient->tipo_sanguineo ?? 'S/T' }})</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="box">
                                        <span class="label">DUM</span>
                                        <span class="val" style="color: #0f766e;">{{ $patient->data_ultima_menstruacao?->format('d/m/Y') ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="box">
                                        <span class="label">DPP</span>
                                        <span class="val" style="color: #be123c;">{{ $patient->data_provavel_parto?->format('d/m/Y') ?? 'N/A' }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="box" style="margin-top: 4px;">
                            <span class="label">Unidade Sanitária de Referência</span>
                            <span class="val">Centro de Saúde de Quelimane Urbano</span>
                        </div>
                    </td>
                    <td style="width: 35%; text-align: center;">
                        <div class="qr-box">
                            <img src="{{ $qrCode }}" alt="QR Code">
                            <div style="font-size: 7px; font-weight: bold; margin-top: 4px; color: #0f766e;">SCANNER FICHA CLÍNICA</div>
                            <div style="font-size: 6px; color: #94a3b8;">ID: #{{ str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="footer">
            Maternidade+ · Apresente este cartão em todas as consultas de Acompanhamento Pré-Natal
        </div>
    </div>
</body>
</html>
