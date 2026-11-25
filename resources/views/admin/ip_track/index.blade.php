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
                                                <th class="align-middle">S.N.</th>
                                                <th class="align-middle">Ip Address</th>
                                                <th class="align-middle">Date Time</th>
                                                <th class="align-middle">Country Name</th>
                                                <th class="align-middle">Region Name</th>
                                                <th class="align-middle">City Name</th>
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

            bootbox.confirm({
                title: "Delete Ip address ?",
                message: "Do you want to delete this ip address ?",
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
                        $.ajax({
                            url: "{{route('ip_track.delete')}}",
                            method: 'get',
                            data: {
                                id: productsId
                            },
                            success: function (data) {
                                ProductModule.productListTbl.ajax.reload(null, false);
                                toastr.success(data.msg);
                                $('#delete_products').hide();
                                $('#check_all').prop("checked", false)

                            }
                        })
                    } else {
                        ProductModule.productListTbl.ajax.reload(null, false);
                    }
                }
            });



        }

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
                    url: "{{route('ip_address.getList')}}",
                    type: "POST",
                    data: function (postData) {

                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [[25, 20, -1], [25, 20, "All"]],
                order: [[0, "DESC"]],
                columns: [
                    {data: 'id'},
                    {data: 'ip_address'},
                    {data: 'date_time'},
                    {data: 'country_name'},
                    {data: 'region_name'},
                    {data: "city_name"},
                    {data: "checkbox"},


                ],
                columnDefs: [
                    {"orderable": false, "targets": [6]},
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
