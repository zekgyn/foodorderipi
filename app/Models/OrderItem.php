<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderItem extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;
    // static function for generating uuid
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'title',
        'price',
        'image',
        'name',
        'menu_id'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(OrderItem::class, $value);
        }

        return $this->where("id", $value)->firstOrFail();
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
