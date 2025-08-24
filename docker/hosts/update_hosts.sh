#!/bin/bash

# Laravel Multi-Tenant Docker Hosts Updater
# Bu script .test domain'lerini localhost'a yönlendirir

HOSTS_FILE="/etc/hosts"
BACKUP_FILE="/tmp/hosts.backup.$(date +%Y%m%d_%H%M%S)"

echo "🚀 Laravel Multi-Tenant Hosts Güncelleyici"
echo "==========================================="

# Backup oluştur
echo "📋 Mevcut hosts dosyası yedekleniyor..."
cp "$HOSTS_FILE" "$BACKUP_FILE"
echo "✅ Yedek dosya: $BACKUP_FILE"

# Mevcut .test girişlerini temizle
echo "🧹 Eski .test girişlerini temizleniyor..."
sudo sed -i '' '/# Laravel Multi-Tenant Docker/d' "$HOSTS_FILE"
sudo sed -i '' '/\.test$/d' "$HOSTS_FILE"

# Yeni girişleri ekle
echo "➕ Yeni .test girişleri ekleniyor..."
cat << 'EOF' | sudo tee -a "$HOSTS_FILE" > /dev/null

# Laravel Multi-Tenant Docker - Auto-generated entries
127.0.0.1 laravel.test
127.0.0.1 a.test
127.0.0.1 b.test  
127.0.0.1 c.test
127.0.0.1 d.test
127.0.0.1 phpmyadmin.test
# Laravel Multi-Tenant Docker - End
EOF

echo "✅ Hosts dosyası güncellendi!"
echo ""
echo "📋 Aktif domain'ler:"
echo "   • laravel.test (ana site)"
echo "   • a.test (tenant A)"
echo "   • b.test (tenant B)"  
echo "   • c.test (tenant C)"
echo "   • d.test (tenant D)"
echo "   • phpmyadmin.test (database yönetimi)"
echo ""
echo "🔥 Artık domain'leri port olmadan kullanabilirsin!"

# DNS cache temizle (macOS)
if [[ "$OSTYPE" == "darwin"* ]]; then
    echo "🔄 DNS cache temizleniyor (macOS)..."
    sudo dscacheutil -flushcache
    sudo killall -HUP mDNSResponder
    echo "✅ DNS cache temizlendi!"
fi

echo "🎉 Kurulum tamamlandı!"