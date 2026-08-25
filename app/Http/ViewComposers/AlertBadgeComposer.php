<?php

namespace App\Http\ViewComposers;

use App\Models\Alerta;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AlertBadgeComposer
{
    /**
     * Bind data to the view.
     *
     * @param mixed $view
     */
    public function compose($view): void
    {
        $count = 0;
        try {
            if (Schema::hasTable('alertas')) {
                $count = Alerta::where('nivel', Alerta::NIVEL_ALTO)
                    ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
                    ->count();
            }
        } catch (\Throwable $e) {
            $count = 0;
        }

        if (is_object($view) && method_exists($view, 'with')) {
            $view->with('alertasAltosCount', $count);
        }
    }
}
