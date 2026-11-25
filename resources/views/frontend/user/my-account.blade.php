@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} -Profile{{ auth()->user()->name }} </title>
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
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">My Account
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<!-- End breadcrumb -->
<div class="my-account-area pt-75 pb-75 mb-10" style="min-height: 50vh;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- My Account Page Start -->
                <div class="myaccount-page-wrapper">
                    <!-- My Account Tab Menu Start -->
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="myaccount-tab-menu nav" role="tablist">
                                <a href="#dashboad" class="active" data-toggle="tab">Dashboard</a>
                                <a href="#address-edit" data-toggle="tab">address</a>
                                <a href="#account-info" data-toggle="tab"> Account Details</a>
                                <a href="#my-order" data-toggle="tab"> My Orders</a>
                                <a href="{{ route('logout') }}">Logout</a>
                            </div>
                        </div>
                        <!-- My Account Tab Menu End -->
                        <!-- My Account Tab Content Start -->
                        <div class="col-lg-8 col-md-8">
                            <div class="tab-content" id="myaccountContent">
                                <!-- Single Tab Content Start -->
                                <div class="tab-pane fade show active" id="dashboad" role="tabpanel">
                                    <div class="myaccount-content">
                                        <div class="welcome">
                                            <p>Hello, <strong>{{ auth()->user()->name }}</strong><a
                                                    href="{{ route('logout') }}" class="logout"> ( Logout</a>)</p>
                                        </div>

                                        <p class="mb-0">From your account dashboard you can view your <a href="#">recent
                                                orders</a> , manage your <a href="#">shipping and billing
                                                addresses</a> , and <a href="#">edit your password and account
                                                details</a></p>
                                    </div>
                                </div>
                                <!-- Single Tab Content End -->
                                <!-- Single Tab Content Start -->
                                <div class="tab-pane fade" id="address-edit" role="tabpanel">
                                    <div class="myaccount-content myaccount-address">
                                        <p> The following addresses will be used on the checkout page by
                                            default.</p>
                                        <div class="row">
                                            @foreach($addresses as $address)
                                            <div class="col-lg-6 col-md-6 col-12 col-sm-6">
                                                <div class="myaccount-address-wrap">
                                                    <h3>address</h3>
                                                    <div class="myaccount-address-content">
                                                        <h4>{{ $address->user->name }}</h4>
                                                        <p>{{ getProviceName($address->province_id) }}
                                                            <br>{{ getDistrictName($address->district_id) }}
                                                        </p>
                                                        <p>{{ $address->street }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <hr>
                                        <div class="row" style="margin-top: 100px;">
                                            <div class="col-12">
                                                <div class="account-details-form">
                                                    <form action="{{ route('address.store') }}" method="post"
                                                        id="update_profile_form">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="select-style billing-select mb-35">
                                                                    <label>State *</label>
                                                                    <select class="form-control" name="province_id">
                                                                        <option value="" selected="selected" disabled>
                                                                            Select State</option>
                                                                        <@foreach($provinces as $state) <option
                                                                            value="{{ $state->zone_state_id }}">{{
                                                                            $state->zone_state_name }}</option>
                                                                            @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="select-style billing-select mb-35">
                                                                    <label>District *</label>
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
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="billing-info input-style mb-5">
                                                                    <label>Street Address *</label>
                                                                    <input class="form-control billing-address"
                                                                        name="street"
                                                                        placeholder="House number and street name"
                                                                        type="text">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="">
                                                            <button
                                                                class="btn px-5 btn-primary-dark transition-3d-hover">Save
                                                                Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Single Tab Content End -->
                                <!-- Single Tab Content Start -->
                                <div class="tab-pane fade" id="account-info" role="tabpanel">
                                    <div class="myaccount-content">
                                        <div class="account-details-form">
                                            <form action="{{ route('profile.update') }}" method="post"
                                                id="update_profile_form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="account-info input-style mb-30">
                                                            <label>Name *</label>
                                                            <input class="form-control" type="text" name="name"
                                                                id="name" value="{{ auth()->user()->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="account-info input-style mb-30">
                                                            <label>Contact *</label>
                                                            <input class="form-control" type="tel" name="contact"
                                                                id="contact" value="{{ auth()->user()->contact }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <span>This will be how your name will be displayed in the account
                                                    section and in reviews</span>
                                                <fieldset>
                                                    <legend>Password change</legend>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="account-info input-style mb-30">
                                                                <label>Current password (leave blank to leave
                                                                    unchanged)</label>
                                                                <input class="form-control" type="password"
                                                                    name="password" id="password">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="account-info input-style mb-30">
                                                                <label>New password (leave blank to leave
                                                                    unchanged)</label>
                                                                <input class="form-control" type="password"
                                                                    name="new_password" id="new_password">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="account-info input-style">
                                                                <label>Confirm new password</label>
                                                                <input class="form-control" type="password"
                                                                    name="new_password_confirmation"
                                                                    id="new_password_confirmation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <div class="account-info-btn">
                                                    <button>Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> <!-- Single Tab Content End -->
                                <div class="tab-pane fade" id="my-order" role="tabpanel">
                                    <div class="myaccount-content">
                                        <div class="account-details-form">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="wishlist-table-content" id="orders_list">
                                                        <div class="table-content table-responsive">
                                                            <div class="container">
                                                                <div class="row">
                                                                    @if($orders->count())
                                                                        @php
                                                                            $total_products = 0;
                                                                        @endphp
                                                                        @foreach ($orders as $order)
                                                            
                                                                            @php
                                                                            $total = 0;
                        $total_order = \App\Models\Order::where([
                            ['user_id',\auth()->user()->id],
                            ['created_at',$order->created_at]
                        ])->get();
                        foreach ($total_order as $item){
                            $total += $item->orderDetails->count();
                        }
                                                                                // dd($total_order);
                                                                                // foreach ($total_order as $val) {
                                                                                // }
                                                                                @endphp
                                                            
                                                                            <div class="col-12 mb-3">
                                                                                <div class="card p-3 bg-light align-middle">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3 text-center">
                                                                                            <h4 class="mb-0 fw-bold text-dark">{{ date('Y-m-d', strtotime($order->created_at)) }}</h4>
                                                                                        </div>
                                                                                        
                                                                                        <div class="col-md-3">
                                                                                            <h4 class="mb-0">Total Order:</h4>
                                                                                        </div>
                                                                                        
                                                                                        <div class="col-md-3">
                                                                                            <span class="fw-bold ">{{ $total }}</span>
                                                                                        </div>
                                                                                        
                                                                                        <div class="col-md-3 text-end">
                                                                                            <a href="javascript:void(0);" onclick="viewOrders('{{ $order->created_at }}')" class="btn btn-outline-primary btn-sm">
                                                                                                View Orders
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                            
                                                                        @endforeach
                                                                    @else
                                                                        <div class="col-12">
                                                                            <div class="alert alert-warning" role="alert">
                                                                                No Order's Found
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- Single Tab Content End -->
                            </div>
                        </div> <!-- My Account Tab Content End -->
                    </div>
                </div> <!-- My Account Page End -->
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
function viewOrders(order_date) {
    const orderDiv = $('#orders_list');
    $.ajax({
        url: "{{ route('view_orders') }}",
        method: "GET",
        data: {
            order_date: order_date // Pass the correct order date here
        },
        success: function(responseData) {
            orderDiv.html('');
            orderDiv.html(responseData.result);
        },
        error: function(responseData) {
            toastr.error(responseData.msg);
        }
    });
}


        function backToDateWiseOrders(){
            const orderDiv = $('#orders_list')
            $.ajax({
                url:"{{ route('back_to_main_order_page') }}",
                method : "GET",

                success:function (responseData){
                    orderDiv.html('')
                    orderDiv.html(responseData.result);
                },
                error: function (responseData){
                    toastr.error(responseData.msg)
                }
            })
        }

        // function changeOrderStatus(id,order_date) {

        //     $('#addBannerModal #addBannerModalContent').html('');
        //     $('#addBannerModal #addBannerModalContent').html($('#reason_for_cancellation').html());
        //     $('#order_id').val(id)
        //     $('#order_date').val(order_date)
        //     $('#addBannerModal').modal('show');

        //     $("#orderCancellation").submit(function(event) {
        //         const orderDiv = $('#orders_list')
        //         if (validator.checkAll($(this))) {
        //             const data = new FormData(this)
        //             const url = $(this).attr('action')

        //             event.preventDefault();
        //             $.ajaxSetup({
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 },
        //             });
        //             window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData) {
        //                 toastr.success(responseData.msg);
        //                 $('#addBannerModal').modal('hide');
        //                 orderDiv.html(responseData.result);
        //             }, function(error) {
        //                 toastr.error(error.msg);
        //                 window.ct.populateFormError("#quickOrder", error.result);
        //             });
        //         }
        //         return false;
        //     });
        // }

        function closeModal(){

            $('#addBannerModal').modal('hide');
        }
        $(document).ready(function () {

            $('#update_profile_form').submit(function (e) {
                e.preventDefault()

                //remove error class if exits
                $("#update_profile_form").removeClass("has-error")
                $(".help-block").remove()

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (data) {
                        console.log(data)
                        if (data.status == 1) {
                            //show success message
                            $("#update_profile_form").trigger("reset")
                            location.reload()
                        } else {
                            console.log(data);
                            $.each(data.error, function (key, value) {
                                $("#" + key).parent().addClass("has-error")
                                $("#" + key).parent().append(
                                    '<span class="help-block text-danger">' +
                                    value + '</span>')
                            })
                        }
                    }
                })
            })
        })
</script>

<script type="text/template" id="reason_for_cancellation">
    <div class="modal-header">
            <h5 class="modal-title" id="brandModalLabel">Cancellation Reason</h5>
    </div>

    <div class="modal-body">
            <div class="login-register-wrap login-register-gray-bg" style="width: 100%">
                <div class="login-register-title">
                    <h1>Reason For Cancellation</h1>
                </div>
                <div class="login-register-form">
                    <form action="{{ route('order.cancel') }}" method="post" autocomplete="off" id="orderCancellation" novalidate class="eq-section">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 item">
                                <div class="login-register-input-style input-style input-style-white">
                                    <label class="required-label">Reason For Cancellation</label>
                                    <textarea  name="reason" required cols="12"></textarea>
                                </div>
                            </div>

                        </div>
                        <input type="hidden" value="" name="id" id="order_id">
                        <input type="hidden" value="" name="order_date" id="order_date">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="login-register-btn">
                                    <button type="submit">Submit</button>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="login-register-btn">
                                    <button type="button" style="background-color: red" style="float: right;" onclick="closeModal()">Close</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
    </div>

</script>
@endsection