<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardAssignment extends Model
{
    protected $fillable = [
        'card_id', 
        'user_id', 
        'assigned_at', 
        'assignment_status', 
        'started_at', 
        'completed_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the card that owns the assignment.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the user that owns the assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
