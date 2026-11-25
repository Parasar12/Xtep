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
            <form method="post" action="{{route('general.store')}}" enctype="multipart/form-data" id="update_setting">
              @csrf

              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Title *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter title" id="title" name="title" value="{{getSiteTitle()}}" required>

                          <span class="text-danger title_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Welcome Text *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter welcome text" id="welcome_text" name="welcome_text" value="{{getWelcomeText()}}" required>

                          <span class="text-danger welcome_text_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Phone *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="bumber" placeholder="Please enter phone number" id="phone" name="phone" value="{{getPhoneNumber()}}" required>
                          <span class="text-danger title_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                          <label for="name" class="col-md-2 col-form-label"> Address *</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Please enter Address" id="address" name="address" value="{{getAddress()}}" required>

                            <span class="text-danger address_error"></span>
                          </div>
                      </div>
                      <div class="mb-3 row">
                          <label for="name" class="col-md-2 col-form-label"> Email *</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Please enter Email" id="email" name="email" value="{{getEmail()}}" required>

                            <span class="text-danger email_error"></span>
                          </div>
                      </div>
                      <div class="mb-3 row">
                          <label for="name" class="col-md-2 col-form-label"> Opening Hours *</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Please enter Opening Hours" id="opening_hours" name="opening_hours" value="{{ getOpeningHours()}}" required>
                            <span class="text-danger opening_hours_error"></span>
                          </div>
                      </div>

                      <div class="mb-3 row">
                          <label for="name" class="col-md-2 col-form-label"> Facebook Page Link *</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Please enter Facebook Page Link" id="fb_page" name="fb_page_link" value="{{ getFacebookPage()}}" required>
                            <span class="text-danger fb_page_link_error"></span>
                          </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Favicon </label>
                        <div class="col-md-8">
                          <input class="form-control" type="file" id="favload" name="favicon" accept=".ico,.png,image/x-icon,image/vnd.microsoft.icon,image/png" onclick="showSelectedFav(event)">
                          <span class="text-danger favicon_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <div class="col-md-2"></div>
                        <div class="col-md-6">
                          <img src="@if(getSiteFavicon()) {{asset('uploads/favicon/'.getSiteFavicon())}} @endif" id="favshow" />
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Logo </label>
                        <div class="col-md-10">
                          <input class="form-control" type="file" id="imgload" name="logo" onclick="showSelectedImage(event)">
                          <span class="text-danger logo_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <div class="col-md-2"></div>
                        <div class="col-md-6">
                          <img style="max-width: 400px;" src="@if(getSiteLogo()) {{asset('uploads/logo/'.getSiteLogo())}} @endif" id="imgshow" />
                        </div>
                      </div>
                        <div class="mb-3 row">
                            <label for="slug" class="col-md-2 col-form-label">Logo Size </label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" name="logo_size" value="{{ getLogoSize() }}">
                                <span class="text-danger logo_size"></span>
                            </div>
                        </div>
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Footer Text *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter footer text" id="footer_text" name="footer_text" maxlength="200" value="{{getFooterText()}}" required>
                          <span class="text-danger footer_text_error"></span>
                        </div>
                      </div>

                        <div class="mb-3 row">
                            <label for="name" class="col-md-2 col-form-label"> Bill Footer Text *</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" placeholder="Please enter footer text" id="bill_footer_text" name="bill_footer_text" maxlength="200" value="{{getBillFooterText()}}" required>
                                <span class="text-danger footer_text_error"></span>
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
                            <a class="btn btn-secondary" href="{{url('general')}}">Back</a>
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

  $(document).ready(function() {

    $(document).on('submit', '#update_setting', function(e) {
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
          $(this).find('form span .text-danger').html('');

        },
        success: function(data) {
          $(this).find('form span .text-danger').html('');
          if (data.status == 0) {
            $.each(data.error, function(key, value) {
              $('form span.' + key + '_error').text(value[0]);
            });
          } else {
            toastr.success(data.msg);
            location.reload()
          }
        },
      });
    });

  });

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

  function showSelectedFav(e) {
    $("#favload").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#favshow').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
  }
</script>
@endsection
