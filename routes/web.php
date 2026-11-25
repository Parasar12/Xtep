<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryBannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DeviceAccessoryController;
use App\Http\Controllers\Admin\DeviceConditionController;
use App\Http\Controllers\Admin\DeviceManagementController;
use App\Http\Controllers\Admin\DeviceSaleController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\admin\FiananceReportController;
use App\Http\Controllers\Admin\FlashSalesController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\admin\LogisticVendorReport;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NavCategoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\admin\OrderStatusReportController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfitLossReportController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\SalePaymentController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SocialSiteController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\IncomeExpenseReportController;
use App\Http\Controllers\IpTrackController;
use App\Http\Controllers\LogisticVendorController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SearchProductController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\WishlistController;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth::routes();

Route::get('admin', function () {
    if (auth()->user()) {
        if (auth()->user()->is_admin) {
            return redirect('admin/dashboard');
        } else {
            return redirect('/');
        }
    } else {
        return view('auth.login');
    }
});

Route::post('admin', [UserProfileController::class, 'adminLogin'])->name('admin.login');

Route::get('user/login', function () {
    if (auth()->user()) {
        return redirect('/account');
    } else {
        return view('frontend.user.login-register');
    }
});

Route::get('user/order-request', function () {
    if (auth()->user()) {
        return redirect('/account');
    } else {
        $provinces = Province::all();
        $disctricts = District::all();
        return view('frontend.user.order-request', compact('provinces', 'disctricts'));
    }
});

Route::post('user/login', [UserProfileController::class, 'userLogin'])->name('user.login');
Route::post('user/signup', [UserProfileController::class, 'userSignup'])->name('user.signup');
Route::post('user/order-request', [UserProfileController::class, 'orderRequest'])->name('user.orderRequest');

Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('home');

//google login
Route::get('google-login', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/* Frontend */
Route::get('/', [FrontendController::class, 'index'])->name('front.index');
Route::get('/about', [FrontendController::class, 'about'])->name('front.about');
Route::get('/privacy', [FrontendController::class, 'privacy'])->name('front.privacy');
Route::get('/terms_and_conditions', [FrontendController::class, 'termsConditions'])->name('front.terms_conditions');

Route::get('ipaddress', [FrontendController::class, 'searchIP']);
Route::get('searchHistory', [FrontendController::class, 'searchProduct']);

Route::get('/faq', [FrontendController::class, 'faq'])->name('front.faq');

Route::get('/contact', [FrontendController::class, 'contact'])->name('front.contact');
Route::post('track/order', [FrontendController::class, 'trackOrder'])->name('track.order');
Route::get('/category/{slug}', [FrontendController::class, 'getCategoryProducts'])->name('front.category.products');

Route::get('/product/{slug}', [FrontendController::class, 'getProductDetails'])->name('front.product.details');

Route::get('search', [FrontendController::class, 'search'])->name('search');
Route::post('fetchMoreProducts', [FrontendController::class, 'fetchMoreProducts'])->name('fetchMoreProducts');

Route::post('/contact', [FrontendController::class, 'saveContact'])->name('contact.store');
/* Frontend */

/* Cart */
Route::get('cart', [CartController::class, 'cartList'])->name('cart.list');
Route::post('cart', [CartController::class, 'addToCart'])->name('cart.store');
Route::post('update-cart', [CartController::class, 'updateCart'])->name('cart.update');
Route::get('remove/', [CartController::class, 'removeCart'])->name('cart.remove');
Route::post('clear/cart', [CartController::class, 'clearAllCartItems'])->name('cart.clear');
Route::get('get/cart', [CartController::class, 'getCart'])->name('getCart');
Route::get('get/cart-count', [CartController::class, 'cartCount'])->name('cart.cart_count');
Route::get('get/singleProductPrice', [CartController::class, 'singleProductPrice'])->name('cart.singleProductPrice');
Route::get('get/totalPrice', [CartController::class, 'totalPrice'])->name('cart.totalPrice');

//side cart
Route::get('get-header-cart', [CartController::class, 'getHeaderCart'])->name('getHeaderCart');

/* Flash Sales */
Route::get('flash-sales/{id}', [FrontendController::class, 'showFlashSaleProducts'])->name('sales.show');

Route::post('quick/order', [FrontendController::class, 'quick_order'])->name('quick_order');
Route::get('/getDistricts', [FrontendController::class, 'getDistricts'])->name('getDistricts');

Route::get('order_show/order_id={code}', [OrderController::class, 'order_show'])->name('order_show');

Route::post('complete_order_from_url', [OrderController::class, 'complete_order_from_url'])->name('complete_order_from_url');

Route::middleware(['admin'])->group(function () {
    Route::post('admin/logout', [UserProfileController::class, 'adminLogout'])->name('admin.logout');
    /* Brands */
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/getBrands', [BrandController::class, 'getBrands'])->name('brands.get');
    Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/edit/{id}', [BrandController::class, 'edit'])->name('brands.edit');
    Route::post('/brands/edit', [BrandController::class, 'update'])->name('brands.update');
    Route::post('/deleteBrands', [BrandController::class, 'deleteBrands'])->name('brands.delete');
    Route::get('/search-brand', [BrandController::class, 'search'])->name('brands.search');


    /* Categories */
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/getCategories', [CategoryController::class, 'getCategoriesList'])->name('categories.get');

    Route::get('/add_category', [CategoryController::class, 'getAddCategoryForm'])->name('category.add');
    Route::post('/add_category', [CategoryController::class, 'addCategory'])->name('category.store');
    Route::get('/view_category', [CategoryController::class, 'viewCategory'])->name('category.view');

    Route::get('/edit_category', [CategoryController::class, 'getEditCategoryForm'])->name('category.edit');
    Route::post('/edit_category', [CategoryController::class, 'editCategory'])->name('category.update');
    Route::post('/delete_category', [CategoryController::class, 'deleteCategory'])->name('category.delete');
    Route::get('/search-category', [CategoryController::class, 'search'])->name('category.search');



    /* Products */
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/getProducts', [ProductController::class, 'getProducts'])->name('products.get');
    Route::post('/delete_products', [ProductController::class, 'deleteProducts'])->name('products.delete');
    Route::get('/products/add', [ProductController::class, 'add'])->name('products.add');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products/view', [ProductController::class, 'view'])->name('products.view');
    Route::post('/products/edit', [ProductController::class, 'update'])->name('products.update');
    Route::get('/product/price_quantity/{id}', [ProductController::class, 'getProductSellingPriceAndQuantity'])->name('product.price.quantity');
    Route::get('/product/check/{id}', [ProductController::class, 'checkExistingProduct'])->name('product.check');
    Route::get('product-stock', [ProductController::class, 'productInStock'])->name('product.stock');
    Route::get('product-stock-count', [ProductController::class, 'getProductStockCount'])->name('product.stock.get');
    Route::get('deleteAttribute', [ProductController::class, 'deleteAttribute'])->name('deleteAttribute');
    Route::get('get_product_attributes', [ProductController::class, 'get_product_attributes'])->name('get_product_attributes');
    Route::get('checkProductIsInStock', [ProductController::class, 'checkProductIsInStock'])->name('checkProductIsInStock');
    Route::post('updateProductAttribute/{attribute_id}', [ProductController::class, 'updateProductAttribute'])->name('updateProductAttribute');

    //getProduct stock report details page
    Route::get('product-stock/details', [ProductController::class, 'getReportDetails'])->name('product.stock.details');

    Route::get('product-sale', [ProductController::class, 'productSale'])->name('product.sale');
    Route::get('product-sale-count', [ProductController::class, 'getProductSaleCount'])->name('product.sale.count');

    Route::get('/products/bulk-import', [ProductController::class, 'bulkImport'])->name('products.bulk-import');
    Route::post('/products/bulk-import', [ProductController::class, 'bulkImportStore'])->name('products.bulk-import.store');

    Route::get('/products/bulk-export', [ProductController::class, 'bulkExport'])->name('products.bulk-export');

    Route::get('/gallary', [ProductController::class, 'gallary'])->name('products.gallary.index');
    Route::get('/getProductGallary', [ProductController::class, 'getProductGallary'])->name('product.gallary.get');
    Route::post('/gallary/store', [ProductController::class, 'gallaryStore'])->name('product.gallary.store');
    Route::get('/gallary/edit/{id}', [ProductController::class, 'gallaryEdit'])->name('product.gallary.edit');
    // Route::post('/deleteGallery', [ProductController::class, 'deleteGallery'])->name('gallery.delete');

    //product attribute form
    Route::get('/get-product-attribute-form', [ProductController::class, 'getAttributeForm'])->name('product.attribute.form');


    Route::post('/gallary/edit', [ProductController::class, 'gallaryUpdate'])->name('product.gallary.update');
    Route::post('/gallary/image/delete', [ProductController::class, 'gallaryImageDelete'])->name('product.gallary.image.delete');

    // deleted Product
    Route::get('/products/deleted_product', [ProductController::class, 'deleted_product'])->name('products.deleted_product');
    Route::post('/getDeletedProduct', [ProductController::class, 'getDeletedProduct'])->name('products.getDeletedProduct');
    Route::post('/undoProducts', [ProductController::class, 'undoProducts'])->name('products.undo');

    //multiple product delete

    Route::get('/deleteMultipleProducts', [ProductController::class, 'deleteMultipleProducts'])->name('multiple_product.delete');
    Route::get('/undoMultipleProducts', [ProductController::class, 'undoMultipleProducts'])->name('undo_multiple.delete');


    /*  Product Attributes*/
    Route::resource('attributes', AttributeController::class);
    Route::get('/getAttributes', [AttributeController::class, 'getAttributes'])->name('attributes.get');
    Route::post('/deleteAttributes', [AttributeController::class, 'deleteAttributes'])->name('attributes.delete');
    Route::get('/search-attribute', [AttributeController::class, 'search'])->name('attribute.search');


    Route::get('/search-sku-products', [ProductController::class, 'searchProductSKU'])->name('products.sku.search');
    Route::get('/get-form-sku-products', [ProductController::class, 'getProductSKUForm'])->name('product.sku.product.form');

    //new product attribute form
    Route::get('product-variant-form', [ProductController::class, 'getProductVariantForm'])->name('product.variant.form');
    Route::post('product-variant-form', [ProductController::class, 'postProductVariantForm'])->name('product.variant.form');

    //update extising product
    Route::get('product-edit-sku/', [ProductController::class, 'editProductBySKU'])->name('products.sku.edit');
    Route::post('product-update-sku', [ProductController::class, 'updateProductBySku'])->name('products.sku.update');

    /*  Flash Sales */
    Route::resource('flash-sales', FlashSalesController::class);
    Route::post('/getFlashSales', [FlashSalesController::class, 'getFlashSales'])->name('flashSales.get');
    Route::post('/deleteFlashSales', [FlashSalesController::class, 'deleteFlashSales'])->name('flash_sales.delete');


    /* Users */
    Route::resource('users', UserController::class);
    Route::get('/getUsers', [UserController::class, 'getUsers'])->name('users.get');
    Route::post('/deleteUser', [UserController::class, 'deleteUser'])->name('users.delete');

    Route::get('customers', [UserController::class, 'getCustomers'])->name('customers.index');
    Route::get('/get_customer', [UserController::class, 'getCustomerList'])->name('customers.get');
    Route::post('/add_customer_address', [UserController::class, 'add_customer_address'])->name('add_customer_address');
    Route::get('/getCustomerAddresses', [UserController::class, 'getCustomerAddresses'])->name('getCustomerAddresses');


    /* Appearance */
    Route::resource('general', GeneralSettingController::class);
    Route::get('mail', [MailController::class, 'index'])->name('mail.index');
    Route::post('mail', [MailController::class, 'store'])->name('mail.store');


    /*Notification*/
    Route::get('notification', [NotificationController::class, 'index'])->name('notification.index');

    /*Customer Ticket*/
    Route::get('support_ticket', [TicketController::class, 'index'])->name('ticket.index');
    Route::get('getTicket', [TicketController::class, 'getTicket'])->name('ticket.get');
    Route::get('ticket/reply/{id}', [TicketController::class, 'ticketConversations'])->name('ticket.reply');
    Route::post('ticket/reply', [TicketController::class, 'ticketReplyStore'])->name('ticket.reply.store');


    Route::get('ticket/issue_category', [TicketController::class, 'issueCategoryIndex'])->name('ticket.issue.index');
    Route::get('getIssueCategory', [TicketController::class, 'getIssueCategory'])->name('ticket.issue.get');
    Route::get('ticket/issue_category/edit/{id}', [TicketController::class, 'issueCategoryEdit'])->name('ticket.issue.edit');
    Route::post('ticket/issue_category', [TicketController::class, 'issueCategoryStore'])->name('ticket.issue.store');
    Route::post('ticket/issue_category/edit', [TicketController::class, 'issueCategoryUpdate'])->name('ticket.issue.update');


    /*Social Site*/
    Route::get('social_site', [SocialSiteController::class, 'index'])->name('social_site.index');
    Route::get('getSocialSites', [SocialSiteController::class, 'getSocialSites'])->name('social_site.get');
    Route::get('/social_site/edit/{id}', [SocialSiteController::class, 'edit'])->name('social_site.edit');
    Route::post('/social_site/edit', [SocialSiteController::class, 'update'])->name('social_site.update');
    Route::post('social_site', [SocialSiteController::class, 'store'])->name('social_site.store');


    /* Order */
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/getOrders/{status}', [OrderController::class, 'getOrders'])->name('orders.get');
    Route::get('/order/detail/{code}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('change/order/status', [OrderController::class, 'changeOrderStatus'])->name('change.order.status');
    Route::get('check_coupon_code', [OrderController::class, 'check_coupon_code'])->name('check_coupon_code');
    Route::get('further_process/{id}', [OrderController::class, 'further_process'])->name('orders.further_process');
    Route::post('further_process/', [OrderController::class, 'updateFurtherProcess'])->name('orders.updateFurtherProcess');
    Route::get('change_order_status/', [OrderController::class, 'change_order_status'])->name('change_order_status');
    Route::post('change_order_status/', [OrderController::class, 'change_order_status_save'])->name('change_order_status.save');
    Route::get('check_order_status/', [OrderController::class, 'check_order_status'])->name('check_order_status');
    Route::get('cancellation_payment_settlement/', [OrderController::class, 'cancellation_payment_settlement'])->name('cancellation_payment_settlement');
    Route::post('cancellation_payment_settlement/', [OrderController::class, 'cancellation_payment_settlement_save'])->name('cancellation_payment_settlement.save');
    Route::get('sendMail/', [OrderController::class, 'sendMail'])->name('sendMail');
    Route::get('print_order/', [OrderController::class, 'print_order'])->name('print_order');
    Route::get('edit/{order_id}', [OrderController::class, 'edit'])->name('orders.edit');
    Route::post('update', [OrderController::class, 'update'])->name('orders.update');
    Route::get('countTotalStatusRecords', [OrderController::class, 'countTotalStatusRecords'])->name('countTotalStatusRecords');
    Route::get('countFilteredRecords', [OrderController::class, 'countFilteredRecords'])->name('countFilteredRecords');
    Route::get('view_order/{code}', [OrderController::class, 'viewOrder'])->name('view_sale');
    Route::get('getCustomerProfile', [OrderController::class, 'getCustomerProfile'])->name('getCustomerProfile');
    Route::get('view_logistic_vendor_order_log', [OrderController::class, 'view_logistic_vendor_order_log'])->name('view_logistic_vendor_order_log');
    Route::get('pay_to_logistic_vendor', [OrderController::class, 'pay_to_logistic_vendor'])->name('pay_to_logistic_vendor');
    Route::get('getCustomerPayment', [OrderController::class, 'getCustomerPayment'])->name('getCustomerPayment');


    /* Sliders */
    Route::resource('sliders', SliderController::class);
    Route::post('/getSliders', [SliderController::class, 'getSliders'])->name('sliders.get');
    Route::post('/delete_sliders', [SliderController::class, 'deleteSliders'])->name('sliders.delete');


    /*  Coupons */
    Route::resource('coupons', CouponController::class);
    Route::get('/getCoupons', [CouponController::class, 'getCoupons'])->name('coupons.get');

    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
    Route::post('/features', [FeatureController::class, 'update'])->name('features.update');

    /* Faqs */
    Route::resource('faqs', FaqController::class);

    /* Category Banners */
    Route::get('banners', [CategoryBannerController::class, 'index'])->name('banners.index');
    Route::get('/getBanners', [CategoryBannerController::class, 'getBanners'])->name('banners.get');
    Route::get('/banners/edit/{id}', [CategoryBannerController::class, 'edit'])->name('banners.edit');
    Route::post('/banners', [CategoryBannerController::class, 'store'])->name('banners.store');
    Route::post('/banners/edit', [CategoryBannerController::class, 'update'])->name('banners.update');
    Route::post('/deleteBanners', [CategoryBannerController::class, 'deleteBanners'])->name('banners.delete');


    /* Wishlists */
    Route::get('product-in-wishlist', [WishlistController::class, 'productInWishlist'])->name('product.wishlist');
    Route::get('products/wishlist/count', [WishlistController::class, 'getProductWishlistCount'])->name('product.wishlist.get');

    /*Menu Setup*/
    Route::get('/menu_setup', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/get_menu', [MenuController::class, 'getMenuList'])->name('menu.get');

    Route::get('/add_menu', [MenuController::class, 'getAddMenuForm'])->name('menu.add');
    Route::post('/add_menu', [MenuController::class, 'addMenu'])->name('menu.store');

    Route::get('/edit_menu', [MenuController::class, 'getEditMenuForm'])->name('menu.edit');
    Route::post('/edit_menu', [MenuController::class, 'editMenu'])->name('menu.update');

    Route::get('/view_menu', [MenuController::class, 'viewMenuDetail'])->name('menu.view');

    Route::get('/aboutus-index', [AboutUsController::class, 'index'])->name('aboutus.index');
    Route::patch('/aboutus-store', [AboutUsController::class, 'update'])->name('aboutus.update');

    /*Permission Management*/
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('/permission/get_user', [PermissionController::class, 'getUserList'])->name('permission.getuser');
    Route::get('/permission/assign', [PermissionController::class, 'assignPermissionIndex'])->name('permission.assign');
    Route::post('/permission/save', [PermissionController::class, 'saveAssignedPermission'])->name('permission.save');

    //faq
    Route::get('/admin-faq', [FaqController::class, 'index'])->name('faq.index');
    Route::get('/getFaq', [FaqController::class, 'getFaq'])->name('faq.get');
    Route::get('/faq/add', [FaqController::class, 'add'])->name('faq.add');
    Route::post('/faq-store', [FaqController::class, 'store'])->name('faq.store');
    Route::get('/faq/edit', [FaqController::class, 'edit'])->name('faq.edit');
    Route::post('/faq/update', [FaqController::class, 'update'])->name('faq.update');

    //privacy_policy
    Route::get('admin-privacy_toc/privacy', [Controller::class, 'privacy'])->name('admin.privacy');
    Route::get('admin-privacy_toc/toc', [Controller::class, 'toc'])->name('admin.toc');
    Route::post('admin-privacy_toc-store', [Controller::class, 'storePtoc'])->name('admin.ptoc.store');


    //Contact Us
    Route::controller(ContactController::class)->group(function () {
        Route::get('/admin/contact', 'index')->name('contact.index');
        Route::get('/get-contact-list', 'getList')->name('contact.get');
        Route::get('/get-contact-view', 'view')->name('contact.view');
    });

    //NavCategory
    Route::controller(NavCategoryController::class)->group(function () {
        Route::get('/admin/nav-category', 'index')->name('nav.category.index');
        Route::post('/admin/nav-category', 'store')->name('nav.category.index');
    });

    //IP Track
    Route::controller(IpTrackController::class)->prefix('admin/ip_track')->group(function () {
        Route::get('/', 'index')->name('ip_address.index');
        Route::post('/getIpAddress', 'getIpAddress')->name('ip_address.getList');
        Route::get('/delete', 'delete')->name('ip_track.delete');
    });


    //Product search by client
    Route::controller(SearchProductController::class)->prefix('admin/client_search')->group(function () {
        Route::get('/', 'index')->name('customer_search_products,.index');
        Route::post('/customer_search_products', 'getCustomerSearchProduct')->name('customer_search_products.get');
        Route::get('/delete', 'delete')->name('searched_record.delete');
    });

    //common controller
    Route::controller(CommonController::class)->prefix('search')->group(function () {
        Route::get('/product_search', 'productSearch')->name('product.searchProducts');
        Route::get('/search_customer', 'searchCustomer')->name('searchCustomer');
        Route::get('/getProductPrice', 'getProductPrice')->name('getProductPrice');
        Route::post('/store', 'store')->name('sales_entry.store');
        Route::get('/getCouponDiscountPrice', 'getCouponDiscountPrice')->name('getCouponDiscountPrice');
        Route::post('/save_user_from_order', 'save_user_from_order')->name('save_user_from_order');
        Route::get('/vendors_search', 'vendors_search')->name('vendors.search');
    });

    //sales entry
    Route::controller(SaleController::class)->prefix('admin/sales_entry')->group(function () {
        Route::get('/', 'index')->name('sales_entry.index');
        Route::post('/getSales', 'getSales')->name('sales_entry.get');
        Route::get('/add', 'add')->name('sales_entry.add');
        Route::post('/store', 'store')->name('sales_entry.store');
        Route::post('view', 'view')->name('sales_entry.view');
        Route::get('getSalesPaymentView', 'getSalesPaymentView')->name('getSalesPaymentView');
        Route::post('payment_store', 'payment_store')->name('sales_payment_store');
        Route::get('getShippingAmount', 'getShippingAmount')->name('getShippingAmount');
        Route::get('getDeliveryVendorCharge', 'getDeliveryVendorCharge')->name('getDeliveryVendorCharge');
    });


    Route::controller(PurchaseReturnController::class)->prefix('admin/purchase_return')->group(function () {
        Route::get('/', 'index')->name('purchase_return.index');
        Route::get('/getPurchaseReturn', 'getPurchaseReturn')->name('purchase_return.get');
        Route::post('/store', 'store')->name('purchase_return.store');
        Route::get('view', 'view')->name('purchase_return.view');
        Route::get('getProductDetailsBySku', 'getProductDetailsBySku')->name('getProductDetailsBySku');
    });

    Route::controller(SaleReturnController::class)->prefix('admin/sales_return')->group(function () {
        Route::get('/', 'index')->name('sales_return.index');
        Route::get('/getSalesReturn', 'getSalesReturn')->name('sales_return.get');
        Route::post('/store', 'store')->name('sales_return.store');
        Route::get('view', 'view')->name('sales_return.view');
        Route::get('searchOrderCode', 'searchOrderCode')->name('orders.code.search');
        Route::get('getOrderDetailsByCode', 'getOrderDetailsByCode')->name('getOrderDetailsByCode');
    });



    Route::controller(SalePaymentController::class)->prefix('admin/sales_payments')->group(function () {
        Route::get('/', 'index')->name('sales_payments_track');
        Route::post('/sales_payment_store', 'sales_payment_store')->name('sales_payment_store');
        Route::get('/paymentForLogistic', 'paymentForLogistic')->name('paymentForLogistic');
        Route::post('/paymentForLogistic', 'paymentForLogisticSave')->name('paymentForLogisticSave');
    });

    Route::controller(IncomeExpenseReportController::class)->prefix('admin/income_expense_reports')->group(function () {
        Route::get('/', 'index')->name('income_expense_reports.index');
        Route::get('/getIncomExpenseReports', 'getIncomExpenseReports')->name('getIncomExpenseReports');
        Route::get('/getDateReport', 'getDateReport')->name('getDateReport');
        Route::get('/asd', 'asd')->name('report.get.data');
        Route::get('/paymentReport', 'paymentReport')->name('paymentReport');
    });

    Route::controller(OrderStatusReportController::class)->prefix('admin/order_status_report')->group(function () {
        Route::get('/', 'index')->name('order_status_report.index');
        Route::get('/getOrderStatusData', 'getOrderStatusData')->name('getOrderStatusData');
        Route::get('/statusReport', 'statusReport')->name('statusReport');
    });

    Route::controller(FiananceReportController::class)->prefix('admin/finance_reports')->group(function () {
        Route::get('/', 'index')->name('finance_reports.index');
        Route::get('/getFinanceReport', 'getFinanceReport')->name('getFinanceReport');
        Route::get('/getPaymentReport', 'getPaymentReport')->name('getPaymentReport');
    });

    Route::controller(ProfitLossReportController::class)->prefix('admin/profit_loss')->group(function () {
        Route::get('/', 'index')->name('profit_loss.index');
        Route::get('/getProfitLossData', 'getProfitLossData')->name('getProfitLossData');
        Route::get('/getPaymentReport', 'getPaymentReport')->name('getPaymentReport');
    });

    Route::controller(LogisticVendorReport::class)->prefix('admin/logistic_vendor_reports')->group(function () {
        Route::get('/', 'index')->name('logistic_vendor_reports.index');
        Route::get('/getLogisticVendorFinance', 'getLogisticVendorFinance')->name('getLogisticVendorFinance');
        Route::get('/getPaymentReport', 'getPaymentReport')->name('getPaymentReport');
    });

    Route::controller(LogisticVendorController::class)->prefix('admin/logistic_vendors')->group(function () {
        Route::get('/', 'index')->name('logistic_vendors.index');
        Route::get('/getLogisticVendors',  'getLogisticVendors')->name('logistic_vendors.get');
        Route::post('/store',  'store')->name('logistic_vendors.store');
        Route::get('/edit/', 'edit')->name('logistic_vendors.edit');
        Route::post('/edit', 'update')->name('logistic_vendors.update');
        Route::post('/delete',  'delete')->name('logistic_vendors.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    /* User Profile */
    Route::get('/account', [UserProfileController::class, 'index'])->name('profile.index');
    Route::post('/account/edit', [UserProfileController::class, 'update'])->name('profile.update');

    /* Order */
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/order/confirmed/{code}', [OrderController::class, 'orderConfirm'])->name('orders.confirm');
    Route::get('/order/details/{id}', [OrderController::class, 'showOrderDetails'])->name('orders.show.details');

    /* Address */
    Route::post('address', [AddressController::class, 'store'])->name('address.store');
    Route::get('getProviceDistrict/{id}', [AddressController::class, 'getProviceDistrict'])->name('address.province.distict');

    Route::get('user/logout', [UserProfileController::class, 'userLogout'])->name('user.logout');

    /* Wishlist */
    Route::get('wishlists', [WishlistController::class, 'index'])->name('wishlists.index');
    Route::post('wishlists', [WishlistController::class, 'store'])->name('wishlists.store');
    Route::post('remove/wishlist', [WishlistController::class, 'remove'])->name('wishlists.remove');
    Route::post('remove/wishlist-list', [WishlistController::class, 'removeList'])->name('wishlists.remove.list');

    /* Shipping Methods */
    Route::resource('shippings', ShippingMethodController::class);
    Route::get('/getShippings', [ShippingMethodController::class, 'getShippings'])->name('shippings.get');


    /* Support Ticket */
    Route::post('support_ticket', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('ticket/details/{ticketId}', [TicketController::class, 'ticketDetails'])->name('ticket.details');
    Route::post('customer/ticket/reply', [TicketController::class, 'ticketReplyByCustomer'])->name('ticket.reply.customer');

    //device management

    Route::get('devicemanagement/problem', [DeviceManagementController::class, 'device_problem'])->name('devicemanagement.problem');
    Route::post('/getProblems', [DeviceManagementController::class, 'getProblems'])->name('problem.get');
    Route::Post('problems/store', [DeviceManagementController::class, 'store'])->name('problems.store');
    Route::get('problems/edit/{id}', [DeviceManagementController::class, 'edit'])->name('problems.edit');
    Route::post('problems/update/{id}', [DeviceManagementController::class, 'updateProblem'])->name('problems.update');
    Route::post('problems/delete', [DeviceManagementController::class, 'deleteProblem'])->name('problems.delete');

    //device accessories
    Route::get('devicemanagement/accessories', [DeviceAccessoryController::class, 'index'])->name('devicemanagement.accessories');
    Route::post('/getAccessories', [DeviceAccessoryController::class, 'getAccessories'])->name('accessory.get');
    Route::Post('accessory/store', [DeviceAccessoryController::class, 'store'])->name('accessory.store');
    Route::get('accessories/edit/{id}', [DeviceAccessoryController::class, 'edit'])->name('accessories.edit');
    Route::post('accessories/update/{id}', [DeviceAccessoryController::class, 'updateAccessories'])->name('accessories.update');
    Route::post('accessories/delete', [DeviceAccessoryController::class, 'deleteAccessory'])->name('accessories.delete');

    //device condition
    Route::get('devicemanagement/condition', [DeviceConditionController::class, 'index'])->name('devicemanagement.condition');
    Route::post('/getConditions', [DeviceConditionController::class, 'getConditions'])->name('condition.get');
    Route::Post('condition/store', [DeviceConditionController::class, 'store'])->name('condition.store');
    Route::get('condition/edit/{id}', [DeviceConditionController::class, 'edit'])->name('condition.edit');
    Route::post('condition/update/{id}', [DeviceConditionController::class, 'updateCondition'])->name('condition.update');
    Route::post('condition/delete', [DeviceConditionController::class, 'deleteCondition'])->name('condition.delete');

    //device sales
    Route::get('devicemanagement/sales', [DeviceSaleController::class, 'index'])->name('devicemanagement.sales');
    Route::post('/getSales', [DeviceSaleController::class, 'getSales'])->name('sales.get');
    Route::get('/sale/detail/{id}', [DeviceSaleController::class, 'saleDetail']);

    //checkout
    Route::get('checkout', [CartController::class, 'addShippingAddress'])->name('cart.shipping');


    //order list
    Route::get('/my-orders', [FrontendController::class, 'order'])->name('my.order.index');
    Route::get('/view_orders', [FrontendController::class, 'view_orders'])->name('view_orders');
    Route::get('/back_to_main_order_page', [FrontendController::class, 'back_to_main_order_page'])->name('back_to_main_order_page');
    //cancel Order
    Route::post('/cancel_order', [OrderController::class, 'cancelOrder'])->name('order.cancel');
});


Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('sendOtp');
Route::post('/verify-otp', [OtpController::class, 'verify_otp'])->name('verifyOtp');
Route::post('/create-guest-user', [UserProfileController::class, 'createGuestUser'])->name('guestUser');


//forget password
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

//get shipping price
Route::get('/get-shipping-price', [FrontendController::class, 'shippingPrice'])->name('getShippingPrice');
Route::get('/get-product-attribute-price', [FrontendController::class, 'getProductAttributePrice'])->name('getProductAttributePrice');
