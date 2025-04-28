<script src="{{ asset('admin/libs/handlebars/handlebars.min.js') }}?v={{ filemtime(public_path('admin/libs/handlebars/handlebars.min.js')) }}"></script>
<script id="widget-template-{{ $tenantWidgetId }}" type="text/x-handlebars-template">
    {!! $widget->content_html !!}
</script>
<div id="widget-rendered-{{ $tenantWidgetId }}"></div>
<script>
    (function(){
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
        
        const data = @json($context);
        const tpl = Handlebars.compile(document.getElementById('widget-template-{{ $tenantWidgetId }}').innerHTML);
        document.getElementById('widget-rendered-{{ $tenantWidgetId }}').innerHTML = tpl(data);
    })();
</script>