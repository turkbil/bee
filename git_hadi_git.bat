@echo off
chcp 65001 > nul
title GIT ZORLA YÜKLEME ARACI

:: Renkli çıktı için
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

:: NUL dosyasını kaldır (varsa)
echo.
echo --- NUL DOSYASI KONTROLÜ ---
if exist "nul" (
  echo NUL dosyası bulundu, kaldırılıyor...
  del /f /q nul 2>nul
) else (
  echo NUL dosyası bulunamadı, devam ediliyor...
)

:: NUL'u gitignore'a ekle
echo nul >> .gitignore
type .gitignore | findstr /v /c:"^$" | sort > .gitignore.temp
move /y .gitignore.temp .gitignore > nul

:: Git durumunu kontrol et
echo.
echo --- GIT DURUMU ---
git status

:: Dosyaları ekle (nul dosyası hariç)
echo.
echo --- DEĞİŞİKLİKLERİ EKLE ---
git add --all -- ":(exclude)nul"

:: Değişiklikleri kontrol et
git diff --cached --quiet
if %ERRORLEVEL% EQU 0 (
  echo.
  echo Yüklenecek değişiklik yok. İşlem tamamlandı.
  goto :sonlandir
)

:: turkbil/bee reposunu ayarla
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

:: Commit
echo.
echo --- COMMIT ---
for /F "tokens=2,3,4 delims=/ " %%a in ('date /t') do set tarih=%%c-%%a-%%b
for /F "tokens=1,2 delims=: " %%a in ('time /t') do set saat=%%a:%%b
git commit -m "Otomatik yükleme - %tarih% %saat%"

:: Branch ismini al
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