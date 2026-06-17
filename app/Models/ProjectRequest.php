<?php

namespace App\Models;

use App\Enums\ProjectRequestStatus;
use Database\Factories\ProjectRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ProjectRequestStatus $status
 * @property Carbon $created_at
 */
class ProjectRequest extends Model
{
    /** @use HasFactory<ProjectRequestFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'project_type',
        'details',
        'status',
        'ip_address',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectRequestStatus::class,
            'created_at' => 'datetime',
        ];
    }
}
