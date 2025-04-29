<script src="{{ asset('admin/libs/handlebars/handlebars.min.js') }}?v={{ filemtime(public_path('admin/libs/handlebars/handlebars.min.js')) }}"></script>

<div id="widget-embed-content-{{ $tenantWidgetId }}">
    <script id="widget-template-{{ $tenantWidgetId }}" type="text/x-handlebars-template">
        {!! $widget->content_html !!}
    </script>

    <!-- Handlebars Helper fonksiyonları -->
    <script>
        // Handlebars helper fonksiyonları tanımlama
        if (typeof Handlebars !== 'undefined') {
            // Helper'lar zaten tanımlanmış mı kontrol et
            if (!Handlebars.helpers.eq) {
                Handlebars.registerHelper('eq', function(v1, v2, options) {
                    if (arguments.length < 3)
                        throw new Error("Handlebars Helper 'eq' ihtiyaç duyduğu parametreleri almadı");
                    return v1 === v2 ? options.fn(this) : options.inverse(this);
                });
            }
            
            if (!Handlebars.helpers.ne) {
                Handlebars.registerHelper('ne', function(v1, v2, options) {
                    if (arguments.length < 3)
                        throw new Error("Handlebars Helper 'ne' ihtiyaç duyduğu parametreleri almadı");
                    return v1 !== v2 ? options.fn(this) : options.inverse(this);
                });
            }
            
            if (!Handlebars.helpers.lt) {
                Handlebars.registerHelper('lt', function(v1, v2, options) {
                    if (arguments.length < 3)
                        throw new Error("Handlebars Helper 'lt' ihtiyaç duyduğu parametreleri almadı");
                    return v1 < v2 ? options.fn(this) : options.inverse(this);
                });
            }
            
            if (!Handlebars.helpers.gt) {
                Handlebars.registerHelper('gt', function(v1, v2, options) {
                    if (arguments.length < 3)
                        throw new Error("Handlebars Helper 'gt' ihtiyaç duyduğu parametreleri almadı");
                    return v1 > v2 ? options.fn(this) : options.inverse(this);
                });
            }
            
            if (!Handlebars.helpers.lte) {
                Handlebars.registerHelper('lte', function(v1, v2, options) {
                    if (arguments.length < 3)
                        throw new Error("Handlebars Helper 'lte' ihtiyaç duyduğu parametreleri almadı");
                    return v1 <= v2 ? options.fn(this) : options.inverse(this);
                });
            }
            
            if (!Handlebars.helpers.gte) {
                Handlebars.registerHelper('gte', function(v1, v2, options) {
                    if (arguments.length < 3)
                        throw new Error("Handlebars Helper 'gte' ihtiyaç duyduğu parametreleri almadı");
                    return v1 >= v2 ? options.fn(this) : options.inverse(this);
                });
            }
            
            if (!Handlebars.helpers.json) {
                Handlebars.registerHelper('json', function(context) {
                    return JSON.stringify(context);
                });
            }
            
            if (!Handlebars.helpers.truncate) {
                Handlebars.registerHelper('truncate', function(str, len) {
                    if (!str || !len) {
                        return str;
                    }
                    if (str.length > len) {
                        return str.substring(0, len) + '...';
                    }
                    return str;
                });
            }
            
            if (!Handlebars.helpers.formatDate) {
                Handlebars.registerHelper('formatDate', function(date, format) {
                    // Basit tarih biçimlendirme
                    if (!date) return '';
                    var d = new Date(date);
                    if (isNaN(d.getTime())) return date;
                    
                    var day = d.getDate().toString().padStart(2, '0');
                    var month = (d.getMonth() + 1).toString().padStart(2, '0');
                    var year = d.getFullYear();
                    
                    return day + '.' + month + '.' + year;
                });
            }
        }
    </script>

    <div id="widget-content-{{ $tenantWidgetId }}"></div>

    <script>
        (function() {
            // Handlebars'ın yüklenip yüklenmediğini kontrol et
            if (typeof Handlebars === 'undefined') {
                console.error("Handlebars yüklenmemiş! Widget render edilemeyecek.");
                document.getElementById('widget-content-{{ $tenantWidgetId }}').innerHTML = '<div class="alert alert-danger">Handlebars kütüphanesi bulunamadı!</div>';
                return;
            }
            
            try {
                const templateElement = document.getElementById('widget-template-{{ $tenantWidgetId }}');
                const contentElement = document.getElementById('widget-content-{{ $tenantWidgetId }}');
                
                if (templateElement && contentElement) {
                    const data = {!! json_encode($context) !!};
                    const template = Handlebars.compile(templateElement.innerHTML);
                    const html = template(data);
                    contentElement.innerHTML = html;
                    console.log('Widget #{{ $tenantWidgetId }} içeriği başarıyla yüklendi');
                } else {
                    console.error('Widget #{{ $tenantWidgetId }} template veya content elementi bulunamadı');
                }
            } catch (error) {
                console.error('Widget render hatası:', error);
                document.getElementById('widget-content-{{ $tenantWidgetId }}').innerHTML = '<div class="alert alert-danger">Widget render hatası: ' + error.message + '</div>';
            }
        })();
    </script>
</div>

@if(!empty($widget->content_css))
<style>
    {!! $widget->content_css !!}
</style>
@endif

@if(!empty($widget->content_js))
<script>
    (function() {
        try {
            {!! $widget->content_js !!}
        } catch (error) {
            console.error('Widget JS hatası:', error);
        }
    })();
</script>
@endif