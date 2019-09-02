<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OrderShipped;
use App\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    public function Orders($type = '')
    {
        if ($type == 'pending') {
            $orders = Order::where('delivered', '0')->get();
        } elseif ($type == 'delivered') {
            $orders = Order::where('delivered', '1')->get();
        } else {
            $orders = Order::all();
        }
        if ($orders) {
            return response()->json([
                'data' => $orders
            ], 200);
        } else {
            return response()->json(false, 404);
        }
    }

    public function ToggleOrders(Request $request, $orderId) 
    { 
        $order = Order::find($orderId);

        if($request->has('delivered')) {
            $time = Carbon::now()->addMinute(1);

            Mail::to($order->user())->later($time, new OrderShipped($order));
        } else {
            $order->delivered = "0";
        }
        $order->save();
        if($order) {
            return response()->json([
                'data' => $order
            ], 200);
        } else {
            return response()->json(false, 404);
        }
    }
}
