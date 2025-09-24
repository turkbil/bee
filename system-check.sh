#!/bin/bash

# Laravel Sistem Durum Kontrol Script'i

echo "🔍 Laravel Sistem Durumu Kontrolü"
echo "================================="

# Laravel server kontrolü
echo "📊 Laravel Server (Port 8001):"
if lsof -ti:8001 >/dev/null 2>&1; then
    echo "   ✅ Çalışıyor"
else
    echo "   ❌ Çalışmıyor"
fi

# PhpMyAdmin kontrolü
echo "📊 PhpMyAdmin (Port 8090):"
if lsof -ti:8090 >/dev/null 2>&1; then
    echo "   ✅ Çalışıyor"
else
    echo "   ❌ Çalışmıyor"
fi

# Horizon kontrolü
echo "📊 Horizon Queue:"
if pgrep -f "php.*horizon" >/dev/null 2>&1; then
    echo "   ✅ Çalışıyor"
else
    echo "   ❌ Çalışmıyor"
fi

# MySQL kontrolü
echo "📊 MySQL:"
if brew services list | grep -q "mysql.*started"; then
    echo "   ✅ Çalışıyor"
else
    echo "   ❌ Çalışmıyor"
fi

# Redis kontrolü
echo "📊 Redis:"
if brew services list | grep -q "redis.*started"; then
    echo "   ✅ Çalışıyor"
else
    echo "   ❌ Çalışmıyor"
fi

# LaunchAgent kontrolü
echo "📊 Otomatik Başlatma (LaunchAgent):"
if launchctl list | grep -q "com.nurullah.laravel"; then
    echo "   ✅ Yüklü ve aktif"
else
    echo "   ❌ Yüklü değil"
fi

# Domain kontrolü
echo "📊 Domain Erişimi:"
if curl -s -I http://laravel.test >/dev/null 2>&1; then
    echo "   ✅ http://laravel.test erişilebilir"
else
    echo "   ❌ http://laravel.test erişilemiyor"
fi

echo ""
echo "🌐 Hızlı Erişim Linkleri:"
echo "   Ana Site: http://laravel.test"
echo "   Admin: http://laravel.test/admin"
echo "   PhpMyAdmin: http://pma.test"
echo "   Horizon: http://laravel.test/horizon/dashboard"