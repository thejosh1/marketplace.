<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Product;
use App\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Support\Facades\DB;

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
            'image' => 'image|mimes:jpeg,jpg,png|max:10000'
        ], $validationMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        //here collect all the values to array
        $categories = Category::pluck('name', 'id');
        $data = collect(request()->all()->except('image'))->$categories()->toArray();

        //define a function to upload the product image
        $image = $request->image;
        if ($image) {
            $imageName = $image->getClientOriginalName();
            $image->move('image', $imageName);
            $data['image'] = $imageName;
        }
        $result = Product::create($data);

        if ($result) {
            return response()->json(['data' => true], 201);
        } else {
            return response()->json(false, 500);
        }
    }


    public function uploadImage($productId, Request $request)
    {
        $product = Product::find($productId);

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
            return response()->json(false, 500);
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
            return response()->json(false, 500);
        }
    }

    public function show(Product $product)
    {
        return $product;

        //link with product review

    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'nullable|string|min:3',
        ]);

        $query = ('q');

        $product = Product::where('Products.id', '>', '0')->with('categories');

        if ($query) {
            $product = $product->search($query);
        }

        //insert search parameters
        $length = (int)(empty($request['perpage']) ? 15 : $request['perpage']);
        $product = $product->paginate($length);
        $data = new ProductsCollection($product);

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $id = (int)$request->route('id');
        if ($product = Product::find($id)) {
            $product->delete();
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
        $id = Product::onlyTrashed()->findorFail($id)->restore();
        if ($id) {
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json(false, 500);
        }
    }

    public function deleteImage(Request $request, $productId)
    {
        //find the product that the image belongs to
        $product = Product::find($productId);

        //get the image
        $image = $request['image'];
        if ($image) {
            $image->delete();
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json([
                'data' => false
            ], 404);
        }
    }

    public function restoreImage($id)
    {
        $id = Product::onlyTrashed()->find('image');
        if ($id) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 500);
        }
    }
}
