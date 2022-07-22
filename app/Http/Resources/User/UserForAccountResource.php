<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserForAccountResource extends JsonResource
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
            'email'          =>  $this->email,
            'send_email'     =>  (string)$this->send_email,
            'can_edit'        => (string)$this->can_edit,
            'can_comment'        => (string)$this->can_comment,
            'edit'           =>  false,

        ];
    }
}
