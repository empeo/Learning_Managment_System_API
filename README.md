# Learning_Managment_System_API
This project is a Learning Management System (LMS) built with Laravel, featuring a comprehensive set of routes to handle authentication, user and admin functionalities, course management, and enrollment with Stripe integration.

# Authentication Routes
The authentication routes handle user login, registration, email verification, and password management. These routes are only accessible to guests (unauthenticated users).

POST /login - User login
POST /register - User registration
POST /email-verification - Email verification
POST /password/email - Send password reset email
POST /password/reset - Reset password

# Admin Routes
Admin routes are protected by auth:sanctum and admin middleware, ensuring that only authenticated admin users can access these routes. They allow admins to manage the dashboard, users, courses, and videos.

GET /admin/dashboard - Admin dashboard
PUT /admin/dashboard/accept/{id} - Accept order
PUT /admin/dashboard/refuse/{id} - Refuse order
Resource /admin/users - Manage users (except create and edit)
Resource /admin/courses - Manage courses (except create and edit)
Resource /admin/courses/course/videos - Manage course videos (except create and edit)

# User Routes
User routes are protected by auth:sanctum and user middleware, ensuring that only authenticated users can access these routes. They allow users to view courses, manage their dashboard, orders, carts, and reviews.

GET /home - Home page with courses
GET /user/dashboard - User dashboard
GET /user/dashboard/courses - View user's courses
Resource /user/dashboard/orders - Manage orders (except index, create, and edit)
Resource /user/dashboard/carts - Manage carts (except create and edit)
POST /user/dashboard/orders/create - Create order from cart
Resource /user/dashboard/reviews - Manage reviews (except index, create, edit, and show)

# Profile and Enrollment Routes
These routes are protected by auth:sanctum middleware and allow authenticated users to manage their profile and handle enrollment using Stripe.

GET /profile - View profile
PUT /profile - Update profile
POST /logout - Logout
POST /user/enrollment/checkout-session - Create Stripe checkout session
GET /checkout/success - Checkout success
GET /checkout/cancel - Checkout cancel
