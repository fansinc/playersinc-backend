<?php

namespace App\Transformers\Users;

use App\Entities\Role;
use League\Fractal\TransformerAbstract;

/**
 * Class RolTransformer.
 */
class RoleTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = ['permissions'];

    /**
     * @param Role $model
     * @return array
     */
    public function transform(Role $model)
    {
        return [
            'id' => $model->uuid,
            'role_id' => $model->id,
            'name' => $model->name,
            'created_at' => (string)$model->created_at->getTimestamp(),
            'updated_at' => (string)$model->updated_at->getTimestamp(),
        ];
    }

    /**
     * @param Role $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(Role $model)
    {
        return $this->collection($model->permissions, new PermissionTransformer());
    }
}
