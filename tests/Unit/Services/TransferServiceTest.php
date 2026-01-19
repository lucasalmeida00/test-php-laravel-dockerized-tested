<?php

namespace Tests\Unit\Services;

use App\DTOS\Transfer\TransferDto;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use App\Repositories\Interfaces\TransferRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Consumers\UtilsApi;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Mockery;
use RuntimeException;

class TransferServiceTest extends TestCase
{
    private Role $roleShopManager;
    private Role $roleDefault;
    private Role $roleWithPermission;

    private User $userShopManager;
    private User $userDefault;
    private User $userWithPermission;
    private User $userWithoutPermission;

    private Mockery\LegacyMockInterface&Mockery\MockInterface&TransferRepositoryInterface $transferRepository;

    private Mockery\LegacyMockInterface&Mockery\MockInterface&UserService $userService;
    private Mockery\LegacyMockInterface&Mockery\MockInterface&UtilsApi $utilsApi;
    private TransferService $transferService;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar permissão
        $permission = Permission::factory()->make([
            'id' => 1,
            'name' => Permission::PERMISSION_CAN_TRANSFER,
        ]);

        $this->roleShopManager = Role::factory()->make(['id' => 1, 'name' => Role::ROLE_SHOPMANAGER]);
        $this->roleShopManager->setRelation('permissions', Collection::make([]));

        $this->roleDefault = Role::factory()->make(['id' => 2, 'name' => Role::ROLE_DEFAULT]);
        $this->roleDefault->setRelation('permissions', Collection::make([]));

        $this->roleWithPermission = Role::factory()->make(['id' => 3, 'name' => Role::ROLE_SHOPMANAGER]);
        $this->roleWithPermission->setRelation('permissions', Collection::make([$permission]));

        $this->userShopManager = User::factory()->make(['id' => 1, 'amount' => 100]);
        $this->userShopManager->setRelation('roles', Collection::make([$this->roleShopManager]));

        $this->userDefault = User::factory()->make(['id' => 2, 'amount' => 0]);
        $this->userDefault->setRelation('roles', Collection::make([$this->roleDefault]));

        $this->userWithPermission = User::factory()->make(['id' => 3, 'amount' => 100]);
        $this->userWithPermission->setRelation('roles', Collection::make([$this->roleWithPermission]));

        $this->userWithoutPermission = User::factory()->make(['id' => 4, 'amount' => 0]);
        $this->userWithoutPermission->setRelation('roles', Collection::make([]));

        $this->transferRepository = Mockery::mock(TransferRepositoryInterface::class);

        $this->userService = Mockery::mock(UserService::class);
        $this->utilsApi = Mockery::mock(UtilsApi::class);
        $this->transferService = new TransferService($this->transferRepository, $this->userService, $this->utilsApi);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testCreateTransfer(): void
    {
        $data = TransferDto::fromArray([
            'user_id' => $this->userWithPermission->id,
            'recipient_id' => $this->userDefault->id,
            'amount' => 50,
        ]);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userWithPermission->id)
            ->andReturn($this->userWithPermission);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userDefault->id)
            ->andReturn($this->userDefault);

        $this->utilsApi->shouldReceive('canTransfer')
            ->once()
            ->andReturn(true);

        $this->transferRepository
            ->shouldReceive('createTransfer')
            ->with($data)
            ->andReturn(
                Transfer::factory()->make(
                    [
                        'amount' => 50,
                        'user_id' => $this->userWithPermission->id,
                        'recipient_id' => $this->userDefault->id
                    ]
                )
            );

        $this->userService->shouldReceive('updateUserAmount')
            ->with($this->userWithPermission, $this->userWithPermission->amount - $data->amount)
            ->andReturn($this->userWithPermission);

        $this->userService->shouldReceive('updateUserAmount')
            ->with($this->userDefault, $this->userDefault->amount + $data->amount)
            ->andReturn($this->userDefault);

        $this->utilsApi->shouldReceive('notifyTransfer')
            ->once()
            ->andReturn(true);

        $transfer = $this->transferService->createTransfer($data);

        $this->assertInstanceOf(Transfer::class, $transfer);
        $this->assertEquals(50, $transfer->amount);
        $this->assertEquals($this->userWithPermission->id, $transfer->user_id);
        $this->assertEquals($this->userDefault->id, $transfer->recipient_id);
    }

    public function testShouldThrowExceptionWhenUserDoesNotHavePermission(): void
    {
        $data = TransferDto::fromArray([
            'user_id' => $this->userWithoutPermission->id,
            'recipient_id' => $this->userDefault->id,
            'amount' => 50,
        ]);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userWithoutPermission->id)
            ->andReturn($this->userWithoutPermission);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userDefault->id)
            ->andReturn($this->userDefault);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('User not have permission to transfer funds.');

        $this->transferService->createTransfer($data);
    }

    public function testShouldThrowExceptionWhenUserCannotTransferToRecipient(): void
    {
        $data = TransferDto::fromArray([
            'user_id' => $this->userWithPermission->id,
            'recipient_id' => $this->userWithPermission->id,
            'amount' => 50,
        ]);

        $this->userWithPermission = User::factory()->make(['id' => 3, 'amount' => 100]);
        $this->userWithPermission->setRelation('roles', Collection::make([$this->roleWithPermission]));

        $this->userService->shouldReceive('getUserById')
            ->with($this->userWithPermission->id)
            ->andReturn($this->userWithPermission);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userWithPermission->id)
            ->andReturn($this->userWithPermission);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You cannot transfer funds to yourself.');

        $this->transferService->createTransfer($data);
    }


    public function testShouldThrowExceptionWhenUserDoesNotHaveEnoughBalance(): void
    {
        $data = TransferDto::fromArray([
            'user_id' => $this->userWithPermission->id,
            'recipient_id' => $this->userDefault->id,
            'amount' => 100,
        ]);

        $this->userWithPermission = User::factory()->make(['id' => 3, 'amount' => 20]);
        $this->userWithPermission->setRelation('roles', Collection::make([$this->roleWithPermission]));

        $this->userService->shouldReceive('getUserById')
            ->with($this->userWithPermission->id)
            ->andReturn($this->userWithPermission);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userDefault->id)
            ->andReturn($this->userDefault);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient balance to complete the transfer.');

        $this->transferService->createTransfer($data);
    }

    public function testShouldThrowExceptionWhenTransferAmountIsNotValid(): void
    {
        $data = TransferDto::fromArray([
            'user_id' => $this->userWithPermission->id,
            'recipient_id' => $this->userDefault->id,
            'amount' => 0,
        ]);

        $this->userWithPermission = User::factory()->make(['id' => 3, 'amount' => 100]);
        $this->userWithPermission->setRelation('roles', Collection::make([$this->roleWithPermission]));

        $this->userService->shouldReceive('getUserById')
            ->with($this->userWithPermission->id)
            ->andReturn($this->userWithPermission);

        $this->userService->shouldReceive('getUserById')
            ->with($this->userDefault->id)
            ->andReturn($this->userDefault);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The transfer amount must be greater than zero.');

        $this->transferService->createTransfer($data);
    }
}
