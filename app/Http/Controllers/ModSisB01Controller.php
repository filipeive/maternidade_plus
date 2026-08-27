<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\MaternalProphylaxis;
use App\Models\AntenatalHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ModSisB01Controller extends Controller
{
    /**
     * Exibe o Livro de Registos da Consulta Pré-Natal (MOD-SIS-B01)
     */
    public function index(Request $request)
    {
        $query = Patient::with(['consultations', 'antenatalHistory', 'prophylaxis', 'ultimoParto'])
            ->where('ativo', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome_completo', 'like', "%{$search}%")
                  ->orWhere('documento_bi', 'like', "%{$search}%")
                  ->orWhere('contacto', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('mod_sis_b01.index', compact('patients'));
    }

    /**
     * Gera o Resumo Mensal da Unidade Sanitária (MOD-SIS-B01-B)
     */
    public function resumoMensal(Request $request)
    {
        $mesAno = $request->input('mes', now()->format('Y-m'));
        $dataInicio = Carbon::parse($mesAno . '-01')->startOfMonth();
        $dataFim = Carbon::parse($mesAno . '-01')->endOfMonth();

        // 1. Primeiras Consultas no Mês
        $primeirasConsultas = Patient::whereBetween('created_at', [$dataInicio, $dataFim])->get();
        $totalPrimeiras = $primeirasConsultas->count();

        $idade10_14 = $primeirasConsultas->filter(fn($p) => $p->idade >= 10 && $p->idade <= 14)->count();
        $idade15_19 = $primeirasConsultas->filter(fn($p) => $p->idade >= 15 && $p->idade <= 19)->count();
        $idade20_24 = $primeirasConsultas->filter(fn($p) => $p->idade >= 20 && $p->idade <= 24)->count();
        $idade25Plus = $primeirasConsultas->filter(fn($p) => $p->idade >= 25)->count();

        // Gestantes inscritas com <= 12 semanas na 1ª CPN
        $primeirasPrecoces = $primeirasConsultas->filter(function($p) {
            return $p->semanas_gestacao && $p->semanas_gestacao <= 12;
        })->count();

        // 2. Coorte de 6 Meses (Mulheres inscritas há 6 meses para avaliação de 4+ consultas e profilaxia concluída)
        $dataCoorteInicio = (clone $dataInicio)->subMonths(6)->startOfMonth();
        $dataCoorteFim = (clone $dataInicio)->subMonths(6)->endOfMonth();

        $coortePacientes = Patient::whereBetween('created_at', [$dataCoorteInicio, $dataCoorteFim])->get();
        $totalCoorte = $coortePacientes->count();

        $quatroOuMaisConsultas = $coortePacientes->filter(function($p) {
            return $p->consultations()->count() >= 4;
        })->count();

        // 3. Indicadores de Nutrição, Malária, HIV, Sífilis, Tétano
        $prophylaxesCoorte = MaternalProphylaxis::whereIn('patient_id', $coortePacientes->pluck('id'))->get();
        
        $sp2Doses = $prophylaxesCoorte->filter(fn($m) => !is_null($m->sp_2_dose))->count();
        $sp4Doses = $prophylaxesCoorte->filter(fn($m) => !is_null($m->sp_4_dose))->count();
        $remtilEntregue = $prophylaxesCoorte->filter(fn($m) => $m->remtil_entregue)->count();
        $salFerroso3Doses = $prophylaxesCoorte->filter(fn($m) => $m->sal_ferroso_folico_3doses)->count();
        $vatConcluido = $prophylaxesCoorte->filter(fn($m) => !is_null($m->vat_2_dose) || !is_null($m->vat_reforco))->count();
        $sifilisTratadas = $prophylaxesCoorte->filter(fn($m) => $m->sifilis_tratamento_mulher)->count();
        $hivTarvEntrada = $prophylaxesCoorte->filter(fn($m) => !is_null($m->tarv_inicio_data) || $m->hiv_status_entrada === 'Positivo')->count();

        $indicadores = [
            'mes_ano' => $dataInicio->translatedFormat('F / Y'),
            'total_primeiras' => $totalPrimeiras,
            'idade_10_14' => $idade10_14,
            'idade_15_19' => $idade15_19,
            'idade_20_24' => $idade20_24,
            'idade_25_plus' => $idade25Plus,
            'primeiras_precoces_12sem' => $primeirasPrecoces,
            'total_coorte_6meses' => $totalCoorte,
            'quatro_ou_mais_consultas' => $quatroOuMaisConsultas,
            'sp2_doses' => $sp2Doses,
            'sp4_doses' => $sp4Doses,
            'remtil_entregue' => $remtilEntregue,
            'sal_ferroso_3doses' => $salFerroso3Doses,
            'vat_concluido' => $vatConcluido,
            'sifilis_tratadas' => $sifilisTratadas,
            'hiv_tarv_entrada' => $hivTarvEntrada,
        ];

        return view('mod_sis_b01.resumo_mensal', compact('indicadores', 'mesAno'));
    }

    /**
     * Exporta o Resumo Mensal MOD-SIS-B01-B em PDF
     */
    public function exportPdf(Request $request)
    {
        $mesAno = $request->input('mes', now()->format('Y-m'));
        $dataInicio = Carbon::parse($mesAno . '-01')->startOfMonth();
        $dataFim = Carbon::parse($mesAno . '-01')->endOfMonth();

        $primeirasConsultas = Patient::whereBetween('created_at', [$dataInicio, $dataFim])->get();
        $totalPrimeiras = $primeirasConsultas->count();

        $dataCoorteInicio = (clone $dataInicio)->subMonths(6)->startOfMonth();
        $dataCoorteFim = (clone $dataInicio)->subMonths(6)->endOfMonth();
        $coortePacientes = Patient::whereBetween('created_at', [$dataCoorteInicio, $dataCoorteFim])->get();
        $totalCoorte = $coortePacientes->count();

        $quatroOuMaisConsultas = $coortePacientes->filter(fn($p) => $p->consultations()->count() >= 4)->count();
        $prophylaxesCoorte = MaternalProphylaxis::whereIn('patient_id', $coortePacientes->pluck('id'))->get();

        $indicadores = [
            'mes_ano' => $dataInicio->translatedFormat('F / Y'),
            'total_primeiras' => $totalPrimeiras,
            'idade_10_14' => $primeirasConsultas->filter(fn($p) => $p->idade >= 10 && $p->idade <= 14)->count(),
            'idade_15_19' => $primeirasConsultas->filter(fn($p) => $p->idade >= 15 && $p->idade <= 19)->count(),
            'idade_20_24' => $primeirasConsultas->filter(fn($p) => $p->idade >= 20 && $p->idade <= 24)->count(),
            'idade_25_plus' => $primeirasConsultas->filter(fn($p) => $p->idade >= 25)->count(),
            'primeiras_precoces_12sem' => $primeirasConsultas->filter(fn($p) => $p->semanas_gestacao && $p->semanas_gestacao <= 12)->count(),
            'total_coorte_6meses' => $totalCoorte,
            'quatro_ou_mais_consultas' => $quatroOuMaisConsultas,
            'sp2_doses' => $prophylaxesCoorte->filter(fn($m) => !is_null($m->sp_2_dose))->count(),
            'sp4_doses' => $prophylaxesCoorte->filter(fn($m) => !is_null($m->sp_4_dose))->count(),
            'remtil_entregue' => $prophylaxesCoorte->filter(fn($m) => $m->remtil_entregue)->count(),
            'sal_ferroso_3doses' => $prophylaxesCoorte->filter(fn($m) => $m->sal_ferroso_folico_3doses)->count(),
            'vat_concluido' => $prophylaxesCoorte->filter(fn($m) => !is_null($m->vat_2_dose) || !is_null($m->vat_reforco))->count(),
            'sifilis_tratadas' => $prophylaxesCoorte->filter(fn($m) => $m->sifilis_tratamento_mulher)->count(),
            'hiv_tarv_entrada' => $prophylaxesCoorte->filter(fn($m) => !is_null($m->tarv_inicio_data) || $m->hiv_status_entrada === 'Positivo')->count(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mod_sis_b01.pdf_resumo', compact('indicadores', 'mesAno'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("MOD-SIS-B01-B_Resumo_Mensal_{$mesAno}.pdf");
    }
}
