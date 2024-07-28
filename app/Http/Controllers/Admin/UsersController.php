<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\CreateUsersRequest;
use App\Http\Requests\Admin\Users\EditUsersRequest;
use App\Http\Requests\Admin\Users\ShowUsersRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UsersController extends Controller
{
    public function index(Request $request)
{
    try {
        $perPage = $request->input('per_page', 10);
        $users = User::Where("role","!=","admin")->paginate($perPage);
        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'links' => [
                'first' => $users->url(1),
                'last' => $users->url($users->lastPage()),
                'prev' => $users->previousPageUrl(),
                'next' => $users->nextPageUrl(),
            ],
        ], 200);
    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()], 500);
    }
}
    public function store(CreateUsersRequest $request)
    {
        try {
            $user = $request->validated();
            $user['password'] = Hash::make($user['password']);
            if ($request->has("image")) {
                $imageFile = $request->file("image");
                $imageName = time() . "." . Str::lower($imageFile->getClientOriginalExtension());
                $imageFile->move(public_path('images/users'), $imageName);
                $user['image'] = $imageName;
            }
            $user = User::create($user);
            $success["message"] = "User created successfully";
            return response()->json($success, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function show(ShowUsersRequest $request)
    {
        try {
            $request->validated();
            $user = User::where('uuid', $request->uuid)->first();
            if ($user) {
                return response()->json(["data" => $user], 201);
            }
            return response()->json(['message' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function update(EditUsersRequest $request)
    {
        try{
            $validatedata = $request->validated();
            $user = User::where(["uuid"=>$request->uuid])->first();
            if($user){
                if ($request->has("image")) {
                    if ($user->image) {
                        $imagePath = public_path('images/users') . '/' . $user->image;
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $imageFile = $request->file("image");
                    $imageName = time() . "." . Str::lower($imageFile->getClientOriginalExtension());
                    $imageFile->move(public_path('images/users'), $imageName);
                    $user->image = $imageName;
                }
                $user->update($validatedata);
                return response()->json(["data"=>$user],201);
            }
            return response()->json(['message'=>'User not found'],404);
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
    public function destroy(ShowUsersRequest $request)
    {
        try {
            $request->validated();
            $user = User::where('uuid', $request->uuid)->first();
            if ($user){
                if ($user->image) {
                    $imagePath = public_path('images/users') . '/' . $user->image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $user->tokens()->delete();
                $user->delete();
                return response()->json(["message" => "User is Deleted Successfully"], 201);
            }
            return response()->json(['message' => 'User is Deleted Failed'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
