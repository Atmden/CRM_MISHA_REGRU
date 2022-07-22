<?php

namespace App\Http\Resources\Plan;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class PlanFoAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {


        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'hashtags'          => $this->hashtags,
            'insta'             => $this->insta,
            'content'           => $this->content,
            'status'            => $this->status,
            'public_at_time'    => $this->public_at->format('H:i'),
            'images'            => $this->images,
            'videos'            => $this->videos,
            'youtubes'          => YoutubeForPlanResource::collection($this->youtubes),
            'tags'              => $this->tags,
            'comment_no_apply'  => $this->comment_no_apply(),
            'comment_apply'  => $this->comment_apply()


        ];
    }
}
