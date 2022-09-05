<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Support\Str;
use App\Models\EmployeeMenuReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Report extends Model
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
        'employee',
        'subtotal'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(Report::class, $value);
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
    // search by employee name
    public function scopeSearch($query, $term)
    {
        if ($term !== null) {
            $term = strtolower('%'. $term . '%');
            $query->where('reports.employee', 'like', $term);
        }
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function reportItems()
    {
        return $this->hasMany(EmployeeMenuReport::class);
    }
}
