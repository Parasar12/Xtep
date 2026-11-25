<?php

use App\Mail\AdminMail;
use App\Mail\CustomerMail;
use App\Models\Product;
use App\Models\GeneralSetting;
use App\Models\Province;
use App\Models\District;

use App\Models\Coupon;
use App\Models\CouponProduct;
use App\Models\CouponCategory;

use App\Models\Category;
use App\Models\Feature;
use App\Models\Cart;
use App\Models\Address;
use App\Models\SaleReturnDetail;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\FlashSale;
use App\Models\OrderDetail;
use App\Models\ProductMaster;
use App\Models\FlashSaleProducts;
use App\Models\ProductAttribute;
use App\Models\SocialSite;
use App\Models\TicketIssueCategory;
use App\Models\ProductGallary;
use App\Models\Brand;
use App\Models\SmsLog;
use App\Models\Reviews;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

if (!function_exists('arrayHierarchy')) {
    function arrayHierarchy($data, $prelinkColumnName, $idColumnName)
    {

        $hierarchyData = array();
        foreach ($data as &$row) {

            if ('0' == $row[$prelinkColumnName]) {
                $hierarchyData[] = &$row;
            } else {
                $pid = $row[$prelinkColumnName];

                if (isset($data[$pid])) {
                    $data[$pid]['child'][] = &$row;
                }
            }
        }

        return $hierarchyData;
    }
}

if (!function_exists('formatMenuOuput')) {
    function formatMenuOuput($data)
    {
        $output = '';

        foreach ($data as $key => $row) {

            if (isset($row['child'])) {
                $output .= '<li>';
                $output .= '<a href="javascript: void(0);" class="has-arrow waves-effect">';
                $output .= '<i class="' . $row['icon_class'] . '"></i>';
                $output .= '<span>' . $row['menu_name'] . '</span>';
            } else {
                $output .= '<li>';
                $output .= '<a href="' . route($row['route']) . '">' . $row['menu_name'] . '</a>';
                $output .= '</li>';
            }

            if (isset($row['child'])) {
                $output .= '</a>';

                $output .= '<ul class="sub-menu" aria-expanded="false">';
                $output .= formatMenuOuput($row['child']);
                $output .= '</ul>';
            } else {
                $output .= '</li>';
            }

            $output .= '</li>';
        }

        return $output;
    }
}

if (!function_exists('populateMenu')) {
    function populateMenu(): string
    {
        $userId = auth()->user()->id;

        $data = DB::table('user_permission')
            ->join('menu_master', 'menu_master.menu_id', '=', 'user_permission.menu_id')
            ->where('user_permission.user_id', $userId)
            ->where('menu_master.menu_type', 'outer')
            ->orderBy('menu_master.pre_menu_id', 'ASC')
            ->orderBy('menu_master.menu_index', 'ASC')
            ->select('menu_master.menu_id', 'menu_master.menu_code', 'menu_master.menu_name', 'menu_master.pre_menu_id', 'menu_master.route', 'menu_master.menu_index', 'menu_master.icon_class')
            ->get();

        $menuData = json_decode(json_encode($data), true);

        // Hidden from the sidebar (routes and permissions untouched).
        // To restore a menu, remove its name from this list.
        $hiddenMenus = ['device management', 'support'];

        $result = array();
        foreach ($menuData as $key => $value) {
            if ($value['pre_menu_id'] == '0' && in_array(strtolower(trim($value['menu_name'])), $hiddenMenus)) {
                continue; // its sub-menus are dropped too, since their parent is missing
            }
            $result[$value['menu_id']] = $value;
        }

        $menu = arrayHierarchy($result, 'pre_menu_id', 'menu_id');

        $output = formatMenuOuput($menu);

        return $output;
    }
}

function checkIfFlashSaleHasUnexpiredProduct($id)
{
    $flashSale = FlashSale::where('id', $id)->first();

    $currentDate = date('Y-m-d');
    $currentDate = date('Y-m-d', strtotime($currentDate));

    if ($currentDate >= $flashSale->start_date && $currentDate <= $flashSale->end_date) {
        return true;
    }
}

function getTicketIssueCategories()
{
    $issueCategories = TicketIssueCategory::where('is_active', 1)->get();
    return $issueCategories;
}

function getSiteTitle()
{
    $title = GeneralSetting::where('key', 'title')->select('value')->first();

    if ($title) {
        return $title->value;
    } else {
        return null;
    }
}


function getShowInHomeCategories()
{
    $categories = Category::where([
        ['is_active', 1],
        ['show_in_home', 1],
    ])->orderBy('ordering', 'asc')->get();
    if ($categories) {
        return $categories;
    } else {
        return null;
    }
}

function getFeaturedProducts()
{
    $features = Product::orderBy('created_at', 'DESC')->where([
        ['is_active', true],
        ['show_in_home', true],
        ['is_featured', true]
    ])->take(8)->get();;
    if ($features) {
        return $features;
    } else {
        return null;
    }
}

function getFooterText()
{
    $footer = GeneralSetting::where('key', 'footer_text')->select('value')->first();

    if ($footer) {
        return $footer->value;
    } else {
        return null;
    }
}

function getLogoSize()
{
    $size = GeneralSetting::where('key', 'logo_size')->select('value')->first();

    if ($size) {
        return $size->value;
    } else {
        return null;
    }
}

function getSiteLogo()
{
    $logo = GeneralSetting::where('key', 'logo')->select('value')->first();

    if ($logo) {
        return $logo->value;
    } else {
        return null;
    }
}

function getSiteFavicon()
{
    $favicon = GeneralSetting::where('key', 'favicon')->select('value')->first();

    if ($favicon) {
        return $favicon->value;
    } else {
        return null;
    }
}

function getSocialLinks()
{
    $links = SocialSite::where('is_active', 1)->get();
    return $links;
}

function getWelcomeText()
{
    $welcomeText = GeneralSetting::where('key', 'welcome_text')->select('value')->first();
    if ($welcomeText) {
        return $welcomeText->value;
    } else {
        return null;
    }
}

function getPhoneNumber()
{
    $phone = GeneralSetting::where('key', 'phone')->select('value')->first();
    if ($phone) {
        return $phone->value;
    } else {
        return null;
    }
}

function getBillFooterText()
{
    $text = GeneralSetting::where('key', 'bill_footer_text')->select('value')->first();
    if ($text) {
        return $text->value;
    } else {
        return null;
    }
}


function getWebsiteUrl()
{
    $text = GeneralSetting::where('key', 'website_url')->select('value')->first();
    if ($text) {
        return $text->value;
    } else {
        return null;
    }
}

function getEmail()
{
    $email = GeneralSetting::where('key', 'email')->select('value')->first();
    if ($email) {
        return $email->value;
    } else {
        return null;
    }
}

function getOpeningHours()
{
    $opening_hours = GeneralSetting::where('key', 'opening_hours')->select('value')->first();
    if ($opening_hours) {
        return $opening_hours->value;
    } else {
        return null;
    }
}

function getFacebookPage()
{
    $fb_page_link = GeneralSetting::where('key', 'facebook_page_link')->select('value')->first();
    if ($fb_page_link) {
        return $fb_page_link->value;
    } else {
        return null;
    }
}

function getAddress()
{
    $address = GeneralSetting::where('key', 'address')->select('value')->first();
    if ($address) {
        return $address->value;
    } else {
        return null;
    }
}

function getFax()
{
    $fax = GeneralSetting::where('key', 'fax')->select('value')->first();
    if ($fax) {
        return $fax->value;
    } else {
        return null;
    }
}

function getProviceName($id)
{
    $province = Province::where('zone_state_id', $id)->first();
    if ($province) {
        return $province->zone_state_name;
    } else {
        return null;
    }
}

function getDistrictName($id)
{
    $district = District::where('district_id', $id)->first();
    if ($district) {
        return $district->district_name;
    } else {
        return null;
    }
}

function generateToken($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function getCouponProducts($id)
{
    $couponProducts = CouponProduct::where('coupon_id', $id)->get();
    $product = [];
    if ($couponProducts) {
        foreach ($couponProducts as $p) {
            $product[] = $p->product_id;
        }
        return $product;
    } else {
        return null;
    }
}

function getExcludeCouponProducts($id)
{
    $couponProducts = CouponProduct::where('coupon_id', $id)->pluck('product_id')->toArray();
    if ($couponProducts) {
        return $couponProducts;
    } else {
        return null;
    }
}

function getCouponCategories($id)
{
    $couponCategories = CouponCategory::where('coupon_id', $id)->get();
    $pr = [];
    if ($couponCategories) {
        foreach ($couponCategories as $p) {
            foreach (getProductByCategory($p->category_id) as $pro) {
                $pr[] = $pro->id;
            }
        }
        return $pr;
    } else {
        return $pr;
    }
}

function getExcludeCouponCategories($id)
{
    $couponCategories = CouponCategory::where('coupon_id', $id)->pluck('category_id')->toArray();
    if ($couponCategories) {
        return $couponCategories;
    } else {
        return null;
    }
}

function getMainCategories()
{
    $mainCategories = Category::where('parent_id', 0)->where('is_active', 1)->get();
    if (!$mainCategories->isEmpty()) {
        return $mainCategories;
    } else {
        return null;
    }
}

function getAllCategories()
{
    $categories = Category::where('parent_id', 0)
        ->where('is_active', 1)
        ->with('categories.children')
        ->get();

    return $categories;
}

function getFeature($id)
{
    $feature = Feature::where('feature', $id)->first();

    if ($feature) {
        return $feature;
    } else {
        return null;
    }
}

function getProductQuantity($id)
{
    $total = ProductMaster::where([
        ['product_id', $id],
        ['is_active', true]
    ])->sum('quantity');
    //    $orderedQuantity = OrderDetail::where([
    //        ['product_id', $id],
    //    ])->sum('quantity');

    //    $remaining = $total - $orderedQuantity;
    return $total;
}

function countCartItems()
{
    if (auth()->user()) {
        $count = Cart::where('user_id', auth()->user()->id)->count();
        return $count;
    } else {
        if (session()->get('cart')) {
            // dd($count);
            return session()->get('cart')->count();
        } else {
            return 0;
        }
    }
}

function countWishlistItems()
{
    if (auth()->user()) {
        $count = Wishlist::where('user_id', auth()->user()->id)->count();
        return $count;
    } else {
        return 0;
    }
}

function getUserCartItems()
{
    if (auth()->user()) {
        $cartItems = Cart::where('user_id', auth()->user()->id)->get();
        return $cartItems;
    } else {
        return session()->get('cart');
    }
}

function getUserCartProductIds()
{
    $ids = [];
    $cartItems = Cart::where('user_id', auth()->user()->id)->get();
    foreach ($cartItems as $item) {
        $ids[] = $item->product_id;
    }
    return $ids;
}

function getUserWishlistItems()
{
    $wishlistItems = Wishlist::where('user_id', auth()->user()->id)->get();
    return $wishlistItems;
}

function getUserAddresses()
{
    if (auth()->user()) {
        $addresses = Address::where('user_id', auth()->user()->id)->get();
        return $addresses;
    }
}

function productInWishlist($id): bool
{
    $exist = Wishlist::where('user_id', auth()->user()->id)->where('product_id', $id)->first();
    if ($exist) {
        return true;
    } else {
        return false;
    }
}

function getCategorySlug($id)
{
    return Category::find($id)->slug;
}


function getCategoryProductsIdInArray($id)
{
    $products = [];
    $categoryProducts = Category::with(['products', 'children.products'])->where('id', $id)
        ->where('is_active', 1)->take(8)->get();
    foreach ($categoryProducts as $cp) {
        if ($cp->products) {
            foreach ($cp->products()->where('is_active', 1)->take(8)->get() as $product) {
                $products[] = $product->id;
            }
            if ($cp->children) {
                foreach ($cp->children as $child) {
                    if ($child->products) {
                        foreach ($child->products()->where('is_active', 1)->take(8)->get() as $childP) {
                            $products[] = $childP->id;
                        }
                    }
                }
            }
        }
    }

    return $products;
}

function getProductPrice($id)
{
    $master = ProductMaster::where('product_id', $id)->where('is_active', 1)->first();

    $flashSales = FlashSale::where('is_active', 1)->get();
    $inFlashSale = false;

    $currentDate = date('Y-m-d');

    if (!$flashSales->isEmpty()) {

        foreach ($flashSales as $val) {

            if (strtotime($currentDate) >= strtotime($val->start_date) && strtotime($currentDate) <= strtotime($val->end_date)) {

                if ($product = FlashSaleProducts::where('flash_sale_id', $val->id)->where('product_id', $id)->first()) {

                    $flashSalesCount = OrderDetail::where('product_id', $id)
                        ->whereIn('delivery_status', [1, 2, 3])
                        ->whereBetween('created_at', [$val->start_date . ' 00:00:00', $val->end_date . ' 23:59:59'])
                        ->sum('quantity');

                    $remainingFlashSalesCount = $product->quantity - $flashSalesCount;

                    if ($remainingFlashSalesCount > 0) {
                        return $product->price;
                    } else {
                        if ($master->price) {
                            return $master->price;
                        } else {
                            return 0;
                        }
                    }
                } else {
                    if ($master->price) {
                        return $master->price;
                    } else {
                        return 0;
                    }
                }
            } else {
                if ($master && $master->price) {
                    return $master->price;
                } else {
                    return 0;
                }
            }
        }
    } else {
        if ($master) {
            return $master->price;
        } else {
            return 0;
        }
    }
}

function getSellingPrice($id)
{
    $master = ProductMaster::where('product_id', $id)->where('is_active', 1)->first();
    // dd($master);
    return $master->price;
}

function getCostPrice($id)
{
    $master = ProductMaster::where('product_id', $id)->where('is_active', 1)->first();

    $flashSales = FlashSale::where('is_active', 1)->get();
    $inFlashSale = false;

    $currentDate = date('Y-m-d');

    if (!$flashSales->isEmpty()) {

        foreach ($flashSales as $val) {

            if (strtotime($currentDate) >= strtotime($val->start_date) && strtotime($currentDate) <= strtotime($val->end_date)) {

                if ($product = FlashSaleProducts::where('flash_sale_id', $val->id)->where('product_id', $id)->first()) {

                    $flashSalesCount = OrderDetail::where('product_id', $id)
                        ->whereIn('delivery_status', [1, 2, 3])
                        ->whereBetween('created_at', [$val->start_date . ' 00:00:00', $val->end_date . ' 23:59:59'])
                        ->sum('quantity');

                    $remainingFlashSalesCount = $product->quantity - $flashSalesCount;

                    if ($remainingFlashSalesCount > 0) {
                        return $product->price;
                    } else {
                        if ($master->cost_price) {
                            return $master->cost_price;
                        } else {
                            return 0;
                        }
                    }
                } else {
                    if ($master->cost_price) {
                        return $master->cost_price;
                    } else {
                        return 0;
                    }
                }
            } else {
                if ($master->cost_price) {
                    return $master->cost_price;
                } else {
                    return 0;
                }
            }
        }
    } else {
        if ($master) {
            return $master->cost_price;
        } else {
            return 0;
        }
    }
}

function adjustFlashSaleProductsPrice()
{
    $flashSales = FlashSale::where('is_active', 1)->get();

    if (!$flashSales->isEmpty()) {
        foreach ($flashSales as $flashSale) {
            foreach ($flashSale->flashSaleProducts as $fp) {
                $product = Product::where('id', $fp->product_id)->first();
                $today = Carbon::now();
                $startDate = Carbon::parse($flashSale->start_date);
                $endDate = Carbon::parse($flashSale->end_date);

                if (($startDate <= $today) && ($endDate >= $today)) {
                    Product::where('id', $fp->product_id)->update([
                        'selling_price' => $fp->price
                    ]);
                } else {
                    Product::where('id', $fp->product_id)->update([
                        'selling_price' => $product->old_selling_price
                    ]);
                }
            }
        }
    }
}

function getCategoryAndChildrenIds($slug)
{
    $ids = [];

    $parentCategory = Category::select('id')->where('slug', $slug)
        ->where('is_active', 1)
        ->first();

    $allCategory = Category::select('id', 'parent_id')
        ->where('is_active', 1)->get();

    $categoryList = find_parents($allCategory, $parentCategory->id);
    array_push($categoryList, $parentCategory->id);

    $categories = Category::with(['categories', 'categories.children'])
        ->where('is_active', 1)
        ->whereIn('id', $categoryList)
        ->get();

    foreach ($categories as $category) {
        $ids[] = $category->id;

        if ($category->categories) {
            foreach ($category->categories as $cat) {
                $ids[] = $cat->id;

                if ($cat->children) {
                    foreach ($cat->children as $childCat) {
                        $ids[] = $childCat->id;
                    }
                }
            }
        }
    }

    $ids = array_unique($ids);
    return $ids;
}

function getCategoryProductsBrands($slug)
{
    $brands = [];
    $categoryProducts = Category::with(['products', 'children.products'])->where('slug', $slug)
        ->where('is_active', 1)
        ->get();

    foreach ($categoryProducts as $cp) {
        if ($cp->products) {
            foreach ($cp->products as $product) {
                $brands[] = $product->brand_id;

                if ($cp->children) {
                    foreach ($cp->children as $child) {
                        if ($child->products) {
                            foreach ($child->products as $childP) {
                                $brands[] = $childP->brand_id;
                            }
                        }
                    }
                }
            }
        }
    }

    $brands = array_unique($brands);

    return $brands;
}

function getCategoryProducts($slug): array
{
    $productList = [];

    $parentCategory = Category::select('id')->where('slug', $slug)
        ->where('is_active', 1)
        ->first();

    $allCategory = Category::select('id', 'parent_id')
        ->where('is_active', 1)->get();

    $categoryList = find_parents($allCategory, $parentCategory->id);
    array_push($categoryList, $parentCategory->id);

    $childCategory = [];
    foreach ($categoryList as $key => $value) {
        $childCategory = getAllChildrenCategory($value);
    }

    $childCategoryList = [];
    if (!empty($childCategory)) {
        foreach ($childCategory as $a => $value) {

            if (isset($value['val'])) {
                $childCategoryList[] = $value['val'];
            }

            if (isset($value['child'])) {
                foreach ($value['child'] as $b => $data) {
                    $childCategoryList[] = $data['val'];
                }
            }
        }
    }

    $finalCategoryList = array_merge($categoryList, $childCategoryList);


    $categoryProducts = Category::with(['products', 'children.products'])
        ->whereIn('id', $finalCategoryList)
        ->where('is_active', 1)
        ->get();


    foreach ($categoryProducts as $cp) {
        if ($cp->products) {
            foreach ($cp->products as $product) {
                $productList[] = $product->id;

                if ($cp->children) {
                    foreach ($cp->children as $child) {
                        if ($child->products) {
                            foreach ($child->products as $childP) {
                                $productList[] = $childP->id;
                            }
                        }
                    }
                }
            }
        }
    }

    return array_unique($productList);
}

function getAllChildrenCategory($parent_id)
{
    $cat = Category::select('id', 'parent_id')
        ->where('is_active', 1)
        ->where('parent_id', $parent_id)->get();

    $children = array();
    $i = 0;
    foreach ($cat as $key => $cat_value) {
        $children[$i] = array();

        $children[$i]['val'] = $cat_value->id;
        $children[$i]['child'] = getAllChildrenCategory($cat_value->id);

        $i++;
    }

    return $children;
}

function find_parents($input, $id)
{
    $parents = [];
    foreach ($input as $k => $val) {

        if ($val->id == $id && $val->parent_id != 0) {
            $parents = find_parents($input, $val->parent_id);
            $parents[] = $val->parent_id;
        }
    }

    return $parents;
}

function getCategoryProductAttributes($slug)
{
    $attributes = [];

    $categoryProducts = Category::with(['products', 'children.products'])->where('slug', $slug)->get();

    foreach ($categoryProducts as $cp) {
        if ($cp->products) {
            foreach ($cp->products as $product) {
                $attributesRows = $product->productAttributes;
                if (!$attributesRows->isEmpty()) {
                    foreach ($attributesRows as $attribute) {
                        $attributes[] = $attribute->attribute_id;
                    }
                }

                if ($cp->children) {
                    foreach ($cp->children as $child) {
                        if ($child->products) {
                            foreach ($child->products as $childP) {
                                $childAttributesRows = $childP->productAttributes;
                                if (!$childAttributesRows->isEmpty()) {
                                    foreach ($childAttributesRows as $attribute) {
                                        $attributes[] = $attribute->attribute_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $attributes = array_unique($attributes);

    return $attributes;
}

function getRelatedProductList($productId)
{
    $info = Product::select('category_id')->where('id', $productId)->first();

    $categorySlug = Category::select('slug')->where('id', $info->category_id)->first();

    $productIdArray = getCategoryProducts($categorySlug->slug);

    $key = array_search($productId, $productIdArray);

    unset($productIdArray[$key]);

    return $productIdArray;
}

function checkIfCouponExists($productId, $categoryId)
{
    $couponArray = Coupon::select('id', 'code', 'is_percent', 'value', 'minimum_spend', 'maximum_spend', 'usage_limit_of_person', 'usage_limit_of_coupon')
        ->where('is_active', 1)
        ->whereDate('start_date', '<=', date('Y-m-d'))
        ->whereDate('end_date', '>=', date('Y-m-d'))
        ->get();

    $couponList = [];
    foreach ($couponArray as $key => $value) {

        $checkProduct = CouponProduct::where('product_id', $productId)
            ->where('coupon_id', $value->id)->first();

        if (!empty($checkProduct->product_id)) {

            $couponDiscount = (1 == $value->is_percent) ? $value->value . ' %' : 'Rs.' . $value->value;

            $couponList[] = array(
                'code' => $value->code,
                'minimum_spend' => $value->minimum_spend,
                'discount' => $couponDiscount,
            );
        } else {
            $checkCategory = CouponCategory::where('category_id', $categoryId)
                ->where('coupon_id', $value->id)->get();

            if (!empty($checkCategory->category_id)) {
                $couponDiscount = (1 == $value->is_percent) ? $value->value . ' %' : 'Rs.' . $value->value;

                $couponList[] = array(
                    'code' => $value->code,
                    'coupon_id' => $value->id,
                    'minimum_spend' => $value->minimum_spend,
                    'discount' => $couponDiscount,
                );
            }
        }
    }

    return $couponList;
}

function getProductDetail($id)
{
    $info = Product::select('id', 'name', 'description', 'sku', 'brand_id', 'category_id', 'in_stock', 'slug')
        ->where('id', $id)->first();

    $attributeData = [];
    foreach ($info->productAttributes as $pa) {
        $attribute = \App\Models\Attribute::find($pa->attribute_id)->name;
        $attributeValues = explode(' ', $pa->value);

        //        return $pa;
        //        return $attributeValues;

        $attributeInfo = [];
        //        return $attributeValues;
        foreach ($attributeValues as $value) {
            $attributeInfo[] = [
                'name' => $value,
                'product_attribute_id' => $pa->id
            ];
        }

        $attributeData[] = array(
            'attribute_name' => $attribute,
            'attribute_id' => $pa->attribute_id,
            'detail' => $attributeInfo,
        );
    }

    $categoryId = ($info->category) ? $info->category->id : 0;
    $couponArrayData = checkIfCouponExists($id, $categoryId);

    $rating = Reviews::where('product_id', $id)->avg('rating');

    $flashSaleRemainingQty = 0;
    if (1 == $info->in_stock) {

        $flashSales = FlashSale::select('id', 'name', 'start_date', 'end_date')
            ->where('is_active', 1)
            ->where('end_date', '>=', date('Y-m-d'))
            ->get();

        if (!$flashSales->isEmpty()) {

            foreach ($flashSales as $data) {

                if ($data->flashSaleProducts) {

                    foreach ($data->flashSaleProducts as $val) {

                        if ($id == $val->product_id) {

                            $salesCount = OrderDetail::where('product_id', $val->product_id)
                                ->whereIn('delivery_status', [1, 2, 3])
                                ->whereBetween('created_at', [$data->start_date . ' 00:00:00', $data->end_date . ' 23:59:59'])->sum('quantity');

                            $flashSaleRemainingQty = $val->quantity - $salesCount;
                        }
                    }
                }
            }
        }
    }

    $orderProductQty = OrderDetail::where('product_id', $info->product_id)
        ->whereIn('delivery_status', [1, 2, 3])
        ->sum('quantity');
    $productQty = 0;
    if (0 == $flashSaleRemainingQty) {
        $orderProductQty = 0;
        $orderProductQty = OrderDetail::where('product_id', $info->product_id)
            ->whereIn('delivery_status', [1, 2, 3])
            ->sum('quantity');

        $totalProductQty = 0;
        $totalProductQty = ProductMaster::where('product_id', $info->id)->sum('quantity');

        $productQty = $totalProductQty - $orderProductQty;
    } else {
        $productQty = $flashSaleRemainingQty;
    }

    $data = array(
        'product_id' => $info->id,
        'name' => $info->name,
        'price' => getProductPrice($info->id),
        'description' => $info->description,
        'sku' => $info->sku,
        'slug' => $info->slug,
        'brand_name' => ($info->brand) ? $info->brand->name : null,
        'category_name' => ($info->category) ? $info->category->name : null,
        'category_id' => ($info->category) ? $info->category->id : null,
        'average_rating' => (!empty($rating)) ? number_format($rating, 1) : 0,
        'track_qty' => $info->in_stock,
        'actual_product_qty' => $productQty,
        'order_product_quantity' => $orderProductQty,
        'image' => getProductGalleryImage($id),
        'coupon_flag' => (empty($couponArrayData)) ? 0 : 1,
        'coupon' => $couponArrayData,
        'attribute_flag' => ($info->productAttributes->isEmpty()) ? 0 : 1,
        'attribute' => $attributeData,
    );

    return $data;
}

function getProductGalleryImage($id)
{
    $gallery = ProductGallary::where([
        ['product_id', $id],
    ])->get();
    $productAttribute = ProductAttribute::where('product_id', $id)->get();

    $array = [];
    $path = url('uploads/products/');

    $products = Product::select('image')->where('is_active', 1)->where('id', $id)->first();

    if (!empty($products->image)) {
        $image = new stdClass();
        $imageInfo = $path . '/' . $products->image;
        $array[] = $imageInfo;
    }

    if ($gallery) {
        foreach ($gallery as $galleryImage) {
            if ($galleryImage->gallary_images) {
                $image = new stdClass();
                $imageInfo = $path . '/' . $galleryImage->gallary_images;
                $array[] = $imageInfo;
            }
        }
    }

    if ($productAttribute) {
        foreach ($productAttribute as $val) {
            $image = new stdClass();
            $imageInfo = $path . '/' . $val->featured_image;
            $array[] = $imageInfo;
        }
    }

    return $array;
}

function getAttributeValues($id)
{
    $values = [];
    $attributes = ProductAttribute::where('attribute_id', $id)->get();
    if (!$attributes->isEmpty()) {
        foreach ($attributes as $attribute) {
            $att = explode(' ', $attribute->value);
            foreach ($att as $a) {
                $values[] = $a;
            }
        }
    }

    $values = array_unique($values);

    return $values;
}

function messageInAllPlatForm($order_id)
{
    $output = array(
        'response' => False,
        'msg' => "Something went wrong. Please try again later",
        'result' => "",
    );
    try {


        $user = User::where([
            ['id', auth()->user()->id],
        ])->first();
        $admins = User::where('is_admin', true)->get();

        $details = OrderDetail::where('order_id', $order_id)->get();

        if ($user->email) {
            Mail::to($user->email)->send(new CustomerMail($details));
        }

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new AdminMail($details, $user));
        }
    } catch (Exception $e) {
        $output['msg'] = $e->getMessage();
        $output['response'] = FALSE;
    }
    return $output;
}

function sendSMS($data): array
{
    $output = array(
        'response' => False,
        'msg' => "Something went wrong. Please try again later",
        'result' => "",
    );

    try {

        $authToken = DB::table('sms_token')->first();

        $args = http_build_query(array(
            'auth_token' => $authToken->auth_token,
            'to' => $data['to'],
            'text' => $data['msg'],
        ));

        $api_url = "https://sms.aakashsms.com/sms/v3/send";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        /*Response*/
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        /*check the response and verify*/
        $response = json_decode($response, true);

        if ($response['error']) {
            throw new Exception($response['message']);
        } else {

            $result = (!empty($response['data']['invalid'])) ? $response['data']['invalid'] : $response['data']['valid'];

            if (!empty($result)) {

                foreach ($result as $key => $value) {
                    $sms = new SmsLog;
                    $sms->message = $value['text'];
                    $sms->credit = $value['credit'];
                    $sms->to_number = $value['mobile'];
                    $sms->network = $value['network'];
                    $sms->status = $value['status'];
                    $sms->created_date = now();
                    $sms->save();
                }

                $output = array(
                    'result' => '',
                    'response' => TRUE,
                    'msg' => 'SMS sent Successfully',
                );
            }
        }
    } catch (Exception $e) {
        $output['msg'] = $e->getMessage();
        $output['response'] = FALSE;
    }

    return $output;
}

function getProductsByAttributeValues($values)
{
    $ids = [];
    $checkValues = explode(",", $values);
    $productAttributes = ProductAttribute::all();
    foreach ($productAttributes as $pa) {
        $values = explode(' ', $pa->value);

        foreach ($checkValues as $cv) {
            if (!empty(array_intersect($checkValues, $values))) {
                $ids[] = $pa->product_id;
            }
        }
    }

    $ids = array_unique($ids);

    return $ids;
}

function getChildrenCategory($parent_id)
{
    return Category::where('parent_id', $parent_id)->get();
}


function getShipingPrice($id)
{
    return ShippingMethod::find($id)->cost;
}

function getOrderStatus($id)
{
    $status = DB::table('order_status')->where('id', $id)->first();
    return $status->name;
}

function getNewProductPrice($product_id)
{
    $product = Product::find($product_id);
    if ($product->productAttributes->count()) {
        $priceArray = array();
        $prodMaster = ProductMaster::where([
            ['product_id', $product->id],
            ['is_active', 1]
        ])->get();

        foreach ($prodMaster as $item) {
            $priceArray[] = $item->price;
        }

        return min($priceArray);
    } else {
        return ProductMaster::where([
            ['product_id', $product->id],
            ['is_active', 1]
        ])->first()->price;
    }
}

function getProductVariantPrice($product, $variant)
{
    $data = ProductMaster::where([
        ['product_id', $product],
        ['is_active', 1]
    ]);

    if (null != $variant || 0 != $variant) {
        $data->where('attribute_id', $variant);
    }
    // dd($data->where('attribute_id', $variant));
    $result = $data->orderBy('created_at', 'DESC')->first();
    // dd($result);
    return $result->price ? $result->price : 0;
}

function getCategory($id)
{
    return Category::find($id);
}

function getProvinceId($city_id)
{
    return District::where('district_id', $city_id)->first()->zone_state_id;
}

function getProductByCategory($category_id)
{
    return Product::where('category_id', $category_id)->get();
}

function getCouponProduct($coupon_id)
{

    $couponCategories = DB::table('coupon_categories')->where('coupon_id', $coupon_id)->get();
    $couponProducts = DB::table('coupon_products')->where('coupon_id', $coupon_id)->get();

    $product = [];

    foreach ($couponProducts as $p) {

        $product[] = $p->product_id;
    }

    foreach ($couponCategories as $category) {
        foreach (getProductByCategory($category->category_id) as $pro) {
            $product[] = $pro->id;
        }
    }

    return $product;
}

function getCouponDiscountedPrice($coupon_id, $product_id, $quantity)
{
    $coupon = DB::table('coupons')->where('id', $coupon_id)->first();
    $prodductPrice = getProductPrice($product_id);

    if ($coupon->is_percent) {
        $coupon_discount_amount = floatval($prodductPrice * ($coupon->value / 100));
        $discountedPrice = floatval($prodductPrice) - floatval($coupon_discount_amount);
    } else {
        $discountedPrice = floatval($prodductPrice) - floatval($coupon->value);
    }
    return $discountedPrice;
}

function getCouponDiscountedPriceByCategory($coupon_id, $product_id, $quantity): float
{

    $coupon = DB::table('coupons')->where('id', $coupon_id)->first();
    $prodductPrice = floatval(getProductPrice($product_id) * $quantity);

    if ($coupon->is_percent) {

        $coupon_discount_amount = floatval($prodductPrice * ($coupon->value / 100));
        $discountedPrice = floatval($prodductPrice) - floatval($coupon_discount_amount);
    } else {
        $discountedPrice = ($prodductPrice) - floatval($coupon->value);
    }

    return $discountedPrice;
}

function getProductName($product_id)
{
    return Product::where('id', $product_id)->first()->name;
}


function CouponProducts($id)
{
    $couponProducts = CouponProduct::where('coupon_id', $id)->pluck('product_id')->toArray();
    if ($couponProducts) {
        return $couponProducts;
    } else {
        return null;
    }
}

function CouponCategories($id)
{
    $couponCategories = CouponCategory::where('coupon_id', $id)->pluck('category_id')->toArray();
    if ($couponCategories) {
        return $couponCategories;
    } else {
        return null;
    }
}

function getProductImage($id)
{
    return Product::where('id', $id)->first()->image;
}

function getVendorCost($id)
{
    $vendor = DB::table('delivery_vendors')->where('id', $id)->first();
    return $vendor->delivery_charge;
}

function getVendorName($id)
{

    $vendor = DB::table('vendors')->where('id', $id)->first();

    if ($vendor)
        return $vendor->name;
    else
        return '';
}


function getUserName($id)
{

    $user = DB::table('users')->where('id', $id)->first();

    if ($user)
        return $user->name;
    else
        return '';
}


function getStockQty($product, $attribute = null)
{
    $entry_stock = ProductMaster::where([
        ['product_id', $product],
        ['attribute_id', $attribute],
        ['is_active', true]
    ])
        ->sum('quantity');


    $purchase_return = \App\Models\PurchaseReturnDetail::where([
        ['product_id', $product],
        ['product_attribute_id', $attribute]
    ])->sum('qty');

    $sales = OrderDetail::where([
        ['product_id', $product],
        ['attribute', $attribute],
    ])
        //    ->orWhere('delivery_status','1')
        //    ->orWhere('delivery_status','2')
        ->where('delivery_status', '3')
        //    ->orWhere('delivery_status','5')
        //    ->orWhere('delivery_status','7')

        ->sum('quantity');

    //    $orderStatus = $sales = OrderDetail::where([
    //        ['product_id',$product],
    //        ['attribute',$attribute],
    //    ])
    //        ->orWhere('delivery_status','4')
    //        ->orWhere('delivery_status','6')
    //        ->orWhere('delivery_status','8')
    //        ->sum('quantity');

    $sales_return = SaleReturnDetail::where([
        ['product_id', $product],
        ['attribute', $attribute]
    ])->sum('quantity');

    return $entry_stock + $sales_return - $sales - $purchase_return;
}

function getFiscalYear()
{

    $today = now()->toDateString();

    $fiscal_year = DB::table('fiscal_year_master')->where([
        ['start_date', '<=', $today],
        ['end_date', '>=', $today],
    ])->first();

    if ($fiscal_year) {
        return $fiscal_year->id;
    } else {
        return '';
    }
}

function getFiscalYearName($id)
{
    $fiscal_year = DB::table('fiscal_year_master')->where('id', $id)->first();
    if ($fiscal_year)
        return $fiscal_year->name;
    else
        return '';
}

function getUserAddressId($user_id)
{
    $address = Address::where('user_id', $user_id)->latest()->first();
    if ($address)
        return $address->id;
    else
        return '';
}

function getModeName($id)
{
    $mode = DB::table('order_modes')->where('id', $id)->first();

    if ($mode)
        return $mode->name;
    else
        return '';
}

function getTotalPending()
{
    return \App\Models\Order::where([
        ['delivery_status', 1],
        ['fiscal_year_id', getFiscalYear()]
    ])->count();
}

function getTotalCancelled()
{
    return \App\Models\Order::where('delivery_status', '8')->count();
}

function getTotalReturned()
{
    return \App\Models\Order::where('delivery_status', '4')->count();
}

function getTotalDelivered()
{
    return \App\Models\Order::where('delivery_status', '3')->count();
}

function getTotalProcessing()
{
    return \App\Models\Order::where('delivery_status', '2')->count();
}

function getLogistictVendorOrderLog($base_url, $logistic_order_code, $token)
{

    $response = Http::withHeaders([
        'Authorization' => $token
    ])->get($base_url, [
        'id' => $logistic_order_code
    ]);

    if (($response->status() == 200))
        return $response->json();

    abort($response->status(), $response->reason());
}
