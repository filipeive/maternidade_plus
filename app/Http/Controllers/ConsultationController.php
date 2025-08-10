<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['patient', 'user'])
            ->orderBy('data_consulta', 'desc')
            ->paginate(15);
        
        return view('consultations.index', compact('consultations'));
    }

    public function create(Patient $patient = null)
    {
        $patients = Patient::where('ativo', true)->orderBy('nome_completo')->get();
        
        return view('consultations.create', compact('patient', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'data_consulta' => 'required|date|after_or_equal:today',
            'tipo_consulta' => 'required|in:1_trimestre,2_trimestre,3_trimestre,pos_parto,emergencia',
            'semanas_gestacao' => 'nullable|integer|min:1|max:42',
            'peso' => 'nullable|numeric|min:30|max:200',
            'pressao_arterial' => 'nullable|string|max:20',
            'batimentos_fetais' => 'nullable|integer|min:110|max:180',
            'altura_uterina' => 'nullable|numeric|min:10|max:50',
            'observacoes' => 'nullable|string',
            'orientacoes' => 'nullable|string',
            'proxima_consulta' => 'nullable|date|after:data_consulta',
            'status' => 'required|in:agendada,confirmada,realizada,cancelada'
        ]);

        $validated['user_id'] = auth()->id();

        $consultation = Consultation::create($validated);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consulta criada com sucesso!');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'user', 'exams']);
        
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        $patients = Patient::where('ativo', true)->orderBy('nome_completo')->get();
        
        return view('consultations.edit', compact('consultation', 'patients'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'data_consulta' => 'required|date',
            'tipo_consulta' => 'required|in:1_trimestre,2_trimestre,3_trimestre,pos_parto,emergencia',
            'semanas_gestacao' => 'nullable|integer|min:1|max:42',
            'peso' => 'nullable|numeric|min:30|max:200',
            'pressao_arterial' => 'nullable|string|max:20',
            'batimentos_fetais' => 'nullable|integer|min:110|max:180',
            'altura_uterina' => 'nullable|numeric|min:10|max:50',
            'observacoes' => 'nullable|string',
            'orientacoes' => 'nullable|string',
            'proxima_consulta' => 'nullable|date|after:data_consulta',
            'status' => 'required|in:agendada,confirmada,realizada,cancelada'
        ]);

        $consultation->update($validated);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consulta atualizada com sucesso!');
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        
        return redirect()->route('consultations.index')
            ->with('success', 'Consulta removida com sucesso.');
    }

    // Método adicional para consultas por paciente
    public function byPatient(Patient $patient)
    {
        $consultations = $patient->consultations()
            ->with(['user', 'exams'])
            ->orderBy('data_consulta', 'desc')
            ->paginate(10);
            
        return view('consultations.by-patient', compact('patient', 'consultations'));
    }

    // Método para confirmar consulta
    public function confirm(Consultation $consultation)
    {
        $consultation->update(['status' => 'confirmada']);
        
        return back()->with('success', 'Consulta confirmada!');
    }

    // Método para marcar como realizada
    public function complete(Consultation $consultation)
    {
        $consultation->update(['status' => 'realizada']);
        
        return back()->with('success', 'Consulta marcada como realizada!');
    }
}