<script id="widget-template-{{ $tenantWidgetId }}" type="text/x-handlebars-template">
    {!! $widget->content_html !!}
</script>
<div id="widget-rendered-{{ $tenantWidgetId }}"></div>
<script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
<script>
    (function(){
        const data = @json($context);
        const tpl = Handlebars.compile(document.getElementById('widget-template-{{ $tenantWidgetId }}').innerHTML);
        document.getElementById('widget-rendered-{{ $tenantWidgetId }}').innerHTML = tpl(data);
    })();
</script>
