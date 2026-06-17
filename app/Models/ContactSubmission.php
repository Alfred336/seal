<?php

namespace App\Models;

use App\Enums\ContactSubmissionStatus;
use Database\Factories\ContactSubmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ContactSubmissionStatus $status
 * @property Carbon $submitted_at
 */
class ContactSubmission extends Model
{
    /** @use HasFactory<ContactSubmissionFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'company',
        'phone',
        'project_type',
        'message',
        'ip_address',
        'status',
        'submitted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ContactSubmissionStatus::class,
            'submitted_at' => 'datetime',
        ];
    }
}
