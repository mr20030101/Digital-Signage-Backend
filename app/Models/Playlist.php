<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class)->withPivot('order')->withTimestamps()->orderBy('order');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
