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
        'calories',
        'protein',
        'carbs',
        'fiber',
    ];

    protected function casts(): array
    {
        return [
            'calories' => 'decimal:2',
            'protein' => 'decimal:2',
            'carbs' => 'decimal:2',
            'fiber' => 'decimal:2',
        ];
    }

    public function dailyMenuItems(): HasMany
    {
        return $this->hasMany(DailyMenuItem::class);
    }
}
