<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Product;
use App\Category;
use Faker\Generator as Faker;


$factory->define(Product::class, function (Faker $faker) {
    //$name = $faker->sentence(15);
    //$slug = Str::slug($name);

    return [
        'name' => $faker->sentence,
        'category_id' => $faker->numberBetween($min=1, $max=50),
        'price' => $faker->randomDigit,
        'in_stock' => $faker->numberBetween($min=1, $max=50),
        'description' => $faker->paragraph(50),
        'image' => $faker->imageUrl(/*'https://www.google.com/url?sa=i&source=images&cd=&ved=2ahUKEwib9vGC1cHiAhWKsBQKHfMRBrQQjRx6BAgBEAU&url=https%3A%2F%2Fwww.gsmarena.com%2Fapple_iphone_xs_max-9319.php&psig=AOvVaw2_TdSuxmLQKd-asArBKe70&ust=1559251067777356'*/ $width=200, $height=200),
        'slug' => str_replace('--', '-', strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', trim($faker->sentence(5))))),
        
    ];
});
