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
								<h4 class="card-title mb-4">Customer List</h4>

								<div class="table-responsive">
									<table id="customerTbl" class="table table-bordered dt-responsive nowrap w-100" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle">Type</th>
												<th class="align-middle">Name</th>
												<th class="align-middle">Email</th>
												<th class="align-middle">Contact Number</th>
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
	var CustomerModule = {};

	$(document).ready(function(){

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});


		CustomerModule.customerListTbl = $('#customerTbl').DataTable({
			dom:'lfrtip',
			destroy:true,
			processing:true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax:{
				url: "{{route('customers.get')}}",
				type: "GET",
				data: function(postData){
				}
			},
			scrollY: "50vh",
			scrollX: true,
			lengthMenu: [[10, 20, -1], [10, 20, "All"]],
			order: [[ 1, "DESC" ]],
			columns: [
			{ data: 'id' },
			{ data: 'is_admin' },
			{ data: 'name' },
			{ data: 'email' },
			{ data: 'contact' },
			{ data: 'is_active' },
			{data:"action"},
			],
			columnDefs: [{ "orderable": false, "targets": [6] }],
		});

		$('#customerTbl').on('click', '.delete_user', function(){
			var userId = $(this).data('id');

			bootbox.confirm({
				title: "Delete Customer ?",
				message: "Do you want to delete this Customer ?",
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
							CustomerModule.customerListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);
						});
					}
					else{
						CustomerModule.customerListTbl.ajax.reload(null, false);
					}
				}
			});
		});

	});

</script>

@endsection
