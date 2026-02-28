<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'issued_at',
        'completion_email_sent',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'completion_email_sent' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate) {
            if (empty($certificate->uuid)) {
                $certificate->uuid = (string) Str::uuid();
            }
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
