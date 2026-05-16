<?php
// HierarchyScope.php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HierarchyScope implements Scope
{
    protected $excludedModels = [
        'App\Models\Role',
        'App\Models\User',
        'App\Models\Enquiry',
    ];

    public function apply(Builder $builder, Model $model)
    {
        if (in_array(get_class($model), $this->excludedModels)) {
            return;
        }
        $user = auth()->user();
        if (!$user) {
            return;
        }
        $builder->whereIn($model->getTable() . '.created_by', $user->getDescendantIds());
    }
}
