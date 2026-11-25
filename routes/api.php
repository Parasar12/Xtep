<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Api\BrandApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\CartApiController;

use App\Http\Controllers\Api\AddressApiController;
use App\Http\Controllers\Api\SliderApiController;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Api\FirebaseController;
use App\Http\Controllers\Api\ShippingMethodsApiController;
use App\Http\Controllers\Api\FlashSaleApiController;
use App\Http\Controllers\Api\GeneralSettingApiController;
use App\Http\Controllers\Api\GuestCheckoutApiController;

use App\Http\Controllers\Api\BackendApiController;
use App\Http\Controllers\Api\GoogleController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::group(['middleware' => \App\Http\Middleware\CorsMiddleware::class], function () {


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/*Gmail Login/Signup*/
Route::post('/gmail/user', [GoogleController::class, 'verifyGmailUser']);



/*Auth Token Check*/
Route::post('/check_auth', [LoginApiController::class, 'checkUserAuthToken']);


/* Sliders */
Route::get('/sliders', [SliderApiController::class, 'index']);

/*  Brands  */
Route::get('/brands', [BrandApiController::class, 'getAllBrands']);
Route::post('/brand/products', [BrandApiController::class, 'getBrandProducts']);

/*  Categories  */
Route::get('/categories', [CategoryApiController::class, 'getAllCategories']);
Route::get('/main_categories', [CategoryApiController::class, 'getParentCategoryList']);
Route::get('/category/products', [CategoryApiController::class, 'getCategoryProducts']);

/*  Products  */
Route::get('/latest/products', [ProductApiController::class, 'latestProducts']);
Route::get('/category_banner/products', [ProductApiController::class, 'categoryBannerProducts']);
Route::post('/product/detail', [ProductApiController::class, 'productDetailByProductId']);


/*Search Products*/
Route::post('/product/search', [ProductApiController::class, 'searchProduct']);
Route::post('/product/related', [ProductApiController::class, 'relatedProducts']);

/* Product Review */
Route::post('/product/review', [ProductApiController::class, 'productReviewList']);
Route::get('/get_home_page_products_with_categories', [ProductApiController::class, 'get_home_page_products_with_categories']);

Route::post('/user/guest_checkout', [GuestCheckoutApiController::class, 'registerUser']);


/*  Flash Sales  */
Route::get('flash_sale', [FlashSaleApiController::class, 'getFlashSales']);

/* Logo */
Route::get('get_logo', [GeneralSettingApiController::class, 'getLogo']);

/* Orders */
Route::get('/deliveryStatus', [OrderApiController::class, 'getAllStatus']);

Route::get('/order/status', [OrderApiController::class, 'orderStatus']);

Route::post('/quick/order', [OrderApiController::class, 'quickOrder']);

Route::get('/districts', [AddressApiController::class, 'getAllDistricts']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    /*Notification*/
    Route::post('/fcm_token', [FirebaseController::class, 'saveUserFcmToken']);

    /*  Addresses  */
    Route::get('/user/addresses', [AddressApiController::class, 'index']);
    Route::post('/user/address/create', [AddressApiController::class, 'store']);
    Route::post('/user/address/edit', [AddressApiController::class, 'update']);
    Route::post('/user/address/delete', [AddressApiController::class, 'delete']);


    /* Orders */
    Route::post('/order/create', [OrderApiController::class, 'createOrder']);
    Route::post('/quick_order/create', [OrderApiController::class, 'createQuickOrder']);
    Route::get('/user/orders', [OrderApiController::class, 'getUserOrders']);
    Route::get('/order/payment_method', [OrderApiController::class, 'paymentMethods']);

    Route::post('/order/apply_coupon', [OrderApiController::class, 'checkCoupon']);

    /*Order Tracking*/
    Route::post('/order/track', [OrderApiController::class, 'orderTracking']);

    /*WishList*/
    Route::get('/user/wishlist', [ProductApiController::class, 'wishlistProducts']);
    Route::post('/product/wishlist/add', [ProductApiController::class, 'addToWishlist']);

    Route::post('/user/check_wishlist', [ProductApiController::class, 'checkWishlistProducts']);

    /*Product reviews*/
    Route::post('/product/reviews', [ProductApiController::class, 'productReviews']);


    /*Profile*/
    Route::get('/user/profile', [LoginApiController::class, 'userProfile']);
    Route::post('/user/profile/edit', [LoginApiController::class, 'editProfile']);
    Route::post('/user/contact/edit', [LoginApiController::class, 'editUserContact']);
    Route::post('/user/contact/verify', [LoginApiController::class, 'verifyEditContact']);
    Route::post('/user/change_password', [LoginApiController::class, 'changeUserPassword']);

    Route::post('/user/update_profile_image', [GeneralSettingApiController::class, 'updateProfilePicture']);


    /*Ticket*/
    Route::post('/user/create_ticket', [GeneralSettingApiController::class, 'createSupportTicket']);
    Route::post('ticket/details', [GeneralSettingApiController::class, 'ticketDetails']);
    Route::post('ticket/reply', [GeneralSettingApiController::class, 'replyTicket']);
    Route::get('ticket/category', [GeneralSettingApiController::class, 'ticketCategoryList']);
    Route::get('ticket_list', [GeneralSettingApiController::class, 'myTicketList']);




    /*Notifications*/
    Route::get('/user/notifications', [LoginApiController::class, 'userNotificationList']);


    /* Cart */
    Route::get('/user/cart', [CartApiController::class, 'index']);
    Route::post('/user/cart', [CartApiController::class, 'store']);
    Route::post('/user/update_cart', [CartApiController::class, 'updateCart']);
    Route::post('/user/remove_product', [CartApiController::class, 'removeProductFromCart']);
    Route::get('/user/clear_cart', [CartApiController::class, 'clearCartProducts']);


    /*Change Password*/
    Route::post('/user/change_password', [LoginApiController::class, 'changeUserPassword']);


    /* logout */
    Route::post('/logout', [LoginApiController::class, 'logout']);
});

/* Province */
Route::get('/provinces', [AddressApiController::class, 'getAllProvinces']);

/* District */
Route::post('/province/districts', [AddressApiController::class, 'getProvinceDistricts']);

/* Login */
Route::post('/login', [LoginApiController::class, 'login']);
Route::post('/login/account_reset', [LoginApiController::class, 'sendAccountResetOTP']);
Route::post('/login/account_reset_verify', [LoginApiController::class, 'verifyAccountResetOTP']);

/* Signup */
Route::post('/signup', [LoginApiController::class, 'signup']);
Route::post('/signup/verify', [LoginApiController::class, 'verifyAccountSignup']);
Route::post('/user/resend_signup_otp', [LoginApiController::class, 'resendSignupOTP']);


/*Forgot Password*/
Route::post('/user/forgot_password', [LoginApiController::class, 'forgotUserPassword']);
Route::post('/user/verify_forgot_password_otp', [LoginApiController::class, 'verifyForgotPasswordOtp']);
Route::post('/user/add_new_password', [LoginApiController::class, 'addNewUserPassword']);

/* push notification */
// Route::post('/send/notification', [FirebaseController::class, 'sendNotification']);

/* Shipping Methods */
Route::get('/shipping/methods', [ShippingMethodsApiController::class, 'index']);


// });