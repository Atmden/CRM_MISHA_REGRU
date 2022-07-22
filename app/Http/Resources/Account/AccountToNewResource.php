<?php

namespace App\Http\Resources\Account;

use App\Http\Resources\Socnet\SocnetForAccountResource;
use App\Http\Resources\Socnet\SocnetForNewAccountResource;
use App\Http\Resources\Socnet\SocnetResource;
use App\Http\Resources\User\UserForAccountResource;
use App\Models\Socnet;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountToNewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $soc_net = Socnet::all();
        return [
            'name'          =>  '',
            'online'        =>  (string)'0',
            'soc_net'       =>  SocnetForNewAccountResource::collection($soc_net)

        ];
    }
}
