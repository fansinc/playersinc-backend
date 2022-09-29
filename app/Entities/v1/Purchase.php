<?php

namespace App\Entities\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use App\Entities\v1\Config\ConfStatus;
use App\Entities\Assets\Asset;
use App\Entities\User;

class Purchase extends Model implements Auditable
{
    use AuditingAuditable, SoftDeletes;

    protected $fillable = [
        'player_id',
        'experience_id',
        'price',
        'user_id', 
        'token_id',
        'card_id',
        'stripeRS',
        'stripeRF', 
        'status_id',
        'created_by',
        'updated_by',
    ];

    public function players()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Experience()
    {
        return $this->belongsTo(Experience::class, 'experience_id');
    }


    public function status()
    {
        return $this->belongsTo(ConfStatus::class,'status_id');
    }
}
