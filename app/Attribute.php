<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'color', 'weight', 'size'
    ];

    public function Product()
    {
        return $this->belongsTo('App\Product');
    }
}
