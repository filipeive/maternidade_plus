<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['consultation.patient', 'consultation.user'])
            ->orderBy('data_solicitacao', 'desc')
            ->paginate(15);
        
        return view('exams.index', compact('exams'));
    }

    public function create(Request $request)
    {
        $consultation_id = $request->consultation_id;
        $consultation = null;
        
        if ($consultation_id) {
            $consultation = Consultation::with('patient')->findOrFail($consultation_id);
        }
        
        $consultations = Consultation::with('patient')
            ->whereIn('status', ['agendada', 'confirmada', 'realizada'])
            ->orderBy('data_consulta', 'desc')
            ->get();
        
        return view('exams.create', compact('consultation', 'consultations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'tipo_exame' => 'required|in:hemograma_completo,glicemia_jejum,teste_tolerancia_glicose,urina_tipo_1,urocultura,ultrassom_obstetrico,teste_hiv,teste_sifilis,hepatite_b,toxoplasmose,rubeola,estreptococo_grupo_b,outros',
            'descricao_exame' => 'nullable|string',
            'data_solicitacao' => 'required|date',
            'data_realizacao' => 'nullable|date|after_or_equal:data_solicitacao',
            'resultado' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:solicitado,realizado,pendente'
        ]);

        $exam = Exam::create($validated);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exame registrado com sucesso!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['consultation.patient', 'consultation.user']);
        
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $consultations = Consultation::with('patient')
            ->whereIn('status', ['agendada', 'confirmada', 'realizada'])
            ->orderBy('data_consulta', 'desc')
            ->get();
        
        return view('exams.edit', compact('exam', 'consultations'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'tipo_exame' => 'required|in:hemograma_completo,glicemia_jejum,teste_tolerancia_glicose,urina_tipo_1,urocultura,ultrassom_obstetrico,teste_hiv,teste_sifilis,hepatite_b,toxoplasmose,rubeola,estreptococo_grupo_b,outros',
            'descricao_exame' => 'nullable|string',
            'data_solicitacao' => 'required|date',
            'data_realizacao' => 'nullable|date|after_or_equal:data_solicitacao',
            'resultado' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:solicitado,realizado,pendente'
        ]);

        $exam->update($validated);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exame atualizado com sucesso!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        
        return redirect()->route('exams.index')
            ->with('success', 'Exame removido com sucesso.');
    }

    // Método para exames por consulta
    public function byConsultation(Consultation $consultation)
    {
        $consultation->load(['patient', 'user']);
        $exams = $consultation->exams()->orderBy('data_solicitacao', 'desc')->paginate(10);
        
        return view('exams.by-consultation', compact('consultation', 'exams'));
    }

    // Método para marcar exame como realizado
    public function markAsCompleted(Exam $exam)
    {
        $exam->update([
            'status' => 'realizado',
            'data_realizacao' => now()
        ]);
        
        return back()->with('success', 'Exame marcado como realizado!');
    }

    // Método para adicionar resultado
    public function addResult(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'resultado' => 'required|string',
            'observacoes' => 'nullable|string',
            'data_realizacao' => 'required|date'
        ]);

        $validated['status'] = 'realizado';
        $exam->update($validated);

        return back()->with('success', 'Resultado do exame adicionado com sucesso!');
    }

    // Relatório de exames pendentes
    public function pending()
    {
        $exams = Exam::with(['consultation.patient', 'consultation.user'])
            ->where('status', 'solicitado')
            ->orderBy('data_solicitacao')
            ->paginate(15);
        
        return view('exams.pending', compact('exams'));
    }
}