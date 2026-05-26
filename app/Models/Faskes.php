<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faskes extends Model
{
    protected $table = 'faskes';

    protected $fillable = [
        'name',
        'type',
        'phone',
        'latitude',
        'longitude'
    ];
}