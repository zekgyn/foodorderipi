<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    // static function for generating uuid and order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid(); //generate uuid
            $model->order_number = strtoupper(bin2hex(openssl_random_pseudo_bytes(5, $cstrong))); //generate order number
        });
    }
    protected $fillable = [
        'order_number',
        'is_placed'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(Order::class, $value);
        }
        return $this->where("id", $value)->firstOrFail();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
