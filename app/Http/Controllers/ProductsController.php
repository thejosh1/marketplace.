<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Product;
use App\Category;
use App\ProductImage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\ProductsCollection;
use Nicolaslopezj\Searchable\SearchableTrait;
//use App\Http\Middleware\CheckUserRole;
//use App\Events\ProductInformationFetched;
use App\Attribute;
use App\Value;
// use DB;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use App\ProductReview;

class ProductsController extends Controller
{
    use SoftDeletes, SearchableTrait;


    public function store(Request $request)
    {

        //validation
        $validationMessages = [
            'required' => 'The :attribute field id required',
            'exists' => 'The specified :attribute reference_id does not exist',
            'integer' => 'The :attribute is of invalid type',
        ];

        $validator = Validator::make($request->all, [
            'name' => 'string|required|max:255',
            'category_id' => 'integer|required|exists:categories, id',
            'original_price' => 'required',
            'discount_price' => 'required',
            'in_stock' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png|max:10000',
            // 'color' => 'string|nullable',
            // 'size' => 'string|nullable',
            // 'weight' => 'float|nullable',
        ], $validationMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        //here collect all the values to array
        $categories = Category::pluck('name', 'id');
        $data = collect(request()->all()->except('image'))->$categories()->toArray();


        //define a function to upload the product image
        $image = $request->image;
        $this->$image = new ProductImage();
        if ($image) {
            $imageName = $image->getClientOriginalName();
            $image->move('image', $imageName);
            $data['image'] = $imageName;
        }

        $product = Product;
        $result = $product()->create($data);

        if ($result) {
            return response()->json(['data' => true], 201);
        } else {
            return response()->json(false, 401);
        }
    }


    public function uploadImage($productId, Request $request)
    {
        $product = Product::find($productId);
        // $auth = Auth::user()->hasRole('merchant');

        //upload image
        $image = $request->file('file');

        if ($image->hasFile) {
            $imageName = time() . getClientOriginalName();
            $image->move('images', $imageName);
            $imagePath = "/images/$imageName";
            $product->images()->create(['image_path' => $imagePath]);
        }

        if ($image) {
            return response()->json(['data' => true], 201);
        } else {
            return response()->json(false, 401);
        }
    }

    public function update(Request $request, $id)
    {
        //$product = Product::find($productId);

        //validation
        $validationMessages = [
            'required' => 'The :attribute field id required',
            'exists' => 'The specified :attribute reference_id does not exist',
            'integer' => 'The :attribute is of invalid type',
        ];

        $validator = Validator::make($request->all, [
            'name' => 'string|required|max:255',
            'category_id' => 'integer|required|exists:categories, id',
            'price' => 'required',
            'in_stock' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png|max:10000'
        ], $validationMessages);


        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        /*here collect all the values to array except the image
        because we need to seperately handle the imsge upload
        */
        $categories = Category::find($id);
        $data = collect(request()->all()->except('image'))->$categories()->toArray();

        //define a function to upload the product image
        $image = $request->image;
        if ($image) {
            $imageName = $image->getClientOriginalName();
            $image->move('image', $imageName);
            $data['image'] = $imageName;
        }

        $categories = Category::pluck('name', 'id');
        $product = Product::findorFail($id)->$categories;
        $result = $product->update($data);

        if ($result) {
            return response()->json(['data' => true], 201);
        } else {
            return response()->json(false, 401);
        }
    }

    public function show(Request $request)
    {
        $id = (int)$request->route('id');
        $product = Product::find($id);
       // $review = ProductReview::pluck('header', 'description', 'rating', 'approved', 'product_id');
        return $product;

        return response()->json([
            'data' => true
        ], 200);

        //link with product review

    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'nullable|min:3'
        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), 422);
        }

        $query = $request['q'];

        $product = Product::where('products.id', '>', '0')->with('categories');
        //$data = collect($request->all())->toArray();

        if ($query) {
            $products = $product->search($query);
        }
        //insert search parameters
        $length = (int)(empty($request['perpage']) ? 15 : $request['perpage']);
        $products = $product->paginate($length);
        $data = new ProductsCollection($products);

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->route('id');
        if ($product = Product::find($id)) {
            $product->auth()->user()->isAdmin()->delete();
            return response()->json([
                'data' => true
            ], 204);
        } else {
            return response()->json([
                'data' => false
            ], 404);
        };
    }

    public function restore($id)
    {
        // if (Auth::user()->isAdmin) {
        //     $admin_id = auth()->user()->id;
        // }

        $id = Product::onlyTrashed()->findorFail($id)->restore();
        /*i know im not supposed to do this but what the hell
        im trying to get admin authorization but id learn */
        if (Auth::user()->isAdmin) {
            if ($id) {
                return response()->json([
                    'data' => true
                ], 200);
            } else {
                return response()->json([
                    'error' => 'for some reason the product is not here'
                ], 404);
            }
        } else {
            return response()->json([
                'error' => 'you are not authorized'
            ], 401);
        }
    }

    public function deleteImage(Request $request, $productId)
    {
        //find the product that the image belongs to
        $product = Product::find($productId);

        //get the image
        $image = $request['image'];
        if ($image) {
            $auth = Auth::user()->isAdmin;
            $image->$auth->delete();
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json([
                'data' => false
            ], 401);
        }
    }

    public function restoreImage($id)
    {
        $id = Product::onlyTrashed()->find($id)->where('image')->restore();
        /* i know here we are again 
        i gotta find a better way to check 
        if user is admin without nesting an if in an if */
        if (Auth::user()->isAdmin) {
            if ($id) {
                return response()->json([
                    'data' => true
                ], 201);
            } else {
                return response()->json(false, 401);
            }
        } else {
            return response()->json([
                'error' => 'not authorized'
            ], 401);
        }
    }
}
