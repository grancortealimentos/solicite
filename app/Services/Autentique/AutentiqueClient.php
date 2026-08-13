<?php

namespace App\Services\Autentique;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AutentiqueClient
{
    private function baseRequest(): PendingRequest
    {
        $baseUrl = config('services.autentique.base_url');
        $apiKey = config('services.autentique.api_key');

        if (blank($baseUrl) || blank($apiKey)) {
            throw new RuntimeException(
                'AUTENTIQUE_BASE_URL e AUTENTIQUE_API_KEY precisam estar configurados no .env.'
            );
        }

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->withHeaders(['X-API-KEY' => $apiKey])
            ->timeout(15);
    }

    private function authenticatedRequest(string $token): PendingRequest
    {
        return $this->baseRequest()->withToken($token);
    }

    /**
     * @return array{token: string, token_type: string, user: array<string, mixed>, force_password_change: bool}
     */
    public function login(string $email, string $password): array
    {
        return $this->handle(
            $this->baseRequest()->post('login', [
                'email' => $email,
                'password' => $password,
            ])
        );
    }

    /**
     * @return array{user: array<string, mixed>, force_password_change: bool}
     */
    public function me(string $token): array
    {
        return $this->handle($this->authenticatedRequest($token)->get('me'));
    }

    public function logout(string $token): void
    {
        $this->handle($this->authenticatedRequest($token)->post('logout'));
    }

    /**
     * @return array{reset_token: string, expires_in_minutes: int}
     */
    public function forgotPassword(string $email): array
    {
        return $this->handle(
            $this->baseRequest()->post('password/forgot', ['email' => $email])
        );
    }

    public function resetPassword(string $resetToken, string $password): void
    {
        $this->handle(
            $this->baseRequest()->post('password/reset', [
                'token' => $resetToken,
                'password' => $password,
            ])
        );
    }

    public function changePassword(string $token, string $currentPassword, string $newPassword): void
    {
        $this->handle(
            $this->authenticatedRequest($token)->put('password/change', [
                'current_password' => $currentPassword,
                'password' => $newPassword,
            ])
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createPerson(string $token, array $data): array
    {
        return $this->handle($this->authenticatedRequest($token)->post('persons', $data));
    }

    /**
     * @return array<string, mixed>
     */
    private function handle(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $body = $response->json() ?? [];

        throw new AutentiqueApiException(
            message: $body['message'] ?? 'Falha ao comunicar com o serviço de autenticação.',
            status: $response->status(),
            errors: $body['errors'] ?? [],
        );
    }
}
