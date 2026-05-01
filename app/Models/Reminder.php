<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
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

    // A reminder belongs to an event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}