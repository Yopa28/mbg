<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMenuItem extends Model
{
    protected $fillable = [
        'daily_menu_id',
        'menu_item_id',
        'portion_type',
        'quantity',
        'unit',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'calories' => 'decimal:2',
            'protein' => 'decimal:2',
            'carbs' => 'decimal:2',
            'fat' => 'decimal:2',
            'fiber' => 'decimal:2',
        ];
    }

    public function dailyMenu(): BelongsTo
    {
        return $this->belongsTo(DailyMenu::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
