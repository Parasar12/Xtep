@extends('layouts.app')

@section('content')

<!-- Begin page -->
<div id="layout-wrapper">
  @include('admin.includes.header')

  @include('admin.includes.left_sidebar')

  <div class="main-content">

    <div class="page-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <form method="post" action="{{route('coupons.update', $coupon->id)}}" enctype="multipart/form-data" novalidate class="eq-section" id="updateCouponForm">
              @csrf
              @method('PATCH')
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row item">
                        <label for="code" class="col-md-2 col-form-label">Code *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter coupon code" id="code" name="code" maxlength="200" value="{{$coupon->code}}" required>
                          <span class="error_txt code_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row item">
                        <label for="name" class="col-md-2 col-form-label">Discount Type *</label>
                        <div class="col-md-10">
                          <select class="form-select" name="is_percent" required>
                            <option value="">Select Type</option>
                            <option value="0" @if($coupon->is_percent == 0) selected @endif>Fixed</option>
                            <option value="1" @if($coupon->is_percent == 1) selected @endif>Percent</option>
                          </select>
                          <span class="error_txt is_percent_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row item">
                        <label for="code" class="col-md-2 col-form-label">Value *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="number" placeholder="Please discount value" id="value" name="value" value="{{$coupon->value}}" step="0.02" required>
                          <span class="error_txt value_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="code" class="col-md-2 col-form-label">Start Date *</label>
                        <div class="col-md-4">
                          <input class="form-control" type="date" id="start_date" name="start_date" value="{{$coupon->start_date}}" required>
                          <span class="error_txt start_date_error"></span>
                        </div>

                        <label for="code" class="col-md-2 col-form-label">End Date *</label>
                        <div class="col-md-4">
                          <input class="form-control" type="date" id="end_date" name="end_date" value="{{$coupon->end_date}}" required>
                          <span class="error_txt end_date_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="code" class="col-md-2 col-form-label">Minimum Spend </label>
                        <div class="col-md-4">
                          <input class="form-control" type="number" id="minimum_spend" name="minimum_spend" value="{{$coupon->minimum_spend}}" step="0.02">
                          <span class="error_txt minimum_spend_error"></span>
                        </div>

                        <label for="code" class="col-md-2 col-form-label">Maximum Spend </label>
                        <div class="col-md-4">
                          <input class="form-control" type="number" id="maximum_spend" name="maximum_spend" value="{{$coupon->maximum_spend}}" step="0.02">
                          <span class="error_txt maximum_spend_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="code" class="col-md-2 col-form-label">Usage Limit per Person *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="number" id="usage_limit_of_person" name="usage_limit_of_person" value="{{$coupon->usage_limit_of_person}}" required>

                          <span class="error_txt usage_limit_of_person_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="code" class="col-md-2 col-form-label">Usage Limit per Coupon *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="number" id="usage_limit_of_coupon" name="usage_limit_of_coupon" value="{{$coupon->usage_limit_of_coupon}}" required>

                          <span class="error_txt usage_limit_of_coupon_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="products" class="col-md-2 col-form-label">Products </label>
                        <div class="col-md-10">
                          <select class="js-example-basic-multiple" name="products[]" multiple="multiple">
                            @if(!$products->isEmpty())

                            @foreach($products as $product)
                            <option value="{{$product->id}}" @if($couponProducts) @foreach($couponProducts as $cp) @if($cp==$product->id)
                              selected
                              @endif
                              @endforeach
                              @endif>{{$product->name}}
                            </option>
                            @endforeach
                            @endif
                          </select>
                          <span class="error_txt products_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Categories </label>
                        <div class="col-md-10">
                          <select class="js-example-basic-multiple" name="categories[]" multiple="multiple">
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if($couponCategories) @foreach($couponCategories as $cc) @if($cc==$category->id)
                              selected
                              @endif
                              @endforeach
                              @endif>{{ $category->name }}
                            </option>
                            @foreach ($category->children as $childCategory)
                            <option value="{{ $childCategory->id }}" @if($couponCategories) @foreach($couponCategories as $cc) @if($cc==$childCategory->id)
                              selected
                              @endif
                              @endforeach
                              @endif>{{ $category->name }} &#8594; {{ $childCategory->name }}
                            </option>
                            @if ($childCategory->categories)
                            @foreach ($childCategory->categories as $cCategory)
                            <option value="{{ $cCategory->id }}" @if($couponCategories) @foreach($couponCategories as $cc) @if($cc==$cCategory->id)
                              selected
                              @endif
                              @endforeach
                              @endif>{{ $category->name }} &#8594; {{ $childCategory->name }} &#8594; {{ $cCategory->name }}
                            </option>
                            @if($cCategory->categories)
                            @foreach($cCategory->categories as $scCategory)
                            <option value="{{ $scCategory->id }}" @if($couponCategories) @foreach($couponCategories as $cc) @if($cc==$scCategory->id)
                              selected
                              @endif
                              @endforeach
                              @endif>{{ $category->name }} &#8594; {{ $childCategory->name }} &#8594; {{ $cCategory->name }} &#8594; {{ $scCategory->name }}
                            </option>
                            @endforeach
                            @endif
                            @endforeach
                            @endif
                            @endforeach
                            @endforeach
                          </select>
                          <span class="error_txt categories_error"></span>
                        </div>
                      </div>

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" @if($coupon->is_active == 1) checked @endif>
                        <label class="form-check-label" for="formCheck1">
                          Is Active
                        </label>
                      </div>


                      <div class="row">
                        <div class="col-md-6">
                          <div class="text-center mt-4">
                            <input type="submit" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="text-center mt-4">
                            <a type="button" style="float: right;" class="btn btn-secondary" href="{{route('coupons.index')}}">Back</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> 
                <!-- end col -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')

<script type="text/javascript">

  $(document).ready(function() {

    $('.js-example-basic-multiple').select2();


    $("#updateCouponForm").submit(function(event) {
      if(validator.checkAll($(this))) {
        var data = new FormData(this);        
        var url = $(this).attr('action');

        event.preventDefault();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        });

        window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
          toastr.success(responseData.msg);          
        }, function(error){
          toastr.error(error.msg);          
          window.ct.populateFormError("#updateCouponForm", error.result);
        });
      }
      return false;
    });

  });

</script>
@endsection