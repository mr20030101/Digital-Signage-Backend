<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    protected $fillable = ['name', 'description', 'width', 'height', 'background_color', 'background_image', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
