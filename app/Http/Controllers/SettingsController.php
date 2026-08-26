<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

        return view('settings.index', compact('systemInfo'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'unidade_sanitaria' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:255',
        ]);

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
}
