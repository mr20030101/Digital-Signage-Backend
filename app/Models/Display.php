<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Display extends Model
{
    protected $fillable = ['name', 'code', 'location', 'status', 'last_seen', 'user_id'];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
