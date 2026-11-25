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
								<h4 class="card-title mb-4">Users
									<button type="button" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2" id="addUser">
										Add New
									</button>
								</h4>

								<div class="table-responsive">
									<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 show_data" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle">Type</th>
												<th class="align-middle">Name</th>
												<th class="align-middle">Email</th>
												<th class="align-middle">Contact Number</th>
												<th class="align-middle">MPIN</th>
												<th class="align-middle">Status</th>
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

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- User Modal -->
<div class="modal" tabindex="-1" role="dialog" id="addUserModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="addUserModalContent"></div>
	</div>
</div>
<!-- end modal -->

@endsection

@section('script')

<script type="text/javascript">
	var UserModule = {};

	$(document).ready(function(){


		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

		UserModule.userListTbl = $('.show_data').DataTable({
			dom:'lfrtip',
			destroy:true,
			processing:true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax:{
				url: "{{route('users.get')}}",
				type: "GET",
				data: function(postData){
				}
			},
			scrollY: "50vh",
			scrollX: true,
			lengthMenu: [[10, 20, -1], [10, 20, "All"]],
			order: [[ 0, "DESC" ]],
			columns: [
			{ data: 'id' },
			{ data: 'is_admin' },
			{ data: 'name' },
			{ data: 'email' },
            { data: 'contact' },
			{ data: 'mpin' },
			{ data: 'is_active' },
			{data:"action"},
			],
			columnDefs: [{ "orderable": false, "targets": [7] }],
		});


		$('#datatable').on('click', '.delete_user', function(){
			var userId = $(this).data('id');

			bootbox.confirm({
				title: "Delete User ?",
				message: "Do you want to delete this User ?",
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
						data.push({name:'userId', value:userId});
						var url = "{{ route('users.delete') }}";

						window.ct.postData(url ,data).then(function(responseData){
							UserModule.userListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);
						});
					}
					else{
						UserModule.userListTbl.ajax.reload(null, false);
					}
				}
			});
		});

		$('#addUser').on('click', function(){
			$('#addUserModal #addUserModalContent').html('');
			$('#addUserModal #addUserModalContent').html($('#add_user').html());
			$('#addUserModal').modal('show');
		});
		$('#addUserModal').on('submit', '#post_user',function(){

			if(validator.checkAll($(this))) {

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				});

				var data = new FormData(this);
				var url = $(this).attr('action');

				window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
					$('#addUserModal').modal('hide');
					toastr.success(responseData.msg);
					UserModule.userListTbl.ajax.reload(null, false);
				}, function(error){
					toastr.error(error.msg);
					window.ct.populateFormError("#post_user", error.result);
				});
			}
			return false;
		});

		@if($errors->any())
		$('#addUserModal #addUserModalContent').html('');
		$('#addUserModal #addUserModalContent').html($('#add_user').html());
		$('#addUserModal').modal('show');
		@endif

	});

</script>

<script type="text/template" id="add_user">
	<div class="modal-header">
		<h5 class="modal-title" id="userModalLabel">Add User</h5>
	</div>

	<div class="modal-body">
		<form method="post" action="{{route('users.store')}}" novalidate class="eq-section" enctype="multipart/form-data" id="post_user">
			@csrf

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div class="mb-3 row">
								<label for="name" class="col-md-4 required-label col-form-label">Name </label>
								<div class="col-md-8 item">
									<input class="form-control" type="text" placeholder="Please enter  Name" id="main_title" name="name" maxlength="200" required>
								</div>
							</div>
                            <div class="mb-3 row">
                                <label for="contact" class="col-md-4 required-label col-form-label">Contact Number </label>
                                <div class="col-md-8 item">
                                    <input class="form-control" type="number" placeholder="Please enter  contact number" id="contact" name="contact_number" maxlength="200" required>
                                </div>
                            </div>
							<div class="mb-3 row">
								<label for="name" class="col-md-4 required-label col-form-label">Email </label>
								<div class="col-md-8 item">
									<input class="form-control" type="email" placeholder="Please enter  Email" id="main_title" name="email" maxlength="200" required>
								</div>
							</div>
							<div class="mb-3 row">
								<label for="name" class="col-md-4 required-label col-form-label">Password </label>
								<div class="col-md-8 item">
									<input class="form-control" type="password" placeholder="Please enter  Password" id="main_title" name="password" maxlength="200" required>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="text-center mt-4">
										<input type="submit" id="save" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
									</div>
								</div>

								<div class="col-md-6">
									<div class="text-center mt-4">
										<button type="button" class="btn btn-secondary" style="float: right;" data-bs-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>

	</div>
</script>


@endsection
