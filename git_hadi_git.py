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
    Tüm değişiklikleri GitHub'a yükler ve çakışma durumunda yerel değişiklikleri tercih eder
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
        
        # Force push işlemi - pull yapmadan doğrudan gönder
        print(f"\n--- GITHUB'A ZORLA GÖNDER ({branch_name}) ---")
        print("Yerel değişiklikler uzak depodaki değişiklikleri ezecek...")
        push_code, push_stdout, push_stderr = run_command(f"git push -f origin {branch_name}")
        
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
    print("=== GIT UPLOAD ARACI (ZORLA GÖNDERME MOD) ===")
    print("Bu araç yerel dosyaları GitHub'a yükler ve çakışmaları yerel değişiklikler lehine çözer.")
    print("Dikkat: Uzak depodaki değişiklikler kaybedilecektir!")
    print("İşlem başlatılıyor...")
    
    success = git_upload()
    
    # İşlem sonrası bekleme (hemen kapanmaması için)
    if success:
        print("\nİşlem başarıyla tamamlandı!")
    else:
        print("\nİşlem sırasında hatalar oluştu!")
    
    print("\nÇıkmak için bir tuşa basın...")
    input()  # Kullanıcı bir tuşa basana kadar bekle