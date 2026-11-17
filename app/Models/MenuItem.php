<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'photo',
    ];

    public function dailyMenuItems(): HasMany
    {
        return $this->hasMany(DailyMenuItem::class);
    }
}
