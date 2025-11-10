<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = [
        'card_id',
        'user_id',
        'subtask_title',
        'description',
        'status',
        'estimated_hours',
        'actual_hours',
        'position',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }
}
