<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS_FAMILY . ' (' . php_uname('r') . ')',
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Nginx/Apache',
            'database' => config('database.default'),
            'httpsms_key' => env('HTTPSMS_KEY') ? 'Configurado no .env' : 'Não Configurado',
            'httpsms_from' => env('HTTPSMS_FROM', '+258862134230'),
            'ai_provider' => Setting::get('ai_provider', env('OPENROUTER_API_KEY') ? 'OpenRouter / Gemini' : 'Simulação Local'),
        ];

        // 1. Unidade Sanitária & MISAU
        $unidadeSanitaria = Setting::get('unidade_sanitaria', 'Centro de Saúde Urbano & Maternidade');
        $provincia = Setting::get('provincia', 'Maputo Cidade');
        $distrito = Setting::get('distrito', 'Kamubukwana');
        $codigoMisau = Setting::get('codigo_misau', 'US-0421');
        $telefoneMaternidade = Setting::get('telefone_maternidade', '+258 21 000 000');
        $emailInstitucional = Setting::get('email_institucional', 'maternidade@misau.gov.mz');
        $responsavelSmi = Setting::get('responsavel_smi', 'Dra. Maria Mondlane (Médica Chefe SMI)');

        // 2. Notificações SMS & Gateway
        $httpsmsKey = Setting::get('httpsms_key', env('HTTPSMS_KEY', ''));
        $httpsmsFrom = Setting::get('httpsms_from', env('HTTPSMS_FROM', '+258862134230'));
        $smsEnabled = Setting::get('sms_enabled', '1');
        $smsLembreteDias = Setting::get('sms_lembrete_dias', '2');
        $smsNotificarParceiro = Setting::get('sms_notificar_parceiro', '1');
        $smsTemplateLembrete = Setting::get('sms_template_lembrete', 'Olá {NOME}, lembramos da sua consulta de CPN marcada para {DATA} às {HORA} no {US}. Traga o seu Cartão da Gestante.');
        $smsTemplateFalta = Setting::get('sms_template_falta', 'Olá {NOME}, notamos a sua ausência na consulta de CPN. A sua saúde e a do bebé são prioridade. Por favor, dirija-se ao {US} ou aguarde a visita da nossa activista comunitária.');

        // 3. Inteligência Artificial & Assistente Clínico
        $aiProvider = Setting::get('ai_provider', 'gemini_direct');
        $aiModelName = Setting::get('ai_model_name', 'gemini-2.5-flash');
        $aiTemperature = Setting::get('ai_temperature', '0.2');
        $aiFloatingWidget = Setting::get('ai_floating_widget', '1');
        $aiCustomInstructions = Setting::get('ai_custom_instructions', 'Priorizar sempre os Manuais Clínicos de Saúde Materna e Neonatal do MISAU de Moçambique.');

        // 4. Protocolos Clínicos & Alertas ARO
        $diasParaFaltosa = Setting::get('dias_para_faltosa', '3');
        $semanasAvisoParto = Setting::get('semanas_aviso_parto', '36');
        $limitePaSistolica = Setting::get('limite_pa_sistolica', '140');
        $limitePaDiastolica = Setting::get('limite_pa_diastolica', '90');
        $limiteHbAnemia = Setting::get('limite_hb_anemia', '7.0');
        $autoGerarAlertas = Setting::get('auto_gerar_alertas', '1');

        // 5. Saúde Comunitária & Visitas de Terreno (APEs)
        $visitaDiasReagendamento = Setting::get('visita_dias_reagendamento', '3');
        $autoDispensarVisitaNaUs = Setting::get('auto_dispensar_visita_na_us', '1');
        $notificarActivistaSms = Setting::get('notificar_activista_sms', '1');

        // 6. Logs do Sistema
        $logPath = storage_path('logs/laravel.log');
        $systemLogs = [];

        if (File::exists($logPath)) {
            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lastLines = array_slice($lines, -80);
            $systemLogs = array_reverse($lastLines);
        }

        return view('settings.index', compact(
            'systemInfo', 'systemLogs',
            'unidadeSanitaria', 'provincia', 'distrito', 'codigoMisau', 'telefoneMaternidade', 'emailInstitucional', 'responsavelSmi',
            'httpsmsKey', 'httpsmsFrom', 'smsEnabled', 'smsLembreteDias', 'smsNotificarParceiro', 'smsTemplateLembrete', 'smsTemplateFalta',
            'aiProvider', 'aiModelName', 'aiTemperature', 'aiFloatingWidget', 'aiCustomInstructions',
            'diasParaFaltosa', 'semanasAvisoParto', 'limitePaSistolica', 'limitePaDiastolica', 'limiteHbAnemia', 'autoGerarAlertas',
            'visitaDiasReagendamento', 'autoDispensarVisitaNaUs', 'notificarActivistaSms'
        ));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'unidade_sanitaria' => 'required|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:255',
            'codigo_misau' => 'nullable|string|max:255',
            'telefone_maternidade' => 'nullable|string|max:100',
            'email_institucional' => 'nullable|email|max:255',
            'responsavel_smi' => 'nullable|string|max:255',
        ]);

        Setting::set('unidade_sanitaria', $request->unidade_sanitaria, 'general');
        Setting::set('provincia', $request->provincia ?? '', 'general');
        Setting::set('distrito', $request->distrito ?? '', 'general');
        Setting::set('codigo_misau', $request->codigo_misau ?? '', 'general');
        Setting::set('telefone_maternidade', $request->telefone_maternidade ?? '', 'general');
        Setting::set('email_institucional', $request->email_institucional ?? '', 'general');
        Setting::set('responsavel_smi', $request->responsavel_smi ?? '', 'general');

        return back()->with('success', 'Parâmetros da Unidade Sanitária e MISAU atualizados com sucesso!');
    }

    public function updateSms(Request $request)
    {
        $request->validate([
            'httpsms_key' => 'nullable|string|max:255',
            'httpsms_from' => 'nullable|string|max:50',
            'sms_enabled' => 'nullable|in:0,1',
            'sms_lembrete_dias' => 'nullable|integer|min:1|max:7',
            'sms_notificar_parceiro' => 'nullable|in:0,1',
            'sms_template_lembrete' => 'nullable|string|max:500',
            'sms_template_falta' => 'nullable|string|max:500',
        ]);

        if ($request->filled('httpsms_key')) {
            Setting::set('httpsms_key', $request->httpsms_key, 'sms');
        }
        Setting::set('httpsms_from', $request->httpsms_from ?? '', 'sms');
        Setting::set('sms_enabled', $request->has('sms_enabled') ? '1' : '0', 'sms');
        Setting::set('sms_lembrete_dias', $request->sms_lembrete_dias ?? '2', 'sms');
        Setting::set('sms_notificar_parceiro', $request->has('sms_notificar_parceiro') ? '1' : '0', 'sms');
        Setting::set('sms_template_lembrete', $request->sms_template_lembrete ?? '', 'sms');
        Setting::set('sms_template_falta', $request->sms_template_falta ?? '', 'sms');

        return back()->with('success', 'Definições do Gateway SMS e Modelos de Notificação atualizados!');
    }

    public function updateAi(Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|in:gemini_direct,openrouter',
            'ai_model_name' => 'required|string|max:100',
            'ai_temperature' => 'required|numeric|min:0|max:1',
            'ai_custom_instructions' => 'nullable|string|max:1000',
        ]);

        Setting::set('ai_provider', $request->ai_provider, 'ai');
        Setting::set('ai_model_name', $request->ai_model_name, 'ai');
        Setting::set('ai_temperature', (string)$request->ai_temperature, 'ai');
        Setting::set('ai_floating_widget', $request->has('ai_floating_widget') ? '1' : '0', 'ai');
        Setting::set('ai_custom_instructions', $request->ai_custom_instructions ?? '', 'ai');

        return back()->with('success', 'Configurações do Assistente Clínico IA gravadas com sucesso!');
    }

    public function updateClinical(Request $request)
    {
        $request->validate([
            'dias_para_faltosa' => 'required|integer|min:1|max:30',
            'semanas_aviso_parto' => 'required|integer|min:28|max:42',
            'limite_pa_sistolica' => 'required|integer|min:120|max:200',
            'limite_pa_diastolica' => 'required|integer|min:70|max:140',
            'limite_hb_anemia' => 'required|numeric|min:4|max:12',
        ]);

        Setting::set('dias_para_faltosa', (string)$request->dias_para_faltosa, 'clinical');
        Setting::set('semanas_aviso_parto', (string)$request->semanas_aviso_parto, 'clinical');
        Setting::set('limite_pa_sistolica', (string)$request->limite_pa_sistolica, 'clinical');
        Setting::set('limite_pa_diastolica', (string)$request->limite_pa_diastolica, 'clinical');
        Setting::set('limite_hb_anemia', (string)$request->limite_hb_anemia, 'clinical');
        Setting::set('auto_gerar_alertas', $request->has('auto_gerar_alertas') ? '1' : '0', 'clinical');

        return back()->with('success', 'Protocolos Clínicos & Parâmetros de Alerta ARO atualizados!');
    }

    public function updateCommunity(Request $request)
    {
        $request->validate([
            'visita_dias_reagendamento' => 'required|integer|min:1|max:15',
        ]);

        Setting::set('visita_dias_reagendamento', (string)$request->visita_dias_reagendamento, 'community');
        Setting::set('auto_dispensar_visita_na_us', $request->has('auto_dispensar_visita_na_us') ? '1' : '0', 'community');
        Setting::set('notificar_activista_sms', $request->has('notificar_activista_sms') ? '1' : '0', 'community');

        return back()->with('success', 'Regras de Saúde Comunitária e Busca Ativa APE atualizadas com sucesso!');
    }

    public function updateNotifications(Request $request)
    {
        return $this->updateSms($request);
    }

    public function backupSettings(Request $request)
    {
        $settings = Setting::all();
        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="maternidade_settings_backup_' . date('Ymd_His') . '.json"',
        ]);
    }

    public function systemInfo()
    {
        return $this->index();
    }

    public function clearCache()
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('cache:clear');

            return back()->with('success', 'Todas as caches do sistema (Configurações, Rotas, Vistas e Dados) foram limpas com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao limpar caches: ' . $e->getMessage());
        }
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (File::exists($logPath)) {
                File::put($logPath, '');
            }
            return back()->with('success', 'Ficheiro de logs do sistema limpo com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao limpar logs: ' . $e->getMessage());
        }
    }
}
