<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewDeleteRequest;
use App\Http\Requests\Review\ReviewStoreRequest;
use App\Http\Requests\Review\ReviewUpdateRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(ReviewStoreRequest $request)
    {
        try {
            $user_id = Auth::user()->id;
            $review = Review::where(["user_id"=>$user_id,"course_id"=>$request->course_id]);
            if(!$review){
                $review = Review::create(["user_id"=>$user_id,"course_id"=>$request->course_id,"rating"=>$request->rating]);
                if(!$review){
                    return response()->json(['message' => 'Failed Store Review'], 500);
                }
                return response()->json(["message"=>"Review created successfully"],201);
            }
            $review->update(["rating"=>$request->rating]);
            return response()->json(['message' => 'Review Updated successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(ReviewUpdateRequest $request)
    {
        try {
            $user_id = Auth::user()->id;
            if(!($request->rating >=1 and $request->rating <=5)){
                return response()->json(['message' => 'Rating must be between 1 and 5'],400);
            }
            $review = Review::where(["user_id"=>$user_id,"course_id"=>$request->course_id]);
            if(!$review){
                return response()->json(['message' => 'Failed Update Review'], 500);
            }
            $review->update(["rating"=>$request->rating]);
            return response()->json(['message' => 'Review updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(ReviewDeleteRequest $request)
    {
        try {
            $user_id = Auth::user()->id;
            $review = Review::where(["user_id"=>$user_id,"course_id"=>$request->course_id]);
            if(!$review){
                return response()->json(['message' => 'Failed Delete Review'], 500);
            }
            $review->delete();
            return response()->json(['message' => 'Review deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
