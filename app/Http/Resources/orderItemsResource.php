<?php

namespace App\Http\Resources;

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
            'menu' => $this->menu->title,
            'employee' => $this->employee->name,
            'price' => $this->menu->price,
        ];
    }
}
