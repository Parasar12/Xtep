@extends('layouts.app')

@section('content')

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
								<h4 class="card-title mb-4">Product Gallary
									<button type="button" id="addGallary" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">Add Gallary</button>
								</h4>
								<div class="table-responsive">
									<table id="galleryTbl" class="table table-bordered dt-responsive  nowrap w-100" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle"> Product Name</th>
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

<!-- Product Modal -->
<div class="modal" id="addProductGallaryModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="addProductGallaryModalContent">
			<form method="post" action="{{route('product.gallary.store')}}" enctype="multipart/form-data" id="add_product_gallery">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title">Add Gallary</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3 row">
						<label class="col-md-2 col-form-label">Product</label>
						<div class="col-md-10">
							<select name="product_id" class="form-control" required>
								<option value="">Select Product</option>
								@foreach($products as $product)
									<option value="{{$product->id}}">{{$product->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-md-2 col-form-label">Images</label>
						<div class="col-md-10">
							<input type="file" name="image[]" class="form-control" accept="image/*" multiple required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-primary waves-effect waves-light" value="Submit">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- end modal -->    
@endsection

@section('script')

<script>
	var ProductGallaryModule = {};

	$(document).ready(function(){

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

		var i = 0;
		ProductGallaryModule.productGallaryListTbl = $('#galleryTbl').DataTable({
			dom:'lfrtip',
			destroy:true,
			processing:true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax:{
				url: "{{route('product.gallary.get')}}",
				type: "GET",
				data: function(postData){
				}
			},
			scrollY: "50vh",
			scrollX: true,
			lengthMenu: [[5, 10, 20, -1], [5, 10, 20, "All"]],
			order: [[ 1, "DESC" ]],
			columns: [
			{ data: 'id' },
			{ data: 'name' },
			{data:"action"},
			],
			columnDefs: [{ "orderable": false, "targets": [2] }],
		});

		$('#addGallary').on('click', function(){
			$('#add_product_gallery')[0].reset();
			$('#addProductGallaryModal').modal('show');
		});

		$(document).on('submit', '#add_product_gallery', function(e){
			e.preventDefault();
			$.ajax({
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(data){
					if (data.status == 0) {
						$.each(data.error, function(key, value){
							toastr.error(value);
						});
					} else {
						toastr.success(data.msg);
						$('#addProductGallaryModal').modal('hide');
						ProductGallaryModule.productGallaryListTbl.ajax.reload();
					}
				},
			});
		});
	});

</script>

@endsection
