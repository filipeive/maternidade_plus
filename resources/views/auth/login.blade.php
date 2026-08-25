<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maternidade+ | Sistema de Acompanhamento Pré-Natal - Moçambique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, 
                #009639 0%,    /* Verde da bandeira */
                #FFD700 30%,   /* Amarelo da bandeira */
                #DC143C 60%,   /* Vermelho da bandeira */
                #000000 100%   /* Preto da bandeira */
            );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Padrão de fundo inspirado nos tecidos moçambicanos */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="capulana" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="2" fill="rgba(255,255,255,0.1)"/><rect x="20" y="20" width="10" height="10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(%23capulana)"/></svg>');
            animation: movePattern 20s ease-in-out infinite;
        }

        @keyframes movePattern {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-10px, -10px); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            min-height: 650px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Seção visual com elementos de Moçambique */
        .visual-section {
            background: linear-gradient(135deg, 
                rgba(0, 150, 57, 0.95) 0%,    /* Verde */
                rgba(255, 215, 0, 0.95) 50%,  /* Amarelo */
                rgba(220, 20, 60, 0.95) 100%  /* Vermelho */
            );
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .visual-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><defs><pattern id="moz-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M20,5 L35,20 L20,35 L5,20 Z" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/><circle cx="20" cy="20" r="3" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23moz-pattern)"/></svg>');
            animation: rotate 30s linear infinite;
            opacity: 0.3;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .moz-emblem {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
            animation: pulse 3s ease-in-out infinite;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
        }

        .system-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
        }

        .system-subtitle {
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 25px;
            opacity: 0.95;
            max-width: 350px;
            line-height: 1.5;
        }

        .moz-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .moz-info h4 {
            font-size: 1rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .moz-info p {
            font-size: 0.85rem;
            line-height: 1.4;
            opacity: 0.9;
        }

        /* Seção do formulário */
        .form-section {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h2 {
            color: #2d3436;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #636e72;
            font-size: 0.95rem;
        }

        /* Logo móvel - oculto por padrão */
        .mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 30px;
            animation: fadeIn 0.8s ease-in-out;
        }

        .mobile-logo .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00b894, #00a085);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 30px rgba(0, 184, 148, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        .mobile-logo h1 {
            color: #2d3436;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .mobile-logo p {
            color: #636e72;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #2d3436;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            font-family: 'Poppins', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #00b894;
            box-shadow: 0 0 0 4px rgba(0, 184, 148, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .form-input::placeholder {
            color: #a0a0a0;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            accent-color: #00b894;
            cursor: pointer;
        }

        .checkbox-label {
            color: #636e72;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }

        .login-button {
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 184, 148, 0.3);
        }

        .login-button:active {
            transform: translateY(-1px);
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .forgot-password {
            text-align: center;
            margin-top: 25px;
        }

        .forgot-password a {
            color: #00b894;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .forgot-password a:hover {
            color: #00a085;
        }

        .error-message, .success-message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
            color: #c0392b;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        .success-message {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(22, 160, 133, 0.1));
            color: #16a085;
            border: 1px solid rgba(39, 174, 96, 0.2);
        }

        /* RESPONSIVIDADE MELHORADA */
        
        /* Tablet Portrait */
        @media (max-width: 1024px) {
            .login-container {
                max-width: 800px;
            }
            
            .visual-section {
                padding: 50px 30px;
            }
            
            .form-section {
                padding: 50px 40px;
            }
        }

        /* Tablet Landscape */
        @media (max-width: 900px) {
            .login-container {
                grid-template-columns: 1fr 1fr;
                max-width: 700px;
            }
            
            .system-title {
                font-size: 1.9rem;
            }
            
            .moz-emblem {
                width: 100px;
                height: 100px;
            }
            
            .moz-emblem::before {
                font-size: 40px;
            }
        }

        /* Ponto de quebra principal - Mobile */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                align-items: stretch;
                min-height: 100vh;
            }
            
            .login-container {
                grid-template-columns: 1fr;
                max-width: none;
                width: 100%;
                min-height: 100vh;
                max-height: none;
                border-radius: 0;
                animation: none;
                box-shadow: none;
                background: white;
            }
            
            /* Ocultar completamente a seção visual em mobile */
            .visual-section {
                display: none;
            }
            
            /* Mostrar logo móvel */
            .mobile-logo {
                display: block;
            }
            
            .form-section {
                padding: 40px 25px;
                min-height: 100vh;
                justify-content: flex-start;
                padding-top: 60px;
            }
            
            .form-header {
                margin-bottom: 30px;
            }
            
            .form-header h2 {
                font-size: 1.6rem;
            }
            
            .form-input {
                padding: 14px 18px;
                font-size: 16px; /* Evita zoom no iOS */
            }
            
            .login-button {
                padding: 18px 20px;
                font-size: 1.1rem;
            }
        }

        /* Mobile pequeno */
        @media (max-width: 480px) {
            body {
                padding: 0;
            }
            
            .form-section {
                padding: 30px 20px;
                padding-top: 50px;
            }
            
            .mobile-logo h1 {
                font-size: 1.6rem;
            }
            
            .form-header h2 {
                font-size: 1.4rem;
            }
            
            .form-input {
                padding: 12px 16px;
            }
            
            .login-button {
                padding: 16px 20px;
                font-size: 1rem;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .checkbox-group {
                margin-bottom: 25px;
            }
        }

        /* Mobile extra pequeno */
        @media (max-width: 360px) {
            .form-section {
                padding: 25px 15px;
                padding-top: 40px;
            }
            
            .mobile-logo {
                margin-bottom: 25px;
            }
            
            .mobile-logo .logo-icon {
                width: 70px;
                height: 70px;
            }
            
            .mobile-logo .logo-icon::before {
                font-size: 28px;
            }
        }

        /* Orientação landscape em mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .form-section {
                padding-top: 30px;
                justify-content: center;
            }
            
            .mobile-logo {
                margin-bottom: 20px;
            }
            
            .mobile-logo .logo-icon {
                width: 60px;
                height: 60px;
            }
            
            .mobile-logo h1 {
                font-size: 1.4rem;
            }
            
            .form-header {
                margin-bottom: 25px;
            }
            
            .form-group {
                margin-bottom: 18px;
            }
        }

        /* Animação de carregamento */
        .loading {
            opacity: 0.8;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Melhorias de acessibilidade */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .form-section {
                background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
            }
            
            .form-header h2 {
                color: #ffffff;
            }
            
            .form-header p {
                color: #cccccc;
            }
            
            .form-input {
                background: rgba(255, 255, 255, 0.1);
                border-color: #444;
                color: #fff;
            }
            
            .form-label {
                color: #ffffff;
            }
            
            .checkbox-label {
                color: #cccccc;
            }
            
            @media (max-width: 768px) {
                .login-container {
                    background: #1a1a1a;
                }
                
                .mobile-logo h1 {
                    color: #ffffff;
                }
                
                .mobile-logo p {
                    color: #cccccc;
                }
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Seção Visual com elementos de Moçambique - Oculta em mobile -->
        <div class="visual-section">
            <div class="moz-emblem">
                <i class="fas fa-baby-carriage text-white" style="font-size: 52px; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));"></i>
            </div>
            <h1 class="system-title">Maternidade<span style="color: #FFD700;">+</span></h1>
            <p class="system-subtitle">Sistema Integrado de Acompanhamento Pré-Natal para Moçambique</p>
            
            <div class="moz-info">
                <h4><i class="fas fa-hospital-user me-2 text-warning"></i>Cuidado Integral</h4>
                <p>Apoiando o MISAU na meta de 8 contactos pré-natais, com foco na redução da mortalidade materna e perinatal em Moçambique.</p>
            </div>
        </div>

        <!-- Seção do Formulário -->
        <div class="form-section">
            <!-- Logo móvel - aparece apenas em dispositivos móveis -->
            <div class="mobile-logo">
                <div class="logo-icon">
                    <i class="fas fa-baby-carriage text-white" style="font-size: 32px; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));"></i>
                </div>
                <h1>Maternidade<span style="color: #00b894;">+</span></h1>
                <p>Sistema de Acompanhamento Pré-Natal</p>
            </div>

            <div class="form-header">
                <h2>Acesso ao Sistema</h2>
                <p>Entre com suas credenciais para continuar</p>
            </div>

            <!-- Mensagens de Status -->
            <div id="status-messages">
                @if (session('status'))
                    <div class="success-message">
                        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <form id="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label"><i class="fas fa-envelope text-success me-2"></i>Endereço de Email</label>
                    <input 
                        id="email" 
                        class="form-input" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="seu.email@saude.gov.mz"
                    />
                </div>

                <!-- Senha -->
                <div class="form-group">
                    <label for="password" class="form-label"><i class="fas fa-lock text-success me-2"></i>Palavra-passe</label>
                    <input 
                        id="password" 
                        class="form-input"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••••••"
                    />
                </div>

                <!-- Lembrar de mim -->
                <div class="checkbox-group">
                    <input id="remember_me" type="checkbox" class="checkbox-input" name="remember">
                    <label for="remember_me" class="checkbox-label">Manter-me conectado</label>
                </div>

                <!-- Botão de Login -->
                <button type="submit" class="login-button" id="login-btn">
                    <span><i class="fas fa-sign-in-alt me-2"></i>Entrar no Sistema</span>
                </button>
            </form>

            <!-- Link para recuperação de senha -->
            <div class="forgot-password">
                <a href="#" onclick="showForgotPassword()">Esqueceu a sua palavra-passe?</a>
            </div>
        </div>
    </div>

    <script>
        function showMessage(type, message) {
            const container = document.getElementById('status-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            messageDiv.innerHTML = `
                <i class="fas ${iconClass}"></i>
                <span>${message}</span>
            `;
            container.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateY(-10px)';
                setTimeout(() => messageDiv.remove(), 500);
            }, 5000);
        }

        // Animação de carregamento no envio real do formulário
        document.getElementById('login-form').addEventListener('submit', function() {
            const button = document.getElementById('login-btn');
            button.innerHTML = '<span><i class="fas fa-spinner fa-spin me-2"></i>A processar...</span>';
            button.classList.add('loading');
        });

        // Função para esqueci a senha
        function showForgotPassword() {
            showMessage('success', 'Link de recuperação será enviado para o seu email em breve.');
        }

        // Animação suave nos inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Detecção de orientação para melhor UX em mobile
        function handleOrientationChange() {
            if (window.innerWidth <= 768) {
                const formSection = document.querySelector('.form-section');
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
                
                setTimeout(() => {
                    window.scrollTo(0, 0);
                }, 100);
            }
        }

        window.addEventListener('orientationchange', handleOrientationChange);
        window.addEventListener('resize', handleOrientationChange);

        // Inicializar efeitos
        document.addEventListener('DOMContentLoaded', function() {
            handleOrientationChange();
            
            // Focus no primeiro campo em desktop
            if (window.innerWidth > 768) {
                document.getElementById('email').focus();
            }
            
            // Exemplo de como mostrar mensagens do Laravel
            // @if (session('status'))
            //     showMessage('success', '{{ session('status') }}');
            // @endif
            
            // @if ($errors->any())
            //     showMessage('error', '@foreach ($errors->all() as $error){{ $error }} @endforeach');
            // @endif
        });
    </script>
</body>
</html>