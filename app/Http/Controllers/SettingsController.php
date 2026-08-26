<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateGeneral(Request $request)
    {
        return back()->with('success', 'Configurações gerais atualizadas com sucesso!');
    }

    public function updateNotifications(Request $request)
    {
        return back()->with('success', 'Preferências de notificação atualizadas!');
    }

    public function systemInfo()
    {
        return view('settings.index');
    }
}
