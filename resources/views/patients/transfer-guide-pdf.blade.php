<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Guia de Transferência Obstétrica — {{ $patient->nome_completo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; padding: 25px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #0f766e; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #0f766e; margin-bottom: 2px; }
        .header h2 { font-size: 15px; font-weight: bold; color: #042f2e; text-transform: uppercase; margin-bottom: 4px; }
        .header p { font-size: 9px; color: #64748b; }
        .meta-bar { display: table; width: 100%; margin-bottom: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 12px; }
        .meta-col { display: table-cell; vertical-align: middle; }
        .meta-col.right { text-align: right; }
        .guide-num { font-size: 13px; font-weight: bold; color: #15803d; font-family: monospace; }
        .section { margin-bottom: 14px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #0f766e; background: #f8fafc; border-left: 3px solid #0f766e; padding: 4px 8px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.data-table th, table.data-table td { border: 1px solid #e2e8f0; padding: 5px 8px; font-size: 10px; text-align: left; }
        table.data-table th { background: #f1f5f9; color: #475569; font-weight: bold; width: 25%; }
        .grid-2 { display: table; width: 100%; }
        .grid-2-col { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
        .grid-2-col:last-child { padding-right: 0; padding-left: 8px; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 8px; font-weight: bold; border-radius: 4px; text-transform: uppercase; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .box-alert { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px 10px; font-size: 10px; color: #991b1b; margin-bottom: 10px; }
        .box-info { background: #f0fdfa; border: 1px solid #ccfbf1; border-radius: 6px; padding: 8px 10px; font-size: 10px; color: #0f766e; margin-bottom: 10px; }
        .signatures { margin-top: 25px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 50%; text-align: center; padding: 10px 20px; }
        .signature-line { border-top: 1px solid #94a3b8; margin-top: 40px; padding-top: 5px; font-size: 9px; color: #475569; }
        .footer { text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>República de Moçambique · Ministério da Saúde (MISAU)</h1>
        <h2>Guia Oficial de Transferência e Referência Obstétrica</h2>
        <p>Serviço Nacional de Saúde (SNS) · Saúde Materno-Infantil (SMI) · Sistema Maternidade+</p>
    </div>

    <div class="meta-bar">
        <div class="meta-col">
            <span style="font-size: 9px; text-transform: uppercase; color: #64748b;">Nº da Guia Oficial:</span><br>
            <span class="guide-num">{{ $patient->guia_transferencia_numero ?? 'GT-' . date('Ym') . '-' . sprintf('%04d', $patient->id) }}</span>
        </div>
        <div class="meta-col right">
            <span style="font-size: 9px; color: #64748b;">Data de Emissão:</span><br>
            <strong>{{ $patient->data_transferencia ? $patient->data_transferencia->format('d/m/Y \à\s H:i') : now()->format('d/m/Y \à\s H:i') }}</strong>
        </div>
    </div>

    {{-- UNIDADES SANITÁRIAS DE ORIGEM E DESTINO --}}
    <div class="section">
        <div class="section-title">1. Fluxo de Transferência Hospitalar</div>
        <table class="data-table">
            <tr>
                <th>US de Origem (Remetente):</th>
                <td><strong>Centro de Saúde / Maternidade Local</strong></td>
                <th>US de Destino:</th>
                <td><strong style="color: #0f766e; font-size: 11px;">{{ $patient->unidade_sanitaria_destino ?? 'Unidade Sanitária de Referência' }}</strong></td>
            </tr>
            <tr>
                <th>Província / Distrito Destino:</th>
                <td>{{ $patient->provincia_destino ?? 'Província N/D' }} / {{ $patient->distrito_destino ?? 'Distrito N/D' }}</td>
                <th>Tipo de Saída / Encaminhamento:</th>
                <td>{{ $patient->motivo_inativacao_formatado }}</td>
            </tr>
        </table>
    </div>

    {{-- IDENTIFICAÇÃO DA PACIENTE --}}
    <div class="section">
        <div class="section-title">2. Identificação da Gestante / Puérpera</div>
        <table class="data-table">
            <tr>
                <th>Nome Completo:</th>
                <td><strong>{{ $patient->nome_completo }}</strong></td>
                <th>Documento (BI / Cédula):</th>
                <td>{{ $patient->documento_bi ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Idade / Data Nasc.:</th>
                <td>{{ $patient->idade }} anos ({{ $patient->data_nascimento?->format('d/m/Y') ?? 'N/D' }})</td>
                <th>Código PTV / NID:</th>
                <td>{{ $patient->codigo_ptv ?? 'N/D' }}</td>
            </tr>
            <tr>
                <th>Contacto Telefónico:</th>
                <td>{{ $patient->contacto ?? 'Sem telefone' }}</td>
                <th>Parceiro / Acompanhante:</th>
                <td>{{ $patient->parceiro_nome ?? $patient->acompanhante_nome ?? 'N/D' }} ({{ $patient->parceiro_contacto ?? $patient->acompanhante_contacto ?? 'Sem contacto' }})</td>
            </tr>
            <tr>
                <th>Endereço Residencial:</th>
                <td colspan="3">{{ $patient->endereco ?? 'N/D' }} (Bairro: {{ $patient->bairro ?? 'N/D' }}, Distrito: {{ $patient->distrito ?? 'N/D' }})</td>
            </tr>
        </table>
    </div>

    {{-- DADOS OBSTÉTRICOS & MOTIVO DA TRANSFERÊNCIA --}}
    <div class="section">
        <div class="section-title">3. Condições Obstétricas & Motivo Clínico da Transferência</div>
        
        <table class="data-table">
            <tr>
                <th>Idade Gestacional Atual:</th>
                <td>
                    <strong>{{ $patient->idade_gestacional_detalhada ?? ($patient->idade_gestacional ? $patient->idade_gestacional . ' Semanas' : 'Não determinada') }}</strong>
                    (DUM: {{ $patient->data_ultima_menstruacao?->format('d/m/Y') ?? 'N/D' }} | DPP: {{ $patient->data_provavel_parto?->format('d/m/Y') ?? 'N/D' }})
                </td>
                <th>Classificação de Risco (ARO):</th>
                <td>
                    @if($patient->isAltoRisco())
                        <span class="badge badge-danger">Alto Risco Obstétrico (Nível II/III)</span>
                    @else
                        <span class="badge badge-success">Risco Habitual</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Histórico Obstétrico:</th>
                <td>Gesta {{ $patient->numero_gestacoes ?? 1 }}, Para {{ $patient->numero_partos ?? 0 }}, Abortos {{ $patient->numero_abortos ?? 0 }}</td>
                <th>Grupo Sanguíneo / Rh:</th>
                <td><strong>{{ $patient->tipo_sanguineo ?? 'N/D' }}</strong> (Parceiro: {{ $patient->tipo_sanguineo_parceiro ?? 'N/D' }})</td>
            </tr>
            <tr>
                <th>Motivo Clínico da Referência:</th>
                <td colspan="3" style="color: #991b1b; font-weight: bold;">
                    {{ $patient->motivo_transferencia }}
                </td>
            </tr>
            @if($patient->resumo_clinico_transferencia)
                <tr>
                    <th>Resumo da Conduta & Evolução:</th>
                    <td colspan="3" style="white-space: pre-line; background: #fffbeb;">
                        {{ $patient->resumo_clinico_transferencia }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    {{-- HISTÓRICO DE PROFILAXIAS E EXAMES CPN --}}
    <div class="section">
        <div class="section-title">4. Resumo de Rastreios Laboratoriais & Profilaxias Administradas</div>
        <table class="data-table">
            <tr>
                <th>Profilaxia Malária (IPTp-SP):</th>
                <td>{{ $patient->prophylaxis->iptp_doses_count ?? 0 }} doses tomadas</td>
                <th>Vacinação Tétano (TT):</th>
                <td>{{ $patient->vaccines->where('tipo_vacina', 'tetano')->count() }} doses registadas</td>
            </tr>
            <tr>
                <th>Suplementação Ferro/Folato:</th>
                <td>{{ $patient->prophylaxis->ferro_doses_count ?? 0 }} doses registadas</td>
                <th>Desparasitação (Mebendazol):</th>
                <td>{{ $patient->prophylaxis->mebendazol_administrado ? 'Sim (Dose Única)' : 'Pendente' }}</td>
            </tr>
        </table>
    </div>

    {{-- CHECKLIST DE ESTABILIZAÇÃO PARA TRANSPORTE MISAU --}}
    <div class="box-alert">
        <strong>⚠️ CHECKLIST DE SEGURANÇA E TRANSPORTE DE EMERGÊNCIA MISAU:</strong><br>
        1. Acesso venoso periférico calibroso pérvio (com Soro Ringer Lactato / Fisiológico se indicado).<br>
        2. Algaliação com saco coletor de urina para controle rigoroso de diurese (se ARO / pré-eclâmpsia).<br>
        3. Acompanhamento por profissional de saúde de SMI e acompanhante jovem saudável para eventual doação de sangue.
    </div>

    {{-- ASSINATURAS E CARIMBO --}}
    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">
                <strong>{{ $patient->profissionalTransferencia->name ?? auth()->user()->name ?? 'Profissional de Saúde Remetente' }}</strong><br>
                <span>Médico / Enfermeira de SMI · US de Origem</span>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Equipa de Receção da Unidade de Destino</strong><br>
                <span>Data: ____/____/2026 · Hora: ____:____ · Carimbo US</span>
            </div>
        </div>
    </div>

    <div class="footer">
        Documento gerado eletronicamente pelo Sistema Maternidade+ em {{ now()->format('d/m/Y \à\s H:i:s') }} · Ministério da Saúde de Moçambique
    </div>

</body>
</html>
