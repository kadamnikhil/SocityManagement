<?php

namespace App\Models;

use App\Models\BaseModel;

class Enquiry extends BaseModel
{
    protected $fillable = ['first_name','last_name','email','mobile','message','remark'];

}
