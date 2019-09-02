<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'image_path'
    ];

    protected $guarded = [];

    public function Products ()
    {
        return $this->hasOne('App\Products');
    }
}
