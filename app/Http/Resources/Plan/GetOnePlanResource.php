<?php

namespace App\Http\Resources\Plan;

use App\Models\Plan;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GetOnePlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $plan = Plan::where('id',$this->id)->get();
        $account = $plan[0]->account;
        Log::debug('Fine');
            $fine = [
              'plan_public' =>
              [
                  'date' => Carbon::parse($plan[0]->public_at)->locale('ru')->isoFormat('D MMMM YYYY'),
                  'plans' => PlanFoAccountResource::collection($plan)
              ]
            ];




        return [
            'account_id' => $account->id,
            'account_name' => $account->name,
            'plans' => $fine,
            'filter' => null,
            'file' => 'GetPlansResource'

        ];

    }
}
