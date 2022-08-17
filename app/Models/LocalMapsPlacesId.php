<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalMapsPlacesId extends Model
{
    use HasFactory;
    protected $fillable = ['place_id','formatted_address'];

    public function local_map()
    {
       return $this->hasOne(LocalMap::class, 'place_id', 'place_id');
    }
}
