<?php

namespace App\Http\Controllers\Enrollment;

use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\CheckoutSessionRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\StripeClient;

class EnrollmentStripeController extends Controller
{
    public $stripe;
    public function __construct()
    {
        $this->stripe = new StripeClient(config("stripe.api_keys.secret"));
    }
    public function createCheckoutSession(CheckoutSessionRequest $request)
    {
        $user = Auth::user();
        $order = Order::where(["user_id" => $user->id, "course_id" => $request->course_id, "status" => "completed"])->with(["user", "course"])->first();
        if (!$order) {
            return response()->json(['message' => 'You have not purchased this course, or it has not been accepted by the admin.'], 400);
        }
        try {
            $imageUrl = asset("images/courses/{$order->course->category->slug}/{$order->course->name}");
            $checkoutSession = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'EGP',
                        'product_data' => [
                            'name' => $order->course->name,
                            // 'images' => [$imageUrl],
                        ],
                        'unit_amount' => $order->price * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
            ]);
            return response()->json([
                'id' => $checkoutSession->id,
                'url' => $checkoutSession->url,
                'price' => $order->price,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function checkoutSuccess(Request $request)
    {
        try{
            $user = Auth::user();
            $order = Order::where(["user_id" => $user->id, "course_id" => $request->course_id])->first();
            if (!$order) {
                return response()->json(['message' => 'You have not purchased this course, or it hasnot been accepted by the admin.'], 400);
            }
            $order->update(["status" => "checkout"]);
            Enrollment::create(['user_id' => $user->id, 'course_id' => $order->course_id]);
            return response()->json(['message' => 'Payment Success'], 200);
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function checkoutCancel(Request $request)
    {
        try{
            $user = Auth::user();
            $order = Order::where(["user_id" => $user->id, "course_id" => $request->course_id])->first();
            if (!$order) {
                return response()->json(['message' => 'You have not purchased this course, or it hasnot been accepted by the admin.'], 400);
            }
            $order->update(["status" => "cancelled"]);
            return response()->json(['message' => 'Payment Cancelled'], 200);
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
