<?php

namespace App\Http\Resources\Account;

use App\Http\Resources\Socnet\SocnetForAccountResource;
use App\Http\Resources\User\UserForAccountResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountToEditResource extends JsonResource
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
            'online'        =>  (string)$this->online,
            'auto_apply'        =>  (string)$this->auto_apply,
            'users_count'   =>  $this->users->count(),
            'users'         =>  UserForAccountResource::collection($this->users),
            'soc_net'       =>  SocnetForAccountResource::collection($this->socnets)

        ];
    }
}
