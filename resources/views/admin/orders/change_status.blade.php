<div id="printThis">
    <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel">Order # {{ $order->code }}</h5>
    </div>

    <div class="modal-body">
<form method="post" action="{{ route('change_order_status.save') }}"
      id="change_status_post" enctype="multipart/form-data"
      class="eq-section" novalidate> @csrf
        <div class="py-2 mt-3">
            <input type="hidden" id="orderId" name="order_id" value="{{ $order->id }}">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="form-group item">
                    <label for="orderStatus" class="required-label">Order Status</label>
                    <select name="order_status_id" id="orderStatus" required
                            class="form-control" >
                        <option value="" selected disabled>Select</option>
                        @foreach($status as $val)
                            <option value="{{ $val->id }}" {{ $val->id == $order->delivery_status ? 'selected' : '' }}>{{ $val->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(($remarks))
                <div class="col-lg-12 col-md-6 col-sm-12">
                    <h5 class="text-center">Remarks :</h5>

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">S.N.</th>
                            <th scope="col">Delivery Status</th>
                            <th scope="col">Status Changed Date</th>
                            <th scope="col">Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($remarks as $remark)

                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ getOrderStatus($remark->status_id) }}</td>
                                <td>{{ ($remark->date ?? '') }}</td>
                                <td>{{ ($remark->remarks) }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            @endif

            <div class="row ml-1">
                <div class="col-lg-6 col-md-6 col-sm-12" id="cod_from_logistic">

                </div>
                <div class="col-lg-6 col-md-6 col-sm-12" id="cod_amount_field">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12" id="customer_payment_detail">
                </div>
            </div>
            <div class="col-lg-12 col-md-6 col-sm-12">
                <label for="remark" class="required-label">Remarks</label>
                <textarea class="form-control" id="remark" name="remarks"></textarea>
            </div>
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
        if(order_status == '2'){
            $('#cod_from_logistic').html(`
               <input type="checkbox" name="cod_from_logistic" id="cod_logistic_checkbox">
                    <label for="cod_logistic_checkbox">Is COD(cash on delivery)</label>

            `)
        }else{
            $('#cod_from_logistic').html('')
        }
        {{--$.ajax({--}}
        {{--    url :"{{ route('check_order_status') }}",--}}
        {{--    method:"GET",--}}
        {{--    data:{--}}
        {{--        order_status,--}}
        {{--        order_id--}}
        {{--    },--}}
        {{--    success:function (responseData){--}}
        {{--        if(responseData.response){--}}

        {{--        }--}}
        {{--        else--}}
        {{--        {--}}
        {{--            toastr.error(responseData.msg)--}}
        {{--            $('#orderStatus').val("{{ $order->delivery_status }}").trigger('change')--}}
        {{--        }--}}
        {{--    }--}}
        {{--})--}}
    })

    $(document).on('click','#cod_logistic_checkbox',function (){

        const mainDiv = $('#customer_payment_detail')
        if($(this).is(':checked')){
            $('#cod_amount_field').html(`
                   <label for="codAmount">COD Amount</label>
                    <input type="text" name="cod_amount" id="codAmount" class="form-control mb-3">
            `)

            const order_id = $('#orderId').val()

            $.ajax({
                url:"{{ route('getCustomerPayment') }}",
                data:{
                    order_id
                },

                success:function (responseData){
                    mainDiv.empty()

                    if(responseData.response){

                        mainDiv.append(`
                           <input type="hidden" name="remaining_amount" value="${responseData.remaining_amount}">
                         <h5>Total Amount : <span class="text-success">Rs. ${responseData.total_amount}</span></h5>
                          <h5>Total Paid Amount  : <span class="text-success">Rs. ${responseData.total_paid_amount}</span></h5>
                          <h5>Total Due Amount : <span class="text-success">Rs. ${responseData.remaining_amount}</span></h5>
                        `)
                    }
                }
            })
        }else{
            mainDiv.empty()
            $('#cod_amount_field').html('')
        }
    })
    $(function (){
        $('#orderStatus').select2()

        $("#change_status_post").on('submit',function (event) {
            event.preventDefault();
            if (validator.checkAll($(this))) {
                const data =  new FormData(this)
                const url = $(this).attr('action')
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
