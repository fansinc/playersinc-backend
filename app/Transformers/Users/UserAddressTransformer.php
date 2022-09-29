<?php

namespace App\Transformers\Users;

use League\Fractal\TransformerAbstract;

use App\Transformers\Assets\AssetTransformer;

use App\Entities\UserAddress;
/**
 * Class UserTransformer.
 */
class UserAddressTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    //  protected $defaultIncludes = ['Assets'];

    /**
     * @param UserAddress $model
     * @return array
     */
    public function transform(UserAddress $model)
    {
      
        return [
            'id' => (int)$model->id,
            'user_id' => (int)$model->user_id,
            'user_email' => (string)$model->User->email,
            'user_name' => (string)$model->User->name,
            'address_line1' => (string)$model->address_line1,
            'address_line2' => (string)$model->address_line2,
            'country' => (string)$model->country,
            'state' => (string)$model->state,
            'city' => (string)$model->city,
            'postal_code' => (string)$model->postal_code,
            'created_at' => (string)$model->created_at->getTimestamp(),
            'updated_at' => (string)$model->updated_at->getTimestamp(),
        ];
    }

    /**
     * @param User $model
     * @return \League\Fractal\Resource\Collection
     */
    
    public function includeUser(UserAddress $model)
    {
        return $this->collection($model->User, new UserTransformer());
    }

    public function includeAssets(UserAddress $model)
    {
        return $this->collection($model->assets, new AssetTransformer());
    }
}
