<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CartDestroyRequest;
use App\Http\Requests\Cart\CartShowRequest;
use App\Http\Requests\Cart\CartStoreRequest;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->input('per_page', 10);
            $cartItems = Cart::where('user_id', $user->id)->paginate($perPage);
            if ($cartItems->isEmpty()) {
                return response()->json(["message" => "No items in the cart"]);
            }
            $cartItems->through(function ($cartItem) {
                return [
                    "id" => $cartItem->id,
                    "course" => $cartItem->course->name,
                    "count" => $cartItem->count,
                    "price" => $cartItem->course->price,
                    "total" => $cartItem->count * $cartItem->course->price,
                ];
            });
            return response()->json([
                'data' => $cartItems->items(),
                'meta' => [
                    'count' => $cartItems->count(),
                    'current_page' => $cartItems->currentPage(),
                    'first_item' => $cartItems->firstItem(),
                    'last_item' => $cartItems->lastItem(),
                    'last_page' => $cartItems->lastPage(),
                    'per_page' => $cartItems->perPage(),
                    'total' => $cartItems->total(),
                ],
                'links' => [
                    'first' => $cartItems->url(1),
                    'last' => $cartItems->url($cartItems->lastPage()),
                    'prev' => $cartItems->previousPageUrl(),
                    'next' => $cartItems->nextPageUrl(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function show(CartShowRequest $request){
        try {
            $user = Auth::user();
            $cartItem = Cart::where(['user_id'=> $user->id,"course_id"=>$request->course_id])->with('course')->first();
            if (!$cartItem) {
                return response()->json(["message" => "No items in the cart"]);
            }
            return response()->json(["message"=>$cartItem]);
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function store(CartStoreRequest $request)
    {
        try {
            $user = Auth::user();
            $order = Order::where(["user_id" => $user->id, "course_id" => $request->course_id])->first();
            if ($order) {
                return response()->json(["message" => "You have already purchased this course"]);
            }
            $cartItem = Cart::where(["user_id" => $user->id, "course_id" => $request->course_id])->with("course")->first();
            if ($cartItem) {
                $cartItem->count = $cartItem->count + $request->count;
                $cartItem->save();
            } else {
                $cartItem = Cart::create(["user_id" => $user->id, "course_id" => $request->course_id, "count" => $request->count]);
            }
            return response()->json(['message' => 'Item added to cart', 'data' => $cartItem], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(CartUpdateRequest $request)
    {
        try {
            $cartItem = Cart::find($request->id);
            if ($cartItem->user_id != Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            $cartItem->update(['count' => $request->count]);
            return response()->json(['message' => 'Cart item updated'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function destroy(CartDestroyRequest $request)
    {
        try {
            $cartItem = Cart::find($request->id);
            if ($cartItem->user_id != Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            $cartItem->delete();
            return response()->json(['message' => 'Cart item removed'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
