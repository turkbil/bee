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
    stdout_output = ""
    stderr_output = ""
    
    for line in process.stdout:
        line = line.strip()
        stdout_output += line + "\n"
        print(line)
    
    # Hata çıktısını al
    stderr_output = process.stderr.read()
    if stderr_output:
        print(f"HATA: {stderr_output}")
    
    return process.poll(), stdout_output, stderr_output

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
        
        # Değişiklik var mı kontrol et
        status_output = subprocess.check_output("git status --porcelain", shell=True, text=True)
        if not status_output.strip():
            print("\nYüklenecek değişiklik yok. İşlem tamamlandı.")
            return True
        
        # Commit mesajı
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        commit_message = f"Otomatik yükleme - {timestamp}"
        
        # Commit
        print("\n--- COMMIT ---")
        run_command(f'git commit -m "{commit_message}"')
        
        # Branch ismini al
        branch_name = subprocess.check_output("git rev-parse --abbrev-ref HEAD", shell=True, text=True).strip()
        
        # Uzak repo kontrolü ve ayarlama
        remote_url = None
        try:
            remote_url = subprocess.check_output("git config --get remote.origin.url", shell=True, text=True).strip()
            print(f"Uzak repo: {remote_url}")
        except subprocess.CalledProcessError:
            # Origin ayarlı değil, kullanıcıdan URL iste
            print("\n--- UZAK REPO AYARLANMADI ---")
            repo_url = input("GitHub repo URL'sini girin (örn. https://github.com/turkbil/bee.git): ")
            if repo_url:
                run_command(f'git remote add origin {repo_url}')
                remote_url = repo_url
            else:
                print("Uzak repo URL'si girilmedi. Push işlemi yapılamayacak.")
                return False
        
        print("\n--- UZAK DEĞİŞİKLİKLERİ ÇEK (PULL) ---")
        print("Bu adım, uzak repo ve yerel repo arasındaki farklılıkları çözer...")
        pull_code, pull_stdout, pull_stderr = run_command(f"git pull origin {branch_name}")
        
        # Merge conflict kontrolü
        if "CONFLICT" in pull_stderr or "Automatic merge failed" in pull_stderr:
            print("\nHATA: Çakışma (merge conflict) tespit edildi. Lütfen çakışmaları manuel olarak çözün.")
            print("İşlem otomatik olarak devam edemez.")
            return False
        
        # Push işlemi
        print(f"\n--- GITHUB'A GÖNDER ({branch_name}) ---")
        push_code, push_stdout, push_stderr = run_command(f"git push origin {branch_name}")
        
        # Upstream hatası kontrolü
        if push_code != 0 and "fatal: The current branch" in push_stderr and "has no upstream branch" in push_stderr:
            print(f"\n--- UPSTREAM BRANCH AYARLANIYOR: {branch_name} ---")
            up_code, up_stdout, up_stderr = run_command(f"git push --set-upstream origin {branch_name}")
            if up_code == 0:
                print("\nUpstream branch başarıyla ayarlandı.")
                return True
            else:
                print("\nHATA: Upstream branch ayarlanamadı.")
                return False
                
        # Push sonucu kontrol
        if push_code == 0:
            print("\nGitHub'a yükleme başarıyla tamamlandı!")
            return True
        else:
            print("\nHATA: GitHub'a yükleme sırasında bir sorun oluştu.")
            return False
            
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