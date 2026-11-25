@extends('layouts.app')

@section('content')

<?php
$categories = \App\Models\Category::where('parent_id', 0)->with('children')->get();

?>

<!-- Begin page -->
<div id="layout-wrapper">
  @include('admin.includes.header')

  @include('admin.includes.left_sidebar')

  <div class="main-content">

    <div class="page-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <form method="post" action="{{route('banners.update')}}" enctype="multipart/form-data" id="update_banner">
              @csrf
              <input type="hidden" name="id" value="{{$banner->id}}">

              <input type="hidden" name="categoryId" value="{{$banner->category_id}}">

              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Category *</label>
                        <div class="col-md-10">
                          <select class="form-select form-control" name="category_id" required>
                            <option value="">Select Type</option>
                            @include('selected_banner_category')
                          </select>
                          <span class="error_txt category_id_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Heading *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter banner heading" id="heading" name="heading" value="{{$banner->heading}}" maxlength="200" required>
                          <span class="error_txt heading_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Title *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter banner title" id="title" name="title" value="{{$banner->title}}" maxlength="200" required>

                          <span class="error_txt title_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Sub Title *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter banner sub title" id="sub_title" name="sub_title" value="{{$banner->sub_title}}" maxlength="200" required>
                          <span class="error_txt sub_title_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Banner Image (295 x 672) </label>
                        <div class="col-md-10">
                          <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)">
                          <span class="error_txt image_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label class="col-md-2 col-form-label"></label>
                        <div class="col-md-10">
                          <img src="@if($banner->banner_image) {{asset('uploads/categories/banners/'.$banner->banner_image)}} @endif" id="imgshow" style="width: 200px; max-height: 200px"/>
                        </div>
                      </div>

                      
                      <div class="row">
                        <div class="col-md-6">
                          <div class="text-center mt-4">
                            <input type="submit" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="text-center mt-4">
                            <a class="btn btn-secondary" style="float: right;" href="{{route('banners.index')}}">Back</a>
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



  $(document).on('submit', '#update_banner', function(e) {
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
        $('form .error_txt').text('');
      },
      success: function(data) {
        if (data.status == 0) {
         
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