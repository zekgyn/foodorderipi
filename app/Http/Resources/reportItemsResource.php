<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class reportItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $order_number = Order::find($this->order_id);
        return
        [
            'id' => $this->id,
            'order_number' => $order_number->order_number,
            'employee' => $this->employee,
            'total' => (double) $this->subtotal,
            'date' => date("Y-m-d", strtotime($this->created_at)),
            'items' => menuItemReportResource::collection($this->whenLoaded('reportItems')),
        ];
    }
}
