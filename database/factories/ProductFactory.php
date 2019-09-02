<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Product;
//use App\Category;
//use App\Attribute;
//use App\Value;
use Faker\Generator as Faker;


$factory->define(Product::class, function (Faker $faker) {
    //$name = $faker->sentence(15);
    //$slug = Str::slug($name);
    // $attributes = Attribute::all()->toArray();
    // $value = Value::all()->toArray();

    return [
        'name' => $faker->sentence,
        'category_id' => $faker->numberBetween($min=1, $max=50),
        'price' => $faker->randomDigit,
        'in_stock' => $faker->numberBetween($min=1, $max=50),
        'description' => $faker->sentence(5),
        //'image' => $faker->imageUrl($width=200, $height=200),
        'slug' => str_replace('--', '-', strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', trim($faker->sentence(5))))),        
    ];

});
