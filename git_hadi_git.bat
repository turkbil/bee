@echo off
chcp 65001 > nul
title GIT ZORLA YÜKLEME ARACI

:: Renkli çıktı
color 0A

echo === GIT ZORLA YÜKLEME ARACI (turkbil/bee) ===
echo Bu araç yerel dosyaları turkbil/bee GitHub reposuna zorla yükler.
echo Dikkat: Uzak depodaki değişiklikler kaybedilecektir!
echo İşlem başlatılıyor...
echo.

cd /d %~dp0
echo Proje dizini: %CD%

:: Git repo kontrolü
if not exist ".git" (
  color 0C
  echo HATA: Bu dizin bir git deposu değil. Lütfen geçerli bir git repo dizininde çalıştırın.
  goto :sonlandir
)

:: Önceki commit'lerde yer almış ama artık .gitignore'da olan klasörleri kaldır
echo.
echo --- ESKİ COMMIT EDİLEN DOSYALAR TEMİZLENİYOR ---
git rm -r --cached node_modules vendor storage/logs storage/pail storage/framework storage/app public/storage > nul 2>&1

:: Git durumunu göster
echo.
echo --- GIT DURUMU ---
git status

:: Değişiklikleri ekle
echo.
echo --- DEĞİŞİKLİKLERİ EKLE ---
git add .

:: Değişiklik var mı kontrol et
git diff --cached --quiet
if %ERRORLEVEL% EQU 0 (
  echo.
  echo Yüklenecek değişiklik yok. İşlem tamamlandı.
  goto :sonlandir
)

:: Uzak repo kontrolü
echo.
echo --- UZAK REPO KONTROLÜ ---
git remote -v | findstr "origin" > nul
if %ERRORLEVEL% NEQ 0 (
  echo Uzak repo ayarlanıyor...
  git remote add origin https://github.com/turkbil/bee.git
  echo Uzak repo turkbil/bee olarak ayarlandı.
) else (
  git remote set-url origin https://github.com/turkbil/bee.git
  echo Uzak repo turkbil/bee olarak güncellendi.
)

:: Commit işlemi
echo.
echo --- COMMIT ---
for /F "tokens=2,3,4 delims=/ " %%a in ('date /t') do set tarih=%%c-%%a-%%b
for /F "tokens=1,2 delims=: " %%a in ('time /t') do set saat=%%a:%%b
git commit -m "Otomatik temiz yükleme - %tarih% %saat%"

:: Aktif branch ismini al
for /f "tokens=*" %%a in ('git rev-parse --abbrev-ref HEAD') do set branch=%%a

:: Force push
echo.
echo --- GITHUB'A ZORLA GÖNDER (%branch%) ---
echo Yerel değişiklikler uzak depodaki değişiklikleri ezecek...
git push -f origin %branch%

if %ERRORLEVEL% EQU 0 (
  color 0A
  echo.
  echo GitHub'a yükleme başarıyla tamamlandı!
) else (
  color 0C
  echo.
  echo HATA: GitHub'a yükleme sırasında bir sorun oluştu.
  echo Tekrar deneniyor...
  git push -f origin %branch%

  if %ERRORLEVEL% EQU 0 (
    color 0A
    echo GitHub'a yükleme başarıyla tamamlandı!
  ) else (
    color 0C
    echo İkinci denemede de başarısız oldu!
  )
)

:sonlandir
echo.
echo Çıkmak için bir tuşa basın...
pause > nul
