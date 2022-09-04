<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Employee;
use Illuminate\Support\Str;
use App\Models\EmployeeMenuItems;
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
        'employee_id',
        'subtotal'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(OrderItem::class, $value);
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

    public function scopeSearch($query, $term)
    {
        if ($term !== null) {
            $term = strtoupper($term . '%');
            $query->where('report_items.name', 'like', $term);
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function employeeItems()
    {
        return $this->hasMany(EmployeeMenuItems::class);
    }
}
