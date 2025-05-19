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

:: Proje dizinine git
cd /d %~dp0
echo Proje dizini: %CD%

:: Git repo kontrolü
if not exist ".git" (
  color 0C
  echo HATA: Bu dizin bir git deposu değil. Lütfen geçerli bir git repo dizininde çalıştırın.
  goto :sonlandir
)

:: Git durumunu kontrol et
echo.
echo --- GIT DURUMU ---
git status -u

:: İzlenmeyen tüm dosyaları listele
echo.
echo --- İZLENMEYEN DOSYALAR ---
git ls-files --others --exclude-standard

:: Tüm dosyaları zorla ekle
echo.
echo --- TÜM DOSYALARI ZORLA EKLE ---
git add -A -f
git status -u

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
git commit -m "Otomatik yükleme - %tarih% %saat%" --allow-empty

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
    echo.
    echo --- OLASI SORUNLAR ---
    
    echo 1. Gizli dosyalar listeleniyor:
    dir /a:h
    
    echo 2. Git ignore içeriği:
    if exist ".gitignore" (
      type .gitignore
    ) else (
      echo .gitignore dosyası bulunamadı.
    )
    
    echo 3. Git Kimlik Bilgileri kontrolü:
    git config user.name
    git config user.email
  )
)

:sonlandir
echo.
echo Çıkmak için bir tuşa basın...
pause > nul