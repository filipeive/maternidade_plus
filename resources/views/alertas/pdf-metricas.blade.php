<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Maternidade Plus - Relatório de M&E</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            border-bottom: 2px solid #009639;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #009639;
            font-size: 18px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #666666;
            font-size: 10px;
        }
        .meta-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 8px 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .kpi-container {
            width: 100%;
            margin-bottom: 25px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kpi-cell {
            width: 20%;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: center;
        }
        .kpi-label {
            font-size: 8.5px;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            color: #212529;
        }
        .kpi-value.danger { color: #dc3545; }
        .kpi-value.success { color: #198754; }
        .kpi-value.warning { color: #fd7e14; }

        h2 {
            font-size: 13px;
            color: #212529;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #dee2e6;
            padding: 6px 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f3f5;
            font-size: 9.5px;
            text-transform: uppercase;
            color: #495057;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .badge-alto { background-color: #f8d7da; color: #842029; }
        .badge-medio { background-color: #fff3cd; color: #664d03; }
        .badge-baixo { background-color: #cff4fc; color: #055160; }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
            font-size: 8.5px;
            color: #868e96;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Maternidade Plus - Relatório de M&E</h1>
        <p>Módulo de Alerta Precoce & Monitoria de Risco Materno-Fetal — MISAU / FNI 2026</p>
    </div>

    <div class="meta-box">
        <strong>Período de Referência:</strong> 
        @if($dataInicio && $dataFim)
            {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
        @elseif($dataInicio)
            A partir de {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}
        @elseif($dataFim)
            Até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
        @else
            Histórico Completo (Acumulado)
        @endif
        &nbsp;|&nbsp; <strong>Data de Emissão:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

    <!-- Tabela de KPIs Principais -->
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-cell">
                    <span class="kpi-label">Gestantes Seguidas</span>
                    <span class="kpi-value">{{ $totalGestantes }}</span>
                </td>
                <td class="kpi-cell">
                    <span class="kpi-label">Total Alertas Emitidos</span>
                    <span class="kpi-value">{{ $totalAlertas }}</span>
                </td>
                <td class="kpi-cell">
                    <span class="kpi-label">Altos Ativos</span>
                    <span class="kpi-value danger">{{ $alertasAltosAtivos }}</span>
                </td>
                <td class="kpi-cell">
                    <span class="kpi-label">Taxa de Resolução</span>
                    <span class="kpi-value success">{{ $taxaResolucao }}%</span>
                </td>
                <td class="kpi-cell">
                    <span class="kpi-label">Tempo Médio Resposta</span>
                    <span class="kpi-value warning">{{ $tempoMedioResolucao }} <span style="font-size: 10px;">dias</span></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabela 1: Alertas por Nível de Severidade -->
    <h2>1. Distribuição e Resolução por Nível de Severidade</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nível</th>
                <th class="text-center">Alertas Emitidos</th>
                <th class="text-center">Alertas Resolvidos</th>
                <th class="text-end">Taxa de Resolução (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tabelaPorNivel as $item)
                <tr>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(['é', 'É'], 'e', $item['nivel'])) }}">
                            {{ $item['nivel'] }}
                        </span>
                    </td>
                    <td class="text-center">{{ $item['emitidos'] }}</td>
                    <td class="text-center">{{ $item['resolvidos'] }}</td>
                    <td class="text-end"><strong>{{ $item['taxa'] }}%</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabela 2: Alertas por Regra Clínica -->
    <h2>2. Detalhamento por Regra Clínica & Tipo de Alerta</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Regra Clínica / Tipo</th>
                <th class="text-center">Emitidos</th>
                <th class="text-center">Resolvidos</th>
                <th class="text-end">Taxa de Resolução (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tabelaPorTipo as $item)
                <tr>
                    <td><strong>{{ $item['tipo'] }}</strong></td>
                    <td class="text-center">{{ $item['emitidos'] }}</td>
                    <td class="text-center">{{ $item['resolvidos'] }}</td>
                    <td class="text-end"><strong>{{ $item['taxa'] }}%</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #888;">Nenhum alerta registrado para os parâmetros selecionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Maternidade Plus 🇲🇿 &copy; {{ date('Y') }} — Sistema Integrado de Saúde Materno-Infantil | Relatório Gerado Automaticamente
    </div>

</body>
</html>
