<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'is_placed'
    ];
    // sgenerate order numbber
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_number = strtoupper(bin2hex(openssl_random_pseudo_bytes(5, $cstrong)));
        });
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
