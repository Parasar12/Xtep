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
            <form method="post" action="{{route('accessories.update', $accessory->id)}}" enctype="multipart/form-data" id="update_accessory">
              @csrf
              
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label"> Name *</label>
                        <div class="col-md-6">
                          <input class="form-control" type="text" placeholder="Please enter Device Accessory name" id="name" name="name" maxlength="200" value="{{$accessory->name}}" required>

                          <span class="error_txt name_error"></span>
                        </div>
                        <h6>Position *</h6>
                        <div class="col-md-2">
                            <input class="form-control" type="number" placeholder="Please enter Device Accessory Position" id="name" name="position" maxlength="200" value="{{$accessory->position}}" required>
                            <span class="error_txt position_error"></span>
                          </div>
                      </div>
                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Featured Image </label>
                        <div class="col-md-10">
                          <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)">
                          <span class="error_txt image_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                          <img style="width: 200px; height: 200px;" src="@if($accessory->featured_image) {{asset('uploads/device_management/'.$accessory->featured_image)}} @endif" id="imgshow" />
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
                            <a class="btn btn-secondary" href="{{route('devicemanagement.accessories')}}">Back</a>
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
  $(document).ready(function() {

    $(document).on('submit', '#update_accessory', function(e) {
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
        cache: false,
        beforeSend: function(data) {
          $(this).find('form span .error_txt').html('');
        },
        success: function(data) {
          $(this).find('form span .error_txt').html('');
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

  });
</script>

@endsection