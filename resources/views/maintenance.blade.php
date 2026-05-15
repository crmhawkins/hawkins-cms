<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en mantenimiento</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Georgia', serif;
            background: #111;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
        }
        .container { max-width: 600px; }
        h1 { font-size: 3rem; font-weight: 300; margin-bottom: 1.5rem; letter-spacing: 0.1em; }
        p { font-size: 1.1rem; line-height: 1.8; color: #aaa; }
        .accent { color: #c9a96e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="accent" style="font-size:4rem;margin-bottom:1rem;">⚙️</div>
        <h1>Volvemos pronto</h1>
        <p>{{ $message }}</p>
    </div>
</body>
</html>
