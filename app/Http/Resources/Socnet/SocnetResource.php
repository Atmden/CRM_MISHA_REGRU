<?php

namespace App\Http\Resources\Socnet;

use Illuminate\Http\Resources\Json\JsonResource;

class SocnetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->id,
            'name'          =>  $this->name,
            'logo'        =>    $this->logo,
            'online'        =>  (string)$this->online,
        ];
    }
}
