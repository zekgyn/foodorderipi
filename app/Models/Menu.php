<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Menu extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'price'
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
            throw (new ModelNotFoundException)->setModel(Menu::class, $value);
        }
        return $this->where("id", $value)->firstOrFail();
    }
    public function scopeSearch($query, $term)
    {
        if ($term !== null) {
            $term = strtolower('%' . $term . '%');
            $query->where('menus.title', 'like', $term);
        }
    }
}
