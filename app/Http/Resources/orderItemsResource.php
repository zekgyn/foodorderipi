<?php

namespace App\Http\Resources;

use App\Http\Resources\employeeMenuResource;
use Illuminate\Http\Resources\Json\JsonResource;

class orderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id'=>$this->id,
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->name,
            'total' => $this->amount,
            'menu_items' => employeeMenuResource::collection($this->whenLoaded('employeeItems')),

        ];
    }
}
