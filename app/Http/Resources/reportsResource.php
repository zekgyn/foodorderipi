<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class reportsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'employee'=> $this->employee,
            'total' => $this->subtotal,
            'date' => $this->created_at,
            'items' => reportItemsResource::collection($this->whenLoaded('reportItems')),
        ];
    }
}
