<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use App\Http\Resource\CartsCollection;
use App\Product;
use Gloudemans\Shoppingcart\CanBeBought;
use App\Cart;
use Illuminate\Mail\Message;

class CartsController extends Controller
{
    use Gloudemans\Shoppingcart\CanBeBought;
    public function addItem(Request $request)
    {
        $cart = Cart::add($request->id, $request->name, $request->price, $request->qty)->associate(Product)->paginate(10);

        if ($cart) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 500);
        }
    }

    public function UpdateCart(Request $request, $id)
    {
        $cart = Cart::Update($request->id, $request->name, $request->qty, $request->price)->associate(Product);

        if ($cart) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json([
                'data' => false
            ], 503);
        }
    }

    public function removeItemFromCart(Request $request, $id)
    {
        $id = (int)$request->route('id');
        if ($cart = Cart::get($id)->associate(Product)) {
            Cart::remove($cart);
            return response()->json([
                'data' => true
            ], 206);
        } else {
            return response()->json(false, 500);
        }
    }

    public function show(Request $request, $id)
    {
        $id = (int)$request['id'];
        if ($id) {
            $data = Cart::get($id);
            if ($data) {
                $item = $data->where($data)->contains(Product::where(['price', 'qty']));
            }
            if ($item) {
                return response()->json([
                    'data' => true
                ], 201);
            }
        } else {
            return response()->json([
                'data' => false
            ], 503);
        }
    }

    public function deleteCart(Request $request, $id)
    {
        $id = (int)$request['id'];
        if ($id) {
            Cart::destroy;
            return response()->json([
                'data' => true
            ], 204)->with(messages('cart removed'));
        } else {
            return response()->json(false, 500)->with(messages('could not remove cart try again another time'));
        }
    }

    public function list(Request $request)
    {
        $cart = Cart::content();

        $cart->count();
        $cart->total()->where(['price', 'qty']);

        if ($cart) {
            $cart->paginate(5);
            return response()->json([
                'data' => true
            ], 206);
        } else {
            return response()->json([
                'data' => false
            ], 401)->with(messages('you dont have any item in your cart yet'));
        }
    }
}
