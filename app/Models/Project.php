<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'project_name',
        'slug',
        'description',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    // Accessor for 'name' to use 'project_name'
    public function getNameAttribute()
    {
        return $this->project_name;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    public function leader()
    {
        return $this->members()->whereHas('user', function($query) {
            $query->where('role', 'leader');
        })->first();
    }
}
