@extends('layouts.app')

@section('title', 'Alertas Precoces')
@section('page-title', 'Módulo de Alerta Precoce')
@section('title-icon', 'fa-exclamation-triangle')

@section('content')
<div class="container-fluid px-0">
    <!-- Header e Ações -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-bell me-2 text-danger"></i>Painel de Alertas Clínicos
            </h4>
            <p class="text-muted mb-0">Monitoria e triagem de sinais de risco materno-fetal baseada em evidência.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('alertas.metricas') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-1"></i>Métricas de Impacto (M&E)
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Cards de Resumo Estatístico -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-start border-danger border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3 text-danger">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Alertas Altos Ativos</span>
                        <h3 class="fw-bold mb-0 text-danger">{{ $estatisticas['altos_ativos'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-start border-warning border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="fas fa-bell fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Ativos / Pendentes</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $estatisticas['total_ativos'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Em Seguimento</span>
                        <h3 class="fw-bold mb-0 text-primary">{{ $estatisticas['em_seguimento'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-start border-success border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Resolvidos</span>
                        <h3 class="fw-bold mb-0 text-success">{{ $estatisticas['resolvidos'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Filtros -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-filter me-2 text-primary"></i>Filtros de Pesquisa
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('alertas.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Pesquisar Paciente / BI</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nome ou BI..."
                               value="{{ request('search') ?? request('paciente') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Nível de Severidade</label>
                    <select name="nivel" class="form-select">
                        <option value="">Todos os Níveis</option>
                        <option value="alto" {{ request('nivel') === 'alto' ? 'selected' : '' }}>Alto</option>
                        <option value="medio" {{ request('nivel') === 'medio' ? 'selected' : '' }}>Médio</option>
                        <option value="baixo" {{ request('nivel') === 'baixo' ? 'selected' : '' }}>Baixo</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Status do Alerta</label>
                    <select name="status" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="em_seguimento" {{ request('status') === 'em_seguimento' ? 'selected' : '' }}>Em Seguimento</option>
                        <option value="resolvido" {{ request('status') === 'resolvido' ? 'selected' : '' }}>Resolvido</option>
                        <option value="ignorado" {{ request('status') === 'ignorado' ? 'selected' : '' }}>Ignorado</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Regra Clínica / Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todas as Regras</option>
                        <option value="pressao_arterial_grave" {{ request('tipo') === 'pressao_arterial_grave' ? 'selected' : '' }}>PA Grave (>= 160/110)</option>
                        <option value="pressao_arterial_alta" {{ request('tipo') === 'pressao_arterial_alta' ? 'selected' : '' }}>PA Elevada (>= 140/90)</option>
                        <option value="bcf_anormal" {{ request('tipo') === 'bcf_anormal' ? 'selected' : '' }}>BCF Anormal (<110 ou >160)</option>
                        <option value="gestante_faltosa" {{ request('tipo') === 'gestante_faltosa' ? 'selected' : '' }}>Gestante Faltosa</option>
                        <option value="alto_risco_sem_seguimento" {{ request('tipo') === 'alto_risco_sem_seguimento' ? 'selected' : '' }}>Alto Risco Sem Seguimento</option>
                        <option value="vacinas_em_atraso" {{ request('tipo') === 'vacinas_em_atraso' ? 'selected' : '' }}>Vacinas em Atraso</option>
                        <option value="exames_criticos" {{ request('tipo') === 'exames_criticos' ? 'selected' : '' }}>Exames Críticos (HIV/Anemia)</option>
                        <option value="ganho_peso_anormal" {{ request('tipo') === 'ganho_peso_anormal' ? 'selected' : '' }}>Ganho/Perda de Peso</option>
                        <option value="pos_termo" {{ request('tipo') === 'pos_termo' ? 'selected' : '' }}>Gestação Pós-Termo (>41 sem)</option>
                        <option value="sangramento_reportado" {{ request('tipo') === 'sangramento_reportado' ? 'selected' : '' }}>Sangramento Reportado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Alertas -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list me-2 text-primary"></i>Lista de Alertas Clínicos ({{ $alertas->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if($alertas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Severidade</th>
                                <th>Gestante</th>
                                <th>Tipo de Alerta</th>
                                <th>Mensagem / Detalhes</th>
                                <th>Data Emissão</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertas as $alerta)
                                <tr>
                                    <td>
                                        @if($alerta->nivel === 'alto')
                                            <span class="badge bg-danger text-white px-2 py-1">
                                                <i class="fas fa-bolt me-1"></i>Alto
                                            </span>
                                        @elseif($alerta->nivel === 'medio')
                                            <span class="badge bg-warning text-dark px-2 py-1">
                                                <i class="fas fa-exclamation me-1"></i>Médio
                                            </span>
                                        @else
                                            <span class="badge bg-info text-dark px-2 py-1">
                                                <i class="fas fa-info-circle me-1"></i>Baixo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alerta->patient)
                                            <div>
                                                <a href="{{ route('patients.show', $alerta->patient) }}" class="fw-bold text-primary text-decoration-none">
                                                    {{ $alerta->patient->nome_completo }}
                                                </a>
                                                <div class="small text-muted">BI: {{ $alerta->patient->documento_bi ?? 'N/D' }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">Paciente N/D</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $alerta->tipo_label }}</span>
                                    </td>
                                    <td style="max-width: 320px;">
                                        <div class="text-dark small">{{ $alerta->mensagem }}</div>
                                        @if($alerta->nota_resolucao)
                                            <div class="small text-muted mt-1 bg-light p-1 rounded">
                                                <i class="fas fa-comment-medical me-1"></i>{{ $alerta->nota_resolucao }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small text-dark">{{ $alerta->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $alerta->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        @if($alerta->status === 'ativo')
                                            <span class="badge bg-danger">Ativo</span>
                                        @elseif($alerta->status === 'em_seguimento')
                                            <span class="badge bg-warning text-dark">Em Seguimento</span>
                                        @elseif($alerta->status === 'resolvido')
                                            <span class="badge bg-success">Resolvido</span>
                                        @else
                                            <span class="badge bg-secondary">Ignorado</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalResolver{{ $alerta->id }}">
                                            <i class="fas fa-stethoscope me-1"></i>Tratar
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal de Resolução / Transição -->
                                <div class="modal fade" id="modalResolver{{ $alerta->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" action="{{ route('alertas.transitar', $alerta) }}">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-notes-medical me-2"></i>Tratar Alerta Clínico
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3 p-3 bg-light rounded border">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <strong>{{ $alerta->patient->nome_completo ?? 'Paciente' }}</strong>
                                                            <span class="badge bg-{{ $alerta->nivel_cor }}">{{ $alerta->nivel_label }}</span>
                                                        </div>
                                                        <p class="mb-0 text-muted small">{{ $alerta->mensagem }}</p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Novo Status <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="em_seguimento" {{ $alerta->status === 'em_seguimento' ? 'selected' : '' }}>
                                                                Em Seguimento (Contacto realizado / Consulta marcada)
                                                            </option>
                                                            <option value="resolvido" {{ $alerta->status === 'resolvido' ? 'selected' : '' }}>
                                                                Resolvido (Conduta executada / Sinais normalizados)
                                                            </option>
                                                            <option value="ignorado" {{ $alerta->status === 'ignorado' ? 'selected' : '' }}>
                                                                Ignorado (Falso positivo verificado)
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nota Clínica de Resolução / Conduta <span class="text-danger">*</span></label>
                                                        <textarea name="nota" class="form-control" rows="4" required maxlength="1000"
                                                                  placeholder="Descreva detalhadamente a conduta tomada, medicação prescrita ou motivo do encerramento...">{{ old('nota', $alerta->nota_resolucao) }}</textarea>
                                                        <small class="text-muted">Obrigatório para fins de rastreabilidade e auditoria clínica.</small>
                                                    </div>

                                                    @if($alerta->acoes->count() > 0)
                                                        <hr>
                                                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Histórico de Transições</h6>
                                                        <div class="small">
                                                            @foreach($alerta->acoes as $acao)
                                                                <div class="mb-2 pb-1 border-bottom">
                                                                    <div class="d-flex justify-content-between text-muted">
                                                                        <span><strong>{{ $acao->user->name ?? 'Sistema' }}</strong>: {{ $acao->de_status ?? $acao->status_anterior ?? 'ativo' }} &rarr; {{ $acao->para_status ?? $acao->status_novo }}</span>
                                                                        <span>{{ $acao->created_at->format('d/m/Y H:i') }}</span>
                                                                    </div>
                                                                    @if($acao->nota)
                                                                        <div class="text-dark">{{ $acao->nota }}</div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i>Registar Ação
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $alertas->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold">Nenhum alerta encontrado</h5>
                    <p class="text-muted">Não existem alertas clínicos correspondentes aos filtros selecionados.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
