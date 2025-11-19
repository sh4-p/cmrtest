<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'assigned_to_id',
        'related_to_type',
        'related_to_id',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function relatedTo()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'Completed')
            ->where('due_date', '<', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    // Helper method to mark as completed
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'Completed',
            'completed_at' => now(),
        ]);
    }
}
