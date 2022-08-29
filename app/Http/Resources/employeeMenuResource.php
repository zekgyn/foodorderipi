<?php

namespace App\Http\Resources;

use App\Models\Menu;
use Illuminate\Http\Resources\Json\JsonResource;

class employeeMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $menu = Menu::select('price', 'title')->where('id', $this->menu_id)->first();
        return [
            'id' => $this->id,
            'menu_id' => $this->menu_id,
            'title' => $menu->title,
            'price' => $menu->price,
            'qty' => $this->quantity,

        ];
    }
}
