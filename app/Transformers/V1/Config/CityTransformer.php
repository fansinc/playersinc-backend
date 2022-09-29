<?php

namespace App\Transformers\V1\Config;

use App\Entities\v1\Config\City;

use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer.
 */
class CityTransformer extends TransformerAbstract
{
   

    /**
     * @param User $model
     * @return array
     */
    public function transform(City $model)
    {
        return [
            'id' => (int) $model->id,
            'name' => (string) $model->name ,
            'state_id ' => (int) $model->state_id,
            'state_name ' => (string) $model->State->name,
            'country_name ' => (string) $model->State->Country->name,
            // 'created_by' => (int) $model->created_by,
            // 'updated_by' => (int) $model->updated_by,
            // 'created_at' => (string) $model->created_at->getTimestamp(),
            // 'updated_at' => (string) $model->updated_at->getTimestamp(),
        ];
    }

    // /**
    //  * @param User $model
    //  * @return \League\Fractal\Resource\Collection
    //  */
    // public function includeAssets(UserProfile $model)
    // {
    //     return $this->collection($model->assets()->latest()->take(1)->get(), new AssetTransformer());
    // }

}
