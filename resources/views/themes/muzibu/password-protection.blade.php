<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Muzibu - Yapım Aşamasında</title>
    <link rel="icon" type="image/x-icon" href="/favicon-fallback.ico">
    <link rel="shortcut icon" href="/favicon-fallback.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1DB954 0%, #121212 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 80px;
            color: #1DB954;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 32px;
            color: #121212;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .password-form {
            margin-top: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        input[type="password"] {
            width: 100%;
            padding: 18px 50px 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s;
            outline: none;
        }

        input[type="password"]:focus {
            border-color: #1DB954;
            box-shadow: 0 0 0 4px rgba(29, 185, 84, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #1DB954;
        }

        button[type="submit"] {
            width: 100%;
            padding: 18px;
            background: #1DB954;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background: #1ed760;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(29, 185, 84, 0.3);
        }

        .error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error i {
            font-size: 18px;
        }

        .info {
            margin-top: 30px;
            padding: 20px;
            background: #f8f8f8;
            border-radius: 12px;
            font-size: 14px;
            color: #666;
        }

        .info i {
            color: #1DB954;
            margin-right: 8px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-music"></i>
            <h1>Muzibu</h1>
        </div>

        <p class="subtitle">
            🎵 <strong>Yeni platformumuz hazırlanıyor!</strong><br>
            muzibu.com sitesi aktif olarak hizmet vermektedir.
        </p>

        @if(isset($error) && $error)
        <div class="error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ $error }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('construction.verify') }}" class="password-form">
            @csrf
            <div class="input-group">
                <input
                    type="password"
                    name="construction_password"
                    id="password"
                    placeholder="Erişim şifresi girin..."
                    required
                    autofocus
                >
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye" id="eye-icon"></i>
                </button>
            </div>
            <button type="submit">
                <i class="fas fa-unlock-alt"></i> Giriş Yap
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Enter key submit
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.closest('form').submit();
            }
        });
    </script>
</body>
</html>
