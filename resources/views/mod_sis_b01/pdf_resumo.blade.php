<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MOD-SIS-B01-B - Resumo Mensal CPN</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #0f766e; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 14px; margin: 0; color: #0f766e; text-transform: uppercase; }
        .header h2 { font-size: 11px; margin: 3px 0 0 0; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; pt: 10px; font-size: 9px; color: #64748b; text-align: justify; }
    </style>
</head>
<body>

    <div class="header">
        <h1>REPÚBLICA DE MOÇAMBIQUE — MINISTÉRIO DA SAÚDE</h1>
        <h2>RESUMO MENSAL DA UNIDADE SANITÁRIA — CONSULTA PRÉ-NATAL (MOD-SIS-B01-B)</h2>
        <p><strong>Mês de Referência:</strong> {{ $indicadores['mes_ano'] }}</p>
    </div>

    <h3>1. PRIMEIRAS CONSULTAS NO MÊS (1ª CPN)</h3>
    <table>
        <thead>
            <tr>
                <th>Indicadores / Característica</th>
                <th class="text-center" style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total das 1ªs Consultas CPN</td>
                <td class="text-center font-bold">{{ $indicadores['total_primeiras'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas com idade entre 10 a 14 anos</td>
                <td class="text-center">{{ $indicadores['idade_10_14'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas com idade entre 15 a 19 anos (Adolescentes)</td>
                <td class="text-center">{{ $indicadores['idade_15_19'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas com idade entre 20 a 24 anos</td>
                <td class="text-center">{{ $indicadores['idade_20_24'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas com idade ≥ 25 anos</td>
                <td class="text-center">{{ $indicadores['idade_25_plus'] }}</td>
            </tr>
            <tr>
                <td class="font-bold">Captação Precoce (Mulheres com ≤ 12 semanas na 1ª CPN)</td>
                <td class="text-center font-bold">{{ $indicadores['primeiras_precoces_12sem'] }}</td>
            </tr>
        </tbody>
    </table>

    <h3>2. AVALIAÇÃO DE COORTE DE 6 MESES & PROFILAXIAS</h3>
    <table>
        <thead>
            <tr>
                <th>Indicadores de Acompanhamento (Coorte de 6 Meses)</th>
                <th class="text-center" style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total de mulheres grávidas inscritas no período (Total da Coorte)</td>
                <td class="text-center font-bold">{{ $indicadores['total_coorte_6meses'] }}</td>
            </tr>
            <tr>
                <td class="font-bold">Mulheres grávidas que fizeram 4 ou mais consultas CPN</td>
                <td class="text-center font-bold">{{ $indicadores['quatro_ou_mais_consultas'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas que receberam 2 doses de SP (Fansidar Malária)</td>
                <td class="text-center">{{ $indicadores['sp2_doses'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas que receberam 4 ou mais doses de SP (Fansidar)</td>
                <td class="text-center">{{ $indicadores['sp4_doses'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas que receberam Rede Mosquiteira (REMTIL)</td>
                <td class="text-center">{{ $indicadores['remtil_entregue'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas que receberam ≥3 doses de Sal Ferroso + Ácido Fólico</td>
                <td class="text-center">{{ $indicadores['sal_ferroso_3doses'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas com Vacinação VAT Imunizada (VAT2 ou Reforço)</td>
                <td class="text-center">{{ $indicadores['vat_concluido'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas com Sífilis tratadas (Penicilina Benzatínica)</td>
                <td class="text-center">{{ $indicadores['sifilis_tratadas'] }}</td>
            </tr>
            <tr>
                <td>Mulheres grávidas HIV+ em TARV à entrada ou iniciado</td>
                <td class="text-center font-bold">{{ $indicadores['hiv_tarv_entrada'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Documento gerado automaticamente pelo sistema <strong>Maternidade+ (MISAU Moçambique)</strong> em {{ date('d/m/Y \à\s H:i') }}. Formulário em conformidade com as normas operacionais do SIS Nacional.</p>
    </div>

</body>
</html>
