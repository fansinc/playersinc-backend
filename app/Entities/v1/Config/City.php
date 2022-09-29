<?php

namespace App\Entities\v1\Config;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    
    public function State()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

   


}
