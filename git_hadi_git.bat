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
:: Git durumunu kontrol et
echo.
echo --- GIT DURUMU ---
git status -u
:: Tüm dosyaları ekle (yeni, değiştirilmiş ve silinen)
echo.
echo --- TÜM DOSYALARI EKLE ---
git add -A
:: Yeni dosyaların eklendiğinden emin ol
git ls-files --others --exclude-standard | findstr "." > nul
if %ERRORLEVEL% EQU 0 (
  echo Yeni dosyalar ekleniyor...
  git add -f .
)
:: Değişiklikleri kontrol et - Bu kısımda değişiklik yaptım
git diff --cached --quiet
if %ERRORLEVEL% EQU 0 (
  git status | findstr "working tree clean" > nul
  if %ERRORLEVEL% EQU 0 (
    echo.
    echo Yüklenecek değişiklik yok. İşlem tamamlandı.
    goto :sonlandir
  ) else (
    echo Değişiklikler tespit edildi, devam ediliyor...
  )
) else (
  echo Değişiklikler bulundu, işleme devam ediliyor...
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
    echo --- GİZLİ DOSYALARI KONTROL ET ---
    echo Gizli dosyalar listeleniyor:
    dir /a:h
    echo.
    echo --- GIT IGNORE KONTROLÜ ---
    if exist ".gitignore" (
      echo .gitignore dosyası bulundu. İçeriği:
      type .gitignore
    ) else (
      echo .gitignore dosyası bulunamadı.
    )
  )
)
:sonlandir
echo.
echo Çıkmak için bir tuşa basın...
pause > nul