<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyMenu extends Model
{
    protected $fillable = [
        'menu_date',
        'title',
        'description',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'menu_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailyMenuItem::class);
    }

    public function kitchenDocumentations(): HasMany
    {
        return $this->hasMany(KitchenDocumentation::class);
    }
}
