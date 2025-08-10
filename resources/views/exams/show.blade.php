{{-- resources/views/exams/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Exame')
@section('page-title', 'Exame: ' . $exam->tipo_exame_label)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informações do Exame</h5>
                    <div class="btn-group">
                        @if ($exam->status === 'solicitado')
                            <a href="{{ route('exams.result-form', $exam) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>Adicionar Resultado
                            </a>
                        @endif
                        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        @if ($exam->resultado)
                            <button class="btn btn-info btn-sm" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Imprimir
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tipo de Exame:</strong> {{ $exam->tipo_exame_label }}</p>
                            @if ($exam->descricao_exame)
                                <p><strong>Descrição:</strong> {{ $exam->descricao_exame }}</p>
                            @endif
                            <p><strong>Data da Solicitação:</strong> {{ $exam->data_solicitacao->format('d/m/Y') }}</p>
                            @if ($exam->data_realizacao)
                                <p><strong>Data da Realização:</strong> {{ $exam->data_realizacao->format('d/m/Y') }}</p>
                            @endif
                            <p><strong>Status:</strong>
                                @php
                                    $statusClass = match ($exam->status) {
                                        'realizado' => 'bg-success',
                                        'solicitado' => 'bg-warning',
                                        'pendente' => 'bg-secondary',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($exam->status) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Solicitado por:</strong> {{ $exam->consultation->user->name }}</p>
                            <p><strong>Data da Consulta:</strong>
                                {{ $exam->consultation->data_consulta->format('d/m/Y H:i') }}</p>
                            <p><strong>Tipo da Consulta:</strong> {{ $exam->consultation->tipo_consulta_label }}</p>
                            @if ($exam->consultation->semanas_gestacao)
                                <p><strong>Semanas de Gestação:</strong> {{ $exam->consultation->semanas_gestacao }}ª</p>
                            @endif
                        </div>
                    </div>

                    @if ($exam->observacoes)
                        <div class="mt-3">
                            <strong>Observações Clínicas:</strong>
                            <p class="text-muted border-start border-3 border-info ps-3 mt-2">{{ $exam->observacoes }}</p>
                        </div>
                    @endif

                    @if ($exam->resultado)
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle me-1"></i>Resultado do Exame</h6>
                                <div class="border rounded p-3 bg-white mt-2">
                                    {!! nl2br(e($exam->resultado)) !!}
                                </div>
                                @if ($exam->data_realizacao)
                                    <small class="text-muted d-block mt-2">
                                        Realizado em {{ $exam->data_realizacao->format('d/m/Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mt-4">
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-1"></i>Aguardando resultado do laboratório
                                @if ($exam->status === 'solicitado')
                                    <a href="{{ route('exams.result-form', $exam) }}" class="btn btn-success btn-sm ms-2">
                                        Adicionar Resultado
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informações da Gestante -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informações da Gestante</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-female fa-3x text-primary"></i>
                        <h6 class="mt-2">{{ $exam->consultation->patient->nome_completo }}</h6>
                    </div>

                    <p class="mb-1"><strong>BI:</strong> {{ $exam->consultation->patient->documento_bi }}</p>
                    <p class="mb-1"><strong>Idade:</strong> {{ $exam->consultation->patient->idade }} anos</p>
                    <p class="mb-1"><strong>Contacto:</strong> {{ $exam->consultation->patient->contacto }}</p>

                    @if ($exam->consultation->patient->tipo_sanguineo)
                        <p class="mb-1"><strong>Tipo Sanguíneo:</strong>
                            {{ $exam->consultation->patient->tipo_sanguineo }}</p>
                    @endif

                    @if ($exam->consultation->patient->semanas_gestacao)
                        <div class="alert alert-info py-2 mt-3">
                            <small>
                                <i class="fas fa-baby me-1"></i>
                                {{ $exam->consultation->patient->semanas_gestacao }}ª semana de gestação
                            </small>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('patients.show', $exam->consultation->patient) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-1"></i>Ver Perfil Completo
                        </a>
                        <a href="{{ route('consultations.show', $exam->consultation) }}"
                            class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-calendar me-1"></i>Ver Consulta
                        </a>
                    </div>
                </div>
            </div>
            <!-- Anexos do Exame -->
            @if ($exam->attachments->isNotEmpty())
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Anexos do Exame</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($exam->attachments as $attachment)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            @if (in_array($attachment->file_type, ['image/jpeg', 'image/png']))
                                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                    class="img-fluid mb-2" style="max-height: 100px;"
                                                    alt="{{ $attachment->file_name }}">
                                            @else
                                                <i class="fas fa-file-alt fa-3x text-muted mb-2"></i>
                                            @endif

                                            <h6 class="text-truncate" title="{{ $attachment->file_name }}">
                                                {{ $attachment->file_name }}
                                            </h6>
                                            <small class="text-muted">
                                                {{ formatFileSize($attachment->file_size) }}
                                            </small>
                                        </div>
                                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                                            <small class="text-muted">
                                                {{ $attachment->created_at->format('d/m/Y H:i') }}
                                            </small>
                                            <div>
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('exams.attachment.download', $attachment) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Histórico de Exames -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Outros Exames da Gestante</h6>
                </div>
                <div class="card-body">
                    @php
                        $outrosExames = \App\Models\Exam::whereHas('consultation', function ($q) use ($exam) {
                            $q->where('patient_id', $exam->consultation->patient_id);
                        })
                            ->where('id', '!=', $exam->id)
                            ->orderBy('data_solicitacao', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if ($outrosExames->count() > 0)
                        @foreach ($outrosExames as $outroExame)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <div>
                                    <small class="fw-bold">{{ $outroExame->tipo_exame_label }}</small><br>
                                    <small class="text-muted">{{ $outroExame->data_solicitacao->format('d/m/Y') }}</small>
                                </div>
                                <div class="d-flex gap-1">
                                    @php
                                        $statusClass = match ($outroExame->status) {
                                            'realizado' => 'bg-success',
                                            'solicitado' => 'bg-warning',
                                            'pendente' => 'bg-secondary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span
                                        class="badge {{ $statusClass }} badge-sm">{{ ucfirst($outroExame->status) }}</span>
                                    <a href="{{ route('exams.show', $outroExame) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center mt-3">
                            <a href="{{ route('exams.index', ['search' => $exam->consultation->patient->nome_completo]) }}"
                                class="btn btn-outline-secondary btn-sm">
                                Ver Todos os Exames
                            </a>
                        </div>
                    @else
                        <p class="text-muted small text-center">Este é o primeiro exame da gestante</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Área de impressão -->
    <div class="d-none" id="print-area">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2>SISTEMA MATERNIDADE+</h2>
            <h3>RESULTADO DE EXAME LABORATORIAL</h3>
            <hr>
        </div>

        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td><strong>Gestante:</strong> {{ $exam->consultation->patient->nome_completo }}</td>
                <td><strong>BI:</strong> {{ $exam->consultation->patient->documento_bi }}</td>
            </tr>
            <tr>
                <td><strong>Idade:</strong> {{ $exam->consultation->patient->idade }} anos</td>
                <td><strong>Data Nascimento:</strong> {{ $exam->consultation->patient->data_nascimento->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td><strong>Tipo de Exame:</strong> {{ $exam->tipo_exame_label }}</td>
                <td><strong>Data Solicitação:</strong> {{ $exam->data_solicitacao->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Solicitado por:</strong> {{ $exam->consultation->user->name }}</td>
                <td><strong>Data Realização:</strong>
                    {{ $exam->data_realizacao ? $exam->data_realizacao->format('d/m/Y') : 'N/A' }}</td>
            </tr>
        </table>

        @if ($exam->resultado)
            <div style="border: 1px solid #ccc; padding: 15px; margin: 20px 0;">
                <h4>RESULTADO:</h4>
                <div style="white-space: pre-line;">{{ $exam->resultado }}</div>
            </div>
        @endif

        <div style="margin-top: 50px; text-align: center;">
            <p>Data de impressão: {{ now()->format('d/m/Y H:i') }}</p>
            <p><small>Sistema Maternidade+ - Hospital {{ config('app.name') }}</small></p>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .d-none {
                display: block !important;
            }
        }
    </style>
@endpush
