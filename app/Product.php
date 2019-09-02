<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Http\Middleware\isAdmin;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class Product extends Model
{
    use SearchableTrait, SoftDeletes;

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'products.name' => 10,
            'products.description' => 1,
            'categories.title' => 5,
        ],
        'joins' => [
            'categories' => ['category_id', 'categories.id'],
        ],
    ];


    protected $fillable = ['name', 'category_id', 'original_price', 'description', 'in_stock'];

    public function categories()
    {
        return $this->belongsTo('App\Category');
    }

    public function Images()
    {
        return $this->hasMany('App\ProductImages');
    }

    public function getDiscount($id)
    {
        // $validator = Validator::make($request->all);

        // if($validator) {
        //     return response()->json([
        //         $validator => true
        //     ], 201);
        // } else {
        //     return response()->json($validator->messages('an unknown error occured'), [$validator => false, 500]);
        // }
        $product = Product::find($id);



        $price = DB::table('products')->where('price')->find($product);


        if ($price) {
            $discountPrice = function ($price) {
                $this->sum(10 / 100 * $price);
            };
        }
    }

    // public static function isAdmin()
    // {
    //     $admin = Auth::user()->isAdmin;
    // }

    // public static function getAdminId() {
    //     if (Auth::user()->isAdmin) {
    //         $admin_id = auth()->user()->id;
    //     }
    // }
}
