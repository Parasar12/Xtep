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
                                    <h4 class="card-title mb-4">Device Condition Setup
                                        <button type="button"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"
                                            id="addCondition">Add New Condition</button>
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-danger float-right" type="button"
                                                onclick="deleteProducts()" id="delete_products" style="display:none"> Delete
                                            </button>

                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="conditionTbl" class="table table-bordered dt-responsive nowrap"
                                            width="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="align-middle">Id</th>
                                                    <th class="align-middle"> Question</th>
                                                    <th class="align-middle">Hint</th>
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
    <div class="modal" tabindex="-1" role="dialog" id="addConditionModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="addConditionModalContent"></div>
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

            accessoryModule.accessoryListTbl = $('#conditionTbl').DataTable({
            	dom: 'lfrtip',
            	destroy: true,
            	processing: true,
            	language: {
            		processing: '<span style="color:white;">Processing...</span>'
            	},
            	serverSide: true,
            	ajax: {
            		url: "{{ route('condition.get') }}",
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
            	{data: 'question'},
            	{data: 'hint'},
            	{data: 'position'},
            	{data: 'action'},
            	// {data: 'status'},
            	],
            	columnDefs: [{"orderable": false,"targets": [4]}],
            });



            $('#conditionTbl').on('click', '.delete_condition', function() {
               
                var conditionId = $(this).data('id');

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
                                name: 'conditionId',
                                value: conditionId
                            });
                            var url = "{{ route('condition.delete') }}";

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


            $('#addCondition').on('click', function() {

                $('#addConditionModal #addConditionModalContent').html('');
                $('#addConditionModal #addConditionModalContent').html($('#add_condition').html());


                $('.js-example-basic-single').select2({
                    dropdownParent: $('#addConditionModal'),
                    tags: true
                });


                $('#addConditionModal').modal('show');

            });

            $('#addConditionModal').on('submit', '#post_condition', function() {
                if (validator.checkAll($(this))) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });

                    var data = new FormData(this);
                    var url = $(this).attr('action');

                    window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData) {
                        $('#addConditionModal').modal('hide');
                        toastr.success(responseData.msg);
                        accessoryModule.accessoryListTbl.ajax.reload(null, false);
                    }, function(error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#post_condition", error.result);
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

    <script type="text/template" id="add_condition">
	<form method="post" action="{{ route('condition.store') }}" enctype="multipart/form-data" id="post_condition" novalidate class="eq-section">
		@csrf

		<div class="modal-header">
			<h5 class="modal-title" id="problemModalLabel">Add New Condition</h5>
		</div>

		<div class="modal-body">

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">	

                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group item">
                                        <label class="required-label"> Question </label>
                                        <input type="text" class="form-control" required id="question" name="question" maxlength="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group item">
                                        <label class="required-label"> Hint </label>
                                        <input type="text" class="form-control" required id="name" name="hint" maxlength="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group item">
                                        <label class="required-label"> Position </label>
                                        <input type="number" class="form-control" required id="name" name="position" maxlength="100">
                                    </div>
                                </div>
                            </div>
	
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button type="submit" id="save" class="btn btn-success btn-rounded">Save Condition</button> 
			<button type="button" class="btn btn-danger btn-rounded" data-bs-dismiss="modal">Close</button>
		</div>

	</form>

</script>
@endsection
