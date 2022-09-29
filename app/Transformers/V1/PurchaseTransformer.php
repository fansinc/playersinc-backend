<?php

namespace App\Transformers\V1;

use App\Entities\v1\Purchase;
use League\Fractal\TransformerAbstract;
use App\Transformers\Assets\AssetTransformer;

class PurchaseTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'Experience'
    ];
    public function transform(Purchase $model)
    {

      
        return [
            'id' => (int) $model->id,
            'player_id' => (int) $model->player_id,
            'player_name' => (string) $model->players->name,
            'experience_id' => (int) $model->experience_id,
            'price' => (string) sprintf("%.2f",(double) $model->price),
            'user_id' => (int) $model->user_id,
            'user_name' => (string) $model->users->name,
            'token_id'=>(string)$model->token_id, 
            'card_id'=>(string)$model->card_id, 
            'stripeRS'=>json_decode($model->stripeRS),
            'status_id' => (int) $model->status_id,
            'status_name' => (string) $model->status->name,
            'created_by' => (int) $model->created_by,
            'updated_by' => (int) $model->updated_by,
            'created_at' => (string) $model->created_at->getTimestamp(),
            'updated_at' => (string) $model->updated_at->getTimestamp(),
        ];
    }

    public function includeExperience(Purchase $model)
    {
        return $this->item($model->Experience, new ExperienceTransformer());
    }

   
}
