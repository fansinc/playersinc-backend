<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

use App\Entities\Assets\Asset;

use App\Entities\User;
class UserAddress extends Model
{
    protected $fillable = [ 
        'user_id',
        'address_line1',
        'address_line2',
        'country',
        'state',
        'city',
        'postal_code',
        'created_by',
        'updated_by'
    ];
    
        public function User()
        {
            return $this->belongsTo(User::class,'user_id');
        }
    
        // public function confCountry()
        // {
        //     return $this->belongsTo(ConfCountry::class,'country_id');
        // }
        
        
        public function assets()
        {
            return $this->morphMany(Asset::class, 'imageable');
        }
    
}
