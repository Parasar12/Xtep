@extends('frontend.layouts.app')
@section('content')
@include('frontend.includes.header')


<main class="main">
  @php
  $billingAddress = \App\Models\Address::where('id',$order->billing_address_id)->first();
  $shippingAddress = \App\Models\Address::where('id',$order->shipping_address_id)->first();
  @endphp

  <div class="page-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12" style="margin-top: 30px;">
          <div class="card">
            <div class="card-body">
              <div class="invoice-title">
                <h4 class="float-end font-size-16">Your Order # {{$order->code}} details.</h4>
                <div class="mb-4">
                  <h4 class="font-size-16">Order & Account Information</h4>
                </div>
              </div>
              <hr>

              <div class="row">
                <div class="col-sm-6 table-responsive">
                  <strong>Order Information:</strong>
                  <table class="table">
                    <tbody>
                      <tr>
                        <td>Order Date </td>
                        <td> {{\Carbon\Carbon::parse($order->date)->isoFormat('MMM Do YYYY') }} </td>
                      </tr>

                      <tr>
                        <td>Shipping Method</td>
                        @if($order->shippingMethod)
                        <td>{{ $order->shippingMethod->title }}</td>
                        @else
                        <td> Free Shipping</td>
                        @endif
                      </tr>

                      <tr>
                        <td>Payment Method</td>
                        <td> {{ $order->name }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="col-sm-6 table-responsive">
                  <strong>Account Information:</strong>
                  <table class="table">
                    <tr>
                      <td>Customer Name</td>
                      <td>{{$order->user->name}}</td>
                    </tr>
                    <tr>
                      <td>Customer Email</td>
                      <td>{{$order->user->email}}</td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6 table-responsive">
                  <strong>Billed To:</strong>
                  <table class="table">
                    <tr>
                      <td>{{$order->user->name}}</td>
                    </tr>
                    <tr>
                      <td>{{getProviceName($billingAddress->province_id)}}</td>
                    </tr>
                    <tr>
                      <td>{{getDistrictName($billingAddress->district_id)}}</td>
                    </tr>
                    <tr>
                      <td>{{$billingAddress->street}}</td>
                    </tr>
                  </table>
                </div>

                <div class="col-sm-6 table-responsive">
                  <strong>Shipped To:</strong>
                  <table class="table">
                    <tr>
                      <td>{{$order->user->name}}</td>
                    </tr>
                    <tr>
                      <td>{{getProviceName($shippingAddress->province_id)}}</td>
                    </tr>
                    <tr>
                      <td>{{getDistrictName($shippingAddress->district_id)}}</td>
                    </tr>
                    <tr>
                      <td>{{$shippingAddress->street}}</td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="py-2 mt-3">
                <h3 class="font-size-15 fw-bold">Order summary</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-nowrap">
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Unit Price</th>
                      <th>Quantity</th>
                      <th>Line Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($order->orderDetails as $details)
                    <tr>
                      @php $attributeData = ''; @endphp

                      @if(!empty($details->attribute))
                      @php $attributeArray =json_decode($details->attribute, TRUE); @endphp
                      @foreach($attributeArray as $i => $data)
                      @php $attribute = \App\Models\Attribute::find($data['attribute_id'])->name; @endphp

                      @php $attributeData .= '[' .$attribute. ':' .$data['value'].']'; @endphp

                      @endforeach
                      @endif

                      <td>{{ $details->product->name }} {{ $attributeData }}</td>
                      <td>Rs.&nbsp;{{ $details->price }}</td>
                      <td>{{ $details->quantity }}</td>
                      <td>Rs.&nbsp;{{ $details->price * $details->quantity }}</td>
                    </tr>
                    @endforeach
                    <tr>
                      <td colspan="3" class="text-end">
                        <strong>Sub Total</strong>
                      </td>
                      <td class="text-end">Rs.&nbsp;{{ $order->grand_total }}</td>
                    </tr>
                    <tr>
                      <td colspan="3" class="text-end">
                        <strong>Coupon Discount</strong>
                      </td>
                      <td class="text-end">Rs.&nbsp;{{ $order->product_coupon_discount }}</td>
                    </tr>
                    <tr>
                      <td colspan="3" class="border-0 text-end">
                        <strong>Shipping</strong>
                      </td>
                      @if($order->shippingMethod)
                      <td class="border-0 text-end">Rs.&nbsp;{{ $order->shippingMethod->cost }}</td>
                      @else
                      <td class="border-0 text-end">0</td>
                      @endif
                    </tr>
                    <tr>
                      <td colspan="3" class="border-0 text-end">
                        <strong>Total</strong>
                      </td>
                      <td class="border-0 text-end">
                        <h4 class="m-0">Rs.&nbsp;{{ $order->grand_total }}</h4>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@section('script')
<script>

</script>

@endsection

@include('frontend.includes.footer')
@endsection