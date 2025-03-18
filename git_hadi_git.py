#!/usr/bin/env python3
"""
Git Upload Script

Bu betik yerel dosyaları GitHub'a yükler.
Kullanım: python git_upload.py
"""

import os
import subprocess
import sys
import time
from datetime import datetime

def run_command(command):
    """
    Komut çalıştırır ve çıktıyı gösterir
    """
    print(f"\n> {command}")
    process = subprocess.Popen(
        command,
        shell=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True
    )
    
    # Çıktıyı gerçek zamanlı görüntüle
    while True:
        output = process.stdout.readline()
        if output == '' and process.poll() is not None:
            break
        if output:
            print(output.strip())
    
    # Hata varsa göster
    stderr = process.stderr.read()
    if stderr:
        print(f"HATA: {stderr}")
    
    return process.poll()

def git_upload():
    """
    Tüm değişiklikleri GitHub'a yükler
    """
    # Proje dizini (geçerli dizin)
    project_dir = os.getcwd()
    print(f"Proje dizini: {project_dir}")
    
    # Git repo kontrolü
    if not os.path.exists(os.path.join(project_dir, '.git')):
        print("HATA: Bu dizin bir git deposu değil. Lütfen geçerli bir git repo dizininde çalıştırın.")
        return False
    
    try:
        # Git durumunu kontrol et
        print("\n--- GIT DURUMU ---")
        run_command("git status")
        
        # Değişiklikleri ekle
        print("\n--- DEĞİŞİKLİKLERİ EKLE ---")
        run_command("git add .")
        
        # Commit mesajı
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        commit_message = f"Otomatik yükleme - {timestamp}"
        
        # Commit
        print("\n--- COMMIT ---")
        run_command(f'git commit -m "{commit_message}"')
        
        # Push
        print("\n--- GITHUB'A GÖNDER ---")
        run_command("git push")
        
        print("\n\nİşlem tamamlandı! Tüm değişiklikler GitHub'a yüklendi.")
        return True
        
    except Exception as e:
        print(f"İşlem sırasında hata oluştu: {str(e)}")
        return False

if __name__ == "__main__":
    print("=== GIT UPLOAD ARACI ===")
    print("Bu araç yerel dosyaları GitHub'a yükler.")
    print("İşlem başlatılıyor...")
    
    success = git_upload()
    
    # İşlem sonrası bekleme (hemen kapanmaması için)
    if success:
        print("\nİşlem başarıyla tamamlandı!")
    else:
        print("\nİşlem sırasında hatalar oluştu!")
    
    print("\nÇıkmak için bir tuşa basın...")
    input()  # Kullanıcı bir tuşa basana kadar bekle