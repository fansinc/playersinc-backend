<?php

namespace App\Transformers\Users;

use App\Entities\User;
use App\Entities\v1\Users\UserProfile;
use App\Transformers\Assets\AssetTransformer;
use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer.
 */
class UserProfileTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [
        'Assets',
    ];

    /**
     * @param User $model
     * @return array
     */
    public function transform(UserProfile $model)
    {
        return [
            'id' => (int) $model->id,
            'user_id' => (int) $model->user_id,
            'grip_auction_id' => (string) $model->grip_auction_id,
            'user_address' => (string) $model->user_address,
            'created_by' => (int) $model->created_by,
            'updated_by' => (int) $model->updated_by,
            'created_at' => (string) $model->created_at->getTimestamp(),
            'updated_at' => (string) $model->updated_at->getTimestamp(),
        ];
    }

    /**
     * @param User $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeAssets(UserProfile $model)
    {
        return $this->collection($model->assets()->latest()->take(1)->get(), new AssetTransformer());
    }

}
