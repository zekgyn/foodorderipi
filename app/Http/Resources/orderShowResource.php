<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class orderShowResource extends JsonResource
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
            'id'=>$this->id,
            'order_number' => $this->order_number,
            'is_complete' => $this->is_complete,
            'total'=> $this->total,
            'created_at' => $this->created_at,
            'order_items' => orderItemsResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
