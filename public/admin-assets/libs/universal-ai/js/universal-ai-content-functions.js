/**
 * UNIVERSAL AI CONTENT FUNCTIONS
 * Tüm modüller için ortak AI Content JavaScript fonksiyonları
 * Pattern: A1 CMS Universal System
 *
 * HugeRTE & TinyMCE Editor Integration
 * AI Content Generation Support
 */

(function() {
    'use strict';

    // 🔒 SINGLETON PATTERN - Sadece bir kere yükle
    if (window.UniversalAIContentFunctions) {
        console.log('ℹ️ Universal AI Content Functions zaten yüklü - atlanıyor');
        return;
    }

    class UniversalAIContentFunctions {
        constructor() {
            console.log('🎯 Universal AI Content Functions başlatılıyor...');
        }

        /**
         * TinyMCE/HugeRTE editörünü güncelle
         */
        updateTinyMCEContent(content, targetField = 'body') {
            try {
                const currentLang = window.currentLanguage || 'tr';
                const editorId = `multiLangInputs.${currentLang}.${targetField}`;

                console.log('🎯 updateTinyMCEContent çağırıldı:', {
                    editorId,
                    currentLang,
                    targetField,
                    contentLength: content ? content.length : 0
                });

                // HugeRTE editor'ları tara
                if (typeof hugerte !== 'undefined') {
                    let targetEditor = null;

                    // Method 1: hugerte.editors array
                    if (hugerte.editors && Array.isArray(hugerte.editors)) {
                        targetEditor = hugerte.editors.find(ed =>
                            ed.id && (ed.id.includes(targetField) || ed.id.includes(currentLang))
                        );
                    }

                    // Method 2: hugerte.activeEditor
                    if (!targetEditor && hugerte.activeEditor) {
                        targetEditor = hugerte.activeEditor;
                    }

                    if (targetEditor && targetEditor.setContent) {
                        console.log('✅ HugeRTE editor bulundu:', targetEditor.id);
                        targetEditor.setContent(content);

                        // Livewire sync
                        const textareaElement = document.getElementById(targetEditor.id);
                        if (textareaElement) {
                            textareaElement.value = content;
                            textareaElement.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        console.log('✅ HugeRTE content güncellendi!');
                        return true;
                    }
                }

                // TinyMCE fallback
                if (typeof tinyMCE !== 'undefined' && tinyMCE.editors) {
                    const editorKeys = Object.keys(tinyMCE.editors);
                    const matchingKey = editorKeys.find(key =>
                        key.includes(targetField) || key.includes(currentLang)
                    );

                    if (matchingKey) {
                        const editor = tinyMCE.editors[matchingKey];
                        if (editor && editor.setContent) {
                            editor.setContent(content);
                            console.log('✅ TinyMCE content güncellendi!');
                            return true;
                        }
                    }
                }

                console.error('❌ Hiçbir editor bulunamadı');
                return false;
            } catch (e) {
                console.error('❌ updateTinyMCEContent error:', e);
                return false;
            }
        }

        /**
         * AI'dan gelen içeriği al ve işle
         */
        receiveGeneratedContent(content, targetField = 'body') {
            try {
                console.log('🎯 AI Content received:', {
                    content: content ? content.substring(0, 100) + '...' : 'empty',
                    targetField
                });

                // ÖNCE TinyMCE editörünü direkt güncelle
                this.updateTinyMCEContent(content, targetField);

                // SONRA Livewire component'i güncelle
                if (window.Livewire && window.Livewire.getByName) {
                    try {
                        // Dinamik component adı desteği
                        const moduleName = window.currentModuleName || 'page';
                        const componentName = `${moduleName}-manage-component`;

                        const component = window.Livewire.getByName(componentName)[0];
                        if (component && component.call) {
                            console.log(`✅ ${componentName} bulundu, receiveGeneratedContent çağırılıyor...`);
                            component.call('receiveGeneratedContent', content, targetField);
                            return;
                        }
                    } catch (e) {
                        console.warn('⚠️ Livewire method failed:', e);
                    }
                }

                console.error('❌ Component bulunamadı');
            } catch (e) {
                console.error('❌ receiveGeneratedContent error:', e);
            }
        }
    }

    // Global instance oluştur
    window.UniversalAIContentFunctions = new UniversalAIContentFunctions();

    // Global fonksiyonları expose et (eski kodlarla uyumluluk için)
    window.updateTinyMCEContent = (content, targetField = 'body') => {
        return window.UniversalAIContentFunctions.updateTinyMCEContent(content, targetField);
    };

    window.receiveGeneratedContent = (content, targetField = 'body') => {
        return window.UniversalAIContentFunctions.receiveGeneratedContent(content, targetField);
    };

    console.log('✅ Universal AI Content Functions yüklendi!');

})();