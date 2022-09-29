<?php

namespace App\Entities;

use App\Entities\v1\Users\UserProfile;
use App\Support\HasRolesUuid;
use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Entities\v1\News;

/**
 * Class User.
 */
class User extends Authenticatable
{
    use Notifiable, UuidScopeTrait, HasApiTokens, HasRoles, SoftDeletes, HasRolesUuid {
        HasRolesUuid::getStoredRole insteadof HasRoles;
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'uuid',
        'email',
        'password',
        'mobile',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    function create(array $attributes = [])
    {
        if (array_key_exists('password', $attributes)) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        $model = static::query()->create($attributes);

        return $model;
    }

    function news()
    {
        return $this->hasMany(News::class, 'player_id');
    }
    
    function lender()
    {
        return $this->hasMany(Lender::class, 'user_id');
    }
    function auctionBids()
    {
        return $this->hasMany(AuctionBid::class, 'user_id');
    }
    function auctionBidTrackers()
    {
        return $this->hasMany(AuctionBidTracker::class, 'user_id');
    }
    function auctionWatchlists()
    {
        return $this->hasMany(AuctionWatchlist::class, 'user_id');
    }
    function auctionWins()
    {
        return $this->hasMany(AuctionWin::class, 'user_id');
    }
    function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'user_id');
    }
    function subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id');
    }
    function bidderDeposit()
    {
        return $this->hasOne(BidderDeposit::class, 'user_id');
    }
    function bidderDepositTrackers()
    {
        return $this->hasMany(BidderDepositTracker::class, 'user_id');
    }
    function auctions()
    {
        return $this->belongsToMany(
            Auction::class,
            'auctions_users',
            'user_id',
            'auction_id');
    }
    function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    function userAddress()
    {
        return $this->hasOne(UserAddress::class, 'user_id');
    }
    function aadharDetail()
    {
        return $this->hasOne(UserAadharDetail::class, 'user_id');
    }
    function checkLeafDetail()
    {
        return $this->hasOne(UserCheckLeafDetail::class, 'user_id');
    }

    function panDetail()
    {
        return $this->hasOne(UserPanDetail::class, 'user_id');
    }
}
