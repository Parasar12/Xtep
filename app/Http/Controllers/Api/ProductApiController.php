<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\ProductGallary;
use App\Models\CategoryBanner;
use App\Models\Wishlist;
use App\Models\Reviews;
use App\Models\User;
use App\Models\Category;

use Illuminate\Support\Facades\DB;
use App\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;

use stdClass;

use Illuminate\Http\Request;

class ProductApiController extends Controller
{

  public function categoryBannerProducts()
  {
    $categoryBannerArray = CategoryBanner::select('category_id', 'heading')
      ->where('is_active', 1)->orderBy('created_at', 'DESC')->get();

    if ($categoryBannerArray->isEmpty()) {
      return ApiResponse::sendNoDataErrorResponse('No Data found.');
    } else {
      return ApiResponse::sendSuccessResponse('Success', $categoryBannerArray);
    }
  }

  public function latestProducts(Request $request)
  {
    $latestProducts = Product::select('id')
      ->skip($request->offset * $request->limit)->take($request->limit)
      ->where('is_active', 1)->orderBy('created_at', 'DESC')->get();

    if ($latestProducts->isEmpty()) {
      return ApiResponse::sendNoDataErrorResponse('No Data found.');
    } else {
      foreach ($latestProducts as $val) {
        $product[] = getProductDetail($val->id);
      }

      return ApiResponse::sendSuccessResponse('Success', $product);
    }
  }

  public function searchProduct(Request $request)
  {
    $rules = array(
      'text' => 'required',
      'limit' => 'required|numeric',
      'offset' => 'required|numeric',
    );

    $messages = array(
      'text.required' => 'Search Text is required.',
      'limit.required' => 'Limit is required.',
      'offset.required' => 'Offset is required.',
    );

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    } else {
      $products = Product::where('is_active', 1)
        ->where('name', 'like', '%' . $request->text . '%')->get();

      $category = Category::where('is_active', 1)
        ->where('slug', 'like', '%' . $request->text . '%')->get();


      $productIdArray = [];
      foreach ($products as $key => $value) {
        $productIdArray[] = $value->id;
      }

      $categoryProductArray = [];
      if (!empty($category)) {
        foreach ($category as $key => $value) {
          $categoryProductArray[] = getCategoryProducts($value->slug);
        }
      }

      if (!empty($categoryProductArray)) {

        foreach ($categoryProductArray as $key => $value) {
          $finalProductArray = array_merge($productIdArray, $value);
        }
      } else {
        $finalProductArray = $productIdArray;
      }

      $finalArrayList = array_unique($finalProductArray);

      $offset = ($request->offset == 0) ? 0 : ($request->offset * $request->limit);

      $catgoryArrayList = array_slice($finalArrayList, $offset, $request->limit);


      if (!empty($catgoryArrayList)) {

        foreach ($catgoryArrayList as $key => $value) {
          $product[] = getProductDetail($value);
        }

        return ApiResponse::sendSuccessResponse('Success', $product);
      } else {
        return ApiResponse::sendNoDataErrorResponse('No Data found.');
      }
    }
  }

  public function relatedProducts(Request $request)
  {

    $data = getRelatedProductList($request->product_id);

    $offset = ($request->offset == 0) ? 0 : ($request->offset * $request->limit);

    $productArrayList = array_slice($data, $offset, $request->limit);

    if (!empty($productArrayList)) {

      $result = array();
      foreach ($productArrayList as $key => $value) {
        $result[] = getProductDetail($value);
      }

      return ApiResponse::sendSuccessResponse('Success', $result);
    } else {
      return ApiResponse::sendNoDataErrorResponse('No Data found.');
    }
  }

  public function wishlistProducts()
  {
    $list = Wishlist::select('product_id')->where('user_id', auth()->user()->id)->get();

    if (count($list) > 0) {
      foreach ($list as $key => $value) {
        $product[] = getProductDetail($value->product_id);
      }

      return ApiResponse::sendSuccessResponse('Success', $product);
    } else {
      return ApiResponse::sendNoDataErrorResponse('No Data found.');
    }
  }

  public function checkWishlistProducts(Request $request)
  {
    $rules = array(
      'product_id' => 'required|numeric',
    );

    $messages = array(
      'product_id.required' => 'Product is required.',
    );

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    } else {

      $check = Wishlist::where('user_id', auth()->user()->id)
        ->where('product_id', $request->product_id)
        ->first();

      $status = (!empty($check->product_id)) ? TRUE : FALSE;

      return ApiResponse::sendSuccessResponse('Success', $status);
    }
  }

  public function productReviews(Request $request)
  {
    $rules = array(
      'product_id' => 'required|numeric',
      'review' => 'required',
      'rating' => 'required',
    );

    $messages = array(
      'product_id.required' => 'Product is required.',
      'review.required' => 'Review is required.',
      'rating.required' => 'Rating is required.',
    );

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    } else {

      $review = new Reviews();
      $review->product_id = $request->product_id;
      $review->user_id = auth()->user()->id;
      $review->rating = $request->rating;
      $review->review = $request->review;
      $review->save();

      return ApiResponse::sendSuccessResponse('Success', 'Review Submitted Successfully');
    }
  }

  public function productReviewList(Request $request)
  {
    $rules = array(
      'product_id' => 'required|numeric',
      'limit' => 'required|numeric',
      'offset' => 'required|numeric',
    );

    $messages = array(
      'product_id.required' => 'Product Name is required.',
      'limit.required' => 'Limit is required.',
      'offset.required' => 'Offset is required.',
    );

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    } else {
      $review = Reviews::select('rating', 'review', 'user_id', 'created_at', 'id')
        ->where('product_id', $request->product_id)
        ->skip($request->offset * $request->limit)->take($request->limit)
        ->orderBy('created_at', 'DESC')
        ->get();

      if ($review->isEmpty()) {
        return ApiResponse::sendNoDataErrorResponse('No Data found.');
      } else {

        $reviewDetail = Reviews::where('product_id', $request->product_id)
          ->selectRaw('count(id) as count_status, rating')
          ->groupBy('rating')
          ->get();

        $reviewTotalCount = Reviews::where('product_id', $request->product_id)->count();

        $rating = Reviews::where('product_id', $request->product_id)->avg('rating');

        $data = [];
        $data['review_count'] = $reviewTotalCount;
        $data['average_review'] = (!empty($rating)) ? number_format($rating, 1) : 0;

        $data['review_detail'] = $reviewDetail;
        foreach ($review as $key => $value) {
          $user = User::select('name', 'profile_image')->where('id', $value->user_id)->first();

          $data['info'][] = array(
            'id' => $value->id,
            'rating' => $value->rating,
            'review' => $value->review,
            'user' => $user->name,
            'user_image' => (null == $user->profile_image) ? '' : url('uploads/profile') . '/' . $user->profile_image,
            'review_datetime' => $value->created_at,
          );
        }

        return ApiResponse::sendSuccessResponse('Success', $data);
      }
    }
  }

  public function addToWishlist(Request $request)
  {
    $rules = array(
      'product_id' => 'required|numeric',
    );

    $messages = array(
      'product_id.required' => 'Product Name is required.',
    );

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    } else {

      $list = Wishlist::select('product_id')
        ->where('product_id', $request->product_id)
        ->where('user_id', auth()->user()->id)->first();

      if (empty($list->product_id)) {

        $wish = new Wishlist;
        $wish->product_id = $request->product_id;
        $wish->user_id = auth()->user()->id;
        $wish->save();

        return ApiResponse::sendSuccessResponse('Product Added To Wishlist Successfully');
      } else {

        Wishlist::where('user_id', auth()->user()->id)
          ->where('product_id', $request->product_id)->delete();

        return ApiResponse::sendSuccessResponse('Product Removed From Wishlist Successfully');
      }
    }
  }

  public function productDetailByProductId(Request $request)
  {
    $rules = array(
      'product_id' => 'required|numeric',
    );

    $messages = array(
      'product_id.required' => 'Product Name is required.',
    );

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    } else {
      $info = getProductDetail($request->product_id);

      return ApiResponse::sendSuccessResponse('Success', $info);
    }
  }



  public function get_home_page_products_with_categories(Request $request)
  {

    $categories = Category::where([
      ['parent_id', 0],
      ['is_active', true]
    ])->get();

    $choosenCategories = [];
    $data = [];
    $i=1;
    $products = [];

    foreach ($categories as $category) {
      if ($category->products->count() && $i<4) {
        $choosenCategories = [
          'category_id' => $category->id,
          'name' => $category->name,
          'slug' => $category->slug,
          'image' => asset($category->image),
        ];

        $categoryProducts = $category->products;
       
        foreach ($categoryProducts as $val) {
          $products[] = getProductDetail($val->id);
        }

        $data[] = [
          'categories' => $choosenCategories,
          'products' => $products
        ];

        $i++;
      }
    }

    return ApiResponse::sendSuccessResponse('Success', $data);
  }
}
