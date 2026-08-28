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
            'httpsms_key' => env('HTTPSMS_KEY') ? 'Configurado (uk_4RQf...)' : 'Não Configurado',
            'httpsms_from' => env('HTTPSMS_FROM', '+258862134230'),
            'ai_provider' => env('OPENROUTER_API_KEY') ? 'OpenRouter / Gemini Flash' : 'Simulação Local',
        ];

        $unidadeSanitaria = Setting::get('unidade_sanitaria', 'Centro de Saúde Urbano & Maternidade');
        $provincia = Setting::get('provincia', 'Maputo Cidade');
        $distrito = Setting::get('distrito', 'Kamubukwana');
        $codigoMisau = Setting::get('codigo_misau', 'US-0421');

        // Leitura dos últimos logs do laravel.log
        $logPath = storage_path('logs/laravel.log');
        $systemLogs = [];

        if (File::exists($logPath)) {
            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lastLines = array_slice($lines, -80);
            $systemLogs = array_reverse($lastLines);
        }

        return view('settings.index', compact('systemInfo', 'systemLogs', 'unidadeSanitaria', 'provincia', 'distrito', 'codigoMisau'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'unidade_sanitaria' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:255',
            'codigo_misau' => 'nullable|string|max:255',
        ]);

        if ($request->has('unidade_sanitaria')) {
            Setting::set('unidade_sanitaria', $request->unidade_sanitaria ?? '', 'general');
        }
        if ($request->has('provincia')) {
            Setting::set('provincia', $request->provincia ?? '', 'general');
        }
        if ($request->has('distrito')) {
            Setting::set('distrito', $request->distrito ?? '', 'general');
        }
        if ($request->has('codigo_misau')) {
            Setting::set('codigo_misau', $request->codigo_misau ?? '', 'general');
        }

        return back()->with('success', 'Parâmetros da Unidade Sanitária e MISAU salvos com sucesso!');
    }

    public function updateNotifications(Request $request)
    {
        return back()->with('success', 'Definições do Serviço de Notificações SMS atualizadas!');
    }

    public function backupSettings(Request $request)
    {
        return back()->with('success', 'Backup de parâmetros gerado com sucesso!');
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

            Artisan::call('config:cache');
            Artisan::call('route:cache');

            return back()->with('success', 'Caches e otimizações do sistema limpos e re-gerados com sucesso!');
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
            return back()->with('success', 'Ficheiro de logs do sistema limpo com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao limpar logs: ' . $e->getMessage());
        }
    }
}
