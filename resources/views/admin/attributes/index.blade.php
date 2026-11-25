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
								<h4 class="card-title mb-4">Attributes
									<button type="button" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2" id="addAttribute">
										Add New
									</button>
								</h4>

								<div class="table-responsive">
									<table id="attributeTbl" class="table table-bordered dt-responsive  nowrap w-100" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle">Name</th>
												<th class="align-middle edit_attribute">Action</th>
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

<!-- Brand Modal -->
<div class="modal" id="addAttributeModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="addAttributeModalContent"></div>
	</div>
</div>
<!-- end modal -->

@endsection

@section('script')

<script type="text/javascript">

	var AttributeModule = {};

	$(document).ready(function() {

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

		AttributeModule.brandListTbl = $('#attributeTbl').DataTable({
			dom: 'lfrtip',
			destroy: true,
			processing: true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax: {
				url: "{{route('attributes.get')}}",
				type: "GET",
				data: function(postData) {}
			},
			scrollY: "50vh",
			scrollX: true,
			lengthMenu: [[10, 20, -1],[10, 20, "All"]],
			order: [[1, "DESC"]],
			columns: [
			{data: 'id'},
			{data: 'name'},
			{data: "action"},
			],
			columnDefs: [{"orderable": false,"targets": [2] }],
		});

		$('#attributeTbl').on('click', '.delete_attribute', function(){
			var attributeId = $(this).data('id');

			bootbox.confirm({
				title: "Delete Attribute ?",
				message: "Do you want to delete this  Attribute ?",
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
						data.push({name:'attributeId', value:attributeId});
						var url = "{{ route('attributes.delete') }}";

						window.ct.postData(url ,data).then(function(responseData){
							AttributeModule.brandListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);          
						});
					}
					else{
						AttributeModule.brandListTbl.ajax.reload(null, false);
					}
				}
			});
		});



		$('#addAttribute').on('click', function() {
			$('#addAttributeModal #addAttributeModalContent').html('');
			$('#addAttributeModal #addAttributeModalContent').html($('#add_attribute').html());
			$('#addAttributeModal').modal('show');
		});

		$('#addAttributeModal').on('submit', '#post_attribute',function(){
			
			if(validator.checkAll($(this))) {

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				});

				var data = new FormData(this);        
				var url = $(this).attr('action');

				window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
					$('#addAttributeModal').modal('hide'); 
					toastr.success(responseData.msg);
					AttributeModule.brandListTbl.ajax.reload(null, false);
				}, function(error){
					toastr.error(error.msg);          
					window.ct.populateFormError("#post_attribute", error.result);
				});
			}
			return false;
		});
	});
</script>

<script type="text/template" id="add_attribute">
	<div class="modal-header">
		<h5 class="modal-title" id="brandModalLabel">Add Attribute</h5>
	</div>
	<div class="modal-body">
		<form method="post" action="{{route('attributes.store')}}" novalidate class="eq-section" enctype="multipart/form-data" id="post_attribute">
			@csrf
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div class="mb-3 row">
								<label for="name" class="col-md-2 required-label col-form-label">Name </label>
								<div class="col-md-10 item">
									<input class="form-control" type="text" placeholder="Please enter Attribute Name" id="main_title" name="name" maxlength="200" required>
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