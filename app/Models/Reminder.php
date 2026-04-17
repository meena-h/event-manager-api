<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'remind_at',
        'status',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
    ];

    // A reminder belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}