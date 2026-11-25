@extends('layouts.app')
@section('content')
    <style>
        .select2-dropdown {
            z-index: 99999;
        }
    </style>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('admin.includes.header')
        @include('admin.includes.left_sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-right">
                                                <a href="{{ route('sales_entry.add') }}"
                                                   class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                                    Add Order
                                                </a>
                                            </div>
                                            <h4 class="card-title mb-4">Order Process </h4>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="productTbl" class="table table-bordered dt-responsive nowrap"
                                               width="100%">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">S. No.</th>
                                                <th class="align-middle">Code</th>
                                                <th class="align-middle">Customer Name</th>
                                                <th class="align-middle">Bill Date</th>
                                                <th class="align-middle">Total Amount</th>
                                                <th class="align-middle order_action">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <!-- end table-responsive -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" role="dialog" id="viewSaleModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="viewSaleModalContent"></div>
            </div>
        </div>
    </div>
    <!-- END layout-wrapper -->
@endsection

@section('script')
    <script src="{{asset('dashboard/js/tagin.min.js')}}"></script>
    <script>


        function viewSales(sale_entry_id){

            $.ajax({
                url:"{{ route('sales_entry.view')  }}",
                method : "GET",
                data : {
                    sale_entry_id
                },
                success : function (responseData){
                     if(responseData.response){
                         $('#viewSaleModal #viewSaleModalContent').html('');
                         $('#viewSaleModal #viewSaleModalContent').html(responseData.result);
                         $('#viewSaleModal').modal('show');
                     }else{
                         toastr.error(responseData.msg)
                     }
                },
                error : function (responseData){

                }
            })

        }

        function paymentSale(sale_entry_id){

            $.ajax({
                url:"{{ route('getSalesPaymentView')  }}",
                method : "GET",
                data : {
                    sale_entry_id
                },
                success : function (responseData){
                    if(responseData.response){
                        $('#viewSaleModal #viewSaleModalContent').html('');
                        $('#viewSaleModal #viewSaleModalContent').html(responseData.result);
                        $('#viewSaleModal').modal('show');
                    }else{
                        toastr.error(responseData.msg)
                    }
                },
                error : function (responseData){

                }
            })
        }

        var ProductModule = {}
        $(document).ready(function () {

            $(window).keydown(function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            ProductModule.productListTbl = $('#productTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{route('sales_entry.get')}}",
                    type: "POST",
                    data: function (postData) {

                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [[25, 20, -1], [10, 20, "All"]],
                order: [[0, "DESC"]],
                columns: [
                    {data: 'id'},
                    {data: 'code'},
                    {data: 'user_id'},
                    {data: 'bill_date'},
                    {data: 'net_total'},
                    {data: "action"},

                ],
                columnDefs: [
                    {"orderable": false, "targets": [5]},
                    {"orderable": false, "targets": [4]}
                ],
            });


            $('#productTbl').on('click', '.delete_product', function () {
                var productId = $(this).data('id');

                bootbox.confirm({
                    title: "Delete Product ?",
                    message: "Do you want to delete this product ?",
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Cancel'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Confirm'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            var data = [];
                            data.push({name: 'productId', value: productId});
                            var url = "{{ route('products.delete') }}";

                            window.ct.postData(url, data).then(function (responseData) {
                                    ProductModule.productListTbl.ajax.reload(null, false);
                                    toastr.success(responseData.msg);
                                },
                                function (error) {
                                    toastr.error(error.msg);
                                });
                        } else {
                            ProductModule.productListTbl.ajax.reload(null, false);
                        }
                    }
                });
            });
        });
    </script>
@endsection
