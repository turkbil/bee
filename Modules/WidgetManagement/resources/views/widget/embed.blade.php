<script src="{{ asset('admin/libs/handlebars/handlebars.min.js') }}?v={{ filemtime(public_path('admin/libs/handlebars/handlebars.min.js')) }}"></script>

<div id="widget-embed-content-{{ $tenantWidgetId }}">
    <script id="widget-template-{{ $tenantWidgetId }}" type="text/x-handlebars-template">
        {!! $widget->content_html !!}
    </script>

    <!-- Handlebars Helper fonksiyonları -->
    <script>
        // Handlebars helper fonksiyonları tanımlama
        Handlebars.registerHelper('eq', function(v1, v2, options) {
            if (arguments.length < 3)
                throw new Error("Handlebars Helper 'eq' ihtiyaç duyduğu parametreleri almadı");
            return v1 === v2 ? options.fn(this) : options.inverse(this);
        });
        
        Handlebars.registerHelper('ne', function(v1, v2, options) {
            if (arguments.length < 3)
                throw new Error("Handlebars Helper 'ne' ihtiyaç duyduğu parametreleri almadı");
            return v1 !== v2 ? options.fn(this) : options.inverse(this);
        });
        
        Handlebars.registerHelper('lt', function(v1, v2, options) {
            if (arguments.length < 3)
                throw new Error("Handlebars Helper 'lt' ihtiyaç duyduğu parametreleri almadı");
            return v1 < v2 ? options.fn(this) : options.inverse(this);
        });
        
        Handlebars.registerHelper('gt', function(v1, v2, options) {
            if (arguments.length < 3)
                throw new Error("Handlebars Helper 'gt' ihtiyaç duyduğu parametreleri almadı");
            return v1 > v2 ? options.fn(this) : options.inverse(this);
        });
        
        Handlebars.registerHelper('lte', function(v1, v2, options) {
            if (arguments.length < 3)
                throw new Error("Handlebars Helper 'lte' ihtiyaç duyduğu parametreleri almadı");
            return v1 <= v2 ? options.fn(this) : options.inverse(this);
        });
        
        Handlebars.registerHelper('gte', function(v1, v2, options) {
            if (arguments.length < 3)
                throw new Error("Handlebars Helper 'gte' ihtiyaç duyduğu parametreleri almadı");
            return v1 >= v2 ? options.fn(this) : options.inverse(this);
        });
        
        Handlebars.registerHelper('truncate', function(str, len) {
            if (!str || !len) {
                return str;
            }
            if (str.length > len) {
                return str.substring(0, len) + '...';
            }
            return str;
        });
        
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
        
        Handlebars.registerHelper('json', function(context) {
            return JSON.stringify(context);
        });
    </script>

    <div id="widget-content-{{ $tenantWidgetId }}"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateElement = document.getElementById('widget-template-{{ $tenantWidgetId }}');
            const contentElement = document.getElementById('widget-content-{{ $tenantWidgetId }}');
            
            if (templateElement && contentElement) {
                const data = @json($context);
                const template = Handlebars.compile(templateElement.innerHTML);
                contentElement.innerHTML = template(data);
                console.log('Widget #{{ $tenantWidgetId }} içeriği başarıyla yüklendi');
            } else {
                console.error('Widget #{{ $tenantWidgetId }} template veya content elementi bulunamadı');
            }
        });
    </script>
</div>

@if(!empty($widget->content_css))
<style>
    {!! $widget->content_css !!}
</style>
@endif

@if(!empty($widget->content_js))
<script>
    {!! $widget->content_js !!}
</script>
@endif