<?php

use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VideosController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Enrollment\EnrollmentStripeController;
use App\Http\Controllers\Pages\PagesController;
use App\Http\Controllers\Review\ReviewController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post("/login", "login")->name("auth.login");
    Route::post("/register", "register")->name("auth.register");
    Route::post('/email-verification', 'emailverfication')->name('auth.emailVerification');
    Route::post("/password/email", "forgetpassword")->name("password.forget");
    Route::post("/password/reset", "resetpassword")->name("password.reset");
})->middleware("guest");

Route::middleware(["auth:sanctum", "admin"])->group(function () {
    Route::get("/admin/dashboard", [AdminController::class, "index"])->name("admin.dashboard");
    Route::put("/admin/dashboard/accept/{id}", [AdminController::class, "orderAccept"])->name("admin.dashboard.accept");
    Route::put("/admin/dashboard/refuse/{id}", [AdminController::class, "orderCancelled"])->name("admin.dashboard.refuse");
    Route::resource("/admin/users", UsersController::class)->except(["create","edit"]);
    Route::resource("/admin/courses", CoursesController::class)->except(["create","edit"]);
    Route::resource("/admin/courses/course/videos", VideosController::class)->except(["create","edit"]);
});

Route::middleware(["auth:sanctum","user"])->group(function(){
    Route::get("/home",[CoursesController::class,"index"])->name("home.index");
    Route::get("/user/dashboard", [OrderController::class, "index"])->name("user.dashboard");
    Route::get("/user/dashboard/courses", [PagesController::class, "index"])->name("user.dashboard");
    Route::resource("/user/dashboard/orders",OrderController::class)->except(['index','create','edit']);
    Route::resource("/user/dashbaord/carts",CartController::class)->except(['create','edit']);
    Route::post("/user/dashboard/orders/create",[OrderController::class,"createOrderFromCart"])->name("orders.createOrderFromCart");
    Route::resource("/user/dashboard/reviews",ReviewController::class)->except(["index","create","edit","show"]);
});

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/profile", [AuthController::class, "profile"])->name("auth.profile");
    Route::put("/profile", [AuthController::class, "updateprofile"])->name("auth.updateprofile");
    Route::post("/logout", [AuthController::class, "logout"])->name("auth.logout");
    Route::post('user/enrollment/checkout-session', [EnrollmentStripeController::class, 'createCheckoutSession'])->name('enrollment.createCheckoutSession');
    Route::get('/checkout/success',[EnrollmentStripeController::class,"checkoutSuccess"])->name('checkout.success');
    Route::get('/checkout/cancel',[EnrollmentStripeController::class,"checkoutCancel"])->name('checkout.cancel');
});

