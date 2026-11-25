@extends('frontend.layouts.app')

@section('content')

@include('frontend.includes.header')

<div class="login-popup">
  <div class="container">
   <div class="row login-signup-container align-items-center">
    <div class="col-12 col-sm-12 col-md-6 login-signup-image">
      <img src="{{asset('images/online_shopping_re_k1sv.svg')}}" alt="" style="height: 300px;">
    </div>

    <div class="col-12 col-sm-12 col-md-6 login-signup-content">
      <div class="tab tab-nav-boxed tab-nav-center tab-nav-underline">
        <ul class="nav nav-tabs text-uppercase" role="tablist">
          <li class="nav-item">
            <a href="#sign-in" class="nav-link active">Sign In</a>
          </li>
          <li class="nav-item">
            <a href="#sign-up" class="nav-link">Sign Up</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="sign-in">
            <form method="post" action="{{ route('user.login') }}" id="user_signin">
              @csrf

              <div class="form-group">
                <label>Email address Or Phone Number *</label>
                <input type="text" class="form-control" name="email" id="email">
                <span class="error"></span>
              </div>

              <div class="form-group mb-0">
                <label>Password *</label>
                <input type="password" class="form-control" name="password" id="password">
                <span class="error"></span>
              </div>

              <div class="form-checkbox d-flex align-items-center justify-content-between">
                <div class="g-recaptcha" data-sitekey="6LfQE4QgAAAAAJRGAzVTt4i_wCN_92o_xvs_tPTt">
                </div>
                <span class="error"></span>

              </div>

              <input type="submit" class="btn btn-primary" value="Sign In">
            </form>
          </div>

          <div class="tab-pane" id="sign-up">
            <form method="post" action="{{ route('user.signup') }}" id="user_signup">
              @csrf
              <div class="form-group">
                <label>Full Name *</label>
                <input type="text" class="form-control" name="signup_username" id="signup_username">

                <span class="error"></span>
              </div>
              <div class="form-group">
                <label>Email Address *</label>
                <input type="text" class="form-control" name="signup_email" id="signup_email">
                <span class="error"></span>
              </div>

              <div class="form-group">
                <label>Contact *</label>
                <input type="text" class="form-control" autocomplete="off" name="signup_contact" id="signup_contact">
                <span class="error"></span>
              </div>

              <div class="form-group">
                <label>Password *</label>
                <input type="password" class="form-control" name="signup_password" id="signup_password">
                <span class="error"></span>
              </div>

              <div class="form-checkbox d-flex align-items-center justify-content-between">
                <div class="g-recaptcha" data-sitekey="6LfQE4QgAAAAAJRGAzVTt4i_wCN_92o_xvs_tPTt">
                </div>
                <span class="error"></span>
              </div>

              <input type="submit" class="btn btn-primary" value="Sign Up">
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>


@include('frontend.includes.footer')
@endsection

@section('script')

<script type="text/javascript">

  $(document).on('submit', '#user_signup', function(e) {
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
        $('form .error').text('');
      },
      success: function(data) {
        if (data.status == 0) {
          $.each(data.error, function(key, value) {

            var name = $("input[name='" + key + "']");
            if (key.indexOf(".") != -1) {
              var arr = key.split(".");
              name = $("input[name='" + arr[0] + "[]']:eq(" + arr[1] + ")");
            }

            name.parent().append(
              '<div class="error right-align pink-text text-mute">' +
              value[0] + '</div>');
          });
        } else {
          toastr.success(data.msg);
        }
      },
    });
  });

  $(document).on('submit', '#user_signin', function(e) {
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
        $('form .error').text('');
      },
      success: function(data) {
        if (data.status == 0) {
          $.each(data.error, function(key, value) {
            var name = $("input[name='" + key + "']");
            if (key.indexOf(".") != -1) {
              var arr = key.split(".");
              name = $("input[name='" + arr[0] + "[]']:eq(" + arr[1] + ")");
            }

            toastr.error(value[0]);
          });
        } else {
          window.location = data.redirect_location;

        }
      },
    });
  });

</script>
@endsection
