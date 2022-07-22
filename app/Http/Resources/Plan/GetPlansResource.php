<?php

namespace App\Http\Resources\Plan;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GetPlansResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $start_day = Carbon::now()->startOfDay();

        $plans = $this->plans()
            ->where('public_at','>=', $start_day)
            ->orderBy('public_at','ASC');

            if (!Auth::user()->can_edit)
            {
                $plans = $plans->where('status_id', '!=','1');
            }

            $plans = $plans->get()
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
            'plans' => $fine,
            'filter' => null,
            'file' => 'GetPlansResource'

        ];

    }
}
