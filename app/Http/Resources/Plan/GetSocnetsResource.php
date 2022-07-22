<?php

namespace App\Http\Resources\Plan;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class GetSocnetsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $plans = $this->plans()->orderBy('public_at','DESC')->get()
            ->groupBy(function($plans) {
                return Carbon::parse($plans->public_at)->locale('ru')->isoFormat('D MMMM YYYY');
            });
        $fine = [];
        foreach ($plans as $key=>$item)
        {
            $fine[] = [
              'plan_public' =>
              [
                  'date' => $key,
                  'plans' => PlanFoAccountResource::collection($item)
              ]
            ];


        }
        //return $fine;
        return [
            'account_id' => $this->id,
            'account_name' => $this->name,
            'plans' => $fine

        ];

    }
}
