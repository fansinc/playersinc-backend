<?php

namespace App\Entities\v1\Config;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    


    public function Cities()
    {
        return $this->hasMany(City::class, 'state_id');
    }

    public function Country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
