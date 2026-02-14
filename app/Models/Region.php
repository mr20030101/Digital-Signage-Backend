<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['layout_id', 'name', 'width', 'height', 'top', 'left', 'z_index', 'playlist_id', 'content_id'];

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
