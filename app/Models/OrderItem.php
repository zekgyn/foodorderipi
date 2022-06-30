<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'image',
        'name',
        'order_id'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
