@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} - Login/Signup</title>
@endsection
@section('content')
<!-- breadcrumb -->
<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Login/Register
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<!-- End breadcrumb -->

<div class="container">
    <div class="mb-4">
        <h1 class="text-center">My Account</h1>
    </div>
    <div class="my-4 my-xl-8">
        <div class="row">
            <div class="col-md-5 ml-xl-auto mr-md-auto mr-xl-0 mb-8 mb-md-0">
                <!-- Title -->
                <div class="border-bottom border-color-1 mb-6">
                    <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">Login</h3>
                </div>
                <p class="text-gray-90 mb-4">Welcome back! Sign in to your account.</p>
                <!-- End Title -->`

                <form action="{{ route('user.login') }}" method="post" autocomplete="off" id="LoginForm">
                    @csrf
                    <!-- Form Group -->
                    <div class="js-form-message form-group">
                        <label class="form-label" for="signinSrEmailExample3">Email address or phone number
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="email" id="email"
                            placeholder="Email address or phone number" aria-label="Email address or phone number"
                            required ">

                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    {{-- <div class=" js-form-message form-group">
                        <label class="form-label" for="signinSrPasswordExample2">Password <span
                                class="text-danger">*</span></label>
                        <input type="password" name="password" required id="password" class="form-control"
                            placeholder="Password" aria-label="Password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i> <!-- Icon for show/hide -->
                                </button>
                            </div>
                    </div> --}}
                    <div class="js-form-message form-group">
                        <label class="form-label" for="signinSrPasswordExample2">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" required id="password" class="form-control" placeholder="Password" aria-label="Password" required>
                            <div class="input-group-append">
                                <button class="btn" type="button" style="    border: 1px solid #dddddd;" id="togglePassword">
                                    <i class="fas fa-eye"></i> <!-- Icon for show/hide -->
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- End Form Group -->

                    <!-- Button -->
                    <div class="mb-1">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary-dark-w px-5">Login</button>
                        </div>
                        <div class="mb-2">
                            <a class="text-blue" href="{{ route('password.request') }}">Lost your password?</a>
                        </div>
                    </div>
                    <!-- End Button -->
                </form>
            </div>
            <div class="col-md-1 d-none d-md-block">
                <div class="flex-content-center h-100">
                    <div class="width-1 bg-1 h-100"></div>
                    <div
                        class="width-50 height-50 border border-color-1 rounded-circle flex-content-center font-italic bg-white position-absolute">
                        or</div>
                </div>
            </div>
            <div class="col-md-5 ml-md-auto ml-xl-0 mr-xl-auto">
                <!-- Title -->
                <div class="border-bottom border-color-1 mb-6">
                    <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">Register</h3>
                </div>
                <p class="text-gray-90 mb-4">Create new account today to reap the benefits of a personalized shopping
                    experience.</p>
                <!-- End Title -->
                <!-- Form Group -->
                <form action="{{ route('user.signup') }}" method="post" id="RegisterForm">
                    @csrf
                    <div class="js-form-message form-group mb-5">
                        <label class="form-label" for="RegisterSrEmailExample3">Name
                            <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="Name" type="text" name="signup_name" required
                            id="signup_name">
                    </div>
                    <div class="js-form-message form-group mb-5">
                        <label class="form-label" for="RegisterSrEmailExample3">Contact
                            <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="Contact" type="tel" name="signup_contact" required
                            id="signup_contact">
                    </div>
                    <div class="js-form-message form-group mb-5">
                        <label class="form-label" for="RegisterSrEmailExample3">Email Address
                            <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="Email Address" type="email" name="signup_email"
                            required id="signup_email">
                    </div>
                    <div class="js-form-message form-group mb-5">
                        <label class="form-label" for="RegisterSrEmailExample3">Password
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input class="form-control" placeholder="Password" type="password" name="signup_password" required id="signup_password">
                            <div class="input-group-append">
                                <button class="btn" style="border: 1px solid #dddddd;" type="button" id="toggleSignupPassword">
                                    <i class="fas fa-eye"></i> <!-- Icon for show/hide -->
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- End Form Group -->
                    <p class="text-gray-90 mb-4">Your personal data will be used to support your experience throughout
                        this website, to manage your account, and for other purposes described in our <a href="#"
                            class="text-blue">privacy policy.</a></p>
                    <!-- Button -->
                    <div class="mb-6">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary-dark-w px-5">Register</button>
                        </div>
                    </div>
                    <!-- End Button -->
                </form>

            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script>
    $(document).ready(function () {
            $('#RegisterForm').submit(function (e) {
                e.preventDefault()

                $(".help-block").remove();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data.status === 0) {
                            $.each(data.error, function (key, value) {
                                $("#" + key).parent().addClass("has-error")
                                $("#" + key).parent().append(
                                    '<span class="help-block text-danger">' +
                                    value + '</span>')
                            });

                        } else {
                            $("#RegisterForm").trigger("reset");
                            toastr.success(data.msg)
                            location.href = "{{ route('home') }}"
                        }
                    }
                })
            })
            $('#LoginForm').submit(function (e) {
                e.preventDefault()

                $(".help-block").remove();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data.status === 0) {
                            $.each(data.error, function (key, value) {
                                $("#" + key).parent().addClass("has-error")
                                $("#" + key).parent().append(
                                    '<span class="help-block text-danger">' +
                                    value + '</span>')
                            });
                        } else {
                            $("#LoginForm").trigger("reset");
                            location.replace(data.redirect_location)
                        }
                    }
                })
            })
        })
document.getElementById('togglePassword').addEventListener('click', function (e) {
    // Get the password input field
    const passwordInput = document.getElementById('password');

    // Toggle the type attribute
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle the icon (optional)
    this.querySelector('i').classList.toggle('fa-eye');
    this.querySelector('i').classList.toggle('fa-eye-slash');
});
document.getElementById('toggleSignupPassword').addEventListener('click', function (e) {
    // Get the password input field
    const passwordInput = document.getElementById('signup_password');

    // Toggle the type attribute
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle the icon (optional)
    this.querySelector('i').classList.toggle('fa-eye');
    this.querySelector('i').classList.toggle('fa-eye-slash');
});

</script>

@endsection
