@component('mail::message')
# Redefinição de senha

Recebemos uma solicitação para redefinir sua senha.

@component('mail::button', ['url' => $url])
Redefinir senha
@endcomponent

Este link expira em {{ $expiresInMinutes }} minutos. Se você não solicitou isso, pode ignorar este e-mail.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
