<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'plan_id'       => $this->plan_id,
            'user_id'       => $this->user_id,
            'content'       => $this->content,
            'apply'         => (string)$this->apply,
            'created_at'    => $this->created_at->format('d/m/Y H:i'),
            'user'          => $this->user,

        ];
    }
}
