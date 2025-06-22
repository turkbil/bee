<?php

namespace Modules\WidgetManagement\App\Support;

use Illuminate\Support\Facades\Log;

class HandlebarsRenderer
{
    /**
     * Handlebars şablonunu render et
     *
     * @param string $template Handlebars şablon metni
     * @param array $context Şablon değişkenleri
     * @return string Render edilmiş HTML
     */
    public function render(string $template, array $context = []): string
    {
        try {
            // Client tarafında işlenecek formatı hazırla
            $scriptId = 'handlebars-' . md5($template);
            
            // İçeriği client tarafında render edeceğimiz için JS üretelim
            $html = '<div id="' . $scriptId . '-output"></div>';
            $html .= '<script>';
            $html .= '(function() {';
            $html .= '  var source = `' . str_replace('`', '\`', $template) . '`;';
            $html .= '  var context = ' . json_encode($context) . ';';
            $html .= '  if (typeof Handlebars !== "undefined") {';
            $html .= '    var template = Handlebars.compile(source);';
            $html .= '    var html = template(context);';
            $html .= '    document.getElementById("' . $scriptId . '-output").innerHTML = html;';
            $html .= '  } else {';
            $html .= '    console.error("Handlebars kütüphanesi yüklenmemiş!");';
            $html .= '    document.getElementById("' . $scriptId . '-output").innerHTML = "Widget yüklenirken hata oluştu."';
            $html .= '  }';
            $html .= '})();';
            $html .= '</script>';
            
            return $html;
        } catch (\Exception $e) {
            Log::error("Handlebars şablonu render hatası: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Handlebars helpers tanımla ve ekle
     * 
     * @return string Helper tanımları içeren JS
     */
    public static function getHelperScripts(): string
    {
        return '
        <script>
        // Handlebars yardımcı fonksiyonları
        if (typeof Handlebars !== "undefined") {
            // Eşitlik kontrolü
            Handlebars.registerHelper("eq", function(v1, v2, options) {
                if(v1 === v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
            
            // Eşit değil kontrolü
            Handlebars.registerHelper("ne", function(v1, v2, options) {
                if(v1 !== v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
            
            // Küçüktür kontrolü
            Handlebars.registerHelper("lt", function(v1, v2, options) {
                if(v1 < v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
            
            // Büyüktür kontrolü
            Handlebars.registerHelper("gt", function(v1, v2, options) {
                if(v1 > v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
            
            // Küçük eşittir kontrolü
            Handlebars.registerHelper("lte", function(v1, v2, options) {
                if(v1 <= v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
            
            // Büyük eşittir kontrolü
            Handlebars.registerHelper("gte", function(v1, v2, options) {
                if(v1 >= v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
            
            // VE operatörü
            Handlebars.registerHelper("and", function() {
                var options = arguments[arguments.length - 1];
                for (var i = 0; i < arguments.length - 1; i++) {
                    if (!arguments[i]) {
                        return options.inverse(this);
                    }
                }
                return options.fn(this);
            });
            
            // VEYA operatörü
            Handlebars.registerHelper("or", function() {
                var options = arguments[arguments.length - 1];
                for (var i = 0; i < arguments.length - 1; i++) {
                    if (arguments[i]) {
                        return options.fn(this);
                    }
                }
                return options.inverse(this);
            });
            
            // Metin kısaltma
            Handlebars.registerHelper("truncate", function(str, len) {
                if (!str || !len) {
                    return str;
                }
                if (str.length > len) {
                    return str.substring(0, len) + "...";
                }
                return str;
            });
            
            // Tarih formatla
            Handlebars.registerHelper("formatDate", function(date, format) {
                if (!date) return "";
                
                var d = new Date(date);
                if (isNaN(d.getTime())) return date;
                
                var day = ("0" + d.getDate()).slice(-2);
                var month = ("0" + (d.getMonth() + 1)).slice(-2);
                var year = d.getFullYear();
                
                return day + "." + month + "." + year;
            });
            
            // JSON formatla
            Handlebars.registerHelper("json", function(context) {
                return JSON.stringify(context);
            });
        }
        </script>
        ';
    }
}