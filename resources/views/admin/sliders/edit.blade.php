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
        <form method="post" novalidate class="eq-section" action="{{route('sliders.update', $slider->id)}}" enctype="multipart/form-data" id="updateSliderForm">
          @csrf
          @method('PATCH')

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">

                  <div class="mb-3 row">
                    <label class="required-label col-md-2 col-form-label">Category </label>
                    <div class="col-md-10 item">
                      <select class="form-select form-control" name="category_id" required>
                        <option value="" selected="selected" disabled="disabled">Select Type</option>
{{--                        @include('selected_banner_category')--}}
                          @foreach($categories as $category)
                              <option value="{{ $category->id }}" {{ $category->id == $slider->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                          @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label class="required-label col-md-2 col-form-label">Main Title </label>
                    <div class="col-md-10 item">
                      <input class="form-control" type="text" placeholder="Please enter image title" id="main_title" name="main_title" value="{{$slider->main_title}}" maxlength="200" required>
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label class="col-md-2 col-form-label">Title </label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" placeholder="Please enter image title" id="title" name="title" value="{{$slider->title}}" maxlength="200">
                    </div>
                  </div>

                    <div class="mb-3 row">
                        <label for="slug" class="col-md-2  col-form-label">Web Slider Image </label>
                        <div class="col-md-6 item">
                            <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)" >
                        </div>
                        <div class="col-md-4">
                            <img src="{{ asset('uploads/sliders/'.$slider->image) }}" id="imgshow" style="width: 200px; max-height: 200px"/>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Mobile Slider Image </label>
                        <div class="col-md-6 item">
                            <input class="form-control" type="file" id="mobileImgload" name="mobile_image" onclick="showSelectedMobileImage(event)" >
                        </div>
                        <div class="col-md-4">
                            <img src="{{ asset('uploads/sliders/'.$slider->mobile_image) }}" id="mobileImgshow" style="width: 200px; max-height: 200px"/>
                        </div>
                    </div>


                  <div class="row">
                    <div class="col-md-1">
                      <div class="text-center mt-4">
                        <input type="submit" id="save" class="btn btn-primary waves-effect waves-light" value="Submit">
                      </div>
                    </div>

                    <div class="col-md-1">
                      <div class="text-center mt-4">
                        <a class="btn btn-secondary" href="{{route('sliders.index')}}">Back</a>
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

  $(document).ready(function(){

    $('#updateSliderForm').on('submit', function(){
      if(validator.checkAll($(this))) {

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        });

        var data = new FormData(this);
        var url = $(this).attr('action');

        window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
          toastr.success(responseData.msg);
          location.href ='/sliders';
        }, function(error){
          toastr.error(error.msg);
          window.ct.populateFormError("#updateSliderForm", error.result);
        });
      }
      return false;
    });
  });

  function showSelectedImage(e){
    $("#imgload").change(function () {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#imgshow').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  function showSelectedMobileImage(e){
      $("#mobileImgload").change(function () {
          if (this.files && this.files[0]) {
              var reader = new FileReader();
              reader.onload = function (e) {
                  $('#mobileImgshow').attr('src', e.target.result);
              }
              reader.readAsDataURL(this.files[0]);
          }
      });
  }

</script>
@endsection
