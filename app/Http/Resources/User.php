<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = parent::toArray($request);
        if (array_key_exists('data',$response)) {
            foreach($response['data'] as $key1 => $value1){
                foreach($value1 as $key2 => $value2){
                    if($value2 == NULL){
                        $response['data'][$key1][$key2] = '';
                    }
                    if($value2 == '0'){
                        $response['data'][$key1][$key2] = 0;
                    }
                }
            }
        }else{
           
            foreach($response as $key => $value){
                if($value == NULL){
                    $response[$key] = '';
                }
                if($value == '0'){
                    $response[$key] = 0;
                }
            }
        }
        return $response;
    }
}
