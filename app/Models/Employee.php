<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Employee extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'phone',
    ];

    // static function for generating uuid
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(Employee::class, $value);
        }
        return $this->where("id", $value)->firstOrFail();
    }
public function scopeSearch($query, $term)
{
    if ($term !== null) {
        $term = strtolower('%' .$term. '%');
        $query->where('employees.name', 'like', $term);
    }
}
    // public function orderItem()
    // {
    //     return $this->belongsTo(OrderItem::class);
    // }
}
