@extends('layouts.app')

@section('title', 'Relatório de Exames')
@section('page-title', 'Relatório de Exames')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Relatório</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Relatório de Exames</h5>
            <div>
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <h6 class="text-muted">Filtros Aplicados:</h6>
            <ul class="list-unstyled">
                <li><strong>Período:</strong> 
                    {{ request('data_inicio') ? \Carbon\Carbon::parse(request('data_inicio'))->format('d/m/Y') : 'Início' }} 
                    até 
                    {{ request('data_fim') ? \Carbon\Carbon::parse(request('data_fim'))->format('d/m/Y') : 'Fim' }}
                </li>
                @if(request('tipo_exame'))
                <li><strong>Tipo de Exame:</strong> {{ $tiposExames[request('tipo_exame')] ?? request('tipo_exame') }}</li>
                @endif
            </ul>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Gestante</th>
                        <th>Tipo de Exame</th>
                        <th>Data Solicitação</th>
                        <th>Data Realização</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exam->id }}</td>
                        <td>{{ $exam->consultation->patient->nome_completo }}</td>
                        <td>{{ $tiposExames[$exam->tipo_exame] ?? $exam->tipo_exame }}</td>
                        <td>{{ $exam->data_solicitacao->format('d/m/Y') }}</td>
                        <td>{{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : '--' }}</td>
                        <td>
                            @if($exam->status === 'realizado')
                                <span class="badge bg-success">Realizado</span>
                            @elseif($exam->status === 'solicitado')
                                <span class="badge bg-warning text-dark">Solicitado</span>
                            @else
                                <span class="badge bg-secondary">Pendente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-2x text-warning mb-3"></i>
                            <h5>Nenhum exame encontrado</h5>
                            <p class="text-muted">Nenhum exame corresponde aos filtros aplicados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $exams->where('status', 'solicitado')->count() }}</h3>
                            <p class="text-muted mb-0">Exames Solicitados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $exams->where('status', 'realizado')->count() }}</h3>
                            <p class="text-muted mb-0">Exames Realizados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h3>{{ $exams->count() }}</h3>
                            <p class="text-muted mb-0">Total de Exames</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-muted text-end">
        Relatório gerado em: {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
        }
        .card-header, .card-footer {
            display: none;
        }
        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
@endpush