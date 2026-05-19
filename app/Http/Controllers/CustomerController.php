<?php

namespace App\Http\Controllers;

use App\Models\Order;

class CustomerController extends Controller
{
    private function customer()
    {
        return auth('api')->user();
    }

    private function isCustomer()
    {
        return $this->customer()
            && $this->customer()->role === 'customer';
    }

    public function dashboard()
    {
        if (!$this->isCustomer()) {

            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $customer = $this->customer();

        return response()->json([

            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'role' => $customer->role
            ],

            'stats' => [

                'total_orders' =>
                    Order::where(
                        'customer_id',
                        $customer->id
                    )->count(),

                'pending_orders' =>
                    Order::where(
                        'customer_id',
                        $customer->id
                    )
                    ->where('status', 'pending')
                    ->count(),

                'assigned_orders' =>
                    Order::where(
                        'customer_id',
                        $customer->id
                    )
                    ->where('status', 'assigned')
                    ->count(),

                'delivered_orders' =>
                    Order::where(
                        'customer_id',
                        $customer->id
                    )
                    ->where('status', 'delivered')
                    ->count()
            ]
        ]);
    }

    public function myOrders()
    {
        if (!$this->isCustomer()) {

            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $orders = Order::where(
            'customer_id',
            $this->customer()->id
        )
        ->with([
            'driver:id,name,email'
        ])
        ->latest()
        ->get();

        return response()->json($orders);
    }
}