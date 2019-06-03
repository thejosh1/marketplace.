<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Category;
use App\Product;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(5),
        'image' => $faker->imageUrl($width=200, $height=200),
        'slug' => str_replace('--', '-', strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', trim($faker->sentence())))),

    ];
});
