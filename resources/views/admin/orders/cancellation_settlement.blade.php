<div id="printThis">
    <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel">Order # {{ $order->code }}</h5>
    </div>

    <div class="modal-body">
        <form method="post" action="{{ route('cancellation_payment_settlement.save') }}"
              id="change_status_post" enctype="multipart/form-data"
              class="eq-section" novalidate> @csrf
            <div class="py-2 mt-3">
                <input type="hidden" id="orderId" name="order_id" value="{{ $order->id }}">

                    <div class="col-lg-12 col-md-6 col-sm-12">
                        <h5 class="text-center">Product Details :</h5>

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">S.N.</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Price </th>
                                <th scope="col">Quantity </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->orderDetails as $detail)

                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ ($detail->product->name) }}</td>
                                    <td>{{ ($detail->price) }}</td>
                                    <td>{{ ($detail->quantity) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tbody>
                                 <tr>
                                     <td colspan="3">

                                     </td>
                                     <td> Gross Amount: Rs.{{ $order->grand_total }}</td>

                                 </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td>Total Discount Amount: Rs.{{ $order->discount_amount }}</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td>Total Tax amount: Rs.{{ $order->tax_amount }}</td>
                            </tr>
                             <tr>
                                <td colspan="3"></td>
                                     <td>Total Coupon Discount: Rs.{{ $order->total_coupon_discount }}</td>
                            </tr>
                                 <tr>
                                <td colspan="3"></td>
                                     <td>Total Delivery Charge: Rs.{{ $order->delivery_vendor_amount }}</td>
                            </tr>
                                 </tr>
                                 <tr>
                                <td colspan="3"></td>
                                     <td>Net Amount: Rs.{{ $order->net_total }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
          <div class="row">
              <div class="col-lg-4">
                  <label for="paidAmount" class="required-label">Total Paid by customer</label>
                  <input id="paidAmount" readonly type="number" value="{{ $total_paid_amount }}" name="total_paid_amount" class="form-control" required>
              </div>
              <div class="col-lg-4">
                  <label for="paidAmount" class="required-label">Total paid to customer</label>
                  <input id="paidAmount" readonly type="number" value="{{ $total_return_amount }}" name="total_paid_to_customer_amount" class="form-control" required>
              </div>

              <div class="col-lg-4">
                  <label for="paidAmount" class="required-label">Remaining</label>
                  <input id="paidAmount" readonly type="number" value="{{ $total_remaing_amount }}" name="remaining_amount" class="form-control" required>
              </div>
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
              <div class="col-lg-4">
                  <label for="paidAmount" class="required-label">Return Amount</label>
                  <input id="paidAmount"  type="number" value="0" name="return_amount" class="form-control" required>
              </div>
          </div>
            @if($expenses->count())
                <div class="row mt-3">
                    <table class="table">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">S.N.</th>
                            <th scope="col">Paid Amount</th>
                            <th scope="col">Payment method</th>
                            <th scope="col">Payment Date</th>
                            <th scope="col">Paid To</th>
                            <th scope="col">Paid By</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>
                                    {{ $expense->total_return_amount }}
                                </td>
                                <td>{{ $expense->payment->name }}</td>
                                <td>{{ date('Y-M-d',strtotime($expense->created_at)) }}</td>
                                <td>{{ $expense->order->user->name }}</td>
                                <td>{{ $expense->user->name }}</td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                </div>
            @endif
            <div class="col-lg-6 mt-4">
                <input id="settlePayment" type="checkbox" name="is_settled" class="mt-3" style="cursor: pointer">
                <label for="settlePayment" class="ml-4"><h5>Do you want to settle ?</h5></label>
            </div>
            <div class="d-print-none mt-5">
                <div class="float-end">
                   <button class="btn btn-success waves-effect waves-light me-1" type="submit" id="save">Submit</button>
                    <a class="btn btn-danger" data-bs-dismiss="modal" style="float: right;">Close</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

    $(document).on('change','#orderStatus',function (){
        const order_status = $(this).val()
        const order_id = $('#orderId').val()
        $.ajax({
            url :"{{ route('check_order_status') }}",
            method:"GET",
            data:{
                order_status,
                order_id
            },
            success:function (responseData){
                if(responseData.response){

                }
                else
                {
                    toastr.error(responseData.msg)
                    $('#orderStatus').val("{{ $order->delivery_status }}").trigger('change')
                }
            }
        })
    })

    $(function (){
        $('#orderStatus').select2()
        $('#payment_method').select2({
            dropdownParent : $('#OrderModal')
        })

        $("#change_status_post").on('submit',function (event) {
            if (validator.checkAll($(this))) {
                const data =  new FormData(this)
                const url = $(this).attr('action')
                event.preventDefault();
                const status_name = $('#orderStatus option:selected').text()

                window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                    toastr.success(responseData.msg);
                    $("#OrderModal").modal('hide');
                    countStatusRecords(status_name)
                    datatable(null,status_name)
                }, function (error) {
                    toastr.error(error.msg);
                    window.ct.populateFormError("#change_status_post", error.result)
                })
            }
            return false
        })

    })
</script>
