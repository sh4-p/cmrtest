<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_id',
        'deal_stage_id',
        'amount',
        'closing_date',
        'probability',
        'assigned_to_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'closing_date' => 'date',
            'probability' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Accessor for expected revenue (amount * probability)
    public function getExpectedRevenueAttribute(): float
    {
        return $this->amount * ($this->probability / 100);
    }

    // Relationships
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function stage()
    {
        return $this->belongsTo(DealStage::class, 'deal_stage_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
