@extends('layouts.app')

@section('title', 'Gestantes')
@section('page-title', 'Gestão de Gestantes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4>Lista de Gestantes</h4>
        <p class="text-muted">Gerencie o cadastro das gestantes em acompanhamento</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nova Gestante
    </a>
</div>

<!-- NOVO: Filtro de pesquisa -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" 
                       placeholder="Pesquisar por nome, documento ou contacto..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search me-1"></i>Pesquisar
                </button>
                @if(request('search'))
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary ms-1">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome Completo</th>
                            <th>Idade</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th>Semanas</th>
                            <th>Status</th>
                            <th>Próxima Consulta</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $patient->nome_completo }}</strong><br>
                                    <small class="text-muted">
                                        G{{ $patient->numero_gestacoes }}P{{ $patient->numero_partos }}A{{ $patient->numero_abortos }}
                                    </small>
                                </div>
                            </td>
                            <td>{{ $patient->idade }} anos</td>
                            <td>{{ $patient->documento_bi }}</td>
                            <td>{{ $patient->contacto }}</td>
                            <td>
                                {{-- CORRIGIDO: usar o accessor correto --}}
                                @if($patient->idade_gestacional)
                                    <span class="badge bg-info">{{ $patient->idade_gestacional }}ª semana</span><br>
                                    <small class="text-muted">{{ $patient->trimestre }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                {{-- NOVO: Status da gravidez --}}
                                @php
                                    $status = $patient->status_gravidez;
                                    $badgeClass = match($status) {
                                        'Gestante' => 'bg-success',
                                        'A termo' => 'bg-warning',
                                        'Pós-parto' => 'bg-secondary',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            </td>
                            <td>
                                @if($patient->consultations->count() > 0)
                                    @php $proximaConsulta = $patient->consultations->first(); @endphp
                                    {{ $proximaConsulta->data_consulta->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Nenhuma agendada</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('consultations.create', $patient) }}" class="btn btn-sm btn-outline-success" title="Nova Consulta">
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $patients->firstItem() ?? 0 }} a {{ $patients->lastItem() ?? 0 }} 
                    de {{ $patients->total() }} gestantes
                    @if(request('search'))
                        (filtrado por "{{ request('search') }}")
                    @endif
                </div>
                <div>
                    {{ $patients->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-female fa-4x text-muted mb-3"></i>
                @if(request('search'))
                    <h5 class="text-muted">Nenhuma gestante encontrada</h5>
                    <p class="text-muted">
                        Não foram encontradas gestantes com o termo "{{ request('search') }}".
                    </p>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Ver Todas as Gestantes
                    </a>
                @else
                    <h5 class="text-muted">Nenhuma gestante cadastrada</h5>
                    <p class="text-muted">Comece adicionando a primeira gestante ao sistema.</p>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Cadastrar Primeira Gestante
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- NOVO: Script para debug (temporário) --}}
@push('scripts')
<script>
    // Função para debug - temporária
    function debugPatient(patientId) {
        fetch(`/patients/${patientId}/debug`)
            .then(response => response.json())
            .then(data => {
                console.log('Debug da gestante:', data);
                alert('Verificar console para dados de debug');
            });
    }
    
    // Adicionar botão de debug se necessário (remover em produção)
    @if(config('app.debug'))
        document.addEventListener('DOMContentLoaded', function() {
            const actionGroups = document.querySelectorAll('.btn-group');
            actionGroups.forEach((group, index) => {
                const patientId = {{ json_encode($patients->pluck('id')->toArray()) }}[index];
                if (patientId) {
                    const debugBtn = document.createElement('button');
                    debugBtn.className = 'btn btn-sm btn-outline-info';
                    debugBtn.title = 'Debug';
                    debugBtn.innerHTML = '<i class="fas fa-bug"></i>';
                    debugBtn.onclick = () => debugPatient(patientId);
                    group.appendChild(debugBtn);
                }
            });
        });
    @endif
</script>
@endpush
@endsection