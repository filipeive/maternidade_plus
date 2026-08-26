@extends('layouts.app-tw')

@section('title', 'Detalhes da Gestante')
@section('page-title', $patient->nome_completo)
@section('title-icon', 'fa-person-pregnant')

@section('breadcrumbs')
    <a href="{{ route('patients.index') }}">Gestantes</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active">{{ $patient->nome_completo }}</span>
@endsection

@section('content')
    @php
        $alertasAtivosPaciente = $patient->alertasAtivos()->orderByRaw("CASE nivel WHEN 'alto' THEN 1 WHEN 'medio' THEN 2 WHEN 'baixo' THEN 3 ELSE 4 END")->get();
        $temAlertaAlto = $alertasAtivosPaciente->where('nivel', 'alto')->count() > 0;
    @endphp

    {{-- Alerta Clínico Crítico Banner --}}
    @if($temAlertaAlto)
        <div class="mb-6 bg-crimson-50 border-l-4 border-crimson-500 rounded-r-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-crimson-500 text-white flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-crimson-900 flex items-center gap-2">
                        <span class="badge-danger">Alto Risco</span> Alerta Clínico Crítico Detectado
                    </h5>
                    <p class="text-xs text-crimson-700 mt-0.5">
                        Esta gestante possui sinais de alerta de nível <strong>Alto</strong> ativos que exigem conduta médica imediata.
                    </p>
                </div>
            </div>
            <a href="{{ route('alertas.index', ['search' => $patient->nome_completo]) }}" class="btn-danger-tw btn-sm-tw shrink-0">
                <i class="fas fa-stethoscope text-xs"></i>
                <span>Ver e Tratar Alertas</span>
            </a>
        </div>
    @endif

    {{-- Alertas Ativos Secundários --}}
    @if($alertasAtivosPaciente->count() > 0)
        <div class="card-tw mb-6 border-l-4 {{ $temAlertaAlto ? 'border-l-crimson-500' : 'border-l-gold-500' }}">
            <div class="card-header-tw">
                <h6 class="font-bold text-surface-900 flex items-center gap-2">
                    <i class="fas fa-bell text-xs {{ $temAlertaAlto ? 'text-crimson-500' : 'text-gold-500' }}"></i>
                    Alertas Clínicos Ativos ({{ $alertasAtivosPaciente->count() }})
                </h6>
                <a href="{{ route('alertas.index', ['search' => $patient->nome_completo]) }}" class="btn-secondary-tw btn-sm-tw">
                    Histórico Completo
                </a>
            </div>
            <div class="divide-y divide-surface-100">
                @foreach($alertasAtivosPaciente as $alerta)
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-surface-50/50 transition-colors">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="badge-{{ $alerta->nivel === 'alto' ? 'danger' : ($alerta->nivel === 'medio' ? 'warning' : 'info') }}">
                                    {{ ucfirst($alerta->nivel) }}
                                </span>
                                <strong class="text-surface-900 text-sm">{{ $alerta->tipo_label }}</strong>
                                <span class="text-2xs text-surface-400">({{ $alerta->created_at->format('d/m/Y H:i') }})</span>
                            </div>
                            <p class="text-xs text-surface-600">{{ $alerta->mensagem }}</p>
                        </div>
                        <a href="{{ route('alertas.index', ['search' => $patient->nome_completo]) }}" class="btn-secondary-tw btn-sm-tw shrink-0">
                            Tratar Alerta
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left / Main Content (2 Columns) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informações Pessoais Card --}}
            <div class="card-tw">
                <div class="card-header-tw flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 font-bold text-base flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($patient->nome_completo ?? 'G', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-surface-900">{{ $patient->nome_completo }}</h3>
                            <p class="text-xs text-surface-500">BI: {{ $patient->documento_bi ?? 'N/D' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('patients.index') }}" class="btn-secondary-tw btn-sm-tw">
                            <i class="fas fa-arrow-left text-xs"></i>
                            <span>Voltar</span>
                        </a>
                        @if ($patient->status_atual === 'gestante' && $patient->podeRegistrarParto())
                            <a href="{{ route('births.create', $patient) }}" class="btn-tw bg-gold-500 text-white hover:bg-gold-600 btn-sm-tw">
                                <i class="fas fa-baby text-xs"></i>
                                <span>Registrar Parto</span>
                            </a>
                        @endif
                        <a href="{{ route('consultations.create', $patient) }}" class="btn-primary-tw btn-sm-tw">
                            <i class="fas fa-calendar-plus text-xs"></i>
                            <span>Nova Consulta</span>
                        </a>
                        <a href="{{ route('patients.edit', $patient) }}" class="btn-secondary-tw btn-sm-tw">
                            <i class="fas fa-edit text-xs"></i>
                            <span>Editar</span>
                        </a>
                    </div>
                </div>

                <div class="card-body-tw">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-2">
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Nome:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->nome_completo }}</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Idade:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->idade }} anos</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Data Nascimento:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->data_nascimento?->format('d/m/Y') }}</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Documento (BI):</span>
                                <span class="font-mono text-surface-900">{{ $patient->documento_bi }}</span>
                            </p>
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Contacto:</span>
                                <span class="font-semibold text-surface-900">{{ $patient->contacto }}</span>
                            </p>
                            @if ($patient->email)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Email:</span>
                                    <span class="text-surface-900">{{ $patient->email }}</span>
                                </p>
                            @endif
                            @if ($patient->contacto_emergencia)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Emergência:</span>
                                    <span class="text-surface-900">{{ $patient->contacto_emergencia }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Endereço:</span>
                                <span class="font-semibold text-surface-900 text-right">{{ $patient->endereco }}</span>
                            </p>
                            @if ($patient->tipo_sanguineo)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Tipo Sanguíneo:</span>
                                    <span class="badge-danger font-bold">{{ $patient->tipo_sanguineo }}</span>
                                </p>
                            @endif
                            <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                <span class="text-surface-500 font-medium">Gestações (GPA):</span>
                                <span class="font-bold text-brand-700">G{{ $patient->numero_gestacoes }}P{{ $patient->numero_partos }}A{{ $patient->numero_abortos }}</span>
                            </p>
                            @if ($patient->data_ultima_menstruacao)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">DUM:</span>
                                    <span class="font-semibold text-surface-900">{{ $patient->data_ultima_menstruacao->format('d/m/Y') }}</span>
                                </p>
                            @endif
                            @if ($patient->data_provavel_parto)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Data Provável do Parto:</span>
                                    <span class="font-semibold text-brand-600">{{ $patient->data_provavel_parto->format('d/m/Y') }}</span>
                                </p>
                            @endif
                            @if ($patient->semanas_gestacao)
                                <p class="flex justify-between border-b border-surface-100 pb-1.5">
                                    <span class="text-surface-500 font-medium">Semanas de Gestação:</span>
                                    <span class="badge-info">{{ $patient->semanas_gestacao }}ª semana</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($patient->alergias)
                        <div class="mt-4 bg-gold-50 border border-gold-200 text-gold-900 p-3 rounded-lg flex items-center gap-2 text-xs">
                            <i class="fas fa-exclamation-triangle text-gold-600 text-sm shrink-0"></i>
                            <div>
                                <strong class="font-semibold">Alergias:</strong> {{ $patient->alergias }}
                            </div>
                        </div>
                    @endif

                    @if ($patient->historico_medico)
                        <div class="mt-4 pt-3 border-t border-surface-100">
                            <span class="text-xs font-semibold text-surface-700 block mb-1">Histórico Médico:</span>
                            <p class="text-xs text-surface-600 bg-surface-50 p-2.5 rounded-lg border border-surface-100 leading-relaxed">{{ $patient->historico_medico }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabs: Consultas e Partos (Alpine.js) --}}
            <div class="card-tw overflow-hidden" x-data="{ activeTab: 'consultas' }">
                <div class="border-b border-surface-200 bg-surface-50/50 px-4 pt-3 flex gap-2">
                    <button @click="activeTab = 'consultas'"
                            :class="activeTab === 'consultas' ? 'bg-white border-brand-500 text-brand-700 shadow-sm' : 'text-surface-500 hover:text-surface-800'"
                            class="px-4 py-2 text-xs font-semibold rounded-t-lg border-b-2 transition-all flex items-center gap-2">
                        <i class="fas fa-calendar-check text-xs"></i>
                        <span>Consultas ({{ $patient->consultations->count() }})</span>
                    </button>

                    <button @click="activeTab = 'partos'"
                            :class="activeTab === 'partos' ? 'bg-white border-brand-500 text-brand-700 shadow-sm' : 'text-surface-500 hover:text-surface-800'"
                            class="px-4 py-2 text-xs font-semibold rounded-t-lg border-b-2 transition-all flex items-center gap-2">
                        <i class="fas fa-baby text-xs"></i>
                        <span>Partos</span>
                        @if ($patient->births->count() > 0)
                            <span class="badge-info text-2xs px-1.5 py-0.2">{{ $patient->births->count() }}</span>
                        @endif
                    </button>
                </div>

                <div class="p-5">
                    {{-- Tab: Consultas --}}
                    <div x-show="activeTab === 'consultas'">
                        @if ($patient->consultations->count() > 0)
                            <div class="space-y-4">
                                @foreach ($patient->consultations->sortByDesc('data_consulta') as $consultation)
                                    <div class="p-4 rounded-xl border border-surface-200 hover:border-brand-200 transition-colors bg-white shadow-sm">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-surface-100">
                                            <div class="flex items-center gap-2">
                                                <span class="badge-info">
                                                    <i class="fas fa-calendar-alt text-2xs mr-1"></i>
                                                    {{ $consultation->data_consulta->format('d/m/Y H:i') }}
                                                </span>
                                                <h6 class="font-semibold text-surface-900 text-sm">{{ $consultation->tipo_consulta_label }}</h6>
                                            </div>
                                            <span class="badge-{{ $consultation->status === 'realizada' ? 'success' : 'warning' }}">
                                                {{ ucfirst($consultation->status) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-3 text-xs">
                                            @if ($consultation->semanas_gestacao)
                                                <div>
                                                    <span class="text-surface-400 block">Semanas:</span>
                                                    <span class="font-medium text-surface-800">{{ $consultation->semanas_gestacao }}ª sem</span>
                                                </div>
                                            @endif

                                            @if ($consultation->peso)
                                                <div>
                                                    <span class="text-surface-400 block">Peso:</span>
                                                    <span class="font-medium text-surface-800">{{ $consultation->peso }} kg</span>
                                                </div>
                                            @endif

                                            @if ($consultation->pressao_arterial)
                                                <div>
                                                    <span class="text-surface-400 block">Pressão Arterial:</span>
                                                    <span class="font-medium text-surface-800">{{ $consultation->pressao_arterial }} mmHg</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if ($consultation->observacoes)
                                            <p class="text-xs text-surface-600 bg-surface-50 p-2.5 rounded-lg mb-2">
                                                <strong>Obs:</strong> {{ $consultation->observacoes }}
                                            </p>
                                        @endif

                                        @if ($consultation->exams->count() > 0)
                                            <div class="flex items-center gap-1 flex-wrap mt-2">
                                                <span class="text-2xs font-semibold text-surface-400 uppercase">Exames:</span>
                                                @foreach ($consultation->exams as $exam)
                                                    <span class="badge-neutral text-2xs">{{ $exam->tipo_exame_label }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-2 text-2xs text-surface-400 flex items-center gap-1">
                                            <i class="fas fa-user-md"></i>
                                            <span>{{ $consultation->user->name ?? 'Profissional' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-full bg-surface-100 text-surface-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-calendar-times text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-surface-600">Nenhuma consulta registrada ainda.</p>
                                <a href="{{ route('consultations.create', $patient) }}" class="btn-primary-tw btn-sm-tw mt-3 inline-flex">
                                    <i class="fas fa-calendar-plus text-xs"></i>
                                    <span>Agendar Primeira Consulta</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Tab: Partos --}}
                    <div x-show="activeTab === 'partos'" style="display: none;">
                        @if ($patient->births->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table-tw">
                                    <thead>
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>Tipo</th>
                                            <th>Local</th>
                                            <th>Bebê</th>
                                            <th>Status</th>
                                            <th class="text-right">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($patient->births as $birth)
                                            <tr>
                                                <td class="font-medium">{{ $birth->data_hora_parto->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge-{{ $birth->tipo_parto === 'normal' ? 'success' : 'warning' }}">
                                                        {{ $birth->tipo_parto_formatado }}
                                                    </span>
                                                </td>
                                                <td>{{ $birth->local_parto ?? 'Não informado' }}</td>
                                                <td class="text-xs">
                                                    @if ($birth->sexo_bebe)
                                                        {{ ucfirst($birth->sexo_bebe) }}, {{ $birth->peso_formatado }}, {{ $birth->altura_formatada }}
                                                    @else
                                                        Não informado
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge-{{ $birth->status_bebe === 'vivo_saudavel' ? 'success' : 'danger' }}">
                                                        {{ $birth->status_bebe_formatado }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('births.show', $birth) }}" class="btn-secondary-tw btn-sm-tw">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-full bg-surface-100 text-surface-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-baby-carriage text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-surface-600">Nenhum parto registrado ainda.</p>
                                @if ($patient->status_atual === 'gestante' && $patient->podeRegistrarParto())
                                    <a href="{{ route('births.create', $patient) }}" class="btn-primary-tw btn-sm-tw mt-3 inline-flex">
                                        <i class="fas fa-baby text-xs"></i>
                                        <span>Registrar Parto</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar (1 Column) --}}
        <div class="space-y-6">

            {{-- Card Status Gestação / Pós-Parto --}}
            <div class="card-tw">
                <div class="card-header-tw">
                    <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">
                        {{ $patient->status_atual === 'pos_parto' ? 'Informações do Pós-Parto' : 'Status da Gestação' }}
                    </h6>
                </div>
                <div class="card-body-tw">
                    @if ($patient->status_atual === 'pos_parto')
                        @if ($patient->ultimoParto)
                            <div class="text-center mb-4">
                                <div class="text-4xl font-extrabold text-brand-600">
                                    {{ now()->diffInDays($patient->ultimoParto->data_hora_parto) }}
                                </div>
                                <span class="text-2xs uppercase tracking-wider text-surface-500 font-semibold">dias pós-parto</span>
                            </div>

                            <div class="bg-brand-50 border border-brand-200 text-brand-800 p-2.5 rounded-lg text-xs mb-3">
                                <i class="fas fa-baby mr-1"></i> Parto: {{ $patient->ultimoParto->data_hora_parto->format('d/m/Y') }}
                            </div>

                            @if ($patient->ultimoParto->alta_hospitalar)
                                <p class="text-xs text-surface-600 mb-1">
                                    <strong>Alta hospitalar:</strong> {{ $patient->ultimoParto->alta_hospitalar->format('d/m/Y') }}
                                </p>
                            @endif

                            @if ($patient->ultimoParto->condicoes_pos_parto)
                                <div class="bg-ocean-50 border border-ocean-200 text-ocean-900 p-2.5 rounded-lg text-xs mt-3">
                                    <strong>Condições:</strong> {{ $patient->ultimoParto->condicoes_pos_parto }}
                                </div>
                            @endif
                        @endif
                    @else
                        @if ($patient->semanas_gestacao)
                            <div class="text-center mb-4">
                                <div class="text-4xl font-extrabold text-brand-600">{{ $patient->semanas_gestacao }}</div>
                                <span class="text-2xs uppercase tracking-wider text-surface-500 font-semibold">semanas de gestação</span>
                            </div>

                            @php
                                $trimestre = $patient->semanas_gestacao <= 12 ? 1 : ($patient->semanas_gestacao <= 28 ? 2 : 3);
                                $progresso = min(100, ($patient->semanas_gestacao / 40) * 100);
                            @endphp

                            <div class="mb-4">
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-surface-700">{{ $trimestre }}º Trimestre</span>
                                    <span class="text-brand-600">{{ number_format($progresso, 1) }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-surface-100 overflow-hidden">
                                    <div class="h-full bg-brand-500 rounded-full transition-all duration-500" style="width: {{ $progresso }}%"></div>
                                </div>
                            </div>

                            @if ($patient->data_provavel_parto)
                                @php
                                    $diasRestantes = now()->diffInDays($patient->data_provavel_parto, false);
                                @endphp
                                <div class="p-3 rounded-lg border text-xs font-medium {{ $diasRestantes <= 28 ? 'bg-gold-50 border-gold-200 text-gold-900' : 'bg-ocean-50 border-ocean-200 text-ocean-900' }}">
                                    <i class="fas fa-calendar mr-1"></i>
                                    @if ($diasRestantes > 0)
                                        {{ $diasRestantes }} dias para o parto
                                    @elseif($diasRestantes == 0)
                                        Data provável é hoje!
                                    @else
                                        {{ abs($diasRestantes) }} dias de atraso
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="text-center text-surface-400 py-4">
                                <i class="fas fa-question-circle text-2xl mb-1"></i>
                                <p class="text-xs">Informe a DUM para acompanhar a gestação</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Próximas Consultas --}}
            <div class="card-tw">
                <div class="card-header-tw">
                    <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Próximas Consultas</h6>
                </div>
                <div class="card-body-tw space-y-3">
                    @php
                        $proximasConsultas = $patient->consultations()
                            ->where('data_consulta', '>', now())
                            ->orderBy('data_consulta')
                            ->limit(3)
                            ->get();
                    @endphp

                    @if ($proximasConsultas->count() > 0)
                        @foreach ($proximasConsultas as $consulta)
                            <div class="p-2.5 rounded-lg bg-surface-50 border border-surface-100 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-surface-900">{{ $consulta->data_consulta->format('d/m/Y H:i') }}</p>
                                    <p class="text-2xs text-surface-500">{{ $consulta->tipo_consulta_label }}</p>
                                </div>
                                <span class="badge-info text-2xs">{{ $consulta->status }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-surface-400 text-center py-2">Nenhuma consulta agendada</p>
                        <a href="{{ route('consultations.create', $patient) }}" class="btn-primary-tw btn-sm-tw w-full">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Agendar Consulta</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Nova Gestação (pós-parto) --}}
            @if ($patient->status_atual === 'pos_parto')
                <div class="card-tw">
                    <div class="card-header-tw">
                        <h6 class="font-bold text-surface-900 text-xs uppercase tracking-wider">Nova Gestação</h6>
                    </div>
                    <div class="card-body-tw">
                        <form action="{{ route('births.nova-gestacao', $patient) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="data_ultima_menstruacao" class="label-tw text-xs">Data da Última Menstruação</label>
                                <input type="date" class="input-tw" name="data_ultima_menstruacao" id="data_ultima_menstruacao" required>
                            </div>
                            <button type="submit" class="btn-primary-tw btn-sm-tw w-full">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Registrar Nova Gestação</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
