<?php

namespace App\Http\Resources\Plan;

use App\Http\Resources\Tag\TagForPlanEditResource;
use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Null_;

class PlanForEditResource extends JsonResource
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
            'plan_id' => $this->id,
            'title' => $this->title,
            'hashtags' => $this->hashtags,
            'content' => $this->content==null?'':$this->content,
            'status' => $this->status,
            'insta' => (string)$this->insta,
            'public_at' => $this->public_at->format('d/m/Y'),
            'time_at' => $this->public_at->format('H:i'),
            'soc_net' => $this->socnets->pluck('id'),
            'tags' => TagForPlanEditResource::collection($this->tags),
            'images' => $this->images,
            'videos'    => $this->videos,
            'youtubes'          => $this->youtubes,


        ];
    }
}
