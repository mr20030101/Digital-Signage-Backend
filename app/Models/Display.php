<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Display extends Model
{
    use HasApiTokens;
    
    protected $fillable = ['name', 'code', 'location', 'status', 'last_seen', 'user_id', 'layout_id', 'ip_address', 'auto_register'];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
