<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Auditoria Clínica de Gestantes & Alertas Precoces</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 11pt;
            margin: 0;
            color: #0f766e;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 9pt;
            margin: 2px 0 0 0;
            color: #334155;
            font-weight: normal;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            margin-bottom: 6px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th {
            background-color: #f1f5f9;
            color: #0f766e;
            font-size: 7pt;
            text-transform: uppercase;
            padding: 4px 5px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        td {
            padding: 4px 5px;
            border: 1px solid #e2e8f0;
            font-size: 7.5pt;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
        }
        .badge-alto { background-color: #fee2e2; color: #991b1b; }
        .badge-medio { background-color: #fef3c7; color: #92400e; }
        .badge-normal { background-color: #d1fae5; color: #065f46; }
        .footer {
            margin-top: 10px;
            font-size: 7pt;
            text-align: right;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>REPÚBLICA DE MOÇAMBIQUE · MINISTÉRIO DA SAÚDE</h1>
    <h2>MAPA DE AUDITORIA CLÍNICA DE GESTANTES & TRIAGEM DE RISCO PRECOCE</h2>
    <h2>{{ \App\Models\Setting::get('unidade_sanitaria', 'Centro de Saúde Urbano') }} · {{ \App\Models\Setting::get('provincia', 'Maputo Cidade') }}</h2>
</div>

<div class="meta-info">
    <span>Data de Emissão: {{ $dataGeracao }}</span>
    <span>Total de Gestantes Ativas Avaliadas: {{ $gestantes->count() }}</span>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 20px;">#</th>
            <th>Nome da Gestante</th>
            <th>BI / NID</th>
            <th>Contacto / Bairro</th>
            <th>IG</th>
            <th>Última CPN</th>
            <th>PA / BCF</th>
            <th>Classificação Risco</th>
            <th>Alertas Precoces Ativos</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gestantes as $idx => $p)
            @php
                $ultimaConsulta = $p->consultations->first();
                $idadeGestacional = $p->getIdadeGestacionalSemanas() ?? ($p->semanas_gestacao ?? 0);
                $paStr = $ultimaConsulta->pressao_arterial ?? 'N/D';
                $bcf = $ultimaConsulta->batimentos_fetais ?? 'N/D';
                $alertas = $p->alertas;
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td><strong>{{ $p->nome_completo }}</strong></td>
                <td>{{ $p->documento_bi ?? 'N/D' }}</td>
                <td>{{ $p->contacto ?? 'S/ Contacto' }} ({{ $p->bairro ?? 'N/D' }})</td>
                <td>{{ $idadeGestacional }} sem</td>
                <td>{{ $ultimaConsulta ? \Carbon\Carbon::parse($ultimaConsulta->data_consulta)->format('d/m/Y') : 'Sem consulta' }}</td>
                <td>PA: {{ $paStr }} | BCF: {{ $bcf }}</td>
                <td>
                    @if($p->risco_gestacional === 'Alto' || $p->isAltoRisco())
                        <span class="badge badge-alto">ALTO RISCO (ARO)</span>
                    @else
                        <span class="badge badge-normal">Habitual</span>
                    @endif
                </td>
                <td>
                    @if($alertas->count() > 0)
                        @foreach($alertas as $a)
                            <span class="badge {{ $a->nivel === 'alto' ? 'badge-alto' : 'badge-medio' }}">{{ $a->tipo_formatado ?? $a->tipo }}</span>
                        @endforeach
                    @else
                        <span class="badge badge-normal">Normal</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Maternidade+ Moçambique · Sistema Integrado de Saúde Materno-Infantil · Página 1
</div>

</body>
</html>
