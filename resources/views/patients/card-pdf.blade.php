<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ficha Pré-Natal (FPN) — {{ $patient->nome_completo }}</title>
    <style>
        @page { margin: 15px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9px; color: #1e293b; margin: 0; padding: 0; background: #fff; line-height: 1.3; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 8px; margin-bottom: 10px; display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .title-moçambique { font-size: 10px; font-weight: bold; color: #0f766e; text-transform: uppercase; }
        .title-doc { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        
        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; background: #f1f5f9; padding: 4px 6px; border-left: 3px solid #0d9488; margin: 8px 0 4px 0; color: #0f766e; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .table-data td, .table-data th { border: 1px solid #cbd5e1; padding: 4px 6px; font-size: 8.5px; }
        .table-data th { background: #f8fafc; font-weight: bold; text-align: left; color: #334155; }
        
        .badge { padding: 1px 4px; border-radius: 3px; font-weight: bold; font-size: 7.5px; text-transform: uppercase; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        
        .qr-box { text-align: center; border: 1px solid #cbd5e1; padding: 4px; border-radius: 4px; display: inline-block; background: #fff; }
        .qr-box img { width: 75px; height: 75px; }
        .footer { font-size: 7.5px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 10px; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="title-moçambique">República de Moçambique — Ministério da Saúde (MISAU)</div>
            <div class="title-doc">FICHA PRÉ-NATAL (FPN) & CARTÃO DA GESTANTE</div>
            <div style="font-size: 8px; color: #64748b;">Unidade Sanitária: Centro de Saúde de Quelimane Urbano · Distrito: {{ $patient->distrito ?? 'Quelimane' }}</div>
        </div>
        <div class="header-right">
            <div class="qr-box">
                <img src="{{ $qrCode }}" alt="QR Code">
                <div style="font-size: 6px; font-weight: bold; color: #0f766e;">ID #{{ str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    {{-- Identificação da Gestante --}}
    <div class="section-title">1. Identificação da Mulher & Contactos</div>
    <table class="table-data">
        <tr>
            <th style="width: 20%;">Nome Completo:</th>
            <td style="width: 40%; font-weight: bold;">{{ $patient->nome_completo }}</td>
            <th style="width: 15%;">BI / NID:</th>
            <td style="width: 25%; font-mono;">{{ $patient->documento_bi ?? 'N/D' }}</td>
        </tr>
        <tr>
            <th>Filiação:</th>
            <td>{{ $patient->filiacao ?? 'Não declarada' }}</td>
            <th>Idade / Nasc.:</th>
            <td>{{ $patient->idade }} anos ({{ $patient->data_nascimento?->format('d/m/Y') }})</td>
        </tr>
        <tr>
            <th>Estado Civil / Trab.:</th>
            <td>{{ ucfirst(str_replace('_', ' ', $patient->estado_civil ?? 'solteira')) }} / {{ $patient->local_trabalho ?? 'Doméstica' }}</td>
            <th>Contacto:</th>
            <td>{{ $patient->contacto }}</td>
        </tr>
        <tr>
            <th>Residência / Bairro:</th>
            <td>{{ $patient->endereco }} ({{ $patient->bairro ?? 'Quelimane' }})</td>
            <th>Ponto Referência:</th>
            <td>{{ $patient->ponto_referencia_residencia ?? '—' }}</td>
        </tr>
    </table>

    {{-- Dados Obstétricos Atuais & Triagem ARO --}}
    <div class="section-title">2. Gravidez Atual & Estratificação de Risco (ARO MISAU)</div>
    @php
        $aro = $patient->estratificacao_aro_misau;
    @endphp
    <table class="table-data">
        <tr>
            <th style="width: 15%;">DUM:</th>
            <td style="width: 18%; font-weight: bold; color: #0f766e;">{{ $patient->data_ultima_menstruacao?->format('d/m/Y') ?? 'N/D' }}</td>
            <th style="width: 15%;">DPP (Naegele):</th>
            <td style="width: 18%; font-weight: bold; color: #be123c;">{{ $patient->data_provavel_parto?->format('d/m/Y') ?? 'N/D' }}</td>
            <th style="width: 15%;">Idade Gestacional:</th>
            <td style="width: 19%; font-weight: bold;">{{ $patient->idade_gestacional_detalhada ?? $patient->semanas_gestacao . 'ª semana' }}</td>
        </tr>
        <tr>
            <th>GPA:</th>
            <td style="font-weight: bold;">G{{ $patient->numero_gestacoes }} P{{ $patient->numero_partos }} A{{ $patient->numero_abortos }}</td>
            <th>Grupo Sang. (Mãe/Parc):</th>
            <td>
                <strong>{{ $patient->tipo_sanguineo ?? 'S/T' }}</strong> / 
                {{ $patient->tipo_sanguineo_parceiro ?? 'N/D' }}
            </td>
            <th>Estratificação ARO:</th>
            <td>
                <span class="badge {{ $aro['nivel'] === 'Nivel_III' ? 'badge-danger' : ($aro['nivel'] === 'Nivel_II' ? 'badge-warning' : 'badge-success') }}">
                    {{ $aro['label'] }}
                </span>
            </td>
        </tr>
        @if(!empty($aro['motivos']))
        <tr>
            <th>Motivos ARO:</th>
            <td colspan="5" style="color: #991b1b; font-size: 8px;">
                {{ implode('; ', $aro['motivos']) }}
            </td>
        </tr>
        @endif
    </table>

    {{-- Antecedentes Obstétricos Gestações Anteriores --}}
    <div class="section-title">3. Histórico de Gestações Anteriores (1ª a 6ª+)</div>
    <table class="table-data">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="width: 5%; text-align: center;">Nº</th>
                <th style="width: 10%;">Ano</th>
                <th style="width: 20%;">Tipo Parto</th>
                <th style="width: 20%;">Local Parto</th>
                <th style="width: 12%; text-align: center;">Prematuro</th>
                <th style="width: 15%; text-align: center;">Desfecho</th>
                <th style="width: 18%;">Peso RN / Obs</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patient->obstetricHistories as $hist)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $hist->numero_gravidez }}ª</td>
                    <td>{{ $hist->ano ?? '—' }}</td>
                    <td>{{ $hist->tipo_parto_label }}</td>
                    <td>{{ $hist->local_parto_label }}</td>
                    <td style="text-align: center;">{{ $hist->prematuro ? 'SIM' : 'NÃO' }}</td>
                    <td style="text-align: center;">
                        @if($hist->nado_morto)
                            <span class="badge badge-danger">Natimorto</span>
                        @elseif($hist->tipo_aborto !== 'nenhum')
                            <span class="badge badge-warning">Aborto</span>
                        @else
                            <span class="badge badge-success">Nato Vivo</span>
                        @endif
                    </td>
                    <td>{{ $hist->peso_rn_gramas ? $hist->peso_rn_gramas . 'g' : '' }} {{ $hist->comentarios }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 6px;">Primigesta ou sem registo de gestações anteriores.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Consultas Pré-Natais Realizadas --}}
    <div class="section-title">4. Evolução das Consultas CPN Realizadas</div>
    <table class="table-data">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="width: 12%;">Data</th>
                <th style="width: 10%; text-align: center;">Sem.</th>
                <th style="width: 12%;">Peso</th>
                <th style="width: 14%;">Tensão Art.</th>
                <th style="width: 12%;">BCF</th>
                <th style="width: 12%;">Alt. Uterina</th>
                <th style="width: 28%;">Conduta / Profissional</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patient->consultations->sortBy('data_consulta') as $c)
                <tr>
                    <td>{{ $c->data_consulta->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ $c->semanas_gestacao ? $c->semanas_gestacao . 'ª' : '—' }}</td>
                    <td>{{ $c->peso ? $c->peso . ' kg' : '—' }}</td>
                    <td style="font-weight: bold;">{{ $c->pressao_arterial ?? '—' }}</td>
                    <td>{{ $c->batimentos_fetais ? $c->batimentos_fetais . ' bpm' : '—' }}</td>
                    <td>{{ $c->altura_uterina ? $c->altura_uterina . ' cm' : '—' }}</td>
                    <td style="font-size: 8px;">{{ $c->user->name ?? 'ESMI' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 6px;">Nenhuma consulta CPN registrada ainda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        FPN emitida digitalmente pelo Sistema Maternidade+ em {{ now()->format('d/m/Y H:i') }} · República de Moçambique · MISAU
    </div>

</body>
</html>
