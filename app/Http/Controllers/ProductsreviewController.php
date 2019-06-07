<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nicolaslopezj\Searchable\SearchableTrait;
use Lcobucci\JWT\Claim\Validatable;
use App\ProductReview;
use Illuminate\Database\Eloquent\Collection;

class ProductsreviewController extends Controller
{
    use SearchableTrait;
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
    public function store(Request $request)
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



        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
