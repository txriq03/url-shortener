<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Link extends Model
{
    protected $fillable = ['url',  'alias', 'user_id', 'click_count'];

    protected $casts = [
        'click_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}