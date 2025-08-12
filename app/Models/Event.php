<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'main_image',
        'more_images',
        'start_date',
        'end_date',
        'description',
        'sub_description',
    ];

    protected $casts = [
        'more_images' => 'array',
    ];
}
