<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $courses = Course::with("reviews")->paginate($perPage);
            if ($courses->isEmpty()) {
                return response()->json(["message" => "Not Added Courses"]);
            }
            $courses->through(function ($course) {
                return [
                    "id" => $course->id,
                    "name" => $course->name,
                    "description" => $course->description,
                    "image" => $course->image,
                    "category" => $course->category->slug,
                    "level" => $course->level->slug,
                    "price" => $course->price,
                    "duration" => $course->duration,
                    "status" => $course->status,
                    "average_review" => $course->averageReview(),
                    "reviews" => $course->reviews->map(function ($review) {
                        return [
                            "id" => $review->id,
                            "rating" => $review->rating,
                            "user" => [
                                "name" => $review->user->first_name . " " . $review->user->last_name,
                            ],
                        ];
                    }),
                ];
            });
            return response()->json([
                'data' => $courses->items(),
                'meta' => [
                    'count' => $courses->count(),
                    'current_page' => $courses->currentPage(),
                    'first_item' => $courses->firstItem(),
                    'last_item' => $courses->lastItem(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
                'links' => [
                    'first' => $courses->url(1),
                    'last' => $courses->url($courses->lastPage()),
                    'prev' => $courses->previousPageUrl(),
                    'next' => $courses->nextPageUrl(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
