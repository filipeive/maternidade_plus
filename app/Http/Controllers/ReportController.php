<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Birth;
use App\Models\Vaccine;
use App\Models\HomeVisit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $stats = [
            'total_gestantes' => Patient::where('ativo', true)->count(),
            'novas_gestantes_mes' => Patient::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'consultas_realizadas_mes' => Consultation::where('status', 'realizada')->whereYear('data_consulta', $year)->whereMonth('data_consulta', $month)->count(),
            'exames_realizados_mes' => Exam::where('status', 'realizado')->whereYear('updated_at', $year)->whereMonth('updated_at', $month)->count(),
            'partos_mes' => Birth::whereYear('data_hora_parto', $year)->whereMonth('data_hora_parto', $month)->count(),
            'vacinas_administradas_mes' => Vaccine::where('status', 'administrada')->whereYear('data_administracao', $year)->whereMonth('data_administracao', $month)->count(),
            'visitas_realizadas_mes' => HomeVisit::where('status', 'realizada')->whereYear('data_visita', $year)->whereMonth('data_visita', $month)->count(),
        ];

        return view('reports.index', compact('stats', 'year', 'month'));
    }
}
