<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Courses\Videos\ShowVideosCoursesRequest;
use App\Http\Requests\Admin\Courses\Videos\UpdateVideosCoursesRequest;
use App\Http\Requests\Admin\Courses\Videos\VideosCoursesRequest;
use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Support\Str;

class VideosController extends Controller
{
    public function store(VideosCoursesRequest $request)
    {
        try {
            $course = Course::where("uuid", $request->uuid)->with("videos")->first();
            if (!$course) {
                return response()->json(['message' => 'Course not found'], 404);
            }
            if ($request->has('videos')) {
                foreach ($request->file('videos') as $videoFile) {
                    $videoName = Str::random(5) . '.' . Str::lower($videoFile->getClientOriginalExtension());
                    $videoPath = "videos/courses/" . $course->category->slug . "/" . $course->name;
                    $videoFile->move(public_path($videoPath), $videoName);
                    $course->videos()->create([
                        'course_id' => $course->id,
                        'path' => $videoPath . "/" . $videoName,
                        'clean_video' => $videoName,
                    ]);
                }
            }
            return response()->json(['message' => 'Videos added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function show(ShowVideosCoursesRequest $request)
    {
        try {
            $video = CourseVideo::where("uuid", $request->uuid)->first();
            if (!$video) {
                return response()->json(['message' => 'Video not found'], 404);
            }
            return response()->json(['video' => $video], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function update(UpdateVideosCoursesRequest $request)
    {
        try {
            $video = CourseVideo::where("uuid", $request->uuid)->first();
            if (!$video) {
                return response()->json(['message' => 'Video not found'], 404);
            }
            dd($request->has("videos"), $video->path);
            if ($request->has("videos")) {
                $videos = $request->file("videos");
                $maxFileSizeInMB = 100;
                $maxFileSizeInBytes = $maxFileSizeInMB * 1024 * 1024;
                if (!in_array($videos->getClientOriginalExtension(), ["mp4,mov,ogg"]) or ($videos->getSize() > $maxFileSizeInBytes)) {
                    return response()->json(['message' => 'Invalid file type or Invalid Size'], 400);
                }
                if ($video->path) {
                    if (file_exists(public_path($video->path))) {
                        unlink(public_path($video->path));
                    }
                }
                $videoName = Str::random(7) . "." . Str::lower($videos->getClientOriginalExtension());
                $videoPath = "videos/courses/" . $video->course->category->slug . "/" . $video->course->name;
                $videos->move(public_path($videoPath), $videoName);
                $video->update([
                    "path" => $videoPath . "/" . $videoName,
                    "clean_video" => $videoName,
                ]);
                $video->update($request->videos);
            }
            return response()->json(['message' => 'Video updated successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function destroy(ShowVideosCoursesRequest $request)
    {
        try {
            $video = CourseVideo::where("uuid", $request->uuid)->first();
            if (!$video) {
                return response()->json(['message' => 'Video not found'], 404);
            }
            if ($video->path) {
                if (file_exists(public_path($video->path))) {
                    unlink(public_path($video->path));
                }
            }
            $video->delete();
            return response()->json(["message" => "Video Is Deleted Successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
