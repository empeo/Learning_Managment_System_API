<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Courses\CreateCoursesRequest;
use App\Http\Requests\Admin\Courses\EditCoursesRequest;
use App\Http\Requests\Admin\Courses\ShowCoursesRequest;
use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoursesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $courses = Course::with("reviews")->paginate($perPage);
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
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function store(CreateCoursesRequest $request)
    {
        try {
            $courseData = $request->validated();
            $imagePath = null;
            $imageFile = null;
            if ($request->has('image')) {
                $imageFile = $request->file('image');
                $imageName = time() . "." . Str::lower($imageFile->getClientOriginalExtension());
                $imagePath = 'images/courses';
                $courseData['image'] = $imageName;
            }
            $course = Course::create($courseData);
            if (!is_null($imagePath) and !is_null($imageFile)) {
                $imageFile->move(public_path($imagePath . "/" . $course->category->slug . "/" . $course->name), $imageName);
            }
            if ($request->has('videos')) {
                foreach ($request->file('videos') as $videoFile) {
                    $videoName = Str::random(5) . "." . Str::lower($videoFile->getClientOriginalExtension());
                    $videoPath = "videos/courses/" . $course->category->slug . "/" . $course->name;
                    $videoFile->move(public_path($videoPath), $videoName);
                    CourseVideo::create([
                        'course_id' => $course->id,
                        'path' => $videoPath . "/" . $videoName,
                        'clean_video' => $videoName,
                    ]);
                }
            }
            return response()->json(['message' => 'Course created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function show(ShowCoursesRequest $request)
    {
        try {
            $uuid = $request->validated();
            $course = Course::where("uuid", $uuid['uuid'])->with("videos")->first();
            if (!$course) {
                return response()->json(["message" => "Course not found"], 404);
            }
            return response()->json($course, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function update(EditCoursesRequest $request)
    {
        try {
            $courses = $request->validated();
            $course = Course::where("uuid", $request->uuid)->first();
            if (!$course) {
                return response()->json(["message" => "Course not found"], 404);
            }
            if ($request->has("image")) {
                if ($course->image) {
                    $coursePath = public_path("images/courses/" . $course->category->slug . "/" . $course->name . "/" . $course->image);
                    if (file_exists($coursePath)) {
                        unlink($coursePath);
                    }
                }
                $imageFile = $request->file('image');
                $imageName = time() . "." . Str::lower($imageFile->getClientOriginalExtension());
                $imageFile->move(public_path('images/courses/' . $course->category->slug . "/" . $course->name), $imageName);
            }
            $course->update($courses);
            return response()->json(["message" => "Course is Updated Successfuly"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function destroy(ShowCoursesRequest $request)
    {
        try {
            $request->validated();
            $course = Course::where("uuid", $request->uuid)->with("videos")->first();
            if (!$course) {
                return response()->json(["message" => "Course not found"], 404);
            }
            if ($course->image) {
                $coursePath = public_path("images/courses/" . $course->category->slug . "/" . $course->name . "/" . $course->image);
                if (file_exists($coursePath)) {
                    unlink($coursePath);
                }
            }
            if ($course->videos) {
                foreach ($course->videos as $video) {
                    $videoPath = public_path($video->path);
                    if (file_exists($videoPath)) {
                        unlink($videoPath);
                    }
                }
            }
            $course->delete();
            return response()->json(["message" => "Course is Deleted Successfuly"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
