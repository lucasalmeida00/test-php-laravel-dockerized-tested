<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use SoftDeletes, HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const TYPE_TRANSFER = 'transfer';
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    const TYPES = [
        self::TYPE_TRANSFER,
        self::TYPE_DEPOSIT,
        self::TYPE_WITHDRAW,
    ];

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
    ];

    const DEFAULT_TYPE = self::TYPE_TRANSFER;
    const DEFAULT_STATUS = self::STATUS_PENDING;

    protected $fillable = [
        'user_id',
        'recipient_id',
        'amount',
        'description',
        'type',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function amount(): Attribute
    {
        return new Attribute(
            get: fn ($value) => preg_replace('/[^0-9]/', '', $value),
            set: fn ($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }
}
