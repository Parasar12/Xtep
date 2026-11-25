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
                                    <h4 class="card-title mb-4">Device Sales 
                                        {{-- <button type="button"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"
                                            id="addProblem">Add New Problem</button> --}}
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-danger float-right" type="button"
                                                onclick="deleteProducts()" id="delete_products" style="display:none"> Delete
                                            </button>

                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="problemTbl" class="table table-bordered dt-responsive nowrap"
                                            width="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="align-middle">Id</th>
                                                    <th class="align-middle">Customer Name</th>
                                                    <th class="align-middle">Brand</th>
                                                    <th class="align-middle">Model</th>
                                                    <th class="align-middle">RAM</th>
                                                    <th class="align-middle">Storage</th>
                                                    <th class="align-middle">Action </th>

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
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <div class="modal fade" tabindex="-1" id="SaleModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="SaleModalContent"></div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ asset('js/tagin.min.js') }}"></script>
    <script>
        var problemModule = {};
        var productsId = [];

    
        $(document).ready(function() {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            problemModule.problemListTbl = $('#problemTbl').DataTable({
            	dom: 'lfrtip',
            	destroy: true,
            	processing: true,
            	language: {
            		processing: '<span style="color:white;">Processing...</span>'
            	},
            	serverSide: true,
            	ajax: {
            		url: "{{ route('sales.get') }}",
            		type: "POST",
            		data: function(postData) {

            		}
            	},
            	scrollY: "50vh",
            	scrollX: true,
            	lengthMenu: [[10, 20, -1],[10, 20, "All"]],
            	order: [[0, "DESC"]],
            	columns: [
            	{data: 'id'},
            	{data: 'customer_name'},
            	{data: 'brand'},
            	{data: 'model'},
            	{data: 'ram'},
            	{data: 'storage'},
            	{data: 'action'},
            	// {data: 'status'},
            	],
            	columnDefs: [{"orderable": false,"targets": [6]}],
            });


        });

        function showOrderDetailModal(code) {
		$.get("{{ url('/sale/detail/')}}" + '/' + code, function(data) {
			$('#SaleModal #SaleModalContent').html(data);
			$("#SaleModal").modal('show');
		});
	}

    </script>

@endsection
