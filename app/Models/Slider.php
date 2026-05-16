<?php

namespace App\Models;

use App\Models\BaseModel;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slider extends  BaseModel
{
    use HasFactory;
    protected $fillable = ['status','title','index','url','photo'];

   

    protected $appends = array('photo_link');

    public function getPhotoLinkAttribute(){
        $photo_link = null;
        if($this->photo != null){
            $photo_link = getDomainUrl().''.Storage::url($this->photo);
        }
        return $photo_link;
    }
}
