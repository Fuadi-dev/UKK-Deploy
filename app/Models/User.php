<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'avatar',
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'status',
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
        ];
    }

    /**
     * Get the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Get projects where user is admin
     */
    public function ownedProjects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get project memberships
     */
    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class);
    }

    /**
     * Get project memberships (alias for compatibility)
     */
    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    /**
     * Get card assignments
     */
    public function cardAssignments()
    {
        return $this->hasMany(CardAssignment::class);
    }

    /**
     * Get assigned card (untuk developer/designer hanya 1 card)
     */
    public function assignedCard()
    {
        return $this->hasOne(Card::class)->where('status', '!=', 'done');
    }

    /**
     * Get subtasks created by user
     */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Get time logs created by user
     */
    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    /**
     * Get user's avatar URL or generate initials
     */
    public function getAvatarAttribute($value)
    {
        return $value;
    }

    /**
     * Get user's avatar URL or fallback to default
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            // If avatar is a full URL (like Google avatar)
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            // If avatar is a local file
            return asset('storage/avatars/' . $this->avatar);
        }
        
        return null;
    }

    /**
     * Get user initials for fallback avatar
     */
    public function getInitials()
    {
        $names = explode(' ', $this->name);
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
