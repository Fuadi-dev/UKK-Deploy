<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'project_id',
        'board_name',
        'description',
        'position',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
