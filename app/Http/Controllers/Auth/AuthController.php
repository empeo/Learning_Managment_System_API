<?php

namespace App\Http\Controllers\Auth;
use App\Events\ProfileEvent;
use App\Events\ResetPasswordEvent;
use App\Events\VerficationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerficationRequest;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ProfileRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $remeber = $request->has("remember") ? true : false;
            $user = User::where("email", $request->email)->first();
            if (Auth::attempt($credentials, $remeber) && !is_null($user->email_verified_at)) {
                $user->tokens()->delete();
                $success["token"] = $user->createToken('auth_token')->plainTextToken;
                $success["message"] = "Login Successfully";
                $success["role"] = $user->role;
                return response()->json($success, 201);
            }
                return response()->json(['message' => 'Unauthorised'], 401);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
    public function register(RegisterRequest $request)
    {
        try {
            $user = $request->validated();
            $user['password'] = Hash::make($user['password']);
            $user['role'] = 'user';
            if ($request->has("image")) {
                $imageFile = $request->file("image");
                $imageName = time() . "." . Str::lower($imageFile->getClientOriginalExtension());
                $imageFile->move(public_path('images/users'), $imageName);
                $user['image'] = $imageName;
            }
            $user = User::create($user);
            $success["message"] = "User created successfully";
            $success["token"] = $user->createToken($user->first_name, ["App::all"])->plainTextToken;
            event(new VerficationEvent($user));
            return response()->json($success, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function emailverfication(EmailVerficationRequest $request)
    {
        try {
            $validatedata = $request->validated();
            $user = User::where('email', $validatedata["email"])->first();
            if (is_null($user["email_verified_at"])) {
                $user->update(['email_verified_at' => now()]);
                $success["message"] = "Email verified successfully";
                return response()->json($success, 201);
            }
            return response()->json(['message' => 'Is Verfied Before'], 402);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function profile()
    {
        try {
            $user = Auth::user();
            $success["message"] = "User found successfully";
            $success["data"] = $user;
            return response()->json($success, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 402);
        }
    }
    public function updateprofile(ProfileRequest $request)
    {
        try {
            $validatedata = $request->validated();
            $user = User::where("email", Auth::user()->email)->first();
            if (is_null($user)){
                return response()->json(['message' => 'User not found'], 402);
            }
            $user->update($validatedata);
            event(new ProfileEvent($user));
            $success["message"] = "User updated successfully";
            $success["data"] = $user;
            return response()->json($success, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function forgetpassword(ForgetPasswordRequest $request)
    {
        try {
            $request->validated();
            $status = Password::sendResetLink($request->only(["email"]));
            return $status == Password::RESET_LINK_SENT ? response()->json("Reset link sent successfully", 201) : response()->json("Reset link not sent", 402);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ensure Your Email!!'], 402);
        }
    }
    public function resetpassword(ResetPasswordRequest $request)
    {
        try {
            $request->validated();
            $status = Password::reset(
                request()->only(["email", "password", "password_confirmation", "token"]),
                function (User $user, string $password) {
                    $user->forceFill([
                        "password" => Hash::make($password),
                        "remember_token" => Str::random(60),
                    ])->save();
                    event(new ResetPasswordEvent($user));
                }
            );
            return $status == Password::PASSWORD_RESET ? response()->json("Password Is Updated", 201) : response()->json("Password Is Failed", 402);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred during password reset', 'error' => $e->getMessage()], 500);
        }
    }
    public function logout()
    {
        try {
            $user = User::where("email",Auth::user()->email)->first();
            $user->tokens()->delete();
            $success["message"] = "Logged out successfully";
            return response()->json($success, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
