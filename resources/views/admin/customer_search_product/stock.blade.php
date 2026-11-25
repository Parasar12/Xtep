@extends('layouts.app')

@section('content')

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
                                    <h4 class="card-title mb-4">Product Stock
                                    </h4>

                                    <div class="table-responsive">
                                        <table id="produtStockTbl"
                                               class="table table-bordered dt-responsive nowrap w-100"
                                               style="width:100%">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">S. No.</th>
                                                <th class="align-middle">Product Name</th>
                                                <th class="align-middle">Total</th>
                                                <th class="align-middle">Ordered</th>
                                                <th class="align-middle">Remaining</th>
                                                <th class="align-middle">Action</th>
                                            </tr>
                                            </thead>
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

    <div class="modal" tabindex="-1" role="dialog" id="viewModal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" id="viewModalContent"></div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript">

        function viewDetails(id) {
            $.ajax({
                url: "{{ route('product.stock.details') }}",
                type: 'GET',
                data: {
                    id
                },
                success: function (response) {
                    if (response.response) {
                        $('#viewModal #viewModalContent').html('');
                        $('#viewModal #viewModalContent').html(response.result);
                        $('#viewModal').modal('show');
                    } else {
                        toastr.error(response.msg)
                    }
                }
            })
        }

        var ProductStockModule = {};

        $(document).ready(function () {
            ProductStockModule.produtStockTbl = $('#produtStockTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,

                ajax: {
                    url: "{{route('product.stock.get')}}",
                    type: "GET",
                    data: function (postData) {
                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [[10, 20, -1], [10, 20, "All"]],
                order: [[1, "DESC"]],
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'total'},
                    {data: 'order'},
                    {data: 'remaining'},
                    {data: 'action'},
                ],
                columnDefs: [
                    {"orderable": false, "targets": [2]},
                    {"orderable": false, "targets": [3]},
                    {"orderable": false, "targets": [4]},
                    {"orderable": false, "targets": [5]},
                ],
            });

        });
    </script>
@endsection
