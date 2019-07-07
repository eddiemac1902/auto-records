<?php

namespace App\Model\Arrivals;

use Illuminate\Database\Eloquent\Model;

class Arrivals extends Model
{
    protected $dates = [
        'in_date',
    ];

    protected $guarded = [];
}
