<?php

namespace App\Transformers\V1\Config;

use App\Entities\v1\Config\Country;

use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer.
 */
class CountryTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'States',
    ];

    /**
     * @param Country $model
     * @return array
     */
    public function transform(Country $model)
    {
        return [
            'id' => (int) $model->id,
            'code' => (string) $model->code,
            'name' => (string) $model->name ,
            'phonecode' => (int) $model->phonecode,
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
    public function includeStates(Country $model)
    {
        return $this->collection($model->States, new StateTransformer());
    }

}
