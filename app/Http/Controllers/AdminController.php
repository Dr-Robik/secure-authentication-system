<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function orders()
    {
        $orders = Order::with([
            'customer',
            'driver'
        ])
        ->latest()
        ->get();

        return response()->json($orders);
    }

    public function assignDriver(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = User::where('role', 'driver')
            ->find($request->driver_id);

        if (!$driver) {

            return response()->json([
                'message' => 'Driver not found'
            ], 404);
        }

        $order = Order::find($orderId);

        if (!$order) {

            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $order->driver_id = $driver->id;
        $order->status = 'assigned';

        $order->save();

        return response()->json([
            'message' => 'Driver assigned successfully',
            'order' => $order
        ]);
    }

    public function drivers()
    {
        $drivers = User::where('role', 'driver')
            ->latest()
            ->get();

        return response()->json($drivers);
    }

    public function createDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:255'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driver = User::create([
            'name' => trim($request->name),
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => 'driver'
        ]);

        return response()->json([
            'message' => 'Driver created successfully',
            'driver' => $driver
        ], 201);
    }

    public function deleteDriver($id)
    {
        $driver = User::where('role', 'driver')
            ->find($id);

        if (!$driver) {

            return response()->json([
                'message' => 'Driver not found'
            ], 404);
        }

        $driver->delete();

        return response()->json([
            'message' => 'Driver deleted successfully'
        ]);
    }
}