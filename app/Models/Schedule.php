<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['display_id', 'playlist_id', 'layout_id', 'start_time', 'end_time', 'days_of_week', 'is_active'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'days_of_week' => 'array',
        'is_active' => 'boolean',
    ];

    public function display()
    {
        return $this->belongsTo(Display::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }
}
