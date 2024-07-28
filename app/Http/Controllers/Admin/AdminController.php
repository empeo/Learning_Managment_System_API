<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrderEvent;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        try {
            $admin = Auth::user();
            $perPage = $request->input('per_page', 10);
            $orders = Order::paginate($perPage);
            if ($orders->isEmpty()) {
                return response()->json(["message" => "Nothing Orders To Display" , 'admin_name' => $admin->first_name . " " . $admin->last_name], 402);
            }
            $orders->through(function ($order) {
                return [
                    'id' => $order->id,
                    'course_name' => $order->course->name,
                    'course_duration' => $order->course->duration,
                    'userName' => $order->user->first_name . " " . $order->user->last_name,
                    'email' => $order->user->email,
                    'phone' => $order->user->phone,
                    'price' => $order->course->price,
                    'status' => $order->status,
                ];
            });
            return response()->json([
                'admin_name' => $admin->first_name . " " . $admin->last_name,
                'data' => $orders->items(),
                'meta' => [
                    'count' => $orders->count(),
                    'current_page' => $orders->currentPage(),
                    'first_item' => $orders->firstItem(),
                    'last_item' => $orders->lastItem(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
                'links' => [
                    'first' => $orders->url(1),
                    'last' => $orders->url($orders->lastPage()),
                    'prev' => $orders->previousPageUrl(),
                    'next' => $orders->nextPageUrl(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function orderAccept(Request $request)
    {
        try {
            $order = Order::find($request->id);
            if (!$order) {
                return response()->json(["message" => "Order Not Found"], 404);
            }
            if ($order->status == "completed" or $order->status == "cancelled") {
                return response()->json(["message" => "Order Already Accepted"], 400);
            }
            $order->status = 'completed';
            $order->save();
            event(new OrderEvent($order));
            return response()->json(["message" => "Order Accepted"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function orderCancelled(Request $request)
    {
        try {
            $order = Order::find($request->id);
            if (!$order) {
                return response()->json(["message" => "Order Not Found"], 404);
            }
            if ($order->status == "cancelled" or $order->status == "completed") {
                return response()->json(["message" => "Order Already Refused"], 400);
            }
            $order->status = 'cancelled';
            $order->save();
            event(new OrderEvent($order));
            return response()->json(["message" => "Order Cancelled"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
