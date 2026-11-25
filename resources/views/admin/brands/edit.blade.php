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
            <form method="post" action="{{route('brands.update')}}" enctype="multipart/form-data" id="update_brand">
              @csrf
              <input type="hidden" name="id" value="{{$brand->id}}">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Brand Name *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter brand name" id="name" name="name" maxlength="200" value="{{$brand->name}}" required>
                          <span class="error_txt name_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Brand Slug *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter brand slug" id="slug" name="slug" value="{{$brand->slug}}" maxlength="200" required>
                          <span class="error_txt slug_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Brand Image </label>
                        <div class="col-md-10">
                          <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)">
                          <span class="error_txt image_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                          <img style="width: 200px; height: 200px;" src="@if($brand->image) {{asset('uploads/brands/'.$brand->image)}} @endif" id="imgshow" />
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <input type="submit" class="btn btn-primary waves-effect waves-light" value="Submit">
                          </div>
                        </div>

                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <a class="btn btn-secondary" href="{{route('brands.index')}}">Back</a>
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

<script>
  function showSelectedImage(e) {
    $("#imgload").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#imgshow').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  $(document).on('submit', '#update_brand', function(e) {
    e.preventDefault();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });

    $.ajax({
      type: 'POST',
      url: $(this).attr('action'),
      data: new FormData(this),
      processData: false,
      dataType: 'json',
      contentType: false,

      beforeSend: function(data) {
        $(this).find('span .error_txt').text('');

      },
      success: function(data) {
        $(this).find('span .error_txt').text('');
        if (data.status == 0) {
          $.each(data.error, function(key, value) {
            $('form span.' + key + '_error').text(value[0]);
          });
        } else {
          toastr.success(data.msg);
          location.href = "/brands";
        }
      },
    });
  });
</script>
@endsection