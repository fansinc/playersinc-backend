<?php

namespace App\Entities\v1\Users;

use App\Entities\Assets\Asset;
use App\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class UserProfile extends Model implements Auditable
{
    use AuditingAuditable,SoftDeletes;

    protected $fillable = [
        'user_id',
        'grip_auction_id',        
        'user_address',     
        'created_by',
        'updated_by',
    ];
    public function assets()
    {
        return $this->morphMany(Asset::class, 'imageable');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
