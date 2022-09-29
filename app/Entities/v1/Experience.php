<?php

namespace App\Entities\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use App\Entities\v1\Config\ConfStatus;
use App\Entities\Assets\Asset;
use App\Entities\User;

class Experience extends Model implements Auditable
{
    use AuditingAuditable, SoftDeletes;

    protected $fillable = [
        'player_id',
        'title',
        'description',
        'price',
        'status_id',
        'created_by',
        'updated_by',
    ];

    public function players()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function assets()
    {
        return $this->morphMany(Asset::class, 'imageable');
    }

    public function Purchase()
    {
        return $this->hasMany(Purchase::class);
    }


    public function status()
    {
        return $this->belongsTo(ConfStatus::class,'status_id');
    }
}
