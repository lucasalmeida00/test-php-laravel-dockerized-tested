<?php

namespace App\Services;

use App\Models\User;
use App\Services\UserService;
use App\Utils\StringUtils;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticatorService
{
    public function __construct(private UserService $userService) {}

    /**
     * Autentica um usuário com email e senha
     *
     * @param string $email
     * @param string $password
     * @return User
     * @throws AuthenticationException
     */
    public function authenticate(string $email, string $password): User
    {
        try {
            $user = $this->userService->getUserByEmail($email);
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid credentials');
        }

        if (!Hash::check($password, $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return $user;
    }

    /**
     * Gera um token de autenticação para o usuário usando Sanctum
     *
     * @param User $user
     * @param string|null $deviceName Nome do dispositivo (opcional)
     * @return string Token gerado
     */
    public function generateToken(User $user, ?string $deviceName = null): string
    {
        $tokenName = $deviceName ?? 'auth-token';

        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Realiza login completo: autentica e gera token
     *
     * @param string $email
     * @param string $password
     * @param string|null $deviceName
     * @return array{user: User, token: string}
     * @throws AuthenticationException
     */
    public function login(string $email, string $password, ?string $deviceName = null): array
    {
        $user = $this->authenticate($email, $password);
        $token = $this->generateToken($user, $deviceName);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoga o token atual do usuário autenticado na requisição
     *
     * @return void
     */
    public function logout(): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $user->tokens()->delete();
        }
    }

    /**
     * Revoga um token específico de um usuário
     *
     * @param User $user
     * @param string $tokenId ID do token a ser revogado
     * @return void
     */
    public function revokeToken(User $user, string $tokenId): void
    {
        $user->tokens()->where('id', $tokenId)->delete();
    }

    /**
     * Revoga todos os tokens do usuário
     *
     * @param User $user
     * @return void
     */
    public function logoutAllDevices(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Valida se o token é válido e retorna o usuário autenticado
     *
     * @param string $token
     * @return User|null
     */
    public function validateToken(string $token): ?User
    {
        // O Sanctum já vem com o model PersonalAccessToken embutido
        $tokenModel = PersonalAccessToken::findToken($token);

        if (!$tokenModel) {
            return null;
        }

        $user = $tokenModel->tokenable;

        return $user instanceof User ? $user : null;
    }

    /**
     * Retorna o usuário autenticado atual baseado no token da requisição
     *
     * @return User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    /**
     * Verifica se o usuário está autenticado
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function refreshToken(string $token): string
    {
        return $this->generateToken($this->validateToken($token));
    }
}
