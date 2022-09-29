<?php

namespace App\Transformers\Users;

use App\Entities\User;

use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer.
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = ['roles'];
    protected $availableIncludes = ['Auctions'];

    /**
     * @param User $model
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id' => $model->uuid,
            'user_id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'mobile'=>$model->mobile,
            'created_at' => (string)$model->created_at->getTimestamp(),
            'updated_at' => (string)$model->updated_at->getTimestamp(),
        ];
    }

    /**
     * @param User $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeRoles(User $model)
    {
        return $this->collection($model->roles, new RoleTransformer());
    }
    public function includeAuctions(User $model)
    {
        return $this->collection($model->auctions, new AuctionTransformer());
    }
}
