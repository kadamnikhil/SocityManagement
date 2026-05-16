<?php

namespace App\Models;

use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissiongroup extends Model
{
    use Hashidable;
    protected $fillable=['name','controller'];

    public function permissions(){
        return $this->hasMany('App\Models\Permission');
    }

}

