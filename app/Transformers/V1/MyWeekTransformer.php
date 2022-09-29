<?php

namespace App\Transformers\V1;

use App\Entities\v1\MyWeek;
use League\Fractal\TransformerAbstract;
use App\Transformers\Assets\AssetTransformer;

class MyWeekTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'Assets',
    ];
    public function transform(MyWeek $model)
    {

      
        return [
            'id' => (int) $model->id,
            'player_id' => (int) $model->player_id,
            'player_name' => (string) $model->players->name,
            'status_id' => (int) $model->status_id,
            'status_name' => (string) $model->status->name,
            'created_by' => (int) $model->created_by,
            'updated_by' => (int) $model->updated_by,
            'created_at' => (string) $model->created_at->getTimestamp(),
            'updated_at' => (string) $model->updated_at->getTimestamp(),
        ];
    }

    public function includeAssets(MyWeek $model)
    {
        return $this->collection($model->assets, new AssetTransformer());
    }
}
