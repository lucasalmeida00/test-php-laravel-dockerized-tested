<?php

namespace Tests\Unit\Services;

use App\Dtos\User\CreateUserDto;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;
use Mockery;

class UserServiceTest extends TestCase
{
    private User $user;
    private Mockery\LegacyMockInterface&Mockery\MockInterface&UserRepositoryInterface $userRepository;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make(['id' => 1]);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testShouldReturnUserWhenGettingUserById(): void
    {
        $this->userRepository
            ->shouldReceive('getUserById')
            ->with($this->user->id)
            ->andReturn($this->user);

        $user = $this->userService->getUserById($this->user->id);

        $this->assertEquals($this->user, $user);
    }

    public function testShouldThrowExceptionWhenUserNotFound(): void
    {
        $this->userRepository
            ->shouldReceive('getUserById')
            ->with($this->user->id)
            ->andThrow(new ModelNotFoundException('User not found'));

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->userService->getUserById($this->user->id);
    }

    public function testShouldReturnUserWhenCreatingUser(): void
    {
        $createUserDto = CreateUserDto::fromArray([
            'name' => $this->user->name,
            'cpf' => $this->user->cpf,
            'email' => $this->user->email,
            'password' => $this->user->password,
        ]);

        $this->userRepository
            ->shouldReceive('createUser')
            ->with($createUserDto)
            ->andReturn($this->user);

        $this->userService->createUser($createUserDto);

        $this->assertInstanceOf(User::class, $this->user);
        $this->assertEquals($this->user->name, $this->user->name);
        $this->assertEquals($this->user->cpf, $this->user->cpf);
        $this->assertEquals($this->user->email, $this->user->email);
        $this->assertEquals($this->user->password, $this->user->password);
    }
}
