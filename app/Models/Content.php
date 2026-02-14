<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['name', 'type', 'file_path', 'thumbnail_path', 'content', 'duration', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class)->withPivot('order')->withTimestamps();
    }
}
