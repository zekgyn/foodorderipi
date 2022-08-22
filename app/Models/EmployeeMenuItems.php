<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeMenuItems extends Model
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
            });
    }
    protected $fillable = [
        'menu_id',
        'quantity'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(EmployeeMenuItems::class, $value);
        }
        return $this->where("id", $value)->firstOrFail();
    }
    //Filter by date
    public function scopeFilterByDate($query, $startDate = null, $endDate = null)
    {
    //Check if start date is passed
    if ($startDate && !$endDate) {
        $startDate = date("Y-m-d", strtotime($startDate));
        $query->where(function ($query) use ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        });
    }

    //Check if end date is passed
    elseif (!$startDate && $endDate) {
        $endDate = date("Y-m-d", strtotime($endDate));
        $query->where(function ($query) use ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        });
    }

    //Check if end date and start date is passed
    elseif ($startDate && $endDate) {
        $query->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->orWhereDate('created_at', $startDate)
                ->orWhereDate('created_at', $endDate);
        });
    }
}

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class,'order_items_id','id');
    }

    //try hasone if current fails
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
