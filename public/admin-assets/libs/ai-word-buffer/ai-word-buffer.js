/**
 * 🚀 UNIVERSAL AI WORD BUFFER SYSTEM
 * Tüm AI modülleri için kelime bazlı gecikme buffer sistemi
 * 
 * Bu dosya tüm AI streaming için kullanılır:
 * - Admin AI Chat
 * - AI Features Test Panel  
 * - AI Helpers
 * - Prowess Testing
 * - Conversation System
 * 
 * Kullanım Örneği:
 * const buffer = new AIWordBuffer(targetElement, {
 *     wordDelay: 400,
 *     minWordLength: 2,
 *     scrollCallback: () => scrollToBottom()
 * });
 * 
 * buffer.start();
 * buffer.addContent('AI\'dan gelen stream verisi...');
 * buffer.flush(); // Tamamlandığında
 */

if (typeof window.AIWordBuffer === 'undefined') {
    window.AIWordBuffer = class AIWordBuffer {
        constructor(targetElement, options = {}) {
            this.targetElement = targetElement;
            this.options = {
                wordDelay: options.wordDelay || 100,           // Kelime başına gecikme (ms) - DAHA DA AZALTILDI
                minWordLength: options.minWordLength || 2,     // Minimum kelime uzunluğu
                showTypingWhileBuffering: options.showTypingWhileBuffering !== false,
                scrollCallback: options.scrollCallback || null,
                maxBufferSize: options.maxBufferSize || 50,    // Max buffer size (kelime)
                punctuationDelay: options.punctuationDelay || 50, // Noktalama işaretlerinde ek gecikme - DAHA DA AZALTILDI
                enableMarkdown: options.enableMarkdown !== false,  // Markdown desteği
                fadeEffect: options.fadeEffect !== false,      // Yumuşak slide efekti
                typewriterSpeed: options.typewriterSpeed || 80, // Daktilo hızı base değeri - AZALTILDI
                initialDelay: options.initialDelay || 100      // İlk kelime için bekleme süresi
            };
            
            this.buffer = '';               // Ham karakter buffer'ı
            this.wordQueue = [];           // Yazdırılacak kelimeler
            this.displayedText = '';       // Şu an ekranda görünen metin
            this.isActive = false;         // Buffer aktif mi?
            this.wordTimer = null;         // Kelime yazma timer'ı
            this.isTypingShown = true;     // "Yazıyor" animasyonu gösteriliyor mu?
            this.totalWordsProcessed = 0;  // İstatistik için
            this.backgroundProcessing = false; // Background işleme durumu
            this.backgroundWorker = null;  // Background timer worker
            
            // 🚀 BACKGROUND PROCESSING - Sayfa gizli olduğunda da çalışmaya devam et
            this.setupBackgroundProcessing();
        }
        
        /**
         * Background processing kurulumu - Sayfa gizli olduğunda da çalışır
         */
        setupBackgroundProcessing() {
            // Page Visibility API ile sayfa durumunu izle
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    // Sayfa gizli olduğunda background mode'a geç
                    this.backgroundProcessing = true;
                    console.log('🌙 AIWordBuffer: Background processing mode activated');
                    this.setupBackgroundWorker();
                } else {
                    // Sayfa görünür olduğunda normal mode'a dön
                    this.backgroundProcessing = false;
                    console.log('☀️ AIWordBuffer: Foreground processing mode activated');
                    this.cleanupBackgroundWorker();
                }
            });
            
            // Focus/blur eventi ile de kontrol et
            window.addEventListener('blur', () => {
                this.backgroundProcessing = true;
                this.setupBackgroundWorker();
            });
            
            window.addEventListener('focus', () => {
                this.backgroundProcessing = false;
                this.cleanupBackgroundWorker();
            });
        }
        
        /**
         * Background worker benzeri sistem kur
         */
        setupBackgroundWorker() {
            if (this.backgroundWorker) return;
            
            // setInterval kullanarak güçlü background processing
            this.backgroundWorker = setInterval(() => {
                if (this.backgroundProcessing && this.wordQueue.length > 0 && !this.wordTimer) {
                    // Background'da daha hızlı yazdır
                    this.scheduleNextWord();
                }
            }, 150); // Her 150ms kontrol et
        }
        
        /**
         * Background worker'ı temizle
         */
        cleanupBackgroundWorker() {
            if (this.backgroundWorker) {
                clearInterval(this.backgroundWorker);
                this.backgroundWorker = null;
            }
        }
        
        /**
         * Buffer sistemini başlat
         */
        start() {
            this.isActive = true;
            this.isTypingShown = true;
            this.processBuffer();
            
            console.log('🚀 AIWordBuffer started', {
                wordDelay: this.options.wordDelay,
                minWordLength: this.options.minWordLength
            });
        }
        
        /**
         * Stream'den gelen içeriği buffer'a ekle
         */
        addContent(content) {
            if (!this.isActive) return;
            
            this.buffer += content;
            this.processBuffer();
        }
        
        /**
         * Buffer'ı işle ve kelimeleri queue'ya ekle
         */
        processBuffer() {
            const words = this.extractWordsFromBuffer();
            this.wordQueue.push(...words);
            
            // Yazma işlemini başlat
            this.scheduleNextWord();
        }
        
        /**
         * Buffer'dan tam kelimeleri çıkar
         * Regex: kelime + boşluk/newline | sadece newline
         */
        extractWordsFromBuffer() {
            const words = [];
            const regex = /\S+[\s\n\r]*|[\n\r]+/g;
            let match;
            let processedIndex = 0;
            
            while ((match = regex.exec(this.buffer)) !== null) {
                const word = match[0];
                
                // Buffer sonunda yarım kelime bırakma (güvenlik marjı)
                if (match.index + word.length <= this.buffer.length - 8) {
                    const trimmedWord = word.trim();
                    
                    // Minimum uzunluk kontrolü veya newline/whitespace
                    if (trimmedWord.length >= this.options.minWordLength || /[\n\r\s]/.test(word)) {
                        words.push(word);
                        processedIndex = match.index + word.length;
                    }
                }
            }
            
            // İşlenmiş kısmı buffer'dan çıkar
            this.buffer = this.buffer.substring(processedIndex);
            this.totalWordsProcessed += words.length;
            
            return words;
        }
        
        /**
         * Bir sonraki kelimenin yazılmasını planla - Background Safe
         */
        scheduleNextWord() {
            if (this.wordTimer) return; // Zaten bir timer çalışıyor
            
            if (this.wordQueue.length > 0) {
                // İlk kelimede "yazıyor" animasyonunu kaldır
                if (this.isTypingShown) {
                    this.clearTypingAnimation();
                }
                
                const nextWord = this.wordQueue[0];
                const delay = this.backgroundProcessing ? Math.min(this.calculateWordDelay(nextWord), 200) : this.calculateWordDelay(nextWord);
                
                // Background processing için daha güvenilir yöntem
                if (this.backgroundProcessing) {
                    // MessageChannel kullanarak background processing
                    this.wordTimer = this.createBackgroundTimer(() => {
                        this.writeNextWord();
                    }, delay);
                } else {
                    // Normal foreground işleme
                    this.wordTimer = setTimeout(() => {
                        this.writeNextWord();
                    }, delay);
                }
            }
        }
        
        /**
         * Background timer oluştur - Page visibility bypass
         */
        createBackgroundTimer(callback, delay) {
            if (typeof MessageChannel !== 'undefined') {
                // MessageChannel ile background-safe timer
                const channel = new MessageChannel();
                const timeoutId = setTimeout(() => {
                    channel.port1.postMessage('execute');
                }, delay);
                
                channel.port2.onmessage = () => {
                    callback();
                    channel.port1.close();
                    channel.port2.close();
                };
                
                return timeoutId;
            } else {
                // Fallback normal setTimeout
                return setTimeout(callback, delay);
            }
        }
        
        /**
         * "Yazıyor" animasyonunu temizle
         */
        clearTypingAnimation() {
            this.targetElement.innerHTML = this.displayedText;
            this.isTypingShown = false;
        }
        
        /**
         * Kelime tipine göre gecikme hesapla - YUMUŞAK TİMİNG
         */
        calculateWordDelay(word) {
            let delay = this.options.typewriterSpeed; // Base speed
            
            // Noktalama işaretleri için ek gecikme (daha az)
            if (/[.!?]$/.test(word.trim())) {
                delay += this.options.punctuationDelay;
            } else if (/[,;:]$/.test(word.trim())) {
                delay += this.options.punctuationDelay * 0.5; // Yarı gecikme
            }
            
            // Kelime uzunluğuna göre dinamik ayarlama - YUMUŞAK
            const wordLength = word.trim().length;
            if (wordLength <= 2) {
                delay *= 0.5;  // Çok kısa kelimeler hızlı
            } else if (wordLength <= 4) {
                delay *= 0.8;  // Kısa kelimeler biraz hızlı
            } else if (wordLength > 10) {
                delay *= 1.3;  // Uzun kelimeler biraz yavaş
            }
            
            // Ortak kelimeler için hızlandırma
            const commonWords = ['ve', 'bir', 'bu', 'şu', 'o', 'ben', 'sen', 'için', 'ile', 'da', 'de'];
            if (commonWords.includes(word.trim().toLowerCase())) {
                delay *= 0.6; // Ortak kelimeler %40 hızlı
            }
            
            return Math.max(40, Math.min(delay, 400)); // 40ms - 400ms arası
        }
        
        /**
         * Sıradaki kelimeyi ekrana yaz - SOLA KAYARAK SMOOTH EFEKTİ
         */
        writeNextWord() {
            if (this.wordQueue.length === 0) {
                this.wordTimer = null;
                return;
            }
            
            const word = this.wordQueue.shift();
            const oldText = this.displayedText;
            this.displayedText += word;
            
            // 🎬 SMOOTH SLIDE-IN FADE EFEKTİ (sadece foreground'da)
            if (this.options.fadeEffect && !this.backgroundProcessing) {
                // Kelime span'ı oluştur
                const newWordSpan = document.createElement('span');
                newWordSpan.style.cssText = `
                    display: inline-block;
                    color: var(--tblr-body-color, #000);
                    opacity: 0;
                    transform: translateX(-35px) scale(0.85);
                    filter: blur(3px);
                    transition: all 4.5s cubic-bezier(0.19, 1, 0.22, 1);
                    will-change: transform, opacity, filter;
                `;
                newWordSpan.textContent = word;
                
                // Eski içeriği güncelle ve yeni span'ı ekle
                this.updateElementContent(oldText);
                this.targetElement.appendChild(newWordSpan);
                
                // SMOOTH slide + fade animasyonu başlat
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        newWordSpan.style.opacity = '1';
                        newWordSpan.style.transform = 'translateX(0px) scale(1)';
                        newWordSpan.style.filter = 'blur(0px)';
                    });
                });
                
                // Animasyon bitince span'ı temizle
                setTimeout(() => {
                    this.updateElementContent(); // Tüm içeriği düz metin olarak güncelle
                }, 2800);
            } else {
                // Normal güncelleme (background mode veya fade effect kapalı)
                this.updateElementContent();
                
                // Background processing bilgisi (sadece ilk kelimede)
                if (this.backgroundProcessing && this.totalWordsProcessed % 10 === 1) {
                    console.log('🌙 Background processing active...');
                }
            }
            
            // Scroll callback'i çağır
            if (this.options.scrollCallback) {
                this.options.scrollCallback();
            }
            
            // Timer'ı temizle ve bir sonrakini planla
            this.wordTimer = null;
            this.scheduleNextWord();
        }
        
        /**
         * Element içeriğini güncelle (güvenli HTML)
         */
        updateElementContent(textOverride = null) {
            let textToShow = textOverride || this.displayedText;
            
            // HTML içerik varsa temizle ama yapıyı koru
            if (textToShow.includes('<') && textToShow.includes('>')) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = textToShow;
                
                // Paragrafları ve satır sonlarını koru
                const paragraphs = tempDiv.querySelectorAll('p');
                let cleanText = '';
                
                paragraphs.forEach(p => {
                    const text = p.textContent || p.innerText || '';
                    if (text.trim()) {
                        cleanText += text.trim() + '\n\n';
                    }
                });
                
                // Eğer paragraf bulunamadıysa normal strip yap
                if (!cleanText.trim()) {
                    cleanText = tempDiv.textContent || tempDiv.innerText || textToShow;
                }
                
                textToShow = cleanText.trim();
            }
            
            if (this.options.enableMarkdown && this.isMarkdownContent(textToShow)) {
                // Markdown içerik varsa HTML olarak render et (güvenli HTML)
                this.targetElement.innerHTML = this.sanitizeHTML(textToShow);
            } else {
                this.targetElement.innerText = textToShow;
            }
        }
        
        /**
         * İçeriğin markdown olup olmadığını kontrol et
         */
        isMarkdownContent(text) {
            // HTML content varsa markdown değil, düz metin olarak işle
            if (text.includes('<') && text.includes('>')) {
                return false;
            }
            
            const markdownPatterns = [
                /\*\*.*\*\*/,  // Bold
                /\*.*\*/,      // Italic
                /`.*`/,        // Code
                /^#+\s/m,      // Headers
                /^\*\s/m,      // Lists
                /^\d+\.\s/m    // Numbered lists
            ];
            
            return markdownPatterns.some(pattern => pattern.test(text));
        }
        
        /**
         * Temel HTML sanitization
         */
        sanitizeHTML(html) {
            const div = document.createElement('div');
            div.textContent = html;
            return div.innerHTML
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code>$1</code>')
                .replace(/\n/g, '<br>');
        }
        
        /**
         * Tüm kalan buffer'ı hızlıca yazdır
         */
        flush() {
            console.log('🏁 AIWordBuffer flushing', {
                remainingBuffer: this.buffer.length,
                queuedWords: this.wordQueue.length,
                totalProcessed: this.totalWordsProcessed
            });
            
            // Kalan buffer'ı kelime olarak ekle
            if (this.buffer.trim()) {
                this.wordQueue.push(this.buffer);
                this.buffer = '';
            }
            
            // Hızlı yazdırma modu
            if (this.wordQueue.length > 0) {
                this.options.wordDelay = 50; // Çok hızlı
                if (this.wordTimer) {
                    clearTimeout(this.wordTimer);
                    this.wordTimer = null;
                }
                this.scheduleNextWord();
            }
            
            this.isActive = false;
        }
        
        /**
         * Buffer'ı tamamen durdur ve temizle
         */
        destroy() {
            console.log('💥 AIWordBuffer destroyed', {
                totalWordsProcessed: this.totalWordsProcessed
            });
            
            if (this.wordTimer) {
                clearTimeout(this.wordTimer);
                this.wordTimer = null;
            }
            
            // Background worker'ı da temizle
            this.cleanupBackgroundWorker();
            
            this.isActive = false;
            this.wordQueue = [];
            this.buffer = '';
        }
        
        /**
         * Buffer durumunu getir (debug için)
         */
        getStatus() {
            return {
                isActive: this.isActive,
                bufferLength: this.buffer.length,
                queueLength: this.wordQueue.length,
                displayedLength: this.displayedText.length,
                totalWordsProcessed: this.totalWordsProcessed,
                isTypingShown: this.isTypingShown
            };
        }
    };
    
    /**
     * Global helper function - tüm AI modüllerinde kullanım için
     */
    window.createAIWordBuffer = function(targetElement, options = {}) {
        return new window.AIWordBuffer(targetElement, options);
    };
    
    /**
     * 🎯 YENİ: API Response'ını word buffer ile işle
     * Tüm AI yanıtları için universal metod
     */
    window.AIWordBuffer.handleAPIResponse = function(response, containerSelector = null) {
        try {
            // Word buffer config kontrolü
            if (!response.word_buffer_enabled || !response.word_buffer_config) {
                // Normal display (word buffer yok)
                const container = containerSelector ? 
                    document.querySelector(containerSelector) : 
                    document.querySelector('.ai-response-container');
                    
                if (container) {
                    container.innerHTML = response.response || response.formatted_response || '';
                }
                return null;
            }

            const config = response.word_buffer_config;
            
            // Container'ı bul
            const container = document.querySelector(config.container_selector || containerSelector || '.ai-response-container');
            if (!container) {
                console.warn('[AIWordBuffer] Container not found:', config.container_selector);
                return null;
            }

            // Word buffer instance oluştur
            const buffer = new window.AIWordBuffer(container, {
                wordDelay: config.delay_between_words || 150,
                fadeEffect: true,
                enableMarkdown: true,
                scrollCallback: config.showcase_mode ? null : (() => {
                    // Auto scroll (showcase mode hariç)
                    container.scrollIntoView({ behavior: 'smooth', block: 'end' });
                })
            });

            // Buffer'ı başlat
            buffer.start();
            
            // Content'i ekle
            const content = response.response || response.formatted_response || '';
            buffer.addContent(content);
            
            // Tamamla
            setTimeout(() => {
                buffer.flush();
                
                // Showcase mode'da özel efektler
                if (config.showcase_mode) {
                    container.style.transition = 'all 0.3s ease';
                    container.style.boxShadow = '0 4px 12px rgba(0,123,255,0.3)';
                    setTimeout(() => {
                        container.style.boxShadow = '';
                    }, 2000);
                }
            }, 100);

            return buffer;

        } catch (error) {
            console.error('[AIWordBuffer] API Response handling error:', error);
            
            // Fallback: Normal display
            const container = containerSelector ? 
                document.querySelector(containerSelector) : 
                document.querySelector('.ai-response-container');
                
            if (container) {
                container.innerHTML = response.response || response.formatted_response || '';
            }
            
            return null;
        }
    };
    
    console.log('✅ AIWordBuffer class globally available');
}