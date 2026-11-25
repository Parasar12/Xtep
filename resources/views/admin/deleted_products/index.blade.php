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
                                    <h4 class="card-title mb-4">Deleted Products
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary float-right" onclick="undoProducts()"
                                            id="undo_products" style="display: none">Undo</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="deletedProductTbl" class="table table-bordered dt-responsive nowrap"
                                            width="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="align-middle">Name</th>
                                                    <th class="align-middle">Category</th>
                                                    <th class="align-middle">Slug</th>
                                                    <th class="align-middle">Status</th>
                                                    <th class="align-middle">Action </th>
                                                    <th class="align-middle">
                                                        <input type='checkbox' id='check_all' name='multiple_undo'
                                                            onchange='checkAllBoxes(this)' value='1'>
                                                       
                                                    </th>

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
@endsection

@section('script')
    <script src="{{ asset('js/tagin.min.js') }}"></script>
    <script>
        var DeltedProductModule = {};
        var productsId = [];

        function arrayProducts(id, checkbox) {
            productsId.push(id)
            $('#undo_products').show();

            var data = $('#multiple_undo-' + id).val()
            if (checkbox.checked == true) {
                productsId.push(id)
                $('#undo_products').show();
            } else {
                for (var i = productsId.length - 1; i >= 0; i--) {
                    if (productsId[i] == data) productsId.splice(i, 1);
                }
                if (productsId == '') {
                    $('#undo_products').hide();
                }
            }
        }

        function checkAllBoxes(checkbox) {

            $('#undo_products').show();
            if (checkbox.checked == true) {
                var data = $('.multiple_undo')

                for (var i = 0, n = data.length; i < n; i++) {
                    data[i].checked = checkbox.checked;
                    var id = data[i].id
                    productsId.push($('#' + id).val())
                }
                console.log(productsId)
            } else {

                $('#undo_products').hide();
                var data = $('.multiple_undo')
                for (var i = 0, n = data.length; i < n; i++) {
                    data[i].checked = checkbox.checked;
                }
                productsId = [];
                console.log(productsId)
            }

        }

        function undoProducts() {
            $.ajax({
                url: "{{ route('undo_multiple.delete') }}",
                method: 'get',
                data: {
                    id: productsId
                },
                success: function(data) {
                    DeltedProductModule.deletedProductListTbl.ajax.reload(null, false);
                    toastr.success(data.msg);
                    $('#undo_products').hide();
					$('#check_all').prop("checked",false)

                }
            })
        }
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            DeltedProductModule.deletedProductListTbl = $('#deletedProductTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{ route('products.getDeletedProduct') }}",
                    type: "POST",
                    data: function(postData) {

                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [
                    [10, 20, -1],
                    [10, 20, "All"]
                ],
                order: [
                    [0, "DESC"]
                ],
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'category_name'
                    },
                    {
                        data: 'slug'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: "action"
                    },
                    {
                        data: "checkbox"
                    },
                ],
                columnDefs: [{
                    "orderable": false,
                    "targets": [5]
                }],
            });

            $('#deletedProductTbl').on('click', '.undo_products', function() {
                var deletedProductId = $(this).data('id');

                bootbox.confirm({
                    title: "Undo Product ?",
                    message: "Do you want to undo this Product ?",
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Cancel'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Confirm'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            var data = [];
                            data.push({
                                name: 'deletedProductId',
                                value: deletedProductId
                            });
                            var url = "{{ route('products.undo') }}";

                            window.ct.postData(url, data).then(function(responseData) {
                                    DeltedProductModule.deletedProductListTbl.ajax.reload(
                                        null, false);
                                    toastr.success(responseData.msg);
                                },
                                function(error) {
                                    toastr.error(error.msg);
                                });
                        } else {
                            DeltedProductModule.deletedProductListTbl.ajax.reload(null, false);
                        }
                    }
                });
            });

        })
    </script>
@endsection
