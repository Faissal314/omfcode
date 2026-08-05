<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'source',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];
}
