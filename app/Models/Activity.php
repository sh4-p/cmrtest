<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'type',
        'user_id',
        'subject_type',
        'subject_id',
        'activity_date',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('activity_date', 'desc')->limit($limit);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
