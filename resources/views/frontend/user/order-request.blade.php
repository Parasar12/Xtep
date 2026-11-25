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
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Order Request
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
        <h1 class="text-center">Order Request</h1>
    </div>
    <div class="my-4 my-xl-8">
        <div class="row">
       
            <div class="col-md-12 ml-md-auto ml-xl-0 mr-xl-auto">
                <!-- Title -->
                <div class="border-bottom border-color-1 mb-6">
                    <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">User Information</h3>
                </div>
                <p class="text-gray-90 mb-4">Create an Order Request.</p>
                <!-- End Title -->
                <!-- Form Group -->
                <form action="{{ route('user.orderRequest') }}" method="post" id="RegisterForm">
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
                        <label class="form-label" >State
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="province_id">
                            <option value="" selected="selected" disabled>
                                Select State</option>
                            <@foreach($provinces as $state) <option
                                value="{{ $state->zone_state_id }}">{{
                                $state->zone_state_name }}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="js-form-message form-group mb-5">
                        <label class="form-label" >District
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="district_id">
                            <<option value="" selected="selected" disabled>
                                Select District
                                </option>
                                @foreach($disctricts as $district)
                                <option
                                    value="{{ $district->district_id }}">{{
                                    $district->district_name }}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="js-form-message form-group mb-5">
                        <label class="form-label" >Billing Address
                            <span class="text-danger">*</span>
                        </label>
                        <input class="form-control billing-address"
                        name="street"
                        placeholder="House number and street name"
                        type="text">
                    </div>
                
            

                    <!-- End Form Group -->
                    <p class="text-gray-90 mb-4">Your personal data will be used to support your experience throughout
                        this website, to manage your account, and for other purposes described in our <a href="#"
                            class="text-blue">privacy policy.</a></p>
                    <p class="text-gray-90 mb-4">Already have an account? <a href="{{route('user.login')}}"
                            class="text-blue">Click here.</a></p>
                    <!-- Button -->
                    <div class="mb-6">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary-dark-w px-5">Proceed to Checkout</button>
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
    $(document).ready(function() {
        $('#RegisterForm').submit(function(e) {
            e.preventDefault()

            $(".help-block").remove();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(data) {
                    if (data.status === 0) {
                        $.each(data.error, function(key, value) {
                            $("#" + key).parent().addClass("has-error")
                            $("#" + key).parent().append(
                                '<span class="help-block text-danger">' +
                                value + '</span>')
                        });

                    } else {
                        $("#RegisterForm").trigger("reset");
                        toastr.success(data.msg)
                        location.href = "{{ route('cart.shipping') }}"
                    }
                }
            })
        })
        $('#LoginForm').submit(function(e) {
            e.preventDefault()

            $(".help-block").remove();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(data) {
                    if (data.status === 0) {
                        $.each(data.error, function(key, value) {
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

</script>

@endsection