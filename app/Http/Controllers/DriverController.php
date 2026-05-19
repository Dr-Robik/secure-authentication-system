<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function myOrders()
    {
        $driver = auth('api')->user();

        $orders = Order::where('driver_id', $driver->id)
            ->with('customer')
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:assigned,in_progress,completed'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = auth('api')->user();

        $order = Order::where('id', $id)
            ->where('driver_id', $driver->id)
            ->first();

        if (!$order) {

            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $order->status = $request->status;

        if ($request->status === 'completed') {
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }
}