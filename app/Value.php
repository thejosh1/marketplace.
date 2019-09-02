<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $fillable = ['brand', 'type', 'product_item_detail'];

    public function Product () 
    {
        return $this->belongsTo('App\Product');
    }
}
