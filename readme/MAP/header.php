<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Türk Bilişim Enterprise CMS'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body x-data="app()" x-init="init()" :class="'theme-' + theme">
    <!-- Progress Bar -->
    <div class="progress-bar" :style="{ width: scrollProgress + '%' }"></div>
    
    <!-- Header Navigation -->
    <header class="header-nav">
        <div class="container mx-auto px-6 h-full flex items-center justify-between">
            <!-- Brand -->
            <div class="nav-brand">
                <div class="nav-brand-icon">
                    <i data-lucide="hexagon" class="w-6 h-6 text-white"></i>
                </div>
                <div class="nav-brand-text">
                    <h1>Türk Bilişim Enterprise CMS</h1>
                    <p><?php echo isset($page_subtitle) ? $page_subtitle : 'Multi-Tenant Architecture'; ?></p>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <a href="index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-accent' : ''; ?>">
                    <span>Ana Sayfa</span>
                </a>
                <a href="tenant-system.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'tenant-system.php') ? 'text-accent' : ''; ?>">
                    <span>Çok Kiracılı Sistem</span>
                </a>
                <a href="ai-system.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'ai-system.php') ? 'text-accent' : ''; ?>">
                    <span>AI Sistemi</span>
                </a>
                <a href="mobile-app.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'mobile-app.php') ? 'text-accent' : ''; ?>">
                    <span>Mobil Uygulama</span>
                </a>
            </nav>
            
            <!-- Actions -->
            <div class="nav-actions">
                <div class="ai-badge"><?php echo isset($page_badge) ? $page_badge : 'Multi-Tenant'; ?></div>
                <button @click="toggleTheme()" class="theme-toggle">
                    <i x-show="theme === 'light'" data-lucide="moon" class="w-5 h-5"></i>
                    <i x-show="theme === 'dark'" data-lucide="sun" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Navigation Sidebar -->
    <nav class="nav-sidebar">
        <?php if (isset($nav_sections) && is_array($nav_sections)): ?>
            <?php foreach ($nav_sections as $section_id => $section_title): ?>
                <div class="nav-dot" 
                     :class="{ 'active': activeSection === '<?php echo $section_id; ?>' }" 
                     @click="scrollToSection('<?php echo $section_id; ?>')" 
                     data-title="<?php echo $section_title; ?>"></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </nav>
    
    <div class="scroll-container" style="padding-top: 90px;">