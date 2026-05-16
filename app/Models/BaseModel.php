<?php
namespace App\Models;

use ReflectionMethod;
use App\Traits\Hashidable;
use App\Scopes\HierarchyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends Model {

    use HasFactory,Hashidable;

    public static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new HierarchyScope());


        static::creating(function($model)
        {
            $user = \Auth::user();
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });

        static::updating(function($model)
        {
            $user = \Auth::user();
            $model->updated_by = $user->id;
        });
    }

    public function createdBy()
    {
        return $this->belongsTo('\App\Models\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('\App\Models\User', 'updated_by');
    }

    public function creatorName()
    {
        if($this->created_by != NULL){
            return $this->createdBy->first_name." ".$this->createdBy->last_name;
        }else{
            return '';
        }

    }

    public function updatorName()
    {
        if($this->updated_by != NULL){
            return $this->updatedBy->first_name." ".$this->updatedBy->last_name;
        }else{
            return '';
        }
    }


    // public function getRelationships()
    // {
    //     $reflectionClass = new \ReflectionClass($this);
    //     $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
    //     $relationships = [];
    //     foreach ($methods as $method) {
    //         if ($this->isRelationshipMethod($method->name)) {
    //             $relationships[] = $method->name;
    //         }
    //     }
    //     return $relationships;
    // }

    // protected function isRelationshipMethod($methodName)
    // {
    //     $relationships = ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany', 'morphTo', 'morphMany', 'morphToMany'];

    //     return in_array($methodName, $relationships);
    // }
}

