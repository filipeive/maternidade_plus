<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::where('ativo', true)
            ->with(['consultations' => function($q) {
                $q->where('data_consulta', '>', now())
                  ->orderBy('data_consulta')
                  ->limit(1);
            }]);

        // Funcionalidade de pesquisa
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nome_completo', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('documento_bi', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('contacto', 'LIKE', "%{$searchTerm}%");
            });
        }

        $patients = $query->orderBy('nome_completo')->paginate(15);
        
        // Se é uma requisição AJAX (para pesquisa em tempo real)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('patients.partials.patient-list', compact('patients'))->render(),
                'pagination' => $patients->links()->render()
            ]);
        }

        return view('patients.index', compact('patients'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->get('q');
        
        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where('ativo', true)
            ->where(function($query) use ($searchTerm) {
                $query->where('nome_completo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('documento_bi', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('contacto', 'LIKE', "%{$searchTerm}%");
            })
            ->select('id', 'nome_completo', 'documento_bi', 'contacto', 'data_nascimento')
            ->limit(10)
            ->get()
            ->map(function($patient) {
                return [
                    'id' => $patient->id,
                    'nome' => $patient->nome_completo,
                    'documento' => $patient->documento_bi,
                    'contacto' => $patient->contacto,
                    'idade' => Carbon::parse($patient->data_nascimento)->age,
                    'url' => route('patients.show', $patient->id)
                ];
            });

        return response()->json($patients);
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'data_nascimento' => 'required|date|before:today',
            'documento_bi' => 'required|string|unique:patients,documento_bi',
            'contacto' => 'required|string',
            'email' => 'nullable|email',
            'contacto_emergencia' => 'nullable|string',
            'endereco' => 'required|string',
            'tipo_sanguineo' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alergias' => 'nullable|string',
            'historico_medico' => 'nullable|string',
            'data_ultima_menstruacao' => 'nullable|date',
            'numero_gestacoes' => 'required|integer|min:1',
            'numero_partos' => 'required|integer|min:0',
            'numero_abortos' => 'required|integer|min:0'
        ]);

        $patient = Patient::create($validated);
        
        // Calcular data provável do parto se DUM fornecida
        if ($patient->data_ultima_menstruacao) {
            $patient->data_provavel_parto = Carbon::parse($patient->data_ultima_menstruacao)
                ->addDays(280);
            $patient->save();
        }

        return redirect()->route('patients.index')
            ->with('success', 'Gestante cadastrada com sucesso!');
    }

    public function show(Patient $patient)
    {
        $patient->load(['consultations.exams', 'consultations.user']);
        
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'data_nascimento' => 'required|date|before:today',
            'documento_bi' => 'required|string|unique:patients,documento_bi,' . $patient->id,
            'contacto' => 'required|string',
            'email' => 'nullable|email',
            'contacto_emergencia' => 'nullable|string',
            'endereco' => 'required|string',
            'tipo_sanguineo' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alergias' => 'nullable|string',
            'historico_medico' => 'nullable|string',
            'data_ultima_menstruacao' => 'nullable|date',
            'numero_gestacoes' => 'required|integer|min:1',
            'numero_partos' => 'required|integer|min:0',
            'numero_abortos' => 'required|integer|min:0'
        ]);

        $patient->update($validated);
        
        // Recalcular data provável do parto se DUM alterada
        if ($request->data_ultima_menstruacao && 
            $request->data_ultima_menstruacao !== $patient->getOriginal('data_ultima_menstruacao')) {
            $patient->data_provavel_parto = Carbon::parse($request->data_ultima_menstruacao)
                ->addDays(280);
            $patient->save();
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Dados da gestante atualizados com sucesso!');
    }

    public function destroy(Patient $patient)
    {
        // Soft delete - marcar como inativo
        $patient->update(['ativo' => false]);
        
        return redirect()->route('patients.index')
            ->with('success', 'Gestante removida do sistema.');
    }

    public function history(Patient $patient)
    {
        $consultations = $patient->consultations()
            ->with(['user', 'exams'])
            ->orderBy('data_consulta', 'desc')
            ->paginate(10);
            
        return view('patients.history', compact('patient', 'consultations'));
    }
}