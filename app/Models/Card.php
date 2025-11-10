<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = [
        'board_id', 
        'user_id', 
        'card_title', 
        'slug', 
        'description', 
        'position', 
        'due_date', 
        'status', 
        'priority', 
        'estimated_hours', 
        'actual_hours',
        'started_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    /**
     * Get the board that owns the card.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the user assigned to the card.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user assigned to the card (alias for assignedUser).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subtasks for the card.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Get the comments for the card.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the time logs for the card.
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    /**
     * Get the card assignments for the card.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(CardAssignment::class);
    }
}
