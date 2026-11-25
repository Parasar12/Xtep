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
                                    <h4 class="card-title mb-4">Device Problem Setup
                                        <button type="button"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"
                                            id="addProblem">Add New Problem</button>
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
                                                    <th class="align-middle">Problem Name</th>
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
    <div class="modal" tabindex="-1" role="dialog" id="addProblemModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="addProblemModalContent"></div>
        </div>
    </div>
    <!-- end modal -->
@endsection

@section('script')
    <script src="{{ asset('js/tagin.min.js') }}"></script>
    <script>
        var problemModule = {};
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

            problemModule.problemListTbl = $('#problemTbl').DataTable({
            	dom: 'lfrtip',
            	destroy: true,
            	processing: true,
            	language: {
            		processing: '<span style="color:white;">Processing...</span>'
            	},
            	serverSide: true,
            	ajax: {
            		url: "{{ route('problem.get') }}",
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
            	{data: 'problem_name'},
            	{data: 'position'},
            	{data: 'action'},
            	// {data: 'status'},
            	],
            	columnDefs: [{"orderable": false,"targets": [3]}],
            });



            $('#problemTbl').on('click', '.delete_problems', function() {
               
                var problemId = $(this).data('id');

                bootbox.confirm({
                    title: "Delete Device Problem ?",
                    message: "Do you want to delete this Device Problem ?",
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
                                name: 'problemId',
                                value: problemId
                            });
                            var url = "{{ route('problems.delete') }}";

                            window.ct.postData(url, data).then(function(responseData) {
                                    problemModule.problemListTbl.ajax.reload(null, false);
                                    toastr.success(responseData.msg);
                                },
                                function(error) {
                                    toastr.error(error.msg);
                                });
                        } else {
                            problemModule.problemListTbl.ajax.reload(null, false);
                        }
                    }
                });
            });


            $('#addProblem').on('click', function() {

                $('#addProblemModal #addProblemModalContent').html('');
                $('#addProblemModal #addProblemModalContent').html($('#add_problem').html());


                $('.js-example-basic-single').select2({
                    dropdownParent: $('#addProblemModal'),
                    tags: true
                });


                $('#addProblemModal').modal('show');

            });

            $('#addProblemModal').on('submit', '#post_problem', function() {
                if (validator.checkAll($(this))) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });

                    var data = new FormData(this);
                    var url = $(this).attr('action');

                    window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData) {
                        $('#addProblemModal').modal('hide');
                        toastr.success(responseData.msg);
                        problemModule.problemListTbl.ajax.reload(null, false);
                    }, function(error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#post_problem", error.result);
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

    <script type="text/template" id="add_problem">
	<form method="post" action="{{ route('problems.store') }}" enctype="multipart/form-data" id="post_problem" novalidate class="eq-section">
		@csrf

		<div class="modal-header">
			<h5 class="modal-title" id="problemModalLabel">Add New Problem</h5>
		</div>

		<div class="modal-body">

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">	

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group item">
                                        <label class="required-label">Problem Name </label>
                                        <input type="text" class="form-control" required id="problem_name" name="problem_name" maxlength="100">
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
			<button type="submit" id="save" class="btn btn-success btn-rounded">Save Problem</button> 
			<button type="button" class="btn btn-danger btn-rounded" data-bs-dismiss="modal">Close</button>
		</div>

	</form>

</script>
@endsection
