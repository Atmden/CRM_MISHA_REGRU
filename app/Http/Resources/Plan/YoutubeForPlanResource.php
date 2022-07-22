<?php

namespace App\Http\Resources\Plan;

use Illuminate\Http\Resources\Json\JsonResource;

class YoutubeForPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        parse_str(parse_url($this->url, PHP_URL_QUERY), $arr);



        return [
            'poster'        =>  isset($arr['v'])?'//img.youtube.com/vi/'. $arr['v'].'/mqdefault.jpg':'',
            'url'            => isset($arr['v'])?'//www.youtube.com/embed/'.$arr['v']:'',
            'id'             => $this->id

        ];
    }
}
