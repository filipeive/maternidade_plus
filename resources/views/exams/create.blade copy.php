@extends('layouts.app')

@section('title', 'Registrar Novo Exame')
@section('page-title', 'Registrar Novo Exame')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('exams.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Consultation ID -->
                <div class="col-md-6 mb-3">
                    <label for="consultation_id" class="form-label">Consulta (Paciente)</label>
                    <select class="form-select @error('consultation_id') is-invalid @enderror" id="consultation_id" name="consultation_id" required>
                        <option value="">Selecione uma consulta</option>
                        @foreach($consultations as $cons)
                            <option value="{{ $cons->id }}" {{ old('consultation_id', $consultation->id ?? '') == $cons->id ? 'selected' : '' }}>
                                {{ $cons->data_consulta->format('d/m/Y') }} - {{ $cons->patient->nome_completo }} ({{ $cons->tipo_consulta_label }})
                            </option>
                        @endforeach
                    </select>
                    @error('consultation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tipo de Exame -->
                <div class="col-md-6 mb-3">
                    <label for="tipo_exame" class="form-label">Tipo de Exame</label>
                    <select class="form-select @error('tipo_exame') is-invalid @enderror" id="tipo_exame" name="tipo_exame" required>
                        <option value="">Selecione o tipo</option>
                        <option value="hemograma_completo" {{ old('tipo_exame') == 'hemograma_completo' ? 'selected' : '' }}>Hemograma Completo</option>
                        <option value="glicemia_jejum" {{ old('tipo_exame') == 'glicemia_jejum' ? 'selected' : '' }}>Glicemia de Jejum</option>
                        <option value="teste_tolerancia_glicose" {{ old('tipo_exame') == 'teste_tolerancia_glicose' ? 'selected' : '' }}>Teste de Tolerância à Glicose</option>
                        <option value="urina_tipo_1" {{ old('tipo_exame') == 'urina_tipo_1' ? 'selected' : '' }}>Urina Tipo 1</option>
                        <option value="urocultura" {{ old('tipo_exame') == 'urocultura' ? 'selected' : '' }}>Urocultura</option>
                        <option value="ultrassom_obstetrico" {{ old('tipo_exame') == 'ultrassom_obstetrico' ? 'selected' : '' }}>Ultrassom Obstétrico</option>
                        <option value="teste_hiv" {{ old('tipo_exame') == 'teste_hiv' ? 'selected' : '' }}>Teste de HIV</option>
                        <option value="teste_sifilis" {{ old('tipo_exame') == 'teste_sifilis' ? 'selected' : '' }}>Teste de Sífilis (VDRL)</option>
                        <option value="hepatite_b" {{ old('tipo_exame') == 'hepatite_b' ? 'selected' : '' }}>Hepatite B (HBsAg)</option>
                        <option value="toxoplasmose" {{ old('tipo_exame') == 'toxoplasmose' ? 'selected' : '' }}>Toxoplasmose</option>
                        <option value="rubeola" {{ old('tipo_exame') == 'rubeola' ? 'selected' : '' }}>Rubéola</option>
                        <option value="estreptococo_grupo_b" {{ old('tipo_exame') == 'estreptococo_grupo_b' ? 'selected' : '' }}>Estreptococo do Grupo B</option>
                        <option value="outros" {{ old('tipo_exame') == 'outros' ? 'selected' : '' }}>Outros</option>
                    </select>
                    @error('tipo_exame')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Descrição (para 'Outros') -->
            <div class="mb-3" id="descricao_exame_div" style="display: {{ old('tipo_exame') == 'outros' ? 'block' : 'none' }};">
                <label for="descricao_exame" class="form-label">Descrição do Exame (se "Outros")</label>
                <input type="text" class="form-control @error('descricao_exame') is-invalid @enderror" id="descricao_exame" name="descricao_exame" value="{{ old('descricao_exame') }}">
                @error('descricao_exame')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <!-- Data da Solicitação -->
                <div class="col-md-4 mb-3">
                    <label for="data_solicitacao" class="form-label">Data da Solicitação</label>
                    <input type="date" class="form-control @error('data_solicitacao') is-invalid @enderror" id="data_solicitacao" name="data_solicitacao" value="{{ old('data_solicitacao', now()->toDateString()) }}" required>
                    @error('data_solicitacao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Data da Realização -->
                <div class="col-md-4 mb-3">
                    <label for="data_realizacao" class="form-label">Data da Realização (Opcional)</label>
                    <input type="date" class="form-control @error('data_realizacao') is-invalid @enderror" id="data_realizacao" name="data_realizacao" value="{{ old('data_realizacao') }}">
                    @error('data_realizacao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="solicitado" {{ old('status', 'solicitado') == 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                        <option value="realizado" {{ old('status') == 'realizado' ? 'selected' : '' }}>Realizado</option>
                        <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Exame</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoExameSelect = document.getElementById('tipo_exame');
        const descricaoDiv = document.getElementById('descricao_exame_div');

        tipoExameSelect.addEventListener('change', function () {
            descricaoDiv.style.display = this.value === 'outros' ? 'block' : 'none';
        });
    });
</script>
@endpush