<?php

/* if (!function_exists('menu_item')) {
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
} */

if (!function_exists('menu_item')) {
    /**
     * Gerar item de menu para sidebar
     *
     * @param string $route Nome da rota
     * @param string $icon Classe do ícone FontAwesome
     * @param string $text Texto do menu
     * @param string $activePattern Padrão para detectar menu ativo
     * @return string HTML do item de menu
     */
    function menu_item($route, $icon, $text, $activePattern = null)
    {
        $activePattern = $activePattern ?: $route;
        $isActive = request()->routeIs($activePattern . '*');
        $activeClass = $isActive ? 'active' : '';
        
        // Verificar se a rota existe
        if (!Route::has($route)) {
            return '<a href="#" class="nav-link ' . $activeClass . '">
                        <i class="fas ' . $icon . '"></i>
                        ' . $text . '
                        <small class="text-warning ms-1">(Em desenvolvimento)</small>
                    </a>';
        }
        
        $url = route($route);
        
        return '<a href="' . $url . '" class="nav-link ' . $activeClass . '">
                    <i class="fas ' . $icon . '"></i>
                    ' . $text . '
                </a>';
    }
}

if (!function_exists('format_date_pt')) {
    /**
     * Formatar data em português moçambicano
     *
     * @param mixed $date Data a ser formatada
     * @param string $format Formato da data
     * @return string Data formatada
     */
    function format_date_pt($date, $format = 'complete')
    {
        if (!$date) return '';
        
        $carbon = \Carbon\Carbon::parse($date)->locale('pt');
        
        switch ($format) {
            case 'complete':
                return $carbon->isoFormat('dddd, D [de] MMMM [de] YYYY');
            case 'short':
                return $carbon->isoFormat('D/MM/YYYY');
            case 'month_year':
                return $carbon->isoFormat('MMMM [de] YYYY');
            case 'day_month':
                return $carbon->isoFormat('D [de] MMMM');
            case 'time':
                return $carbon->isoFormat('HH:mm');
            case 'datetime':
                return $carbon->isoFormat('D/MM/YYYY [às] HH:mm');
            default:
                return $carbon->isoFormat($format);
        }
    }
}

if (!function_exists('pregnancy_weeks')) {
    /**
     * Calcular semanas de gestação
     *
     * @param string $lastMenstrualPeriod Data da última menstruação
     * @return int|null Semanas de gestação
     */
    function pregnancy_weeks($lastMenstrualPeriod)
    {
        if (!$lastMenstrualPeriod) return null;
        
        $lmp = \Carbon\Carbon::parse($lastMenstrualPeriod);
        $weeks = $lmp->diffInWeeks(now());
        
        return $weeks > 42 ? null : $weeks;
    }
}

/* if (!function_calls('trimester')) {
    /**
     * Determinar trimestre da gestação
     *
     * @param int $weeks Semanas de gestação
     * @return string|null Trimestre
     *//*
    function trimester($weeks)
    {
        if (!$weeks) return null;
        
        if ($weeks <= 12) return '1º trimestre';
        if ($weeks <= 28) return '2º trimestre';
        return '3º trimestre';
    }
} */

if (!function_exists('due_date')) {
    /**
     * Calcular data provável do parto
     *
     * @param string $lastMenstrualPeriod Data da última menstruação
     * @return \Carbon\Carbon|null Data provável do parto
     */
    function due_date($lastMenstrualPeriod)
    {
        if (!$lastMenstrualPeriod) return null;
        
        return \Carbon\Carbon::parse($lastMenstrualPeriod)->addDays(280);
    }
}

if (!function_exists('age_from_birthdate')) {
    /**
     * Calcular idade a partir da data de nascimento
     *
     * @param string $birthdate Data de nascimento
     * @return int Idade em anos
     */
    function age_from_birthdate($birthdate)
    {
        if (!$birthdate) return 0;
        
        return \Carbon\Carbon::parse($birthdate)->age;
    }
}

if (!function_exists('vaccine_type_label')) {
    /**
     * Obter label da vacina em português
     *
     * @param string $type Tipo da vacina
     * @return string Label da vacina
     */
    function vaccine_type_label($type)
    {
        $types = [
            'tetanica' => 'Antitetânica (dT)',
            'hepatite_b' => 'Hepatite B',
            'influenza' => 'Influenza (Gripe)',
            'covid19' => 'COVID-19',
            'febre_amarela' => 'Febre Amarela',
            'iptp' => 'Prevenção Malária (IPTp)',
        ];
        
        return $types[$type] ?? $type;
    }
}

if (!function_exists('exam_type_label')) {
    /**
     * Obter label do exame em português
     *
     * @param string $type Tipo do exame
     * @return string Label do exame
     */
    function exam_type_label($type)
    {
        $types = [
            'hemograma_completo' => 'Hemograma Completo',
            'glicemia_jejum' => 'Glicemia em Jejum',
            'teste_tolerancia_glicose' => 'Teste de Tolerância à Glicose',
            'urina_tipo_1' => 'Urina Tipo I',
            'urocultura' => 'Urocultura',
            'ultrassom_obstetrico' => 'Ultrassom Obstétrico',
            'teste_hiv' => 'Teste HIV',
            'teste_sifilis' => 'Teste Sífilis',
            'hepatite_b' => 'Hepatite B',
            'toxoplasmose' => 'Toxoplasmose',
            'rubéola' => 'Rubéola',
            'estreptococo_grupo_b' => 'Estreptococo Grupo B',
            'outros' => 'Outros'
        ];
        
        return $types[$type] ?? $type;
    }
}

if (!function_exists('risk_level_badge')) {
    /**
     * Gerar badge HTML para nível de risco
     *
     * @param string $level Nível de risco
     * @return string HTML do badge
     */
    function risk_level_badge($level)
    {
        $classes = [
            'Baixo' => 'bg-success',
            'Moderado' => 'bg-warning',
            'Alto' => 'bg-danger'
        ];
        
        $class = $classes[$level] ?? 'bg-secondary';
        
        return '<span class="badge ' . $class . '">' . $level . '</span>';
    }
}

if (!function_exists('status_badge')) {
    /**
     * Gerar badge HTML para status
     *
     * @param string $status Status
     * @param array $customColors Cores customizadas
     * @return string HTML do badge
     */
    function status_badge($status, $customColors = [])
    {
        $defaultColors = [
            'agendada' => 'bg-primary',
            'confirmada' => 'bg-info',
            'realizada' => 'bg-success',
            'cancelada' => 'bg-danger',
            'pendente' => 'bg-warning',
            'solicitado' => 'bg-secondary',
            'administrada' => 'bg-success',
            'vencida' => 'bg-danger',
            'reagenda' => 'bg-warning',
            'nao_encontrada' => 'bg-secondary'
        ];
        
        $colors = array_merge($defaultColors, $customColors);
        $class = $colors[$status] ?? 'bg-secondary';
        
        return '<span class="badge ' . $class . '">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('blood_type_badge')) {
    /**
     * Gerar badge HTML para tipo sanguíneo
     *
     * @param string $bloodType Tipo sanguíneo
     * @return string HTML do badge
     */
    function blood_type_badge($bloodType)
    {
        if (!$bloodType) return '<span class="badge bg-secondary">N/A</span>';
        
        $colors = [
            'A+' => 'bg-success',
            'A-' => 'bg-info',
            'B+' => 'bg-warning',
            'B-' => 'bg-primary',
            'AB+' => 'bg-danger',
            'AB-' => 'bg-dark',
            'O+' => 'bg-success',
            'O-' => 'bg-danger'
        ];
        
        $class = $colors[$bloodType] ?? 'bg-secondary';
        
        return '<span class="badge ' . $class . '">' . $bloodType . '</span>';
    }
}

if (!function_exists('format_phone')) {
    /**
     * Formatar número de telefone moçambicano
     *
     * @param string $phone Número de telefone
     * @return string Telefone formatado
     */
    function format_phone($phone)
    {
        if (!$phone) return '';
        
        // Remover caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Formatar para padrão moçambicano (+258 XX XXX XXXX)
        if (strlen($phone) === 9 && !str_starts_with($phone, '258')) {
            $phone = '258' . $phone;
        }
        
        if (strlen($phone) === 12 && str_starts_with($phone, '258')) {
            return '+258 ' . substr($phone, 3, 2) . ' ' . substr($phone, 5, 3) . ' ' . substr($phone, 8, 4);
        }
        
        return $phone;
    }
}

if (!function_exists('mozambique_provinces')) {
    /**
     * Obter lista das províncias de Moçambique
     *
     * @return array Lista das províncias
     */
    function mozambique_provinces()
    {
        return [
            'maputo_cidade' => 'Maputo Cidade',
            'maputo_provincia' => 'Maputo Província',
            'gaza' => 'Gaza',
            'inhambane' => 'Inhambane',
            'sofala' => 'Sofala',
            'manica' => 'Manica',
            'tete' => 'Tete',
            'zambézia' => 'Zambézia',
            'nampula' => 'Nampula',
            'cabo_delgado' => 'Cabo Delgado',
            'niassa' => 'Niassa'
        ];
    }
}

if (!function_exists('notification_icon')) {
    /**
     * Obter ícone para tipo de notificação
     *
     * @param string $type Tipo de notificação
     * @return string Classe do ícone
     */
    function notification_icon($type)
    {
        $icons = [
            'consultation' => 'fa-calendar-check',
            'exam' => 'fa-microscope',
            'vaccine' => 'fa-syringe',
            'visit' => 'fa-home-heart',
            'alert' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle',
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'error' => 'fa-times-circle'
        ];
        
        return 'fas ' . ($icons[$type] ?? 'fa-bell');
    }
}

if (!function_exists('generate_patient_code')) {
    /**
     * Gerar código único para gestante
     *
     * @param int $patientId ID da gestante
     * @return string Código único
     */
    function generate_patient_code($patientId)
    {
        $year = date('Y');
        $code = 'MAT' . $year . str_pad($patientId, 6, '0', STR_PAD_LEFT);
        
        return $code;
    }
}

if (!function_exists('mask_document')) {
    /**
     * Mascarar documento de identidade (para privacidade)
     *
     * @param string $document Documento
     * @return string Documento mascarado
     */
    function mask_document($document)
    {
        if (strlen($document) <= 4) return $document;
        
        $visible = substr($document, 0, 3) . str_repeat('*', strlen($document) - 6) . substr($document, -3);
        return $visible;
    }
}

if (!function_exists('emergency_level_color')) {
    /**
     * Obter cor para nível de emergência
     *
     * @param string $level Nível de emergência
     * @return string Classe CSS da cor
     */
    function emergency_level_color($level)
    {
        $colors = [
            'baixa' => 'text-success',
            'media' => 'text-warning', 
            'alta' => 'text-danger',
            'critica' => 'text-danger'
        ];
        
        return $colors[$level] ?? 'text-secondary';
    }
}
