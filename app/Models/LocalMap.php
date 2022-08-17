<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalMap extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'address', 'region', 'lat', 'lng', 'place_id'];
}
