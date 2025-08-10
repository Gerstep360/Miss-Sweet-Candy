{{-- resources/views/emails/user-created.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido a Miss Sweet Candy</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 40px 30px; text-align: center; }
        .content { padding: 40px 30px; }
        .button { display: inline-block; background: #f59e0b; color: white; padding: 15px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .footer { background: #1f2937; color: #9ca3af; padding: 20px 30px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a Miss Sweet Candy!</h1>
            <p>Tu cuenta ha sido creada exitosamente</p>
        </div>
        
        <div class="content">
            <h2>Hola {{ $user->name }},</h2>

            <p>Te damos la bienvenida a <strong>Miss Sweet Candy</strong>. Una cuenta ha sido creada para ti con los siguientes datos:</p>

            <div style="background: #f9fafb; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <p><strong>Nombre:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Rol:</strong> {{ $user->roles->first()->name ?? 'No asignado' }}</p>
            </div>
            
            <p>Para completar la configuración de tu cuenta, necesitas establecer tu contraseña personalizada. Haz clic en el siguiente botón:</p>
            
            <div style="text-align: center;">
                <a href="{{ $activationUrl }}" class="button">Configurar mi contraseña</a>
            </div>
            
            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                <strong>Nota:</strong> Este enlace es válido por tiempo limitado. Si tienes problemas para acceder, contacta con el administrador del sistema.
            </p>
            
            <p>Una vez que configures tu contraseña, podrás acceder al sistema y comenzar a disfrutar de todos los servicios de Café Aroma.</p>
            
            <p>¡Esperamos verte pronto!</p>
            
            <p style="margin-top: 30px;">
                Saludos,<br>
                <strong>El equipo de Miss Sweet Candy</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Miss Sweet Candy. Todos los derechos reservados.</p>
            <p>Si no solicitaste esta cuenta, puedes ignorar este mensaje.</p>
        </div>
    </div>
</body>
</html>