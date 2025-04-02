<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Widget Önizleme</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Widget CSS */
        {!! $widget->content_css !!}
    </style>
</head>
<body>
    <!-- Widget HTML -->
    {!! $widget->content_html !!}

    <!-- Widget JavaScript -->
    <script>
        {!! $widget->content_js !!}
    </script>
</body>
</html>