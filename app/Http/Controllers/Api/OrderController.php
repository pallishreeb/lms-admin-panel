<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // Validate and create an order in your database

        // Use Razorpay PHP SDK to create a Razorpay order
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        $order = $api->order->create([
            'amount' => $request->amount, // Amount in paise
            'currency' => 'INR',
            'payment_capture' => 1,
        ]);

        // Return the order ID to the React Native app
        return response()->json(['orderId' => $order->id, 'amount' => $order->amount]);
    }

    public function getOrders()
    {
        // Your logic for fetching orders goes here
    }

}
