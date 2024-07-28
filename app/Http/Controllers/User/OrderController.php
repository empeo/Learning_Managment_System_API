<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Order\OrderStoreRequest;
use App\Http\Requests\User\Order\OrderUserDelete;
use App\Http\Requests\User\Order\OrderUserShow;
use App\Http\Requests\User\Order\OrderUserUpdate;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $user_name = Auth::user()->first_name . " " . Auth::user()->last_name;
            $perPage = $request->input('per_page', 10);
            $orders = Order::where("user_id", $user->id)->paginate($perPage);
            if ($orders->isEmpty()) {
                return response()->json(["message" => "Not Added Order"]);
            }
            $orders->through(function ($order) {
                return [
                    "id" => $order->id,
                    "name" => $order->course->name,
                    "category" => $order->course->category->slug,
                    "level" => $order->course->level->slug,
                    "price" => $order->price,
                    "duration" => $order->course->duration,
                    "count" => $order->count,
                ];
            });
            return response()->json([
                'user_name' => $user_name,
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
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function createOrderFromCart(OrderStoreRequest $request)
    {
        try {
            $user = Auth::user();
            $cartItem = Cart::where(['user_id' => $user->id, 'course_id' => $request->course_id])->first();
            if (!$cartItem) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }
            $order = Order::where(["user_id" => $cartItem->user_id, "course_id" => $cartItem->course_id])->first();
            if ($order) {
                $order->count = $order->count + $request->count;
                $order->price = $order->count * $order->course->price;
                $order->save();
                return response()->json(['message' => 'Order created successfully', 'data' => $order], 201);
            }
            $course = Course::findOrFail($request->course_id);
            $order = Order::create([
                'user_id' => $user->id,
                'course_id' => $cartItem->course_id,
                'price' => $cartItem->course->price * $cartItem->count,
                'count' => $cartItem->count,
                'status' => 'pending',
            ]);
            $userAdmin = User::where("email", "empop214@gmail.com")->first();
            $userAdmin->notify(new OrderCreatedNotification($order, $user, $course));
            $cartItem->delete();
            return response()->json(['message' => 'Order created successfully', 'data' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function store(OrderStoreRequest $request)
    {
        try {
            $user = Auth::user();
            $order = Order::where(["user_id" => $user->id, "course_id" => $request->course_id])->first();
            if ($order) {
                $order->count += $request->count;
                $order->price = $order->count * $order->course->price;
                $order->save();
            } else {
                $course = Course::findOrFail($request->course_id);
                $price = $request->count * $course->price;
                $order = Order::create([
                    "user_id" => $user->id,
                    "course_id" => $request->course_id,
                    "count" => $request->count,
                    "price" => $price,
                    "status" => "pending"
                ]);
            }
            $userAdmin = User::where("email", "empop214@gmail.com")->first();
            $userAdmin->notify(new OrderCreatedNotification($order, $user, $course));
            return response()->json(["message" => "Order created successfully", "data" => $order], 201);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function show(OrderUserShow $request)
    {
        try {
            $order = Order::where("uuid", $request->uuid)->first();
            if (!$order) {
                return response()->json(["message" => "Order Not Found"]);
            }
            $data['order_count'] = $order->count;
            $data['order_name'] = $order->course->name;
            $data['order_description'] = $order->course->description;
            $data['order_price'] = $order->price;
            $data['order_category'] = $order->course->category->slug;
            $data['order_level'] = $order->course->level->slug;
            $data['order_duration'] = $order->course->duration;
            $data['order_image'] = $order->course->image;
            $data['order_status'] = $order->course->status;
            return response()->json(["message" => $data], 201);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function update(OrderUserUpdate $request)
    {
        try {
            $order = Order::where("uuid", $request->uuid)->first();
            if (!$order) {
                return response()->json(["message" => "Order Not Found"], 404);
            }
            if ($request->count) {
                $order->update(["count" => $request->count, "price" => $order->course->price * $request->count]);
            }
            return response()->json(["message" => "Order updated successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function destroy(OrderUserDelete $request)
    {
        try {
            $order = Order::where("uuid", $request->uuid)->first();
            if (!$order) {
                return response()->json(["message" => "Order not found"], 404);
            }
            $order->delete();
            return response()->json(["message" => "Order Deleted is Successfully"]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
