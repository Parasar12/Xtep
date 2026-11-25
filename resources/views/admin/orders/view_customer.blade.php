<div class="modal-header bg-soft-info p-3">
    <h5 class="modal-title" id="exampleModalLabel">Customer Details</h5>

</div>
<div class="modal-body" >
    <input type="hidden" id="id-field" />
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    <div class="position-absolute bottom-0 end-0">

                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-6">
            <div>
                <label for="companyname-field" class="form-label">Customer Name : {{ $user->name }}</label>
            </div>
        </div>
        <div class="col-lg-6">
            <div>
                <label for="owner-field" class="form-label">Customer Email: {{ $user->email }}</label>
            </div>
        </div>
        <div class="col-lg-6">
            <div>
                <label for="location-field" class="form-label">Customer Contact Number: {{ $user->contact }}</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="item">
                <label for="location-field" class="form-label">Customer Secondary Contact Number: {{ $user->secondary_contact }}</label>
            </div>
        </div>
        <hr>
    </div>
   @if($addresses->count())
        <div class="modal-header bg-soft-info p-3">
            <h5 class="modal-title" id="exampleModalLabel">Customer Addresses</h5>

        </div>
        <div class="row mt-3">
            @foreach($addresses as $address)
                <div class="col-lg-12">
                    <div>
                        <label for="companyname-field" class="form-label">{{ $loop->iteration }} . {{ getProviceName($address->province_id) .'-'. getDistrictName($address->district_id) .'-' . $address->street}}</label>
                    </div>
                </div>
            @endforeach

        </div>
   @endif

    <div class="modal-header bg-soft-info p-3 mt-3">
        <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>

    </div>
    <div class="row mt-3">

        <div class="col-lg-12">
            <div>
                <h5 class="mt-3"> Order Status : <span class="text-danger">{{ $order->deliveryStatus->name }}</span> </h5>
                <h5 class="mt-3"> Shipping Address : <span class="text-danger">{{ getProviceName($order->addresses->province_id) .'-'. getDistrictName($order->addresses->district_id) .'-' . $order->addresses->street}}</span> </h5>
                <h5 class="mt-3"> Profit/Loss : <span class="text-danger">Rs. {{ $order->profit_loss->net_amount ?? 0 }}</span> </h5>
                <h5 class="mt-3"> Ordered Mode : <span class="text-danger">{{ $order->order_mode->name }}</span> </h5>
                <h5 class="mt-3"> Sales By : <span class="text-danger">{{ $order->sales_by->name }}</span> </h5>
                <h5 class="mt-3"> Net Total : <span class="text-danger">Rs. {{ $order->net_total  ?? 0}}</span> </h5>
                <h5 class="mt-3"> Paid By Customer : <span class="text-danger">Rs. {{ $total_paid_amount  ?? 0}}</span> </h5>
                <h5 class="mt-3"> Due Amount : <span class="text-danger">Rs. {{ $remaining_amount ?? 0 }}</span> </h5>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <div class="hstack gap-2 justify-content-end">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
    </div>
</div>

