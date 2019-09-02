<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nicolaslopezj\Searchable\SearchableTrait;
use Lcobucci\JWT\Claim\Validatable;
use App\ProductReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Product;

class ProductsreviewController extends Controller
{
    use SearchableTrait, SoftDeletes;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'nullable|min:3',
        ]);

        if ($validator->fails) {
            return response()->json($validator->messages, 422);
        }

        $query = ('q');

        $data = collect($request->all())->toArray;
        $review = ProductReview::where('ProductReview', '>', '0');

        if ($query) {
            $result = $review->search($query);
        }

        $length = (int)(empty($request['per_page']) ? 15 : $request['per_page']);
        $review = $review->paginate($length);

        if ($review) {
            return response()->json([
                'data' => true
            ], 206);
        } else {
            return response()->json(false, 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $ProductId)
    {
        $validator = Validator::make($request->all(), [
            'header' => 'nullable|string|max:250',
            'text' => 'nullable|string|max:250',
            'rating' => 'nullable|string|max:250',
            'approved' => 'nullable|boolean|default:1',
            'product_id' => 'required|integer|exists:products, id'
        ]);

        if ($validator->fails) {
            return response()->json($validator->messages, 422);
        }

        $data = collect($request->all())->toArray();
        $data['product_id'] = $ProductId;
        $review = ProductReview::create($data);

        if ($review) {
            return response()->json([
                'data' => $review
            ], 201);
        } else {
            return response()->json([
                'data' => false
            ], 500);
        }
    }

        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
    public function show(ProductReview $productReview)
    {
        return $productReview;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $ProductId)
    {
        $validator = Validator::make($request->all(), [
            'header' => 'nullable|string|max:250',
            'text' => 'nullable|string|max:250',
            'rating' => 'nullable|string|max:250',
            'approved' => 'nullable|boolean|default:1'
        ]);

        if ($validator->fails) {
            return response()->json($validator->messages, 422);
        }

        $data = collect($request->all())->toArray();
        $data['product_id'] = $ProductId;
        $review = ProductReview::update($data);

        if ($review) {
            return response()->json([
                'data' => $review
            ], 201);
        } else {
            return response()->json([
                'data' => false
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = (int)$request->route('id');

         if($id) {
            $review = ProductReview::find($id);
            $review->delete;
            return response()->json([
                'data' => true
            ], 204);
        } else {
            return response()->json(false, 500);
        }
    }

    public function restore(Request $request)
    {
        $id = (int)$request->route('id');

         if($id) {
            ProductReview::onlyTrashed()->findorFail($id)->restore();
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 404);
        }
    }
}
