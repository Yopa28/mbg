<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sppg extends Model
{
    protected $table = 'sppg';

    protected $fillable = [
        'name',
        'code',
        'address',
        'head_name',
        'phone',
        'logo',
    ];
}


