<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthenticatorService;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;
use Mockery;

class AuthenticatorServiceTest extends TestCase
{
    private User $user;
    private Mockery\LegacyMockInterface&Mockery\MockInterface&UserService $userService;
    private AuthenticatorService $authenticatorService;
    private string $email = 'test@example.com';
    private string $password = 'password123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make(['id' => 1]);
        $this->email = $this->user->email;
        $this->password = 'password123';

        $this->userService = Mockery::mock(UserService::class);
        $this->authenticatorService = new AuthenticatorService($this->userService);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testShouldReturnUserWhenAuthenticatingWithValidCredentials(): void
    {
        Hash::shouldReceive('check')
            ->once()
            ->with($this->password, $this->user->password)
            ->andReturn(true);

        $this->userService
            ->shouldReceive('getUserByEmail')
            ->once()
            ->with($this->email)
            ->andReturn($this->user);

        $result = $this->authenticatorService->authenticate($this->email, $this->password);

        $this->assertEquals($this->user, $result);
    }

    public function testShouldThrowExceptionWhenAuthenticatingWithInvalidEmail(): void
    {
        $this->userService
            ->shouldReceive('getUserByEmail')
            ->once()
            ->with($this->email)
            ->andThrow(new ModelNotFoundException('User not found'));

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authenticatorService->authenticate($this->email, $this->password);
    }

    public function testShouldThrowExceptionWhenAuthenticatingWithInvalidPassword(): void
    {
        Hash::shouldReceive('check')
            ->once()
            ->with($this->password, $this->user->password)
            ->andReturn(false);

        $this->userService
            ->shouldReceive('getUserByEmail')
            ->once()
            ->with($this->email)
            ->andReturn($this->user);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authenticatorService->authenticate($this->email, $this->password);
    }

    public function testShouldGenerateTokenForUser(): void
    {
        $deviceName = 'test-device';
        $expectedToken = 'test-token-123';

        $userMock = Mockery::mock(User::class);
        $tokenMock = Mockery::mock();
        $tokenMock->plainTextToken = $expectedToken;

        $userMock->shouldReceive('createToken')
            ->once()
            ->with($deviceName)
            ->andReturn($tokenMock);

        $token = $this->authenticatorService->generateToken($userMock, $deviceName);

        $this->assertEquals($expectedToken, $token);
    }

    public function testShouldGenerateTokenWithDefaultNameWhenDeviceNameNotProvided(): void
    {
        $expectedToken = 'test-token-123';

        $userMock = Mockery::mock(User::class);
        $tokenMock = Mockery::mock();
        $tokenMock->plainTextToken = $expectedToken;

        $userMock->shouldReceive('createToken')
            ->once()
            ->with('auth-token')
            ->andReturn($tokenMock);

        $token = $this->authenticatorService->generateToken($userMock);

        $this->assertEquals($expectedToken, $token);
    }

    public function testShouldReturnUserAndTokenWhenLoggingIn(): void
    {
        $expectedToken = 'test-token-123';
        $loginUser = User::factory()->make(['id' => 2]);
        $loginEmail = $loginUser->email;

        $userMock = Mockery::mock(User::class);

        $userMock->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn($loginUser->password);

        Hash::shouldReceive('check')
            ->once()
            ->with($this->password, $loginUser->password)
            ->andReturn(true);

        $this->userService
            ->shouldReceive('getUserByEmail')
            ->once()
            ->with($loginEmail)
            ->andReturn($userMock);

        $tokenMock = Mockery::mock();
        $tokenMock->plainTextToken = $expectedToken;

        $userMock->shouldReceive('createToken')
            ->once()
            ->with('auth-token')
            ->andReturn($tokenMock);

        $result = $this->authenticatorService->login($loginEmail, $this->password);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals($userMock, $result['user']);
        $this->assertEquals($expectedToken, $result['token']);
    }

    public function testShouldThrowExceptionWhenLoggingInWithInvalidCredentials(): void
    {
        $this->userService
            ->shouldReceive('getUserByEmail')
            ->once()
            ->with($this->email)
            ->andThrow(new ModelNotFoundException('User not found'));

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authenticatorService->login($this->email, $this->password);
    }

    public function testShouldLogoutUser(): void
    {
        $userMock = Mockery::mock(User::class);
        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userMock);

        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($tokensMock);

        $this->authenticatorService->logout();
    }

    public function testShouldNotLogoutWhenUserIsNotAuthenticated(): void
    {
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(null);

        $this->authenticatorService->logout();

        $this->assertTrue(true);
    }

    public function testShouldRevokeToken(): void
    {
        $tokenId = '123';
        $userMock = Mockery::mock(User::class);

        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('where')
            ->once()
            ->with('id', $tokenId)
            ->andReturnSelf();
        $queryMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($queryMock);

        $this->authenticatorService->revokeToken($userMock, $tokenId);
    }

    public function testShouldLogoutAllDevices(): void
    {
        $userMock = Mockery::mock(User::class);
        $tokensMock = Mockery::mock();
        $tokensMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturn($tokensMock);

        $this->authenticatorService->logoutAllDevices($userMock);
    }

    public function testShouldReturnUserWhenValidatingValidToken(): void
    {
        $token = 'valid-token-123';

        $tokenModel = Mockery::mock();
        $tokenModel->tokenable = $this->user;

        $personalAccessTokenMock = Mockery::mock('alias:' . PersonalAccessToken::class);
        $personalAccessTokenMock->shouldReceive('findToken')
            ->once()
            ->with($token)
            ->andReturn($tokenModel);

        $result = $this->authenticatorService->validateToken($token);

        $this->assertEquals($this->user, $result);
    }

    public function testShouldReturnNullWhenValidatingInvalidToken(): void
    {
        $token = 'invalid-token-123';

        $personalAccessTokenMock = Mockery::mock('alias:' . PersonalAccessToken::class);
        $personalAccessTokenMock->shouldReceive('findToken')
            ->once()
            ->with($token)
            ->andReturn(null);

        $result = $this->authenticatorService->validateToken($token);

        $this->assertNull($result);
    }

    public function testShouldReturnAuthenticatedUser(): void
    {
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($this->user);

        $result = $this->authenticatorService->getAuthenticatedUser();

        $this->assertEquals($this->user, $result);
    }

    public function testShouldReturnNullWhenNoAuthenticatedUser(): void
    {
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(null);

        $result = $this->authenticatorService->getAuthenticatedUser();

        $this->assertNull($result);
    }

    public function testShouldReturnTrueWhenUserIsAuthenticated(): void
    {
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        $result = $this->authenticatorService->isAuthenticated();

        $this->assertTrue($result);
    }

    public function testShouldReturnFalseWhenUserIsNotAuthenticated(): void
    {
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(false);

        $result = $this->authenticatorService->isAuthenticated();

        $this->assertFalse($result);
    }
}
