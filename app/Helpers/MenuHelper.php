<?php

if (!function_exists('menu_item')) {
    function menu_item($route, $icon, $text, $pattern = null)
    {
        // Se não foi especificado um padrão, usa o nome da rota como padrão
        $pattern = $pattern ?? $route . '.*';
        
        // Verifica se a rota existe
        if (Route::has($route)) {
            return '<a href="' . route($route) . '" class="nav-link text-light ' . (request()->routeIs($pattern) ? 'active bg-primary' : '') . '">
                <i class="fas ' . $icon . ' me-2"></i> ' . $text . '
            </a>';
        }
        
        // Se a rota não existe, retorna um span (link mudo)
        return '<span class="nav-link {$isActive} text-light" style="cursor: default;">
            <i class="fas ' . $icon . ' me-2"></i> ' . $text . '
        </span>';
    }
}
