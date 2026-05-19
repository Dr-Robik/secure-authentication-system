<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();

        if ($user->role === 'customer') {

            $orders = Order::where('customer_id', $user->id)
                ->with('driver')
                ->latest()
                ->get();

            return response()->json($orders);
        }

        if ($user->role === 'driver') {

            $orders = Order::where('driver_id', $user->id)
                ->with('customer')
                ->latest()
                ->get();

            return response()->json($orders);
        }

        if ($user->role === 'admin') {

            $orders = Order::with([
                'customer',
                'driver'
            ])
            ->latest()
            ->get();

            return response()->json($orders);
        }

        return response()->json([], 200);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();

        if ($user->role !== 'customer') {

            return response()->json([
                'message' => 'Only customers can create orders'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'pickup_location' => 'required|string|max:255',
            'delivery_location' => 'required|string|max:255',
            'weight_kg' => 'required|numeric|min:0.1'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::create([
            'customer_id' => $user->id,
            'pickup_location' => trim($request->pickup_location),
            'delivery_location' => trim($request->delivery_location),
            'weight_kg' => $request->weight_kg,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    public function show($id)
    {
        $user = auth('api')->user();

        $order = Order::with([
            'customer',
            'driver'
        ])->find($id);

        if (!$order) {

            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        if (
            $user->role === 'customer' &&
            $order->customer_id !== $user->id
        ) {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        if (
            $user->role === 'driver' &&
            $order->driver_id !== $user->id
        ) {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        return response()->json($order);
    }

    public function cancel($id)
    {
        $user = auth('api')->user();

        $order = Order::find($id);

        if (!$order) {

            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->customer_id !== $user->id) {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        if ($order->status !== 'pending') {

            return response()->json([
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        $order->status = 'cancelled';

        $order->save();

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order
        ]);
    }
}