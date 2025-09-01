<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Geçici Olarak Erişilemez</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Light Mode Renkleri */
            --light-primary: #0ea5e9;
            --light-primary-dark: #0284c7;
            --light-secondary: #f0f9ff;
            --light-bg: #f8fafc;
            --light-card-bg: #ffffff;
            --light-text: #0f172a;
            --light-text-secondary: #64748b;
            --light-border: #e2e8f0;
            --light-icon-bg: #f0f9ff;
            --light-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            --light-btn-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
            --light-status-bg: #f0f9ff;
            --light-status-text: #0ea5e9;
            --light-switch-bg: #e2e8f0;
            --light-wave-color: rgba(14, 165, 233, 0.1);
            --light-particle: rgba(14, 165, 233, 0.1);
            --light-clock-face: #ffffff;
            --light-clock-border: #e2e8f0;
            --light-hour-hand: #0f172a;
            --light-minute-hand: #0f172a;
            --light-clock-center: #0ea5e9;
            
            /* Dark Mode Renkleri */
            --dark-primary: #38bdf8;
            --dark-primary-dark: #0ea5e9;
            --dark-secondary: #1e293b;
            --dark-bg: #0f172a;
            --dark-card-bg: #1e293b;
            --dark-text: #f8fafc;
            --dark-text-secondary: #cbd5e1;
            --dark-border: #334155;
            --dark-icon-bg: #334155;
            --dark-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            --dark-btn-shadow: 0 4px 10px rgba(56, 189, 248, 0.3);
            --dark-status-bg: #334155;
            --dark-status-text: #38bdf8;
            --dark-switch-bg: #334155;
            --dark-wave-color: rgba(14, 165, 233, 0.05);
            --dark-particle: rgba(56, 189, 248, 0.2);
            --dark-clock-face: #1e293b;
            --dark-clock-border: #334155;
            --dark-hour-hand: #f8fafc;
            --dark-minute-hand: #f8fafc;
            --dark-clock-center: #38bdf8;
            
            /* Varsayılan olarak Light Mode ayarları */
            --primary: var(--light-primary);
            --primary-dark: var(--light-primary-dark);
            --secondary: var(--light-secondary);
            --bg: var(--light-bg);
            --card-bg: var(--light-card-bg);
            --text: var(--light-text);
            --text-secondary: var(--light-text-secondary);
            --border: var(--light-border);
            --icon-bg: var(--light-icon-bg);
            --shadow: var(--light-shadow);
            --btn-shadow: var(--light-btn-shadow);
            --status-bg: var(--light-status-bg);
            --status-text: var(--light-status-text);
            --switch-bg: var(--light-switch-bg);
            --wave-color: var(--light-wave-color);
            --particle: var(--light-particle);
            --clock-face: var(--light-clock-face);
            --clock-border: var(--light-clock-border);
            --hour-hand: var(--light-hour-hand);
            --minute-hand: var(--light-minute-hand);
            --clock-center: var(--light-clock-center);
        }
        
        /* Dark Mode İçin CSS Değişkenleri */
        [data-theme='dark'] {
            --primary: var(--dark-primary);
            --primary-dark: var(--dark-primary-dark);
            --secondary: var(--dark-secondary);
            --bg: var(--dark-bg);
            --card-bg: var(--dark-card-bg);
            --text: var(--dark-text);
            --text-secondary: var(--dark-text-secondary);
            --border: var(--dark-border);
            --icon-bg: var(--dark-icon-bg);
            --shadow: var(--dark-shadow);
            --btn-shadow: var(--dark-btn-shadow);
            --status-bg: var(--dark-status-bg);
            --status-text: var(--dark-status-text);
            --switch-bg: var(--dark-switch-bg);
            --wave-color: var(--dark-wave-color);
            --particle: var(--dark-particle);
            --clock-face: var(--dark-clock-face);
            --clock-border: var(--dark-clock-border);
            --hour-hand: var(--dark-hour-hand);
            --minute-hand: var(--dark-minute-hand);
            --clock-center: var(--dark-clock-center);
        }
        
        /* Light Mode İçin CSS Değişkenleri */
        [data-theme='light'] {
            --primary: var(--light-primary);
            --primary-dark: var(--light-primary-dark);
            --secondary: var(--light-secondary);
            --bg: var(--light-bg);
            --card-bg: var(--light-card-bg);
            --text: var(--light-text);
            --text-secondary: var(--light-text-secondary);
            --border: var(--light-border);
            --icon-bg: var(--light-icon-bg);
            --shadow: var(--light-shadow);
            --btn-shadow: var(--light-btn-shadow);
            --status-bg: var(--light-status-bg);
            --status-text: var(--light-status-text);
            --switch-bg: var(--light-switch-bg);
            --wave-color: var(--light-wave-color);
            --particle: var(--light-particle);
            --clock-face: var(--light-clock-face);
            --clock-border: var(--light-clock-border);
            --hour-hand: var(--light-hour-hand);
            --minute-hand: var(--light-minute-hand);
            --clock-center: var(--light-clock-center);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 2rem 1rem;
        }
        
        /* Animasyonlu Arka Plan Parçacıkları */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background-color: var(--particle);
            animation: float 15s infinite ease-in-out;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-30px) translateX(30px);
            }
            50% {
                transform: translateY(20px) translateX(-20px);
            }
            75% {
                transform: translateY(30px) translateX(25px);
            }
        }
        
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30vh;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%230ea5e9' fill-opacity='0.1' d='M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,170.7C960,160,1056,160,1152,149.3C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            z-index: -1;
            opacity: 0.7;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
            background-color: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        
        .blob {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background-color: var(--secondary);
            z-index: 0;
        }
        
        .content {
            position: relative;
            z-index: 1;
            padding: 2.5rem;
        }
        
        /* Canlı Saat */
        .clock-wrapper {
            position: absolute;
            top: 1rem;
            left: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .analog-clock {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--clock-face);
            border: 2px solid var(--clock-border);
            position: relative;
            margin-bottom: 0.5rem;
        }
        
        .hour-hand, .minute-hand {
            position: absolute;
            background-color: var(--hour-hand);
            transform-origin: bottom center;
            top: 0;
            left: 50%;
        }
        
        .hour-hand {
            width: 2px;
            height: 12px;
            margin-left: -1px;
            top: 8px;
        }
        
        .minute-hand {
            width: 2px;
            height: 16px;
            margin-left: -1px;
            top: 4px;
            background-color: var(--minute-hand);
        }
        
        .clock-center {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: var(--clock-center);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .digital-time {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        /* Tema Geçiş Butonu */
        .theme-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
            cursor: pointer;
            background-color: var(--switch-bg);
            border-radius: 30px;
            padding: 5px;
            width: 60px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }
        
        .theme-toggle i {
            font-size: 16px;
            color: var(--text-secondary);
        }
        
        .toggle-slider {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--primary);
            transition: transform 0.3s ease;
        }
        
        [data-theme='dark'] .toggle-slider {
            transform: translateX(30px);
        }
        
        .icon-container {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--icon-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .icon {
            font-size: 32px;
            color: var(--primary);
        }
        
        .pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: var(--primary);
            opacity: 0.2;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.2;
            }
            70% {
                transform: scale(1.2);
                opacity: 0;
            }
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }
        
        h1 {
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.2rem;
            color: var(--text);
        }
        
        p {
            text-align: center;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        
        .divider {
            width: 60px;
            height: 4px;
            background-color: var(--primary);
            margin: 0 auto 2rem;
            border-radius: 2px;
        }
        
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            margin: 0 auto;
            width: fit-content;
            box-shadow: var(--btn-shadow);
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(14, 165, 233, 0.4);
        }
        
        .status {
            display: inline-block;
            background-color: var(--status-bg);
            border-radius: 6px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            color: var(--status-text);
            margin: 1.5rem auto;
            font-weight: 500;
            display: block;
            text-align: center;
            width: fit-content;
        }
        
        /* Sosyal Medya Linkleri */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .social-link {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        .social-link:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            border-top: 1px solid var(--border);
            margin-top: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
            
            .content {
                padding: 2rem 1.5rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hareketli Arka Plan Parçacıkları -->
    <div class="particles" id="particles">
        <!-- Parçacıklar JS ile eklenecek -->
    </div>
    
    <div class="wave"></div>
    <div class="container">
        <div class="blob"></div>
        
        <!-- Canlı Saat -->
        <div class="clock-wrapper">
            <div class="analog-clock">
                <div class="hour-hand" id="hour-hand"></div>
                <div class="minute-hand" id="minute-hand"></div>
                <div class="clock-center"></div>
            </div>
            <span class="digital-time" id="digital-time">00:00</span>
        </div>
        
        <!-- Tema Değiştirme Butonu -->
        <div class="theme-toggle" id="theme-toggle">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
            <div class="toggle-slider"></div>
        </div>
        
        <div class="content">
            <div class="icon-container">
                <div class="pulse"></div>
                <i class="icon fas fa-power-off"></i>
            </div>
            
            <h1>Erişim Engellendi</h1>
            <div class="divider"></div>
            
            <p>Bu siteye şu anda erişim yoktur. Lütfen daha sonra tekrar deneyiniz.</p>
            
            <div class="status">503 Service Unavailable</div>
            
            <a href="mailto:support@domain.com" class="btn">
                <i class="fas fa-envelope"></i>
                İletişime Geç
            </a>
            
            <footer>
                &copy; {{ date('Y') }} - Tüm hakları saklıdır
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mevcut saat bilgisi
            const now = new Date();
            const hours = now.getHours();
            
            // Tema tercihi için localStorage anahtarı
            const THEME_PREF_KEY = 'offline-page-theme-preference';
            
            // Parçacıklar oluştur
            createParticles();
            
            // Canlı saat başlat
            startClock();
            
            // Saate göre tema ayarla (6-18 arası light, diğer saatler dark)
            if (!localStorage.getItem(THEME_PREF_KEY)) {
                const theme = (hours >= 6 && hours < 18) ? 'light' : 'dark';
                setTheme(theme);
            } else {
                // Kullanıcı manuel olarak tema seçmişse, onu kullan
                setTheme(localStorage.getItem(THEME_PREF_KEY));
            }
            
            // Tema değiştirme butonuna click event listener ekle
            document.getElementById('theme-toggle').addEventListener('click', toggleTheme);
        });
        
        // Tema değiştirme fonksiyonu
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        }
        
        // Tema ayarlama fonksiyonu
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('offline-page-theme-preference', theme);
            
            // Toggle slider pozisyonunu güncelle
            const slider = document.querySelector('.toggle-slider');
            if (theme === 'dark') {
                slider.style.transform = 'translateX(30px)';
            } else {
                slider.style.transform = 'translateX(0)';
            }
        }
        
        // Hareketli parçacıklar oluştur
        function createParticles() {
            const particles = document.getElementById('particles');
            const count = 12; // Parçacık sayısı
            
            for (let i = 0; i < count; i++) {
                const size = Math.random() * 50 + 20; // 20-70px arası
                const particle = document.createElement('div');
                particle.classList.add('particle');
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.opacity = `${Math.random() * 0.6 + 0.1}`;
                // Farklı animasyon süresi
                particle.style.animationDelay = `${Math.random() * 5}s`;
                particle.style.animationDuration = `${Math.random() * 10 + 10}s`; // 10-20s arası
                
                particles.appendChild(particle);
            }
        }
        
        // Canlı saat
        function startClock() {
            function updateClock() {
                const now = new Date();
                const hours = now.getHours();
                const minutes = now.getMinutes();
                
                // Digital saat
                document.getElementById('digital-time').textContent = 
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                
                // Analog saat
                const hourDeg = (hours % 12) * 30 + minutes * 0.5; // 360 / 12 = 30 derece saat başına
                const minuteDeg = minutes * 6; // 360 / 60 = 6 derece dakika başına
                
                document.getElementById('hour-hand').style.transform = `rotate(${hourDeg}deg)`;
                document.getElementById('minute-hand').style.transform = `rotate(${minuteDeg}deg)`;
                
                // Bir sonraki dakika için zamanla
                setTimeout(updateClock, 60000);
            }
            
            updateClock();
        }
    </script>
</body>
</html>