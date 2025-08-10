<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttachment;
use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['consultation.patient', 'consultation.user']);
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tipo_exame')) {
            $query->where('tipo_exame', $request->tipo_exame);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_solicitacao', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_solicitacao', '<=', $request->data_fim);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('consultation.patient', function($q) use ($search) {
                $q->where('nome_completo', 'like', '%' . $search . '%')
                  ->orWhere('documento_bi', 'like', '%' . $search . '%');
            });
        }
        
        $exams = $query->orderBy('data_solicitacao', 'desc')->paginate(15);
        
        // Estatísticas para o dashboard
        $stats = [
            'total' => Exam::count(),
            'pendentes' => Exam::where('status', 'solicitado')->count(),
            'realizados' => Exam::where('status', 'realizado')->count(),
            'hoje' => Exam::whereDate('data_solicitacao', today())->count()
        ];
        
        return view('exams.index', compact('exams', 'stats'));
    }

    public function create(Request $request)
    {
        $consultation = null;
        $patient = null;
        
        if ($request->consultation_id) {
            $consultation = Consultation::with('patient')->findOrFail($request->consultation_id);
            $patient = $consultation->patient;
        }
        
        $tiposExames = [
            'hemograma_completo' => 'Hemograma Completo',
            'glicemia_jejum' => 'Glicemia de Jejum',
            'teste_tolerancia_glicose' => 'Teste de Tolerância à Glicose',
            'urina_tipo_1' => 'EAS (Urina Tipo 1)',
            'urocultura' => 'Urocultura',
            'ultrassom_obstetrico' => 'Ultrassom Obstétrico',
            'teste_hiv' => 'Teste HIV',
            'teste_sifilis' => 'Teste de Sífilis',
            'hepatite_b' => 'Hepatite B',
            'toxoplasmose' => 'Toxoplasmose',
            'rubeola' => 'Rubéola',
            'estreptococo_grupo_b' => 'Estreptococo Grupo B',
            'outros' => 'Outros'
        ];
        
        return view('exams.create', compact('consultation', 'patient', 'tiposExames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'tipo_exame' => 'required|string',
            'descricao_exame' => 'nullable|string',
            'data_solicitacao' => 'required|date',
            'observacoes' => 'nullable|string'
        ]);

        $validated['status'] = 'solicitado';
        
        $exam = Exam::create($validated);
        
        return redirect()->route('exams.show', $exam)
            ->with('success', 'Exame solicitado com sucesso!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['consultation.patient', 'consultation.user']);
        
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $exam->load(['consultation.patient']);
        
        $tiposExames = [
            'hemograma_completo' => 'Hemograma Completo',
            'glicemia_jejum' => 'Glicemia de Jejum',
            'teste_tolerancia_glicose' => 'Teste de Tolerância à Glicose',
            'urina_tipo_1' => 'EAS (Urina Tipo 1)',
            'urocultura' => 'Urocultura',
            'ultrassom_obstetrico' => 'Ultrassom Obstétrico',
            'teste_hiv' => 'Teste HIV',
            'teste_sifilis' => 'Teste de Sífilis',
            'hepatite_b' => 'Hepatite B',
            'toxoplasmose' => 'Toxoplasmose',
            'rubeola' => 'Rubéola',
            'estreptococo_grupo_b' => 'Estreptococo Grupo B',
            'outros' => 'Outros'
        ];
        
        return view('exams.edit', compact('exam', 'tiposExames'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'tipo_exame' => 'required|string',
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
            ->with('success', 'Exame removido com sucesso!');
    }

    // Métodos específicos para laboratório
    public function byConsultation(Consultation $consultation)
    {
        $consultation->load(['patient', 'exams']);
        
        return view('exams.by-consultation', compact('consultation'));
    }

    public function resultForm(Exam $exam)
    {
        if ($exam->status === 'realizado') {
            return redirect()->route('exams.show', $exam)
                ->with('info', 'Este exame já possui resultado.');
        }
        
        $exam->load(['consultation.patient']);
        
        return view('exams.result-form', compact('exam'));
    }

    // No método storeResult
    public function storeResult(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'data_realizacao' => 'required|date|after_or_equal:' . $exam->data_solicitacao->format('Y-m-d'),
            'resultado' => 'required|string',
            'observacoes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240' // 10MB max
        ]);

        DB::transaction(function () use ($exam, $validated, $request) {
            $exam->update([
                'data_realizacao' => $validated['data_realizacao'],
                'resultado' => $validated['resultado'],
                'observacoes' => $validated['observacoes'] ?? $exam->observacoes,
                'status' => 'realizado'
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('exam_attachments/' . $exam->id, 'public');
                    
                    $exam->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id()
                    ]);
                }
            }
        });

        return redirect()->route('exams.show', $exam)
            ->with('success', 'Resultado do exame registrado com sucesso!');
    }

    public function pendingResults()
    {
        $exams = Exam::with(['consultation.patient', 'consultation.user'])
            ->where('status', 'solicitado')
            ->orderBy('data_solicitacao')
            ->paginate(20);
            
        return view('exams.pending-results', compact('exams'));
    }

    public function generateReport(Request $request)
    {
        $query = Exam::with(['consultation.patient']);
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_solicitacao', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_solicitacao', '<=', $request->data_fim);
        }
        
        if ($request->filled('tipo_exame')) {
            $query->where('tipo_exame', $request->tipo_exame);
        }
        
        $exams = $query->orderBy('data_solicitacao', 'desc')->get();
        
        return view('exams.report', compact('exams'));
    }
    public function downloadAttachment(ExamAttachment $attachment)
    {   
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $attachment->file_path, 
            $attachment->file_name
        );
    }                       
}