<?php

namespace App\Entities\v1\Config;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    


    public function States()
    {
        return $this->hasMany(State::class, 'country_id');
    }

  
}
