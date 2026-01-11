<?php

namespace Modules\WidgetManagement\app\Services\Widget;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class WidgetRenderService
{
    protected $_contextStack = [];
    const MAX_STACK_DEPTH = 10;
    // Log mesajlarını kapatmak için debugMode'u false yap
    protected $debugMode = false;
    protected $useHandlebars = true; // Handlebars kullanımı için bayrak

    private function debug($message, $data = [])
    {
        if ($this->debugMode) {
            Log::debug('[WidgetRender] ' . $message, $data);
        }
    }

    /**
     * HTML escape helper - XSS koruması
     */
    protected function escapeHtml($value)
    {
        // Sadece string değerleri escape et
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        // Numeric ve boolean değerleri olduğu gibi döndür
        return $value;
    }

    public function processVariables(string $content, array $settings): string
    {
        if ($this->useHandlebars) {
            // Handlebars şablonu için işleme yapmayız, çünkü
            // şablon browser tarafında Handlebars.js ile yorumlanacak
            return $content;
        }
    
        $this->debug('processVariables - Başlangıç', ['settings' => $settings]);
        
        $result = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($settings) {
            $key = trim($matches[1]);
            
            // Widget öneki baştan kaldırılıyor (widget.* formatında gelen değişkenler çalışsın)
            if (strpos($key, 'widget.') === 0) {
                $keyWithoutPrefix = substr($key, 7); // "widget." önekini çıkarır
                if (isset($settings[$key])) {
                    return $this->escapeHtml($settings[$key]);
                } elseif (isset($settings[$keyWithoutPrefix])) {
                    return $this->escapeHtml($settings[$keyWithoutPrefix]);
                }
            }
            
            if (strpos($key, '#') === 0 || strpos($key, '/') === 0) {
                $this->debug('processVariables - Kontrol bloğu atlanıyor', ['key' => $key]);
                return $matches[0];
            }
            
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $value = $settings;
                
                $this->debug('processVariables - Noktalı değişken', ['key' => $key, 'parts' => $parts]);
                
                // Ana tablonun adı
                $mainTableName = isset($settings['module']['name']) ? $settings['module']['name'] : '';
                
                // İlişki adı kontrol
                $relationName = $parts[0];
                
                // İlişkili tablo adı kontrol et
                $relatedTableName = null;
                if (isset($settings['meta']['table'])) {
                    foreach ($settings as $settingKey => $settingValue) {
                        if (is_array($settingValue) && $relationName === $settingKey) {
                            $relatedTableName = $settingKey;
                            break;
                        } elseif (is_array($settingValue) && Str::singular($relationName) === $settingKey) {
                            $relatedTableName = $settingKey;
                            break;
                        } elseif (is_array($settingValue) && Str::plural($relationName) === $settingKey) {
                            $relatedTableName = $settingKey;
                            break;
                        }
                    }
                }
                
                // İlişki adını düzenlenmiş ad olarak kontrol et
                if (isset($settings['_relationMappings']) && isset($settings['_relationMappings'][$relationName])) {
                    $relatedTableName = $settings['_relationMappings'][$relationName];
                }
                
                // Eğer ilişki adı bir tabloya eşleşiyorsa doğrudan o tablodan veri al
                if ($relatedTableName && isset($settings[$relatedTableName])) {
                    $value = $settings[$relatedTableName];
                    // İlk öğeyi al (tek bir kayıt için)
                    if (is_array($value) && !empty($value) && isset($value[0])) {
                        $value = $value[0];
                    }
                    
                    // İkinci parça (alan adı) ile devam
                    if (isset($parts[1]) && isset($value[$parts[1]])) {
                        $value = $value[$parts[1]];
                        $this->debug('processVariables - İlişki değeri bulundu', ['value' => $value]);
                        return is_scalar($value) ? $this->escapeHtml($value) : '';
                    }
                }

                // Normal yolla ilerlemeye devam et
                foreach ($parts as $part) {
                    if (isset($value[$part])) {
                        $value = $value[$part];
                        $this->debug('processVariables - Değer bulundu', ['part' => $part, 'value' => is_array($value) ? '[Array]' : $value]);
                    } else {
                        $this->debug('processVariables - Değer bulunamadı', ['part' => $part]);
                        return '';
                    }
                }

                return is_scalar($value) ? $this->escapeHtml($value) : '';
            }

            $this->debug('processVariables - Basit değişken', ['key' => $key, 'value' => isset($settings[$key]) ? $settings[$key] : 'YOK']);
            return $this->escapeHtml($settings[$key] ?? '');
        }, $content);
        
        $this->debug('processVariables - Sonuç', ['result_length' => strlen($result)]);
        return $result;
    }
    
    public function processItems(string $content, array $items): string
    {
        if ($this->useHandlebars) {
            // Handlebars için ek bir işlem yapmıyoruz
            // {{#each items}}...{{/each}} zaten Handlebars tarafından işlenecek
            return $content;
        }

        $this->debug('processItems - Başlangıç', ['items_count' => count($items)]);
        $pattern = '/\{\{#each\s+items\}\}(.*?)\{\{\/each\}\}/s';
        
        $result = preg_replace_callback($pattern, function ($matches) use ($items) {
            $itemTemplate = $matches[1];
            $result = '';
            
            $currentStack = $this->_contextStack ?? [];
            $this->debug('processItems - İçerik şablonu', ['template' => $itemTemplate]);
            
            foreach ($items as $index => $item) {
                $this->debug('processItems - İşleniyor', ['index' => $index, 'item' => array_keys($item)]);
                
                // Her öğeye meta bilgiler ekle
                $item['@first'] = $index === 0;
                $item['@last'] = $index === count($items) - 1;
                $item['@index'] = $index;
                
                $newStack = [...$currentStack, $item];
                $this->_contextStack = $newStack;
                
                $itemContent = $this->processTemplateItem($itemTemplate, $item);
                $result .= $itemContent;
                
                $this->debug('processItems - Öğe işlendi', ['index' => $index, 'content_length' => strlen($itemContent)]);
            }
            
            $this->_contextStack = $currentStack;
            
            return $result;
        }, $content);
        
        $this->debug('processItems - Sonuç', ['result_length' => strlen($result)]);
        return $result;
    }
    
    public function processModuleData(string $content, array $moduleData): string
    {
        if ($this->useHandlebars) {
            // Handlebars formatına uygun veri dönüşümü
            // Bu noktada moduleData zaten frontend'e gönderilecek ve Handlebars tarafından işlenecek
            return $content;
        }

        $this->debug('processModuleData - Başlangıç', ['keys' => array_keys($moduleData)]);
        $content = $this->processConditionalBlocks($content, $moduleData);
        
        // İç içe each bloklarını işlemek için önce tüm each bloklarını bul
        $pattern = '/\{\{#each\s+([\w\.]+)\}\}(.*?)\{\{\/each\}\}/s';
        
        $result = preg_replace_callback($pattern, function ($matches) use ($moduleData) {
            $collectionName = trim($matches[1]);
            $itemTemplate = $matches[2];
            $result = '';
            
            $this->debug('processModuleData - Koleksiyon işleniyor', ['collectionName' => $collectionName]);
            
            if ($collectionName != 'items') {
                $collection = $this->findCollectionByName($collectionName, $moduleData);
                $this->debug('processModuleData - Koleksiyon bulundu', ['collectionName' => $collectionName, 'count' => is_array($collection) ? count($collection) : 'NULL']);
                
                if (is_array($collection)) {
                    $currentStack = $this->_contextStack ?? [];
                    
                    if (count($currentStack) >= self::MAX_STACK_DEPTH) {
                        $this->debug('processModuleData - Maksimum stack derinliğine ulaşıldı');
                        return "";
                    }
                    
                    // İlişki eşleşmelerini kur
                    if (!isset($moduleData['_relationMappings'])) {
                        $moduleData['_relationMappings'] = [];
                    }
                    
                    // İlişki adını ve tablo adını eşle
                    $moduleData['_relationMappings'][$collectionName] = $collectionName;
                    
                    // Tekil ve çoğul formlar için de eşleşme ekle
                    $singularName = Str::singular($collectionName);
                    $pluralName = Str::plural($collectionName);
                    
                    if ($singularName !== $collectionName) {
                        $moduleData['_relationMappings'][$singularName] = $collectionName;
                    }
                    
                    if ($pluralName !== $collectionName) {
                        $moduleData['_relationMappings'][$pluralName] = $collectionName;
                    }
                    
                    // Kategori benzeri ilişkiler için özel işlem
                    if (Str::contains($collectionName, ['categor', 'kategor'])) {
                        $moduleData['_relationMappings']['category'] = $collectionName;
                        $moduleData['_relationMappings']['categories'] = $collectionName;
                    }
                    
                    foreach ($collection as $index => $item) {
                        $this->debug('processModuleData - Koleksiyon öğesi işleniyor', ['index' => $index, 'collectionName' => $collectionName]);
                        
                        // Her öğeye meta bilgiler ekle
                        $item['_parent'] = $moduleData;
                        $item['_index'] = $index;
                        $item['@first'] = $index === 0;
                        $item['@last'] = $index === count($collection) - 1;
                        $item['@index'] = $index;
                        
                        // Önemli: Koleksiyon adı altında kendisini ekle (ilişkili erişim için)
                        $item[$collectionName] = $item;
                        $this->debug('processModuleData - Koleksiyon adı altında kendisi eklendi', ['collectionName' => $collectionName]);
                        
                        // Ana objenin veri yapısını subobject olarak tüm öğelere aktar
                        foreach ($moduleData as $key => $value) {
                            if ($key !== $collectionName && $key !== '_contextStack' && is_array($value)) {
                                if (!isset($item[$key])) {
                                    $item[$key] = $value;
                                    $this->debug('processModuleData - Ana veri öğesi kopyalandı', ['key' => $key]);
                                }
                            }
                        }
                        
                        // İlişki eşleşmelerini alt öğelere de aktar
                        $item['_relationMappings'] = $moduleData['_relationMappings'];
                        
                        // Module meta verisi kopyala
                        if (isset($moduleData['module']) && !isset($item['module'])) {
                            $item['module'] = $moduleData['module'];
                        }
                        
                        $newStack = [...$currentStack, $item];
                        $this->_contextStack = $newStack;
                        
                        // İç içe each ve koşul bloklarını işle
                        $processedTemplate = $this->processNestedLoops($itemTemplate, $item);
                        $processedTemplate = $this->processConditionalBlocks($processedTemplate, $item);
                        $processedTemplate = $this->processTemplateItem($processedTemplate, $item);
                        $result .= $processedTemplate;
                        
                        $this->debug('processModuleData - Koleksiyon öğesi işlendi', ['index' => $index, 'result_length' => strlen($result)]);
                    }
                    
                    $this->_contextStack = $currentStack;
                } else {
                    $this->debug('processModuleData - Koleksiyon bulunamadı', ['collectionName' => $collectionName]);
                    $result = $this->processConditionalBlocks($matches[0], $moduleData);
                }
            }
            
            return $result;
        }, $content);
        
        $this->debug('processModuleData - Sonuç', ['result_length' => strlen($result)]);
        return $result;
    }
    
    protected function findCollectionByName($name, $data)
    {
        $this->debug('findCollectionByName - Başlangıç', ['name' => $name]);
        
        if (empty($name) || empty($data)) {
            $this->debug('findCollectionByName - Boş parametre', ['name' => $name, 'data_empty' => empty($data)]);
            return null;
        }
        
        if (strpos($name, '.') !== false) {
            $parts = explode('.', $name);
            $current = $data;
            
            $this->debug('findCollectionByName - Noktalı koleksiyon', ['name' => $name, 'parts' => $parts]);
            
            foreach ($parts as $part) {
                if (isset($current[$part])) {
                    $current = $current[$part];
                    $this->debug('findCollectionByName - Alt koleksiyon bulundu', ['part' => $part]);
                } else {
                    $this->debug('findCollectionByName - Alt koleksiyon bulunamadı', ['part' => $part]);
                    return null;
                }
            }
            
            return $current;
        }
        
        // Düzeltme: Çoğul/tekil isim varyasyonlarını da kontrol et
        $result = isset($data[$name]) ? $data[$name] : null;
        
        // Eğer doğrudan bulunamadıysa, çoğul/tekil formları kontrol et
        if ($result === null) {
            $pluralName = Str::plural($name);
            if ($pluralName !== $name && isset($data[$pluralName])) {
                $result = $data[$pluralName];
                $this->debug('findCollectionByName - Çoğul form bulundu', ['plural' => $pluralName]);
            }
            
            $singularName = Str::singular($name);
            if ($result === null && $singularName !== $name && isset($data[$singularName])) {
                $result = $data[$singularName];
                $this->debug('findCollectionByName - Tekil form bulundu', ['singular' => $singularName]);
            }
            
            // _relationMappings kullanarak arama
            if ($result === null && isset($data['_relationMappings']) && isset($data['_relationMappings'][$name])) {
                $mappedName = $data['_relationMappings'][$name];
                if (isset($data[$mappedName])) {
                    $result = $data[$mappedName];
                    $this->debug('findCollectionByName - İlişki eşleşmesi üzerinden bulundu', ['mapped' => $mappedName]);
                }
            }
        }
        
        $this->debug('findCollectionByName - Sonuç', ['name' => $name, 'found' => $result !== null]);
        return $result;
    }
    
    protected function processNestedLoops($template, $parentContext)
    {
        if ($this->useHandlebars) {
            // Handlebars için ek işlem yapmıyoruz
            // İç içe each blokları Handlebars tarafından işlenecek
            return $template;
        }

        $this->debug('processNestedLoops - Başlangıç', ['parent_context_keys' => array_keys($parentContext)]);
        $pattern = '/\{\{#each\s+([\w\.]+)\}\}(.*?)\{\{\/each\}\}/s';
        
        $result = preg_replace_callback($pattern, function ($matches) use ($parentContext) {
            $subCollectionName = trim($matches[1]);
            $subTemplate = $matches[2];
            $result = '';
            
            $this->debug('processNestedLoops - İç içe döngü işleniyor', ['subCollectionName' => $subCollectionName]);
            
            // İlk olarak, direkt olarak parent context içinde bu collection var mı kontrol et
            $subCollection = null;
            
            // 1. Direkt arama
            if (isset($parentContext[$subCollectionName]) && is_array($parentContext[$subCollectionName])) {
                $subCollection = $parentContext[$subCollectionName];
                $this->debug('processNestedLoops - Alt koleksiyon direkt bulundu', ['subCollectionName' => $subCollectionName]);
            }
            // 2. İlişki adıyla arama
            else if (strpos($subCollectionName, '.') !== false) {
                $subCollection = $this->findCollectionByName($subCollectionName, $parentContext);
                $this->debug('processNestedLoops - Alt koleksiyon noktalı notasyonla bulundu', ['subCollectionName' => $subCollectionName]);
            }
            // 3. Özel collection'ları kontrol et
            else {
                // items koleksiyonu için özel durum
                if ($subCollectionName === 'items' && isset($parentContext['items']) && is_array($parentContext['items'])) {
                    $subCollection = $parentContext['items'];
                    $this->debug('processNestedLoops - Alt koleksiyon items olarak bulundu');
                }
                
                // 4. Tüm parent verileri içinde arama
                if ($subCollection === null) {
                    $this->debug('processNestedLoops - Alt koleksiyon parent içinde aranıyor');
                    
                    // İlk olarak direkt erişim dene
                    if (isset($parentContext[$subCollectionName]) && is_array($parentContext[$subCollectionName])) {
                        $subCollection = $parentContext[$subCollectionName];
                        $this->debug('processNestedLoops - Alt koleksiyon direkt parent içinde bulundu', ['subCollectionName' => $subCollectionName]);
                    } else {
                        // Derinlemesine arama 
                        foreach ($parentContext as $key => $value) {
                            if (is_array($value)) {
                                // Birinci seviye kontrol (key = subCollectionName)
                                if ($key === $subCollectionName) {
                                    $subCollection = $value;
                                    $this->debug('processNestedLoops - Alt koleksiyon parent içinde key eşleşmesi ile bulundu', ['key' => $key]);
                                    break;
                                }
                                
                                // İkinci seviye kontrol (içerideki bir değer dizisi)
                                if (isset($value[$subCollectionName]) && is_array($value[$subCollectionName])) {
                                    $subCollection = $value[$subCollectionName];
                                    $this->debug('processNestedLoops - Alt koleksiyon parent içinde nested olarak bulundu', ['parent_key' => $key, 'subCollectionName' => $subCollectionName]);
                                    break;
                                }
                            }
                        }
                    }
                }
                
                // 5. Plural/tekil varyasyonları kontrol et
                if ($subCollection === null) {
                    $pluralName = Str::plural($subCollectionName);
                    $singularName = Str::singular($subCollectionName);
                    $this->debug('processNestedLoops - Alt koleksiyon plural/tekil varyasyonları kontrol ediliyor', ['plural' => $pluralName, 'singular' => $singularName]);
                    
                    if ($pluralName !== $subCollectionName && isset($parentContext[$pluralName])) {
                        $subCollection = $parentContext[$pluralName];
                        $this->debug('processNestedLoops - Alt koleksiyon çoğul formunda bulundu', ['pluralName' => $pluralName]);
                    } elseif ($singularName !== $subCollectionName && isset($parentContext[$singularName])) {
                        $subCollection = $parentContext[$singularName];
                        $this->debug('processNestedLoops - Alt koleksiyon tekil formunda bulundu', ['singularName' => $singularName]);
                    }
                    
                    // Ayrıca alt koleksiyonlarda da çoğul/tekil kontrolü yap
                    if ($subCollection === null) {
                        foreach ($parentContext as $key => $value) {
                            if (is_array($value)) {
                                if (isset($value[$pluralName]) && is_array($value[$pluralName])) {
                                    $subCollection = $value[$pluralName];
                                    $this->debug('processNestedLoops - Alt koleksiyon çoğul form olarak alt koleksiyonda bulundu', ['parent_key' => $key, 'pluralName' => $pluralName]);
                                    break;
                                } elseif (isset($value[$singularName]) && is_array($value[$singularName])) {
                                    $subCollection = $value[$singularName];
                                    $this->debug('processNestedLoops - Alt koleksiyon tekil form olarak alt koleksiyonda bulundu', ['parent_key' => $key, 'singularName' => $singularName]);
                                    break;
                                }
                            }
                        }
                    }
                }
                
                // 6. _relationMappings kullanarak arama
                if ($subCollection === null && isset($parentContext['_relationMappings'])) {
                    $mappings = $parentContext['_relationMappings'];
                    if (isset($mappings[$subCollectionName])) {
                        $mappedName = $mappings[$subCollectionName];
                        if (isset($parentContext[$mappedName])) {
                            $subCollection = $parentContext[$mappedName];
                            $this->debug('processNestedLoops - Alt koleksiyon ilişki eşleşmesi ile bulundu', ['mappedName' => $mappedName]);
                        } else {
                            // Diğer dizi seviyelerinde de arama yap
                            foreach ($parentContext as $key => $value) {
                                if (is_array($value) && isset($value[$mappedName]) && is_array($value[$mappedName])) {
                                    $subCollection = $value[$mappedName];
                                    $this->debug('processNestedLoops - Alt koleksiyon ilişki eşleşmesi ile alt seviyede bulundu', ['parent_key' => $key, 'mappedName' => $mappedName]);
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Özel durumlar - relation mapping çoğul/tekil formları da dene
                    if ($subCollection === null) {
                        $pluralMappedName = Str::plural($subCollectionName);
                        $singularMappedName = Str::singular($subCollectionName);
                        
                        if (isset($mappings[$pluralMappedName])) {
                            $mappedName = $mappings[$pluralMappedName];
                            if (isset($parentContext[$mappedName])) {
                                $subCollection = $parentContext[$mappedName];
                                $this->debug('processNestedLoops - Alt koleksiyon ilişki eşleşmesi çoğul form ile bulundu', ['pluralMappedName' => $pluralMappedName, 'mappedName' => $mappedName]);
                            }
                        } else if (isset($mappings[$singularMappedName])) {
                            $mappedName = $mappings[$singularMappedName];
                            if (isset($parentContext[$mappedName])) {
                                $subCollection = $parentContext[$mappedName];
                                $this->debug('processNestedLoops - Alt koleksiyon ilişki eşleşmesi tekil form ile bulundu', ['singularMappedName' => $singularMappedName, 'mappedName' => $mappedName]);
                            }
                        }
                    }
                }
                
                // 7. _parent içinde arama
                if ($subCollection === null && isset($parentContext['_parent'])) {
                    $parent = $parentContext['_parent'];
                    if (isset($parent[$subCollectionName]) && is_array($parent[$subCollectionName])) {
                        $subCollection = $parent[$subCollectionName];
                        $this->debug('processNestedLoops - Alt koleksiyon üst kontekstte bulundu', ['subCollectionName' => $subCollectionName]);
                    } else if (isset($parent['_relationMappings']) && isset($parent['_relationMappings'][$subCollectionName])) {
                        $mappedName = $parent['_relationMappings'][$subCollectionName];
                        if (isset($parent[$mappedName])) {
                            $subCollection = $parent[$mappedName];
                            $this->debug('processNestedLoops - Alt koleksiyon üst kontekst ilişki eşleşmesi ile bulundu', ['mappedName' => $mappedName]);
                        }
                    }
                    
                    // _parent'ın diğer dizi seviyelerinde de arama yap
                    if ($subCollection === null) {
                        foreach ($parent as $key => $value) {
                            if (is_array($value) && isset($value[$subCollectionName]) && is_array($value[$subCollectionName])) {
                                $subCollection = $value[$subCollectionName];
                                $this->debug('processNestedLoops - Alt koleksiyon _parent alt seviyesinde bulundu', ['parent_key' => $key, 'subCollectionName' => $subCollectionName]);
                                break;
                            }
                        }
                    }
                }
                
                // 8. Özel durumlar - Ana tablo ve alt tabloları kontrol et
                if ($subCollection === null && isset($parentContext['meta']) && isset($parentContext['meta']['table'])) {
                    $mainTable = $parentContext['meta']['table'];
                    $mainTablePlural = Str::plural($mainTable);
                    
                    // Ana tablo adı aynısı mı diye kontrol et
                    if ($subCollectionName == $mainTable || $subCollectionName == $mainTablePlural) {
                        if (isset($parentContext[$mainTable])) {
                            $subCollection = $parentContext[$mainTable];
                            $this->debug('processNestedLoops - Alt koleksiyon ana tablo eşleşmesi olarak bulundu', ['mainTable' => $mainTable]);
                        }
                    }
                }
                
                // 9. Ana tablodaki ilişkiler
                if ($subCollection === null && isset($parentContext['meta']) && isset($parentContext[$subCollectionName])) {
                    $subCollection = $parentContext[$subCollectionName];
                    $this->debug('processNestedLoops - Alt koleksiyon ana tabloda bulundu', ['subCollectionName' => $subCollectionName]);
                }
            }
            
            // Koleksiyon bir dizi değil, tek bir nesne ise onu diziye çevir
            if ($subCollection !== null && !is_null($subCollection) && !is_array($subCollection)) {
                $subCollection = [$subCollection];
                $this->debug('processNestedLoops - Tek nesne diziye çevrildi');
            }
            
            if (is_array($subCollection) && !empty($subCollection)) {
                $this->debug('processNestedLoops - Alt koleksiyon bulundu', ['count' => count($subCollection)]);
                $currentStack = $this->_contextStack ?? [];
                
                if (count($currentStack) >= self::MAX_STACK_DEPTH) {
                    $this->debug('processNestedLoops - Maksimum stack derinliğine ulaşıldı');
                    return "";
                }
                
                // İlişki eşleşmelerini kur
                if (!isset($parentContext['_relationMappings'])) {
                    $parentContext['_relationMappings'] = [];
                }
                
                // İlişki adını ve tablo adını eşle
                $parentContext['_relationMappings'][$subCollectionName] = $subCollectionName;
                
                // Tekil ve çoğul formlar için de eşleşme ekle
                $singularName = Str::singular($subCollectionName);
                $pluralName = Str::plural($subCollectionName);
                
                if ($singularName !== $subCollectionName) {
                    $parentContext['_relationMappings'][$singularName] = $subCollectionName;
                }
                
                if ($pluralName !== $subCollectionName) {
                    $parentContext['_relationMappings'][$pluralName] = $subCollectionName;
                }
                
                foreach ($subCollection as $index => $subItem) {
                    // Eğer alt öğe bir dizi değilse çevir
                    if (!is_array($subItem)) {
                        $subItem = ['value' => $subItem];
                    }
                    
                    // Her öğeye meta bilgiler ekle
                    $subItem['_parent'] = $parentContext;
                    $subItem['_index'] = $index;
                    $subItem['@first'] = $index === 0;
                    $subItem['@last'] = $index === count($subCollection) - 1;
                    $subItem['@index'] = $index;
                    
                    // ÖNEMLİ: Koleksiyon adı altında kendisini ekle (ilişkili erişim için)
                    $subItem[$subCollectionName] = $subItem;
                    
                    // Hem tekil hem çoğul formlar için referans ekleyelim
                    $subItem[$singularName] = $subItem;
                    $subItem[$pluralName] = $subItem;
                    
                    $this->debug('processNestedLoops - Alt öğe işleniyor', ['index' => $index, 'subItem_keys' => array_keys($subItem)]);
                    
                    // İlişki eşleşmelerini alt öğelere de aktar
                    $subItem['_relationMappings'] = $parentContext['_relationMappings'];
                    
                    // Context'i stack'e ekle
                    $newStack = [...$currentStack, $subItem];
                    $this->_contextStack = $newStack;
                    
                    // İç içe each ve koşul bloklarını işle
                    $processedSubTemplate = $this->processNestedLoops($subTemplate, $subItem);
                    $processedSubTemplate = $this->processConditionalBlocks($processedSubTemplate, $subItem);
                    $processedSubTemplate = $this->processTemplateItem($processedSubTemplate, $subItem);
                    $result .= $processedSubTemplate;
                    
                    $this->debug('processNestedLoops - Alt öğe işlendi', ['index' => $index]);
                }
                
                $this->_contextStack = $currentStack;
            } else {
                // Collection bulunamadıysa koşul bloklarını işleyelim ve boş döndürelim
                $this->debug('processNestedLoops - Alt koleksiyon bulunamadı veya boş, şartlı bloklar işleniyor', ['subCollectionName' => $subCollectionName]);
                return '';
            }
            
            return $result;
        }, $template);
        
        $this->debug('processNestedLoops - Sonuç', ['result_length' => strlen($result)]);
        return $result;
    }
    
    public function processTemplateItem($template, $item)
    {
        if ($this->useHandlebars) {
            // Handlebars için ek işlem yapmıyoruz
            return $template;
        }

        $this->debug('processTemplateItem - Başlangıç', ['item_keys' => array_keys($item)]);
        
        $result = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($item) {
            $key = trim($matches[1]);
            
            // Özel tag'ler için atla (each, if, vb.)
            if (strpos($key, '#') === 0 || strpos($key, '/') === 0) {
                $this->debug('processTemplateItem - Kontrol bloğu atlanıyor', ['key' => $key]);
                return $matches[0];
            }
            
            // Parent verilerine erişim için "../" notasyonu
            if (strpos($key, '../') === 0) {
                $parentLevel = substr_count($key, '../');
                $remainingKey = trim(substr($key, $parentLevel * 3));
                $this->debug('processTemplateItem - Parent erişimi', ['parentLevel' => $parentLevel, 'remainingKey' => $remainingKey]);
                
                if (isset($item['_parent'])) {
                    $this->debug('processTemplateItem - Parent verisi var');
                    $parent = $item['_parent'];

                    // Kalan anahtar için parent içinde arama yap
                    if (isset($parent[$remainingKey])) {
                        $value = $parent[$remainingKey];
                        $this->debug('processTemplateItem - Parent değeri bulundu', ['value' => is_scalar($value) ? $value : '[Object]']);
                        return is_scalar($value) ? $this->escapeHtml($value) : '';
                    }
                }

                return '';
            }

            // Özel durum: @first, @last, @index gibi meta değerler
            if (strpos($key, '@') === 0) {
                $this->debug('processTemplateItem - Meta değer', ['key' => $key, 'value' => isset($item[$key]) ? $item[$key] : 'YOK']);
                return isset($item[$key]) ? $this->escapeHtml($item[$key]) : '';
            }
            
            // İlişki notasyonu (table_name.title gibi)
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $relationName = $parts[0];
                $fieldName = $parts[1];
                
                $this->debug('processTemplateItem - İlişki erişimi', ['relationName' => $relationName, 'fieldName' => $fieldName]);
                
                // 1. Direkt olarak ilişki adıyla erişim
                if (isset($item[$relationName]) && is_array($item[$relationName])) {
                    // İlişki bir dizi elemanı mı yoksa bir koleksiyon mu?
                    if (isset($item[$relationName][0]) && is_array($item[$relationName][0])) {
                        // Bir koleksiyon - ilk elemanı kullan
                        if (isset($item[$relationName][0][$fieldName])) {
                            $value = $item[$relationName][0][$fieldName];
                            $this->debug('processTemplateItem - İlişki koleksiyon ilk elemanı değeri bulundu', ['value' => $value]);
                            return is_scalar($value) ? $this->escapeHtml($value) : '';
                        }
                    } else if (isset($item[$relationName][$fieldName])) {
                        // Direkt obje erişimi
                        $value = $item[$relationName][$fieldName];
                        $this->debug('processTemplateItem - İlişki değeri bulundu', ['value' => $value]);
                        return is_scalar($value) ? $this->escapeHtml($value) : '';
                    }
                }

                $this->debug('processTemplateItem - İlişki direkt bulunamadı, alternatif aranıyor', ['relationName' => $relationName, 'fieldName' => $fieldName]);

                // 2. Ana veride doğrudan erişim
                $value = $item;
                foreach ($parts as $part) {
                    if (is_array($value) && isset($value[$part])) {
                        $value = $value[$part];
                    } elseif (is_object($value) && isset($value->{$part})) {
                        $value = $value->{$part};
                    } else {
                        $value = null;
                        break;
                    }
                }

                if ($value !== null && is_scalar($value)) {
                    $this->debug('processTemplateItem - Değer ana veride bulundu', ['value' => $value]);
                    return $this->escapeHtml($value);
                }
                
                // 3. Tekil/çoğul formatları dene
                $singularRelation = Str::singular($relationName);
                $pluralRelation = Str::plural($relationName);

                if ($relationName !== $singularRelation && isset($item[$singularRelation]) && is_array($item[$singularRelation])) {
                    if (isset($item[$singularRelation][$fieldName])) {
                        $value = $item[$singularRelation][$fieldName];
                        $this->debug('processTemplateItem - İlişki tekil formda bulundu', ['singularRelation' => $singularRelation, 'value' => $value]);
                        return is_scalar($value) ? $this->escapeHtml($value) : '';
                    }
                }

                if ($relationName !== $pluralRelation && isset($item[$pluralRelation]) && is_array($item[$pluralRelation])) {
                    // Çoğul form bir koleksiyon olabilir
                    if (isset($item[$pluralRelation][0]) && is_array($item[$pluralRelation][0])) {
                        if (isset($item[$pluralRelation][0][$fieldName])) {
                            $value = $item[$pluralRelation][0][$fieldName];
                            $this->debug('processTemplateItem - İlişki çoğul form koleksiyon ilk elemanı değeri bulundu', ['value' => $value]);
                            return is_scalar($value) ? $this->escapeHtml($value) : '';
                        }
                    } else if (isset($item[$pluralRelation][$fieldName])) {
                        $value = $item[$pluralRelation][$fieldName];
                        $this->debug('processTemplateItem - İlişki çoğul formda bulundu', ['pluralRelation' => $pluralRelation, 'value' => $value]);
                        return is_scalar($value) ? $this->escapeHtml($value) : '';
                    }
                }
                
                // 4. _parent'ta arama
                if (isset($item['_parent']) && isset($item['_parent'][$relationName])) {
                    $this->debug('processTemplateItem - Parent içinde aranıyor', ['relationName' => $relationName]);

                    if (is_array($item['_parent'][$relationName])) {
                        // İlişki bir koleksiyon mu?
                        if (isset($item['_parent'][$relationName][0]) && is_array($item['_parent'][$relationName][0])) {
                            foreach ($item['_parent'][$relationName] as $parentItem) {
                                if (isset($parentItem[$fieldName])) {
                                    $value = $parentItem[$fieldName];
                                    $this->debug('processTemplateItem - Parent koleksiyonu içinde bulundu', ['value' => $value]);
                                    return is_scalar($value) ? $this->escapeHtml($value) : '';
                                }
                            }
                        }
                        // İlişki tek bir nesne mi?
                        else if (isset($item['_parent'][$relationName][$fieldName])) {
                            $value = $item['_parent'][$relationName][$fieldName];
                            $this->debug('processTemplateItem - Parent objesi içinde bulundu', ['value' => $value]);
                            return is_scalar($value) ? $this->escapeHtml($value) : '';
                        }
                    }
                }

                // 5. Çoğul/Tekil form varyasyonlarını kontrol et
                $pluralRelationName = Str::plural($relationName);
                if ($pluralRelationName !== $relationName && isset($item[$pluralRelationName])) {
                    if (is_array($item[$pluralRelationName])) {
                        if (isset($item[$pluralRelationName][$fieldName])) {
                            $value = $item[$pluralRelationName][$fieldName];
                            $this->debug('processTemplateItem - Çoğul formda bulundu', ['value' => $value]);
                            return is_scalar($value) ? $this->escapeHtml($value) : '';
                        }
                    }
                }

                $singularRelationName = Str::singular($relationName);
                if ($singularRelationName !== $relationName && isset($item[$singularRelationName])) {
                    if (is_array($item[$singularRelationName])) {
                        if (isset($item[$singularRelationName][$fieldName])) {
                            $value = $item[$singularRelationName][$fieldName];
                            $this->debug('processTemplateItem - Tekil formda bulundu', ['value' => $value]);
                            return is_scalar($value) ? $this->escapeHtml($value) : '';
                        }
                    }
                }
                
                // 6. İlişki eşleşmelerini kontrol et
                if (isset($item['_relationMappings']) && isset($item['_relationMappings'][$relationName])) {
                    $mappedName = $item['_relationMappings'][$relationName];
                    if (isset($item[$mappedName]) && is_array($item[$mappedName])) {
                        if (isset($item[$mappedName][$fieldName])) {
                            $value = $item[$mappedName][$fieldName];
                            $this->debug('processTemplateItem - İlişki eşleşmesi ile bulundu', ['mappedName' => $mappedName, 'value' => $value]);
                            return is_scalar($value) ? $this->escapeHtml($value) : '';
                        }

                        // Koleksiyon olabilir mi?
                        if (isset($item[$mappedName][0]) && is_array($item[$mappedName][0])) {
                            if (isset($item[$mappedName][0][$fieldName])) {
                                $value = $item[$mappedName][0][$fieldName];
                                $this->debug('processTemplateItem - İlişki eşleşmesi ile koleksiyon ilk elemanında bulundu', ['mappedName' => $mappedName, 'value' => $value]);
                                return is_scalar($value) ? $this->escapeHtml($value) : '';
                            }
                        }
                    }
                }

                $this->debug('processTemplateItem - İlişki değeri hiçbir yerde bulunamadı', ['key' => $key]);
                return '';
            }

            // Basit değişken erişimi
            if (is_array($item) && isset($item[$key])) {
                $value = $item[$key];
                $this->debug('processTemplateItem - Basit değişken bulundu', ['key' => $key, 'value' => is_array($value) ? '[Array]' : (is_scalar($value) ? $value : '[Object]')]);
                return is_scalar($value) ? $this->escapeHtml($value) : '';
            } elseif (is_object($item) && isset($item->{$key})) {
                $value = $item->{$key};
                $this->debug('processTemplateItem - Nesne özelliği bulundu', ['key' => $key, 'value' => is_scalar($value) ? $value : '[Object]']);
                return is_scalar($value) ? $this->escapeHtml($value) : '';
            }
            
            $this->debug('processTemplateItem - Değişken bulunamadı', ['key' => $key]);
            return '';
        }, $template);
        
        $this->debug('processTemplateItem - Sonuç', ['result_length' => strlen($result)]);
        return $result;
    }
    
    public function processConditionalBlocks(string $content, array $settings): string
    {
        if ($this->useHandlebars) {
            // Handlebars için ek işlem yapmıyoruz
            // Koşul blokları Handlebars tarafından işlenecek
            return $content;
        }

        $this->debug('processConditionalBlocks - Başlangıç', ['settings_keys' => array_keys($settings)]);
        $pattern = '/\{\{#if\s+(.*?)\}\}(.*?)(?:\{\{else\}\}(.*?))?\{\{\/if\}\}/s';
        
        $result = preg_replace_callback($pattern, function ($matches) use ($settings) {
            $condition = trim($matches[1]);
            $trueContent = $matches[2];
            $falseContent = $matches[3] ?? '';
            
            $this->debug('processConditionalBlocks - Koşul işleniyor', ['condition' => $condition]);
            
            // Özel koleksiyon kontrolü
            if (isset($settings[$condition]) && is_array($settings[$condition]) && !empty($settings[$condition])) {
                $this->debug('processConditionalBlocks - Koleksiyon koşulu TRUE', ['condition' => $condition]);
                $result = true;
            } else {
                // Çoğul/tekil form kontrolü
                $pluralCondition = Str::plural($condition);
                $singularCondition = Str::singular($condition);
                
                if ($pluralCondition !== $condition && isset($settings[$pluralCondition]) && is_array($settings[$pluralCondition]) && !empty($settings[$pluralCondition])) {
                    $this->debug('processConditionalBlocks - Çoğul form koleksiyon koşulu TRUE', ['condition' => $condition, 'plural' => $pluralCondition]);
                    $result = true;
                } elseif ($singularCondition !== $condition && isset($settings[$singularCondition]) && is_array($settings[$singularCondition]) && !empty($settings[$singularCondition])) {
                    $this->debug('processConditionalBlocks - Tekil form koleksiyon koşulu TRUE', ['condition' => $condition, 'singular' => $singularCondition]);
                    $result = true;
                } else {
                    // İlişki eşleşmeleri kontrolü
                    if (isset($settings['_relationMappings']) && isset($settings['_relationMappings'][$condition])) {
                        $mappedName = $settings['_relationMappings'][$condition];
                        if (isset($settings[$mappedName]) && is_array($settings[$mappedName]) && !empty($settings[$mappedName])) {
                            $this->debug('processConditionalBlocks - İlişki eşleşmesi koleksiyon koşulu TRUE', ['condition' => $condition, 'mapped' => $mappedName]);
                            $result = true;
                        } else {
                            $this->debug('processConditionalBlocks - İlişki eşleşmesi koleksiyon koşulu FALSE', ['condition' => $condition, 'mapped' => $mappedName]);
                            $result = false;
                        }
                    } else {
                        $this->debug('processConditionalBlocks - Koleksiyon koşulu FALSE', ['condition' => $condition]);
                        $result = false;
                    }
                }
            }
            
            if (strpos($condition, '.') !== false) {
                // Noktalı erişim koşulu
                $parts = explode('.', $condition);
                $obj = $settings;
                $valid = true;
                
                foreach ($parts as $part) {
                    if (isset($obj[$part])) {
                        $obj = $obj[$part];
                    } else {
                        $valid = false;
                        break;
                    }
                }
                
                if ($valid) {
                    if (is_array($obj)) {
                        $result = !empty($obj);
                    } else {
                        $result = $obj ? true : false;
                    }
                } else {
                    $result = false;
                }
            } else if (!isset($settings[$condition]) && !isset($result)) {
                // Basit değişken kontrolü
                if (isset($settings[$condition])) {
                    if (is_array($settings[$condition])) {
                        $result = !empty($settings[$condition]);
                    } else {
                        $result = $settings[$condition] ? true : false;
                    }
                } else {
                    $result = false;
                }
            }
            
            $this->debug('processConditionalBlocks - Koşul sonucu', ['condition' => $condition, 'result' => $result]);
            return $result ? $trueContent : $falseContent;
        }, $content);
        
        $this->debug('processConditionalBlocks - Sonuç', ['result_length' => strlen($result)]);
        return $result;
    }

    // Handlebars için JSON verisine uygun şekilde dönüştürme
    public function prepareDataForHandlebars($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        // Handlebars'ın daha kolay kullanabileceği formata dönüştürme işlemleri
        // Kullanılan özel meta verileri temizleyebiliriz
        $result = [];
        foreach ($data as $key => $value) {
            // İç içe dizileri de dönüştür
            if (is_array($value)) {
                $result[$key] = $this->prepareDataForHandlebars($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    // Handlebars kullanımını açıp kapatmak için metot
    public function setUseHandlebars($useHandlebars = true)
    {
        $this->useHandlebars = $useHandlebars;
        return $this;
    }
}