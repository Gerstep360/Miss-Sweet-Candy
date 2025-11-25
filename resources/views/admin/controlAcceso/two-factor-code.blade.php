{{-- resources/views/emails/two-factor-code.blade.php --}}
<x-mail::layout>
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            {{ config('app.name') }}
        </x-mail::header>
    </x-slot>

    <x-mail::panel>
        <h1 style="color: #1f2937; font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px;">
            Código de Verificación
        </h1>
        
        <p style="color: #6b7280; text-align: center; margin-bottom: 10px;">
            Usa el siguiente código para configurar la autenticación de dos factores:
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <div style="display: inline-block; background: #f3f4f6; padding: 15px 30px; border-radius: 8px; border: 2px dashed #d1d5db;">
                <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1f2937;">
                    {{ $code }}
                </span>
            </div>
        </div>

        <p style="color: #6b7280; text-align: center; margin-bottom: 5px;">
            <strong>Válido por:</strong> {{ $minutesValid }} minutos
        </p>
        <p style="color: #6b7280; text-align: center; margin-bottom: 20px;">
            <strong>Expira:</strong> {{ $expiresAt->format('d/m/Y H:i:s') }}
        </p>

        <div style="background: #fef3cd; border: 1px solid #fcd34d; border-radius: 6px; padding: 12px; margin: 20px 0;">
            <p style="color: #92400e; margin: 0; font-size: 14px;">
                ⚠️ <strong>Importante:</strong> No compartas este código con nadie. El equipo de {{ config('app.name') }} nunca te pedirá tu código de verificación.
            </p>
        </div>
    </x-mail::panel>

    <x-mail::footer>
        <x-mail::subcopy>
            Si no solicitaste este código, puedes ignorar este mensaje.<br>
            © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
        </x-mail::subcopy>
    </x-mail::footer>
</x-mail::layout>