<?php

namespace App\Models;

use App\Enums\CallRequestStatus;
use Database\Factories\CallRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property CallRequestStatus $status
 * @property Carbon $created_at
 */
class CallRequest extends Model
{
    /** @use HasFactory<CallRequestFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'preferred_date',
        'notes',
        'status',
        'ip_address',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'status' => CallRequestStatus::class,
            'created_at' => 'datetime',
        ];
    }
}
