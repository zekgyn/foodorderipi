<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'price',
        'image'
    ];
    // public function orders()
    // {
    //     $this->belongsToMany(Order::class);
    // }
    public function orderItem()
    {
        return $this->hasOne(OrderItem::class);
    }
}
