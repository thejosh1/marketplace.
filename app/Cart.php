<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['product_name', 'product_id', 'price', 'qty'];

    protected $hidden = ['session_id'];

    public function products (){
        return $this->hasMany(App\Product::class);
    }
}

