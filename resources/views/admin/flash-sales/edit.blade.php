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
            <form method="post" action="{{route('flash-sales.update', $sale->id)}}" id="update_flash" novalidate class="eq-section">
              @csrf
              @method('PATCH')
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group item">
                            <label class="required-label">Campaign Name </label>
                            <input class="form-control" type="text" value="{{ $sale->name }}" placeholder="Please enter campaign name" name="name" required>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group item">
                            <label class="required-label">Campaign Start Date </label>
                            <input class="form-control" value="{{ $sale->start_date }}" type="date" placeholder="Campaign Start Date" name="start_date" id="start_date" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group item">
                            <label class="required-label">Campaign End Date </label>
                            <input class="form-control" value="{{ $sale->end_date }}" type="date" placeholder="Campaign End Date" name="end_date" id="end_date" required>
                          </div>
                        </div>
                      </div>

                      <div class="cloning_div">
                        @if(!$flashProducts->isEmpty())
                        @foreach($flashProducts as $flash)
                        <div class="control-group mainSet" style="margin-top:10px">
                          <hr>

                          <div class="mb-3 row">
                            <div class="input-group-btn">
                              <button class="btn btn-danger btn-remove" type="button"><i class="fa fa-trash"></i> Remove</button>
                            </div>
                          </div>

                          <div class="mb-3 row">
                            <label class="col-md-2 required-label">Products </label>
                            <div class="col-md-10 item">
                              <select class="js-example-basic-single" name="products[]" onchange="getSellingPriceAndQuantity(this)" required>
                                <option value="" disabled>Select</option>
                                @if(!$products->isEmpty())

                                @foreach($products as $product)
                                <option value="{{$product->id}}" @if($product->id == $flash->product_id) selected @endif>{{$product->name}}</option>
                                @endforeach
                                @endif
                              </select>
                              <span class="error_txt products_error"></span>
                            </div>
                          </div>

                          <div class="mb-3 row">
                            <label class="col-md-2 required-label">Price </label>
                            <div class="col-md-4 item">
                              <div class="selling_price"></div>
                              <input class="form-control" type="number" required placeholder="Please enter price" name="price[]" value="{{ $flash->price }}">
                            </div>

                            <label class="col-md-2 col-form-label">Quantity </label>
                            <div class="col-md-4 item">
                              <div class="quantity"></div>
                              <input class="form-control" type="number" required placeholder="Please enter quantity" name="quantity[]" value="{{$flash->quantity}}">
                            </div>
                          </div>
                        </div>
                        @endforeach
                        @endif

                      </div>

                      <div class="main_div" style="display:none">
                        <div class="control-group mainSet" style="margin-top:10px">
                          <hr>

                          <div class="mb-3 row">
                            <div class="input-group-btn">
                              <button class="btn btn-danger btn-remove" type="button"><i class="fa fa-trash"></i> Remove</button>
                            </div>
                          </div>

                          <div class="mb-3 row">
                            <label class="col-md-2 required-label">Products </label>
                            <div class="col-md-10 item">
                              <select class="js-example-basic-single" name="products[]" required>
                                <option value="" selected="selected" disabled="disabled">Select</option>
                                @if(!$products->isEmpty())
                                @foreach($products as $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                                @endif
                              </select>
                            </div>
                          </div>

                          <div class="mb-3 row">
                            <label class="col-md-2 required-label">Price </label>
                            <div class="col-md-4 item">
                              <input class="form-control" type="number" required placeholder="Please enter price" name="price[]">
                            </div>

                            <label class="col-md-2 required-label">Quantity </label>
                            <div class="col-md-4 item">
                              <input class="form-control" type="number" required placeholder="Please enter quantity" name="quantity[]">
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <div class="col-md-2"></div>

                        <div class="col-md-10">
                          <div class="input-group control-group form-group">
                            <div class="input-group-btn">
                              <button class="btn btn-success btn-add-more" type="button"><i class="glyphicon glyphicon-plus"></i>Add Product</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <input type="submit" id="editBtn" class="btn btn-primary waves-effect waves-light" value="Submit">
                          </div>
                        </div>

                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <a class="btn btn-secondary" href="{{route('flash-sales.index')}}">Back</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> <!-- end col -->
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

  $('.cloning_div .js-example-basic-single').select2();

  $(".btn-add-more").click(function(e) {
    var elem = document.querySelector('.main_div .mainSet');
    var clone = elem.cloneNode(true);

    $(".cloning_div").append(clone);
    $('.cloning_div .js-example-basic-single').select2();
  });

  $("body").on("click", ".btn-remove", function() {
    $(this).parents(".control-group").remove();
  });

  function getSellingPriceAndQuantity(e) {
    var id = $(e).find('option:selected').val();
    $(e).closest('.control-group').find('.selling_price').text('');
    $(e).closest('.control-group').find('.quantity').text('');

    $.get("{{ url('/product/price_quantity/')}}" + '/' + id, function(data) {
      $(e).closest('.control-group').find('.selling_price').text('Selling price is Rs.' + data.price);
      $(e).closest('.control-group').find('.quantity').text('Available quantity is ' + data.quantity);
    });
  }


  $(document).on('submit', '#update_flash', function(e) {
    if(validator.checkAll($(this))) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      });

      var data = new FormData(this);        
      var url = $(this).attr('action');

      window.ct.postDataMultiPartForm(url, data, $("#editBtn")).then(function(responseData){
        toastr.success(responseData.msg);
      }, function(error){
        toastr.error(error.msg);          
        window.ct.populateFormError("#update_flash", error.result);
      });
    }
    return false;
  });

</script>

@endsection