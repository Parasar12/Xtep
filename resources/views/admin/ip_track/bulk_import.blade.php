@extends('layouts.app')

@section('content')

<!-- Begin page -->
<div id="layout-wrapper">

  @include('admin.includes.header')

  @include('admin.includes.left_sidebar')

  <div class="main-content">
    <div class="page-content">
      <div class="col-12">
        <div class="jumbotron" style="background: white">
          <h1 class="display-4">Instructions : </h1>
          <p> 1. Download the format file and fill it with proper data.</p>
          <p>2. You can download the example file to understand how the data must be filled.</p>
          <p>3. Once you have downloaded and filled the format file upload it in the form below and submit.</p>
          <p> 4. After uploading products you need to edit them and set product images and attributes.</p>
          <p> 5. You can get brand and category id from their list please input the right ids.</p>
        </div>
      </div>

      <div class="col-md-12">
        <form class="product-form" action="{{route('products.bulk-import.store')}}" method="POST" enctype="multipart/form-data" id="bulk_import_store">
          @csrf
          <div class="card mt-2 rest-part">
            <div class="row">
              <div class="card-header col-md-6">
                <a href="{{asset('bulk_import_format/product_import_bulk_format.xlsx')}}" download="" class="btn btn-secondary"><i class="fa fa-download"></i>&nbsp;Download Product Bulk Upload Format</a>
              </div>

              <div class="card-header col-md-6">
                <a href="{{route('products.bulk-export')}}" download="" style="float: right;" class="btn btn-secondary"><i class="fa fa-upload"></i>&nbsp;Bulk Export All Products</a>
              </div>
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <input type="file" class="form-control" name="products_file">
                  <span class="products_file_error"></span>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12" style="padding-top: 20px">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
              
            </div>
          </div>

        </form>
      </div>
    </div>

  </div>

</div>

@endsection

@section('script')

<script>

  $(document).on('submit', '#bulk_import_store', function(e) {
    e.preventDefault();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });

    $.ajax({
      type: $(this).attr('method'),
      url: $(this).attr('action'),
      data: new FormData(this),
      processData: false,
      dataType: 'json',
      contentType: false,

      beforeSend: function(data) {
        $(document).find('form span').text('');

      },
      success: function(data) {
        if (data.status == 0) {
          console.log(data);
          $.each(data.error, function(key, value) {
            $('form span.' + key + '_error').text(value[0]);
          });
        } else {
          toastr.success(data.msg);
        }
      },
    });
  });
</script>

@endsection