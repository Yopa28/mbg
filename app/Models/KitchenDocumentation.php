<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenDocumentation extends Model
{
    protected $fillable = [
        'daily_menu_id',
        'category',
        'title',
        'description',
        'photo_path',
        'temperature',
        'captured_at',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'captured_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function dailyMenu(): BelongsTo
    {
        return $this->belongsTo(DailyMenu::class);
    }
}
