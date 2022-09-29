<?php

namespace App\Transformers\V1\Config;

use App\Entities\v1\Config\ConfPaymentStatus;
use League\Fractal\TransformerAbstract;

class ConfPaymentStatusTransformer extends TransformerAbstract
{
    public function transform(ConfPaymentStatus $model)
    {
        // if (isset($_GET['select'])) {
        //     return [
        //         'id' => (int) $model->id,
        //         'name' => (string) $model->name,
        //     ];
        // }
        return [
            'id' => (int) $model->id,
            'name' => (string) $model->name,
            'created_by' => (int) $model->created_by,
            'updated_by' => (int) $model->updated_by,
            'created_at' => (string) $model->created_at->getTimestamp(),
            'updated_at' => (string) $model->updated_at->getTimestamp(),
        ];
    }
}
