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
                wordDelay: options.wordDelay || 5,            // Kelime başına gecikme (ms) - rocket speed
                minWordLength: options.minWordLength || 1,     // Minimum kelime uzunluğu
                showTypingWhileBuffering: options.showTypingWhileBuffering !== false,
                scrollCallback: options.scrollCallback || null,
                maxBufferSize: options.maxBufferSize || 50,    // Max buffer size (kelime)
                punctuationDelay: options.punctuationDelay || 10, // Noktalama işaretlerinde ek gecikme
                enableMarkdown: options.enableMarkdown !== false  // Markdown desteği
            };
            
            this.buffer = '';               // Ham karakter buffer'ı
            this.wordQueue = [];           // Yazdırılacak kelimeler
            this.displayedText = '';       // Şu an ekranda görünen metin
            this.isActive = false;         // Buffer aktif mi?
            this.wordTimer = null;         // Kelime yazma timer'ı
            this.isTypingShown = true;     // "Yazıyor" animasyonu gösteriliyor mu?
            this.totalWordsProcessed = 0;  // İstatistik için
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
         * Bir sonraki kelimenin yazılmasını planla
         */
        scheduleNextWord() {
            if (this.wordTimer) return; // Zaten bir timer çalışıyor
            
            if (this.wordQueue.length > 0) {
                // İlk kelimede "yazıyor" animasyonunu kaldır
                if (this.isTypingShown) {
                    this.clearTypingAnimation();
                }
                
                const nextWord = this.wordQueue[0];
                const delay = this.calculateWordDelay(nextWord);
                
                this.wordTimer = setTimeout(() => {
                    this.writeNextWord();
                }, delay);
            }
        }
        
        /**
         * "Yazıyor" animasyonunu temizle
         */
        clearTypingAnimation() {
            this.targetElement.innerHTML = this.displayedText;
            this.isTypingShown = false;
            
            // CSS sınıfını koru (font size için)
            if (!this.targetElement.classList.contains('brand-story-text')) {
                this.targetElement.classList.add('brand-story-text');
            }
        }
        
        /**
         * Kelime tipine göre gecikme hesapla
         */
        calculateWordDelay(word) {
            let delay = this.options.wordDelay;
            
            // Noktalama işaretleri için ek gecikme
            if (/[.!?;:]$/.test(word.trim())) {
                delay += this.options.punctuationDelay;
            }
            
            // Çok kısa kelimeler için daha az gecikme
            if (word.trim().length <= 3) {
                delay *= 0.7;
            }
            
            // Çok uzun kelimeler için biraz daha fazla gecikme
            if (word.trim().length > 8) {
                delay *= 1.2;
            }
            
            return Math.max(3, Math.min(delay, 50)); // 3ms - 50ms arası
        }
        
        /**
         * Sıradaki kelimeyi ekrana yaz
         */
        writeNextWord() {
            if (this.wordQueue.length === 0) {
                this.wordTimer = null;
                return;
            }
            
            const word = this.wordQueue.shift();
            this.displayedText += word;
            
            // Ekranda güvenli gösterim (innerText HTML injection'ı önler)
            if (this.options.enableMarkdown && this.isMarkdownContent(this.displayedText)) {
                // Markdown içerik varsa HTML olarak render et (güvenli HTML)
                this.targetElement.innerHTML = this.sanitizeHTML(this.displayedText);
            } else {
                // innerText kullanırken CSS class'ları korunur
                this.targetElement.innerText = this.displayedText;
            }
            
            // CSS sınıfını koru (font size için)
            if (!this.targetElement.classList.contains('brand-story-text')) {
                this.targetElement.classList.add('brand-story-text');
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
         * İçeriğin markdown olup olmadığını kontrol et
         */
        isMarkdownContent(text) {
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
                this.options.wordDelay = 3; // Rocket speed
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
    
    console.log('✅ AIWordBuffer class globally available');
}