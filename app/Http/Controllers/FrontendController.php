<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Cart;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Slider;
use App\Models\AboutUs;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Feature;
use App\Models\Product;
use App\Models\Category;
use App\Models\District;
use App\Rules\ReCaptcha;
use App\Mail\ContactMail;
use App\Models\FlashSale;
use Illuminate\Http\Request;
use App\Models\ProductMaster;
use App\Models\GeneralSetting;
use App\Models\ShippingMethod;
use App\Models\ProductAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use Illuminate\Pagination\LengthAwarePaginator;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
//        dd(bcrypt('Nepal1234%'));

        $this->clearQuickOrderData();
        $categories = Category::where([
            ['is_active', 1],
            ['show_in_home', 1],
        ])->orderBy('ordering', 'asc')->get();

        $allCategories = Category::where([
            ['is_active', 1],
            ['parent_id', 0]
        ])->orderBy('ordering', 'asc')->get();


        $flashSale = FlashSale::where('is_active', 1)->first();
        // dd($flashSale->flashSaleProducts);
        // dd($flashSale->flashSaleProducts->first()->product_id);

        $featuredProducts = Product::orderBy('created_at', 'DESC')->where([
            ['is_active', true],
            ['show_in_home', true],
            ['is_featured', true]
        ])->take(8)->get();
        $productsOnSale = Product::orderBy('created_at', 'DESC')->where([
            ['is_active', true],
            ['show_in_home', true],
            ['on_sale', true]
        ])->take(8)->get();

        $topRatedProducts = Product::orderBy('created_at', 'DESC')->where([
            ['is_active', true],
            ['show_in_home', true],
            ['top_rated', true]
        ])->take(8)->get();


        $sliders = Slider::where('is_active', 1)->get();
        $currentTime = Carbon::now();
        $oneWeekBefore = Carbon::now()->subdays(7);
        $recentViewedProducts = Product::whereNotNull('viewed_date')->whereBetween('viewed_date', [$oneWeekBefore, $currentTime])->orderBy('viewed_date', 'DESC')->get();
        $features = Feature::orderBy('feature', 'asc')->get();
        $latestProducts = Product::orderBy('created_at', 'DESC')->where([
            ['is_active', true],
            ['show_in_home', true],
        ])->take(8)->get();


        // $ip = $_SERVER['REMOTE_ADDR'];

        // if ($ip != Null) {

        //     $location = Location::get($ip);

        //     if ($location) {
        //         DB::table('visitor_ip')->insert([
        //             'ip_address' => $ip,
        //             'country_name' => $location->countryName,
        //             'region_name' => $location->regionName,
        //             'city_name' => $location->cityName,
        //             'time_zone' => $location->timezone,
        //             'latitude' => $location->latitude,
        //             'longitude' => $location->longitude,
        //             'country_code' => $location->countryCode,
        //             'regionCode' => $location->regionCode
        //         ]);
        //     }
        // }

        return view('frontend.index', compact(
            'sliders',
            'flashSale',
            'categories',
            'recentViewedProducts',
            'allCategories',
            'features',
            'latestProducts',
            'featuredProducts',
            'productsOnSale',
            'topRatedProducts',
        ));
    }

    public function trackOrder(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $order = Order::where('code', $request->code)
            ->leftJoin('payment_mode', 'orders.payment_type', '=', 'payment_mode.payment_mode_id')
            ->first();

        if ($order) {
            return view('track_order_details', compact('order'));
        } else {
            return view('404');
        }
    }

    public function about()
    {
        $this->clearQuickOrderData();
        $about = AboutUs::first();
        return view('frontend.about-us', compact('about'));
    }

    public function privacy()
    {
        $this->clearQuickOrderData();
        $data = DB::table('global_config')->where('title', 'privacy_policy')->first();
        return view('frontend.privacy-policy', compact('data'));
    }

    public function termsConditions()
    {
        $this->clearQuickOrderData();
        $data = DB::table('global_config')->where('title', 'terms_and_conditions')->first();

        return view('frontend.terms-of-service', compact('data'));
    }

    public function faq()
    {
        $this->clearQuickOrderData();
        $faqs = Faq::where('is_active', 1)->get();
        return view('frontend.faq', compact('faqs'));
    }


    public function contact()
    {
        $this->clearQuickOrderData();
        $mapLink = GeneralSetting::where('key', 'map_link')->value('value');
        return view('frontend.contact-us',compact('mapLink'));
    }

    public function saveContact(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'phone' => 'required|digits:10',
            'subject' => 'required|max:255',
            'message' => 'required|string|max:2000',
            'g-recaptcha-response' => [new ReCaptcha]
        ]);

        Contact::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'email' => $request->email,
            'message' => $request->message
        ]);

        return redirect()->route('front.contact')->with('success', 'Message Sent Successfully. We Will Contact You Later.');
    }

    public function getCategoryProducts(Request $request, $slug)
    {
        $sort_by = $request->sort_by;
        if ($request->get('price')) {
            list($min, $max) = explode('-', $request->get('price'));
            $min = str_replace('Rs.', '', trim($min));
            $max = str_replace('Rs.', '', trim($max));
            $_COOKIE['min_price'] = $min;
            $_COOKIE['max_price'] = $max;
        } else {
            $_COOKIE['min_price'] = null;
            $_COOKIE['max_price'] = null;
        }

        $c = Category::where('slug', $slug)->first();

        $productIds = getCategoryProducts($slug);

        $products = Product::where('is_active', true)->whereIn('id', $productIds);

        if (isset($_COOKIE['brand'])) {
            $brandIds = explode(",", $_COOKIE['brand']);

            $products = $products->whereIn('brand_id', $brandIds);
        }


        if (isset($_COOKIE['attribute'])) {
            $attributes = $_COOKIE['attribute'];
            $ids = getProductsByAttributeValues($attributes);
            $products = $products->whereIn('id', $ids);
        }

        $products = $products->get();

        $products->map(function ($item) {
            $item['price'] = getProductPrice($item->id);

            return $item;
        });

        if (isset($_COOKIE['min_price'])) {
            $products = $products->where('price', '>=', $_COOKIE['min_price']);
        }

        if (isset($_COOKIE['max_price'])) {
            $products = $products->where('price', '<=', $_COOKIE['max_price']);
        }

        if ($sort_by != null) {
            switch ($sort_by) {

                case 'newest':
                    $products = $products->sortBy('created_at', SORT_REGULAR, true);
                    break;
                case 'oldest':
                    $products = $products->sortBy('created_at');
                    break;
                case 'price-asc':
                    $products = $products->unique()->sortBy('price');

                    break;
                case 'price-desc':
                    $products = $products->unique()->sortBy('price', SORT_REGULAR, true);

                    break;
                default:
                    break;
            }
        }

        $totalProducts = count($products);

        $perPage = 20;
        $page = Paginator::resolveCurrentPage('page');

        $products = new LengthAwarePaginator($products->forPage($page, $perPage), $totalProducts, $perPage, $page, ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page',]);
        $categories = Category::where('parent_id', 0)->where('is_active', true)->get();
        $sub_categories = Category::where('parent_id', '!=', 0)->where('is_active', 1)->get();


        return view('frontend.product.index', compact('slug', 'c', 'totalProducts', 'products', 'categories', 'sub_categories'));
    }

    public function getProductDetails($slug)
    {
        $this->clearQuickOrderData();
        $product = Product::where('slug', $slug)->first();
        $gallery = getProductGalleryImage($product->id);

        $features = Feature::orderBy('feature', 'asc')->get();
        $latestProduct = Product::orderBy('created_at', 'DESC')
            ->where([
                ['is_active', true],
                ['id', '!=', $product->id],
                ['show_in_home', true]
            ])->take(5)
            ->get();

        $relatedProducts = Product::where([
            ['category_id', $product->category_id],
            ['is_active', true],
            ['id', '!=', $product->id],
            ['show_in_home', true]
        ])->get();
        $cities = District::where('is_deleted_flag', 'n')->get();


        return view('frontend.product.view', compact('product', 'gallery', 'latestProduct', 'relatedProducts', 'features', 'cities'));
    }


    public function search(Request $request)
    {
        $products = $this->initProductSearch($request)->paginate(10);
        // dd($products);
        $totalProducts = count($this->initProductSearch($request)->get());

        $productsForPrices = Product::where('is_active', 1)->get();
        $productIds = $productsForPrices->pluck('id');
        $prices = ProductMaster::whereIn('product_id', $productIds)->where('is_active', 1)->get();

        $categories = Category::where('parent_id', 0)->where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('frontend.product.index', compact(
            'categories',
            'products',
            'totalProducts',
            'brands',
            // 'perPage',
            'prices'
        ))->with([
            'price' => $request->price,
            'sort_by' => $request->sort_by
        ]);
    }

    public function initProductSearch($request)
    {
        $products = Product::query()
            ->where('products.is_active', 1)
            ->select('products.*', 'product_masters.price')
            ->leftJoin('product_masters', 'products.id', '=', 'product_masters.product_id')
            ->where('product_masters.is_active', 1)
            ->whereNull('product_masters.attribute_id')
            ->groupBy('products.id');

        $search_value = isset($request->product) ? ($request->product) : '';
        if ($search_value != Null) {
            // $values = array('ip_address' => $ip, 'searched_product' => $search_value);
            // DB::table('search_record')->insert($values);
        }

        if ($request->product) {
            $products = $products->where('name', 'like', '%' . $request->product . '%');
        }


        if ($request->subcategory) {
            $filterCategory = Category::where('slug', $request->subcategory)
                ->first();
            if ($filterCategory)
                $products = $products->where('sub_category_id', $filterCategory->id);
        }


        if ($request->category) {
            $filterCategory = Category::where('slug', $request->category)
                ->first();
            if ($filterCategory)
                $products = $products->where('category_id', $filterCategory->id);
        }
        $sort_by = $request->sort_by;


        if (isset($_COOKIE['brand'])) {
            $brandsArray = explode(",", $_COOKIE['brand']);
            $products = $products->whereIn('brand_id', $brandsArray);
        }

        if (isset($_COOKIE['attribute'])) {
            $attributes = $_COOKIE['attribute'];
            $ids = getProductsByAttributeValues($attributes);
            $products = $products->whereIn('id', $ids);
        }

        if ($sort_by) {
            switch ($sort_by) {
                case 'newest':
                    $products = $products->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $products = $products->orderBy('created_at', 'asc');
                    break;
                case 'price-asc':
                    $products = $products->orderBy('product_masters.price', 'asc');
                    if ($request->get('price')) {
                        list($min, $max) = explode('-', $request->get('price'));
                        $min = str_replace('Rs.', '', trim($min));
                        $max = str_replace('Rs.', '', trim($max));

                        $products = $products
                            ->whereBetween('product_masters.price', [$min, $max]);
                        return $products;
                    }
                    break;
                case 'price-desc':
                    $products = $products->orderBy('product_masters.price', 'desc');
                    if ($request->get('price')) {
                        list($min, $max) = explode('-', $request->get('price'));
                        $min = str_replace('Rs.', '', trim($min));
                        $max = str_replace('Rs.', '', trim($max));

                        $products = $products
                            ->whereBetween('product_masters.price', [$min, $max]);
                        return $products;
                    }
                    break;
                default:
                    break;
            }
        }
        // dd($products);

        if ($request->get('price')) {
            list($min, $max) = explode('-', $request->get('price'));
            $min = str_replace('Rs.', '', trim($min));
            $max = str_replace('Rs.', '', trim($max));

            $products = $products
                // ->leftJoin('product_masters', 'products.id', '=', 'product_masters.product_id')
                ->whereBetween('product_masters.price', [$min, $max]);
        }


        return $products;
    }


    public function fetchMoreProducts(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => '',
            'total' => []
        ];

        try {

            $products = ($this->initProductSearch($request)->skip($request->get('start'))->take('12')->get());
            $fetchedTotalProducts = $products->count();
            $view = view('frontend.product.child_search', compact('products'))->render();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $view;
            $output['total'] = $fetchedTotalProducts;
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function ajax_search(Request $request)
    {
        $keywords = array();

        if ($request->category_id) {
            $products = Product::where('is_active', 1)->where('name', 'like', '%' . $request->search . '%')->get();
        } else {
            $products = Product::where('is_active', 1)->where('name', 'like', '%' . $request->search . '%')->get();
        }

        $categories = Category::where('name', 'like', '%' . $request->search . '%')->get()->take(3);

        // return view('frontend.partials.search_content', compact('products', 'categories'));
    }

    public function showFlashSaleProducts($id)
    {
        $this->clearQuickOrderData();
        $flashSale = FlashSale::where('is_active', 1)->where('id', $id)->first();
        if ($flashSale) {
            return view('frontend.flash_sale_details', compact('flashSale'));
        } else {
            return view('404');
        }
    }

    public function order()
    {
        $this->clearQuickOrderData();
        $orders = Order::where('user_id', auth()->id())->groupBy('created_at')->get();

        return view('frontend.order', compact('orders'));
    }

    public function view_orders()
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => 'something went wrong',

        ];
        try {
            $this->clearQuickOrderData();

            $dateOrders = Order::where([
                ['user_id', auth()->id()],
                ['created_at', \request()->order_date]
            ])->get();

            $output['response'] = true;
            $output['result'] = view('frontend.orders.order_details', compact('dateOrders'))->render();
        } catch (Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    public function back_to_main_order_page()
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => 'something went wrong',

        ];
        try {
            $this->clearQuickOrderData();
            $orders = Order::where('user_id', auth()->id())->groupBy('created_at')->get();

            $output['response'] = true;
            $output['result'] = view('frontend.orders.order_main_page', compact('orders'))->render();
        } catch (Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    public function shippingPrice(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $shipping = ShippingMethod::find($request->id);
            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $shipping->cost,
            );
        } catch (Exception $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function getProductAttributePrice(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $price = ProductMaster::where([
                ['product_id', $request->product],
                ['attribute_id', $request->attribute],
                ['is_active', true]
            ])->first()->price;

            $qty = ProductMaster::where([
                ['product_id', $request->product],
                ['attribute_id', $request->attribute],
                ['is_active', true]
            ])->first()->quantity;

            $attribute = ProductAttribute::where([
                ['product_id', $request->product],
                ['id', $request->attribute],
                ['is_active', true]
            ])->first();

            if ($attribute->featured_image) {
                $image = $attribute->featured_image;
            } else {
                $image = Product::where('id', $request->product)->first()->image;
            }
            $gallery = getProductGalleryImage($request->product);
            $attribute_image = view('frontend.product.attribute_image', compact('image', 'gallery'))->render();
            $qtyView = view('frontend.product.qtyView', compact('qty'))->render();
            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $price,
                'attribute_image' => $attribute_image,
                'qty' => $qtyView
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function quick_order(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {


            DB::beginTransaction();

            if ($request->otp) {

                if (!($request->email))
                    throw new Exception("Email is required");

                $validator = Validator::make($request->all(), [
                    'otp' => ['required', 'digits:6'],
                    'email' => ['nullable', 'email']
                ]);

                if ($validator->fails()) {
                    $output['result'] = $validator->errors()->toArray();
                    throw new Exception("Form Validation Error");
                }


                $otp = $request->session()->get('OTP');
                if ($request->otp == $otp) {
                    Session::forget('OTP');
                    $output['response'] = true;
                    $output['msg'] = 'login successfully';
                    $user = User::where('email', $request->email)->first();

                    if ($user) {
                        Auth::login($user);
                        $output['response'] = true;
                        $output['msg'] = 'logged in successfully';
                    } else {
                        $output['result'] = [
                            'email' => 'email does not exist'
                        ];
                    }
                } else {

                    $output['response'] = false;
                    $output['msg'] = 'Invalid OTP. Please Enter Valid OTP.';
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'contact_number' => ['required', 'digits:10'],
                    'email' => ['nullable']
                ]);

                if ($validator->fails()) {
                    $output['result'] = $validator->errors()->toArray();
                    throw new Exception("Form Validation Error");
                }

                $data = [];
                $user = User::where([
                    ['contact', $request->contact_number],
                    ['email', $request->email],
                ])->first();

                if ($user) {
                    Auth::login($user);

                    $output['response'] = true;
                } else {

                    if (User::where('email', $request->email)->exists()) {
                        $output['result'] =
                            [
                                'email' => 'this email has already other number'
                            ];

                        throw new Exception('form validation error');
                    } else {


                        $newUser = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'contact' => $request->contact_number,
                            'password' => Hash::make($request->contact_number),
                            'type' => 'automatic',
                            'is_verified' => true,
                            'is_admin' => 0
                        ]);
                        Auth::login($newUser);
                    }

                    //                $ok = messageInAllPlatForm($newUser->id);

                    Address::create([
                        'user_id' => $newUser->id,
                        'district_id' => $request->city,
                        'province_id' => getProvinceId($request->city),
                        'street' => $request->location
                    ]);


                    $output['response'] = true;
                }
            }

            if ($output['response']) {
                DB::table('quick_orders')->where('user_id', \auth()->user()->id)->delete();

                DB::table('quick_orders')->insert([
                    'user_id' => \auth()->user()->id,
                    'product_id' => $request->product_id,
                    'price' => getProductPrice($request->product_id),
                    'quantity' => $request->quantity,
                    'attribute' => $request->attribute_id
                ]);
                Session::put('quick_order', 'y');

                DB::commit();
                $output = array(
                    'response' => true,
                    'msg' => "quick ordered successfully",
                    'result' => '',
                );
            }
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function getDistricts(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $districts = District::where('zone_state_id', $request->province_id)->get();

            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $districts,
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function searchIP()
    {

        $data = DB::table('visitor_ip')->get();


        echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
        echo '<tr><td colspan=2 style="background-color:#333333;"><strong><font color=white>ARRAY</font></strong></td></tr>';

        foreach ($data as $data) {
            echo '<tr><td valign="top" style="width:40px;background-color:#F0F0F0;">';

            $location = Location::get($data->ip_address);
            print_r($location);

            //  echo '<strong>' . $location['city'] . "</strong></td><td>";
            echo "</td></tr>";
        }

        echo "</table>";
    }

    public function searchProduct()
    {
        $data = DB::table('search_record')->get();

        echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
        echo '<tr><td colspan=2 style="background-color:#333333;"><strong><font color=white>ARRAY</font></strong></td></tr>';

        foreach ($data as $history) {
            echo '<tr><td valign="top" style="width:40px;background-color:#F0F0F0;">';

            print_r($history);
            echo "</td></tr>";
        }
        echo "</table>";
    }
}
