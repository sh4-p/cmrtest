<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
        'last_login_at',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // =========================
    // CRM Relationships
    // =========================

    /**
     * Companies owned by this user
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'owner_id');
    }

    /**
     * Contacts owned by this user
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'owner_id');
    }

    /**
     * Leads assigned to this user
     */
    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_to_id');
    }

    /**
     * Deals assigned to this user
     */
    public function assignedDeals()
    {
        return $this->hasMany(Deal::class, 'assigned_to_id');
    }

    /**
     * Tasks assigned to this user
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to_id');
    }

    /**
     * Activities performed by this user
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
