<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InventariSIS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-gradient-1: #FDD835;
            --bg-gradient-2: #1E88E5;
            --card-bg: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --input-bg: #F5F5F5;
            --input-border: #E0E0E0;
            --logo-bg: #FDD835;
            --system-name-color: #42A5F5;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-hover: rgba(0, 0, 0, 0.15);
        }

        body.dark-mode {
            --bg-gradient-1: #1a1042;
            --bg-gradient-2: #0a0e27;
            --card-bg: #151937;
            --text-primary: #e8eaf6;
            --text-secondary: #b0b5d1;
            --input-bg: #1e2442;
            --input-border: #2d3354;
            --logo-bg: #FDD835;
            --system-name-color: #8a61e6;
            --shadow: rgba(138, 97, 230, 0.2);
            --shadow-hover: rgba(138, 97, 230, 0.3);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-1) 0%, var(--bg-gradient-2) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            transition: background 0.3s ease;
        }

        .dark-mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toggle-btn {
            background: var(--card-bg);
            border: 2px solid var(--input-border);
            width: 60px;
            height: 32px;
            border-radius: 16px;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px var(--shadow);
        }

        .toggle-btn:hover {
            box-shadow: 0 4px 12px var(--shadow-hover);
        }

        .toggle-btn::before {
            content: '☀️';
            position: absolute;
            top: 3px;
            left: 4px;
            width: 22px;
            height: 22px;
            background: #1E88E5;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        body.dark-mode .toggle-btn::before {
            content: '🌙';
            transform: translateX(28px);
            background: #424242;
        }

        .login-container {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 20px 60px var(--shadow), 0 0 0 1px var(--input-border);
            overflow: hidden;
            width: 100%;
            max-width: 950px;
            display: flex;
            min-height: 550px;
            transition: all 0.3s ease;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            background: linear-gradient(180deg, #1E88E5 0%, #1565C0 100%);
            padding: 60px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        body.dark-mode .login-left {
            background: linear-gradient(180deg, #8a61e6 0%, #5e35b1 100%);
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-30%, -30%) scale(1.1); }
        }

        .logo-container {
            background: var(--logo-bg);
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 35px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05) rotate(2deg);
        }

        .logo-container img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            display: block;
        }

        .system-name {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--system-name-color);
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            margin-bottom: 15px;
            text-align: center;
            position: relative;
            z-index: 1;
            letter-spacing: 1px;
        }

        .system-description {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
            margin-top: 10px;
        }

        .login-right {
            padding: 60px 50px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--card-bg);
            transition: background 0.3s ease;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid var(--input-border);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--input-bg);
            color: var(--text-primary);
        }

        .form-group input:focus {
            outline: none;
            border-color: #1E88E5;
            background: var(--card-bg);
            box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.15);
            transform: translateY(-2px);
        }

        .form-group input::placeholder {
            color: var(--text-secondary);
            opacity: 0.6;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .remember-me label {
            color: var(--text-secondary);
            font-size: 0.95rem;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input[type="checkbox"]:checked {
            accent-color: #1E88E5;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
            box-shadow: 0 12px 28px rgba(30, 136, 229, 0.4);
            transform: translateY(-3px);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 0.95rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: #FFEBEE;
            color: #C62828;
            border-left: 4px solid #EF5350;
        }

        body.dark-mode .alert-error {
            background: #3d1f1f;
            color: #ff6b6b;
            border-left: 4px solid #ff6b6b;
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            color: #999;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-left {
                padding: 40px 30px;
            }

            .logo-container {
                padding: 20px;
            }

            .logo-container img {
                width: 80px;
                height: 80px;
            }

            .system-name {
                font-size: 2rem;
            }

            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="dark-mode-toggle">
        <button class="toggle-btn" onclick="toggleDarkMode()" aria-label="Toggle dark mode"></button>
    </div>
    
    <div class="login-container">
        <div class="login-left">
            <div class="logo-container">
                <img src="{{ asset('images/pln-logo.png') }}" alt="PLN Logo">
            </div>
            <h1 class="system-name">InventariSIS</h1>
            <p class="system-description">Sistem Inventaris Internal</p>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk dengan akun Anda</p>
            </div>

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email / Username</label>
                    <input 
                        type="text" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        placeholder="Masukkan email atau username"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Masukkan password"
                    >
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ingat Saya</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    MASUK
                </button>
            </form>
        </div>
    </div>

    <script>
        // Check for saved dark mode preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
        }

        // Add smooth focus animation
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateX(5px)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateX(0)';
            });
        });
    </script>
</body>
</html>
