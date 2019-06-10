<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoriesCollection;
use App\Category;
use Validator;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriesController extends Controller
{
    use SoftDeletes;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories)->additional(['meta' => [
            'version' => '1.0.0',
            'API_base_url' => url('/'),
        ]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatorMessages = [
            'required' => 'The :attribute field_id is required',
            'exists' => 'The specified :attribute reference_id is required',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'string|required|max:255',
            'image' => 'image|mimes:jpeg,jpg,png|max:10000'
        ], $validatorMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);

            //collect all the values to array
            $products = Product::pluck('name', 'image', 'id');
            $data = collect(request()->all()->except('image'))->$products->toArray();

            //upload the image
            $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,jpg,png|max:10000'
            ]);
            if ($validator->fails) {
                return response()->json(false, 401);
            }

            $image = $request->image;
            if ($image) {
                $imageName = $image->getClientOriginalName();
                $image->move('image', $imageName);
                $data['image'] = $imageName;
            }
            $result = Category::create($data);

            if ($result) {
                return response()->json([
                    'data' => true
                ], 201);
            } else {
                return response()->json(false, 500);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $categories)
    {
        return $categories;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatorMessages = [
            'required' => 'The :attribute field_id is required',
            'exists' => 'The specified :attribute reference_id is required',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'string|required|max:255',
            'image' => 'image|mimes:jpeg,jpg,png|max:10000'
        ], $validatorMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);

            //collect all the values to array
            $products = Product::pluck('name', 'image', 'id');
            $data = collect(request()->all()->except('image'))->$products->toArray();

            //upload the image
            $image = $request->image;
            if ($image) {
                $imageName = $image->getClientOriginalName();
                $image->move('image', $imageName);
                $data['image'] = $imageName;
            }
            $result = Category::update($data);

            if ($result) {
                return response()->json([
                    'data' => true
                ], 201);
            } else {
                return response()->json(false, 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id = (int)$request->route('id');
        if ($categories = Category::find($id)) {
            $categories->delete;
            return response()->json([
                'data' => true
            ], 206);
        } else {
            return response()->json(false, 500);
        }
    }

    public function restore($id)
    {

        $id = Category::onlyTrashed()->findorFail($id)->restore();
        if ($id) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json([
                'data' => false
            ], 404);
        }
    }

    public function uploadImage($productId, Request $request)
    {
        $category = Category::find($productId);

        $image = $request->file('file');

        if ($image->hasFile) {
            $imageName = time() . getClientOriginalName();
            $image->move('image', $imageName);
            $imagePath = 'image/$imageName';
            $category->images()->create(['image_path' => $imagePath]);
        }

        if ($image) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 500);
        }
    }

    public function deleteImage($productId, Request $request)
    {
        $pcategory = Category::find($productId);

        $image = $request['image'];

        if ($image->hasFile) {
            $image->delete;
            return response()->json([
                'data' => true
            ], 206);
        } else {
            return response()->json(false, 500);
        }
    }

    public function restoreImage($productId, Request $request)
    {
        $id = Category::onlyTrashed()->findorFail('image');

        if ($id->hasFile) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json([
                'data' => false
            ], 503);
        }
    }
}
