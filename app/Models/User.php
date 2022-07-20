<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    public function resolveRouteBinding($value, $field = null)
    {
        if (!Str::isUuid($value)) {
            throw (new ModelNotFoundException)->setModel(Order::class, $value);
        }
        return $this->where("id", $value)->firstOrFail();
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
