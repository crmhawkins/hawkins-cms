<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Nuevo mensaje de contacto</title></head>
<body style="font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333">
    <h2 style="color:#1a1a2e">Nuevo mensaje de contacto</h2>
    <table style="width:100%;border-collapse:collapse">
        <tr><td style="padding:8px 0;font-weight:bold;width:120px">Nombre:</td><td>{{ $submission->name }}</td></tr>
        <tr><td style="padding:8px 0;font-weight:bold">Email:</td><td><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></td></tr>
        @if($submission->subject)
        <tr><td style="padding:8px 0;font-weight:bold">Asunto:</td><td>{{ $submission->subject }}</td></tr>
        @endif
        <tr><td style="padding:8px 0;font-weight:bold;vertical-align:top">Mensaje:</td><td style="white-space:pre-line">{{ $submission->message }}</td></tr>
        <tr><td style="padding:8px 0;font-weight:bold">Fecha:</td><td>{{ $submission->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>
    <hr style="margin:20px 0;border:none;border-top:1px solid #eee">
    <p style="color:#999;font-size:12px">Mensaje recibido en {{ config('app.name') }}</p>
</body>
</html>
