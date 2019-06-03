<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Category extends Model
{
    use SearchableTrait;

    protected $searchable = [
        'column' => [
            'categories.name' => 10,
        ],
        'joins' => [
            'products' => ['products.id', 'product_id'],
        ],
    ];

    protected $fillable = ['title'];

    Public function product () {
        return $this->hasMany(App\product::class);
    }
}
