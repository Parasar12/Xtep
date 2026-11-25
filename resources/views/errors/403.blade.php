<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>403 Forbidden Access</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
  <meta content="Themesbrand" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="assets/images/favicon.ico">

  <link href="{{asset('css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="{{asset('css/icons.min.css')}}" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="{{asset('css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body>

  <div class="account-pages my-5 pt-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="text-center mb-5">
            <h1 class="display-2 fw-medium">4<i class="bx bx-buoy bx-spin text-primary display-3"></i>3</h1>
            <h4 class="text-uppercase">Forbidden Access</h4>
            <div class="mt-5 text-center">
              <a class="btn btn-primary waves-effect waves-light" href="{{route('admin.login')}}">Back to Dashboard</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>