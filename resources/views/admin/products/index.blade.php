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
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="form-group item">
                                                <label for="FiscalYear" class="required-label">Fiscal Year</label>
                                                <select name="fiscal_year" id="FiscalYear" required
                                                        class="form-control">
                                                    <option value="" selected disabled>Select</option>
                                                    @foreach($fiscalYear as $year)
                                                        <option value="{{ $year->id }}" >{{ $year->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-12 mt-3">
                                            <button type="button" id="search" class="btn btn-outline-success">Search</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="float-right">
                                                <a href="{{ route('products.add') }}"
                                                   class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                                    Add New Product
                                                </a>
{{--                                                <a href="{{ route('products.sku.edit') }}"--}}
{{--                                                   class="btn btn-info btn-rounded waves-effect waves-light mb-2 me-2">--}}
{{--                                                    Update Product--}}
{{--                                                </a>--}}
                                            </div>
                                            <h4 class="card-title mb-4">Products </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-danger float-right" type="button"
                                                    onclick="deleteProducts()" id="delete_products"
                                                    style="display:none"> Delete
                                            </button>

                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="productTbl" class="table table-bordered dt-responsive nowrap"
                                               width="100%">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">Fiscal Year</th>
                                                <th class="align-middle">Name</th>
                                                <th class="align-middle">Category</th>
                                                <th class="align-middle">Vendor</th>
                                                <th class="align-middle">Created By</th>
                                                <th class="align-middle">Status</th>
                                                <th class="align-middle">Action</th>
                                                <th class="align-middle">
                                                    <input type='checkbox' id='check_all'
                                                           name='multiple_delete'
                                                           onchange='checkAllBoxes(this)'
                                                           value='1'></th>

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
        <div class="modal fade" tabindex="-1" id="OrderModal" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="OrderModalContent"></div>
            </div>
        </div>
    </div>
    <!-- END layout-wrapper -->
@endsection

@section('script')
    <script src="{{asset('dashboard/js/tagin.min.js')}}"></script>
    <script>

        var ProductModule = {};
        var productsId = [];

        function arrayProducts(id, checkbox) {

            var data = $('#multiple_delete-' + id).val()

            if (checkbox.checked == true) {
                productsId.push(id)
                $('#delete_products').show();
            } else {

                for (var i = productsId.length - 1; i >= 0; i--) {
                    if (productsId[i] == data) productsId.splice(i, 1);
                }
                if (productsId == '') {
                    $('#delete_products').hide();
                }

            }

        }

        function checkAllBoxes(checkbox) {

            $('#delete_products').show();
            if (checkbox.checked == true) {
                var data = $('.multiple_delete')

                for (var i = 0, n = data.length; i < n; i++) {
                    data[i].checked = checkbox.checked;
                    var id = data[i].id
                    productsId.push($('#' + id).val())
                }
                console.log(productsId)
            } else {

                $('#delete_products').hide();
                var data = $('.multiple_delete')
                for (var i = 0, n = data.length; i < n; i++) {
                    data[i].checked = checkbox.checked;
                }
                productsId = [];
                console.log(productsId)
            }

        }

        function deleteProducts() {
            $.ajax({
                url: "{{route('multiple_product.delete')}}",
                method: 'get',
                data: {
                    id: productsId
                },
                success: function (data) {
                    location.reload()
                    toastr.success(data.msg);
                    $('#delete_products').hide();
                    $('#check_all').prop("checked", false)

                }
            })


        }

        function viewProducts(product_id){

            $.ajax({
                url:"{{ route('products.view') }}",
                method:"GET",
                data:{
                    product_id
                },
                success:function (responseData){
                    if(responseData.response){

                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');

                    }
                }
            })
        }

        function datatable(fiscal_year = null){
            $('#productTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{route('products.get')}}",
                    type: "POST",
                    data: {
                       fiscal_year,

                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [[10, 20, -1], [10, 20, "All"]],
                order: [[0, "DESC"]],
                columns: [
                    {data: 'fiscal_year_id'},
                    {data: 'name'},
                    {data: 'category_name'},
                    {data: 'vendor_id'},
                    {data: 'created_by'},
                    {data: 'status'},
                    {data: "action"},
                    {data: "checkbox"},


                ],
                columnDefs: [
                    {"orderable": false, "targets": [5]},
                    {"orderable": false, "targets": [6]},
                    {"orderable": false, "targets": [7]},
                ],
            });
        }

        $(document).ready(function () {

            $('#FiscalYear').select2()

            $('#search').on('click',function (){
                const fiscal_year = $('#FiscalYear').val()
                datatable(fiscal_year)
            })

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
            datatable();

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
