#!/usr/bin/env python3
"""
HTML tasarım dosyalarını section'lara böler.
Her section ayrı bir HTML dosyası olarak kaydedilir.
"""

import os
import re
from pathlib import Path

def extract_sections(html_content):
    """HTML içeriğinden section'ları çıkarır"""
    # <section ... </section> taglerini bul
    pattern = r'(<section[^>]*>.*?</section>)'
    sections = re.findall(pattern, html_content, re.DOTALL)
    return sections

def extract_head(html_content):
    """HTML head kısmını çıkarır"""
    match = re.search(r'<head>(.*?)</head>', html_content, re.DOTALL)
    return match.group(1) if match else ''

def create_html_page(head_content, section_content, current, total, base_name):
    """Tek bir section için complete HTML sayfası oluşturur"""

    # Navigation butonları
    prev_link = f'{base_name}-{current-1}.html' if current > 1 else '#'
    next_link = f'{base_name}-{current+1}.html' if current < total else '#'
    index_link = 'index.html'

    prev_disabled = 'opacity-50 cursor-not-allowed' if current == 1 else 'hover:bg-white/20'
    next_disabled = 'opacity-50 cursor-not-allowed' if current == total else 'hover:bg-white/20'

    html = f'''<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
{head_content}
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white min-h-screen">

    <!-- Navigation Bar -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-slate-900/90 backdrop-blur-lg border-b border-white/10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Ana Sayfa -->
                <a href="{index_link}" class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-home"></i>
                    <span class="hidden sm:inline">Ana Sayfa</span>
                </a>

                <!-- Sayfa Numarası -->
                <div class="text-center">
                    <div class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                        {current} / {total}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Tasarım Numarası</div>
                </div>

                <!-- Prev/Next Butonları -->
                <div class="flex items-center gap-2">
                    <a href="{prev_link}" class="px-4 py-2 bg-white/10 rounded-lg {prev_disabled} transition-all flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span class="hidden sm:inline">Önceki</span>
                    </a>
                    <a href="{next_link}" class="px-4 py-2 bg-white/10 rounded-lg {next_disabled} transition-all flex items-center gap-2">
                        <span class="hidden sm:inline">Sonraki</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20">
        {section_content}
    </div>

</body>
</html>'''

    return html

def process_file(input_file, output_dir):
    """Bir HTML dosyasını işler ve section'lara böler"""

    # Dosya adını al
    file_name = Path(input_file).stem

    # HTML içeriğini oku
    with open(input_file, 'r', encoding='utf-8') as f:
        html_content = f.read()

    # Head ve section'ları çıkar
    head_content = extract_head(html_content)
    sections = extract_sections(html_content)

    if not sections:
        print(f"⚠️  {file_name}: Section bulunamadı!")
        return 0

    # Her section için ayrı dosya oluştur
    created_files = []
    for i, section in enumerate(sections, 1):
        output_file = f"{output_dir}/{file_name}-{i}.html"
        page_html = create_html_page(head_content, section, i, len(sections), file_name)

        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(page_html)

        created_files.append(output_file)

    print(f"✅ {file_name}: {len(sections)} section → {len(created_files)} dosya")
    return len(created_files)

def main():
    """Ana fonksiyon"""

    # Çalışma dizini
    work_dir = '/Users/nurullah/Desktop/cms/laravel/public/design'
    os.chdir(work_dir)

    # Tüm design-*.html dosyalarını bul (numaralı dosyaları hariç tut)
    design_files = [f for f in os.listdir('.') if f.startswith('design-') and f.endswith('.html') and not re.search(r'-\d+\.html$', f)]

    print(f"\n🚀 {len(design_files)} dosya işlenecek...\n")

    total_files = 0
    for design_file in sorted(design_files):
        count = process_file(design_file, '.')
        total_files += count

    print(f"\n✨ Toplam {total_files} dosya oluşturuldu!\n")

if __name__ == '__main__':
    main()
