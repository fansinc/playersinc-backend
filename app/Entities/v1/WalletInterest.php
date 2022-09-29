<?php

namespace App\Entities\v1;

use Illuminate\Database\Eloquent\Model;

use App\Entities\User;

class WalletInterest extends Model
{
    protected $fillable = [ 
        'user_id'
    ];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}


