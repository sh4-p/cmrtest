<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'source',
        'status',
        'assigned_to_id',
        'converted_to_contact_id',
        'converted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'converted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Accessor for full name
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function convertedToContact()
    {
        return $this->belongsTo(Contact::class, 'converted_to_contact_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    // Helper method to convert lead to contact
    public function convertToContact(array $additionalData = []): Contact
    {
        $contact = Contact::create(array_merge([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'owner_id' => $this->assigned_to_id,
            'notes' => $this->notes,
        ], $additionalData));

        $this->update([
            'converted_to_contact_id' => $contact->id,
            'converted_at' => now(),
            'status' => 'Converted',
        ]);

        return $contact;
    }
}
