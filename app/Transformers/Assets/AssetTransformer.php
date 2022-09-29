<?php

namespace App\Transformers\Assets;

use App\Entities\Assets\Asset;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Storage;

/**
 * Class AssetTransformer.
 */
class AssetTransformer extends TransformerAbstract
{
    /**
     * @param \App\Entities\Assets\Asset $model
     * @return array
     */
    public function transform(Asset $model)
    {
        if($model->type=="image")
        {
            return [
                'id' => $model->uuid,
                'type' => $model->type,
                'path' => $model->path,
                'mime' => $model->mime,

                'links' => [
                    'full' => url('api/assets/' . $model->uuid . '/render'),
                    'thumb' => url('api/assets/' . $model->uuid . '/render?width=200&height=200'),
                ],
                
                'created_at' => (string)$model->created_at->getTimestamp(),
                
                // 'created_at' => $model->created_at->toIso8601String(),
            ];

        }
        else if($model->type=="video")
        {
            return [
                'id' => $model->uuid,
                'type' => $model->type,
                'path' => $model->path,
                'mime' => $model->mime,
                'links' => [
                    'full' => '',
                    'thumb' => '',
                ],
                'linksvideo'=> [
                    'full' => url('storage/'.$model->path),

                ],
                'created_at' => (string)$model->created_at->getTimestamp(),
                
                // 'created_at' => $model->created_at->toIso8601String(),
            ];

        }
    }
}
