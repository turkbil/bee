<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Error</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 1rem;
        }
        p {
            color: #718096;
            margin-bottom: 2rem;
        }
        code {
            background: #f7fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            display: block;
            text-align: left;
            color: #e53e3e;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">❌</div>
        <h1>Configuration Error</h1>
        <p>Auto-recovery failed. Please run the following command manually:</p>
        <code>composer config-refresh</code>
        <p style="margin-top: 2rem; font-size: 0.875rem;">
            Error: {{ $error ?? 'Unknown error' }}
        </p>
        <a href="/" class="btn">Try Again</a>
    </div>
</body>
</html>
