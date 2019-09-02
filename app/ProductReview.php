<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'header', 'rating', 'approved', 'description', 'product_id'
    ];

    /**
     * attributes that should be cast to native types
     */
    protected $cast = [
        'product_id' => 'integer',
        'approved' => 'boolean'
    ];
}
