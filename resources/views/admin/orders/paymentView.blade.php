<div class="modal-header bg-soft-info p-3">
    <h5 class="modal-title" id="exampleModalLabel"></h5>

</div>
<form method="post" action="{{ route('sales_payment_store') }}" autocomplete="off" class="eq-section" novalidate id="salesPaymentSubmit">@csrf
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
            <div class="col-lg-4">
                <div>
                    <label for="companyname-field" class="form-label">Code</label>
                    <input type="text" id="companyname-field" class="form-control" value="#{{ $order->code }}" readonly />
                </div>
            </div>
            <div class="col-lg-4">
                <div>
                    <label for="owner-field" class="form-label">Bill Date</label>
                    <input type="text" id="owner-field" class="form-control" value="{{ $order->created_at }}" readonly />
                </div>
            </div>
            <div class="col-lg-4">
                <div>
                    <label for="location-field" class="form-label">Bill Amount(Rs.)</label>
                    <input type="text" id="location-field" class="form-control" value="{{ $order->net_total }}" readonly />
                </div>
            </div>
            <div class="col-lg-4">
                <div>
                    <label for="employee-field" class="form-label">Total Received Amount(Rs.)</label>
                    <input type="text" id="employee-field" class="form-control" value="{{ $total_paid_amount }}" readonly />
                </div>
            </div>
            <div class="col-lg-4">
                <div>
                    <label for="website-field" class="form-label">Total Due Amount(Rs.)</label>
                    <input type="text" id="website-field" class="form-control" name="due_amount" value="{{ $remain_paid_amount }}" readonly />
                </div>
            </div>
            <input type="hidden" name="sale_id" id="order_id" value="{{ $order->id }}">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">Payment Method</label>
                    <select name="payment_method_id" id="payment_method" class="form-control" required>
                        <option selected disabled value="">Select Payment Method</option>
                        @foreach($paymnent_methods as $method)
                            <option value="{{ $method->id }}" >{{ $method->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="item">
                    <label for="contact_email-field" class="form-label required-label item">Received Amount(Rs.)</label>
                    <input type="text" id="contact_email-field" class="form-control " name="paid_amount" placeholder="Received Amount" required />
                </div>
            </div>

        </div>
        @if($payments->count())
           <div class="row mt-3">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">S.N.</th>
                    <th scope="col">Paid Amount</th>
                    <th scope="col">Payment method</th>
                    <th scope="col">Payment Date</th>
                    <th scope="col">Received By</th>
                </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>
                            {{ $payment->paid_amount }}
                        </td>
                        <td>{{ $payment->method->name }}</td>
                        <td>{{ date('Y-M-d',strtotime($payment->created_at)) }}</td>
                        <td>{{ $payment->user->name }}</td>
                    </tr>

                @endforeach

                </tbody>
            </table>

        </div>
         @endif
    </div>
    <div class="modal-footer">
        <div class="hstack gap-2 justify-content-end">
            <button type="submit" class="btn btn-success" id="save">Pay</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
        </div>
    </div>
</form>

<script>
    $(function(){
        $("#salesPaymentSubmit").submit(function (event) {
            if (validator.checkAll($(this))) {
                const data =  new FormData(this)
                const url = $(this).attr('action')

                event.preventDefault();

                window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                    toastr.success(responseData.msg);
                    $('#OrderModal').modal('hide')
                    Datatable()
                }, function (error) {
                    toastr.error(error.msg);
                    window.ct.populateFormError("#salesPaymentSubmit", error.result)
                })
            }
            return false
        })
        $('#payment_method').select2({
            'dropdownParent':$('#OrderModal')
        })
    })
</script>
