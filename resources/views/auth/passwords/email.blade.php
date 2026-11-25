@extends('frontend.layouts.master')
@section('content')

<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Forgot
                        Password
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<!-- End breadcrumb -->
<div class="container">

    <div class="my-4 my-xl-8">
        <div class="row">
            <div class="col-12 mb-8 mb-md-0">
                <!-- Title -->
                <div class="border-bottom border-color-1 mb-6">
                    <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">Reset Password</h3>
                </div>
                <!-- End Title -->
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}" onsubmit="return manage(this)"
                    id="password_email">
                    @csrf
                    <!-- Form Group -->
                    <div class="js-form-message form-group">
                        <label class="form-label" for="signinSrEmailExample3">Email address or phone number
                            <span class="text-danger">*</span>
                        </label>

                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" placeholder="Email address or phone number" class="" value="{{ old('email') }}"
                            required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!-- End Form Group -->

                    <!-- Button -->
                    <div class=" mb-1">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary-dark-w px-5">Send Password Reset Link</button>
                        </div>

                    </div>
                    <!-- End Button -->
                </form>
            </div>

        </div>
    </div>
</div>

@endsection