<?php

namespace App\Http\Resources\Plan;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FilterPlansResource extends JsonResource
{

    private $filter;

    public function __construct($resource, $filter)
    {

        $this->filter = json_decode($filter->filter);

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $plans = $this->plans();


        if ($this->filter->period != null) {
            if ($this->filter->period->id != null) {

                if ($this->filter->period->id == 1) // Предыдущий месяц
                {
                    $plans = $plans->whereMonth('public_at', Carbon::now()->addMonth(-1)->month);
                }
                if ($this->filter->period->id == 2) // Текущий месяц
                {
                    // правка от 04/05/2021 - к текущему месяцу, надо показывать и следующие посты
                    // $plans = $plans->whereMonth('public_at', Carbon::now()->month);
                    $plans = $plans->where('public_at', '>=', Carbon::now()->startOfMonth());
                }
                if ($this->filter->period->id == 3) // Следующий месяц
                {
                    $plans = $plans->whereMonth('public_at', Carbon::now()->addMonth(1)->month);
                }
                if ($this->filter->period->id == 4) // Произвольный период
                {
                    $date_from = Carbon::createFromFormat('d/m/Y', $this->filter->public_from, 'UTC')->startOfDay();
                    $date_to = Carbon::createFromFormat('d/m/Y', $this->filter->public_to, 'UTC')->endOfDay();
                    $plans = $plans->whereBetween('public_at', [$date_from, $date_to]);
                }
            }
        }

        if (!Auth::user()->can_edit)
        {
            $plans = $plans->where('status_id', '!=','1');
        }
        $plans = $plans->orderBy('public_at','ASC');
        if(count($this->filter->status)> 0)
        {
            $plans = $plans->whereIN('status_id',$this->filter->status);
        }

        if (count($this->filter->soc_net)> 0)
        {
            $plans = $plans->whereHas('socnets', function ($q) use ($request) {
                $q->whereIn('id', $this->filter->soc_net); // where in for array
            });
        }

        if (count($this->filter->tags)>0)
        {
            $tags = $this->filter->tags;

            $plans = $plans->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('id', array_column($tags, 'id')); // where in for array
            });
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

        $insta_arr = [];

        foreach ($plans as $item)
        {
            foreach ($item as $value)
            {
                if ($value->insta)
                {
                    $insta_arr[]= [
                        new PlanForEditResource($value)
                    ];
                }
            }
        }

        //return $fine;
        return [
            'account_id' => $this->id,
            'account_name' => $this->name,
            'plans' => $fine,
            'filter' => $this->filter,
            'insta' => array_reverse($insta_arr)

        ];

    }
}
