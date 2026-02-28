<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Course extends Model
{
    use HasFactory, SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $slug = $field ?? $this->getRouteKeyName();
        if ($slug !== 'slug') {
            return static::query()->withoutTrashed()->where($slug, $value)->firstOrFail();
        }

        return static::query()
            ->withoutTrashed()
            ->with('lessons')
            ->where('slug', $value)
            ->firstOrFail();
    }

    protected $fillable = [
        'title',
        'slug',
        'image',
        'level',
        'description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
