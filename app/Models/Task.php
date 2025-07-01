<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'priority'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
