<!DOCTYPE html>
<html>
<head>
    <title>Debug Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .error { background: #f8d7da; padding: 10px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 20px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>🐛 Debug Dashboard</h1>

    <div class="info">
        <h3>Información del Usuario</h3>
        <p><strong>Nombre:</strong> {{ $simple_data['user']->name }}</p>
        <p><strong>Rol:</strong> {{ $simple_data['user']->rol }}</p>
        <p><strong>Email:</strong> {{ $simple_data['user']->email }}</p>
    </div>

    @if(isset($e))
    <div class="error">
        <h3>❌ Error Detectado:</h3>
        <p><strong>Mensaje:</strong> {{ $e->getMessage() }}</p>
        <p><strong>Archivo:</strong> {{ $e->getFile() }}:{{ $e->getLine() }}</p>

        <h4>Stack Trace:</h4>
        <pre>{{ $e->getTraceAsString() }}</pre>
    </div>
    @endif

    <div class="info">
        <h3>✅ El sistema básico funciona</h3>
        <p>La autenticación y las rutas funcionan correctamente.</p>
        <p>El problema está en las vistas específicas de dashboard.</p>
    </div>

    <p><a href="{{ route('login') }}">← Volver al Login</a></p>
</body>
</html>