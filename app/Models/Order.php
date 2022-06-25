<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['menu_id', 'phone','location'];

    protected function menus()
    {
        $this->hasOne(Menu::class);
    }
}
