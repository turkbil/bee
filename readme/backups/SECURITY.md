# 🔒 BACKUP GÜVENLİK POLİTİKASI

## ⚠️ KRİTİK UYARI

**ASLA backup dosyalarını Git'e yüklemeyin!**

## 🚫 Git'e YÜKLENMEYECEKler:
- ❌ `*.sql` dosyaları
- ❌ `*.sql.gz` dosyaları
- ❌ `*.tar.gz` dosyaları
- ❌ Tarih klasörleri (`20*/`)

## ✅ Git'e YÜKLENEBİLİR:
- ✅ `backup.sh` (script)
- ✅ `backup-fast.sh` (script)
- ✅ `backup-minimal.sh` (script)
- ✅ `README.md` (döküman)
- ✅ `SECURITY.md` (bu dosya)

## 🔐 GÜVENLİ BACKUP STRATEJİSİ

### 1. Lokal Backup
```bash
# Backup al
bash readme/backups/backup.sh

# Güvenli yere kopyala
scp readme/backups/*.tar.gz user@backup-server:/secure/location/
```

### 2. Şifreli Cloud Backup
```bash
# Backup'ı şifrele
gpg -c readme/backups/complete_backup_*.tar.gz

# Sadece şifreli dosyayı cloud'a yükle
rclone copy readme/backups/*.gpg remote:backups/
```

### 3. Otomatik Temizlik
```bash
# 7 günden eski backup'ları sil
find readme/backups/ -name "*.tar.gz" -mtime +7 -delete
```

## 🛡️ GÜVENLİK KONTROL LİSTESİ

- [ ] Backup dosyaları `.gitignore`'da mı?
- [ ] `git status` temiz mi?
- [ ] Backup'lar şifreli mi?
- [ ] Eski backup'lar temizlendi mi?
- [ ] Backup lokasyonu güvenli mi?

## 📊 NEDEN ÖNEMLİ?

Backup dosyaları içerir:
- 🔑 **Veritabanı şifreleri**
- 👤 **Kullanıcı bilgileri**
- 💳 **Ödeme verileri**
- 📧 **Email adresleri**
- 🔐 **Hash'lenmiş şifreler**

**BU BİLGİLER ASLA PUBLIC OLMAMALI!**

## 🆘 YANLIŞ YÜKLEME DURUMUNDA

Eğer yanlışlıkla yüklediyseniz:

```bash
# 1. Hemen Git history'den sil
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch readme/backups/*.tar.gz" \
  --prune-empty --tag-name-filter cat -- --all

# 2. Force push
git push origin --force --all

# 3. GitHub'dan da temizle
# Settings > Danger Zone > Delete this repository (gerekirse)

# 4. TÜM ŞİFRELERİ DEĞİŞTİR!
```

---

**UNUTMA:** Güvenlik her zaman önceliklidir! 🔒