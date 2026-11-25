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
								<h4 class="card-title mb-4">Brands
									<button type="button" id="addBrand" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">Add New Brand</button>
								</h4>

								<div class="table-responsive">
									<table id="brandTbl" class="table table-bordered dt-responsive nowrap w-100" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle">Brand Name</th>
												<th class="align-middle">Slug</th>
												<th class="align-middle">Status</th>
												<th class="align-middle edit_brand">Action</th>
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

<div class="modal" tabindex="-1" role="dialog" id="addBrandModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="addBrandModalContent"></div>
	</div>
</div>


@endsection

@section('script')

<script type="text/javascript">
	var BrandModule = {};

	$(document).ready(function() {

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

		BrandModule.brandListTbl = $('#brandTbl').DataTable({   
			dom: 'lfrtip',
			destroy: true,
			processing: true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax: {
				url: "{{route('brands.get')}}",
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
			{data: 'slug'},
			{data: 'is_active'},
			{data: "action"},
			],
			columnDefs: [{"orderable": false,"targets": [4]}],
		});

		$('#brandTbl').on('click', '.delete_brand', function(){
			var brandId = $(this).data('id');

			bootbox.confirm({
				title: "Delete Brand ?",
				message: "Do you want to delete this Brand ?",
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
						data.push({name:'brandId', value:brandId});
						var url = "{{ route('brands.delete') }}";

						window.ct.postData(url ,data).then(function(responseData){
							BrandModule.brandListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);
						});
					}
					else{
						BrandModule.brandListTbl.ajax.reload(null, false);
					}
				}
			});
		});


		$('#addBrand').on('click', function() {
			$('#addBrandModal #addBrandModalContent').html('');
			$('#addBrandModal #addBrandModalContent').html($('#add_brand').html());
			$('#addBrandModal').modal('show');
		});

		$('#addBrandModal').on('submit', '#post_brand',function(){

			if(validator.checkAll($(this))) {

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				});

				var data = new FormData(this);
				var url = $(this).attr('action');

				window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
					$('#addBrandModal').modal('hide');
					toastr.success(responseData.msg);
					BrandModule.brandListTbl.ajax.reload(null, false);
				}, function(error){
					toastr.error(error.msg);
					window.ct.populateFormError("#post_brand", error.result);
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

<script type="text/template" id="add_brand">
	<div class="modal-header">
		<h5 class="modal-title" id="brandModalLabel">Add Brand</h5>
	</div>

	<div class="modal-body">
		<form method="post" action="{{route('brands.store')}}" novalidate class="eq-section" enctype="multipart/form-data" id="post_brand">
			@csrf
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">


							<div class="mb-3 row">
								<label for="name" class="col-md-2 required-label col-form-label">Name </label>
								<div class="col-md-10 item">
									<input class="form-control" type="text" placeholder="Please enter Brand Name" id="main_title" name="name" maxlength="200" required>
								</div>
							</div>
							<div class="mb-3 row">
								<label for="slug" class="col-md-2 required-label col-form-label">Image </label>
								<div class="col-md-10 item">
									<input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)" required>
								</div>
							</div>

							<div class="mb-3 row">
								<label class="col-md-2 col-form-label"></label>
								<div class="col-md-10">
									<img src="" id="imgshow" style="width: 200px; max-height: 200px"/>
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
				</div> <!-- end col -->
			</div>
		</form>
	</div>
</script>

@endsection
