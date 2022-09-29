<?php

namespace App\Transformers\V1\Config;

use App\Entities\v1\Config\State;

use League\Fractal\TransformerAbstract;

/**
 * Class StateTransformer.
 */
class StateTransformer extends TransformerAbstract
{
    // /**
    //  * @var array
    //  */
    protected $availabletIncludes = [
        'Cities',
    ];

    /**
     * @param State $model
     * @return array
     */
    public function transform(State $model)
    {
        return [
            'id' => (int) $model->id,
            'name' => (string) $model->name ,
            'country_id ' => (int) $model->country_id,
            'country_name ' => (string) $model->Country->name,
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
    public function includeCities(State $model)
    {
        return $this->collection($model->Cities, new CityTransformer());
    }

}
