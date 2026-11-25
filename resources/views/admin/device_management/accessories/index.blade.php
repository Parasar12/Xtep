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
                                    <h4 class="card-title mb-4">Device Accessories Setup
                                        <button type="button"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"
                                            id="addAccessory">Add New Accessory</button>
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-danger float-right" type="button"
                                                onclick="deleteProducts()" id="delete_products" style="display:none"> Delete
                                            </button>

                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="accessoryTbl" class="table table-bordered dt-responsive nowrap"
                                            width="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="align-middle">Id</th>
                                                    <th class="align-middle"> Name</th>
                                                    <th class="align-middle">Position</th>
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

    <!-- Product Modal -->
    <div class="modal" tabindex="-1" role="dialog" id="addAccessoryModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="addAccessoryModalContent"></div>
        </div>
    </div>
    <!-- end modal -->
@endsection

@section('script')
    <script src="{{ asset('js/tagin.min.js') }}"></script>
    <script>
        var accessoryModule = {};
        var productsId = [];

    
        $(document).ready(function() {

            $(window).keydown(function(event) {
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

            accessoryModule.accessoryListTbl = $('#accessoryTbl').DataTable({
            	dom: 'lfrtip',
            	destroy: true,
            	processing: true,
            	language: {
            		processing: '<span style="color:white;">Processing...</span>'
            	},
            	serverSide: true,
            	ajax: {
            		url: "{{ route('accessory.get') }}",
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
            	{data: 'name'},
            	{data: 'position'},
            	{data: 'action'},
            	// {data: 'status'},
            	],
            	columnDefs: [{"orderable": false,"targets": [3]}],
            });



            $('#accessoryTbl').on('click', '.delete_accessories', function() {
               
                var accessoryId = $(this).data('id');

                bootbox.confirm({
                    title: "Delete Device Accessory ?",
                    message: "Do you want to delete this Device Accessory ?",
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
                                name: 'accessoryId',
                                value: accessoryId
                            });
                            var url = "{{ route('accessories.delete') }}";

                            window.ct.postData(url, data).then(function(responseData) {
                                    accessoryModule.accessoryListTbl.ajax.reload(null, false);
                                    toastr.success(responseData.msg);
                                },
                                function(error) {
                                    toastr.error(error.msg);
                                });
                        } else {
                            accessoryModule.accessoryListTbl.ajax.reload(null, false);
                        }
                    }
                });
            });


            $('#addAccessory').on('click', function() {

                $('#addAccessoryModal #addAccessoryModalContent').html('');
                $('#addAccessoryModal #addAccessoryModalContent').html($('#add_accessory').html());


                $('.js-example-basic-single').select2({
                    dropdownParent: $('#addAccessoryModal'),
                    tags: true
                });


                $('#addAccessoryModal').modal('show');

            });

            $('#addAccessoryModal').on('submit', '#post_accessory', function() {
                if (validator.checkAll($(this))) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });

                    var data = new FormData(this);
                    var url = $(this).attr('action');

                    window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData) {
                        $('#addAccessoryModal').modal('hide');
                        toastr.success(responseData.msg);
                        accessoryModule.accessoryListTbl.ajax.reload(null, false);
                    }, function(error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#post_accessory", error.result);
                    });
                }
                return false;
            });

        });

        function showSelectedImage(e) {
            $("#imgload").change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imgshow').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }


    </script>

    <script type="text/template" id="add_accessory">
	<form method="post" action="{{ route('accessory.store') }}" enctype="multipart/form-data" id="post_accessory" novalidate class="eq-section">
		@csrf

		<div class="modal-header">
			<h5 class="modal-title" id="problemModalLabel">Add New Accessory</h5>
		</div>

		<div class="modal-body">

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">	

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group item">
                                        <label class="required-label"> Name </label>
                                        <input type="text" class="form-control" required id="name" name="name" maxlength="100">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group item">
                                        <label class="required-label">Position </label>
                                        <input type="number" class="form-control" required id="position" name="position" maxlength="8" step="0.01">
                                    </div>
                                </div>
                            </div>
								
								
							<div id="new_product">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group item">
											<label class="required-label">Featured Image </label>
											<input class="form-control" type="file"
											id="imgload" name="image" required onclick="showSelectedImage(event)">
										</div>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-2 col-form-label"></label>
									<div class="col-md-10">
										<img src="" id="imgshow" style="width: 200px; max-height: 300px"/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button type="submit" id="save" class="btn btn-success btn-rounded">Save Accessory</button> 
			<button type="button" class="btn btn-danger btn-rounded" data-bs-dismiss="modal">Close</button>
		</div>

	</form>

</script>
@endsection
