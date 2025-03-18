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
        
        # Push işlemi
        print(f"\n--- GITHUB'A GÖNDER ({branch_name}) ---")
        push_result = subprocess.run(f"git push origin {branch_name}", shell=True, capture_output=True, text=True)
        
        # Push sonucunu kontrol et
        if push_result.returncode != 0:
            error_message = push_result.stderr
            print(error_message)
            
            # Upstream branch hatası
            if "fatal: The current branch" in error_message and "has no upstream branch" in error_message:
                print(f"\nOtomatik olarak upstream branch ayarlanıyor: {branch_name}")
                upstream_result = subprocess.run(f"git push --set-upstream origin {branch_name}", 
                                               shell=True, capture_output=True, text=True)
                
                if upstream_result.returncode == 0:
                    print("Upstream branch başarıyla ayarlandı ve push işlemi tamamlandı.")
                    return True
                else:
                    # Rejected hatası
                    if "! [rejected]" in upstream_result.stderr and "fetch first" in upstream_result.stderr:
                        print("\n--- GIT PULL İLE UZAK DEĞİŞİKLİKLERİ ÇEKİLİYOR ---")
                        pull_result = run_command(f"git pull origin {branch_name}")
                        
                        # Pull başarılı olduysa tekrar push dene
                        if pull_result == 0:
                            print("\n--- UZAK DEĞİŞİKLİKLER BİRLEŞTİRİLDİ, TEKRAR PUSH DENENIYOR ---")
                            final_push = run_command(f"git push origin {branch_name}")
                            if final_push == 0:
                                print("Push işlemi başarıyla tamamlandı.")
                                return True
                            else:
                                print("Son push işlemi başarısız oldu. Lütfen manuel olarak kontrol edin.")
                                return False
                        else:
                            print("Pull işlemi başarısız oldu. Çakışmalar manuel olarak çözülmeli.")
                            return False
            
            # Diğer push hataları
            else:
                print("Push işlemi başarısız oldu. Lütfen hata mesajını kontrol edin.")
                return False
        else:
            print(push_result.stdout)
            if push_result.stderr:
                print(push_result.stderr)
            print("\nPush işlemi başarıyla tamamlandı.")
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