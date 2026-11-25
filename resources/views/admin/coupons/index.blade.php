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
								<h4 class="card-title mb-4">Coupons
									<button type="button" id="addCoupon" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
										Add New
									</button>
								</h4>

								<div class="table-responsive">
									<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 show_data" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle">Code</th>
												<th class="align-middle">Discount</th>
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

<div class="modal" tabindex="-1" role="dialog" id="addCouponModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="addCouponModalContent"></div>
	</div>
</div>


@endsection

@section('script')

<script type="text/javascript">

	var CouponModule = {};

	$(document).ready(function() {
		CouponModule.couponListTbl = $('.show_data').DataTable({
			dom: 'lfrtip',
			destroy: true,
			processing: true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax: {
				url: "{{route('coupons.get')}}",
				type: "GET",
				data: function(postData) {}
			},
			scrollY: "50vh",
			scrollX: true,
			lengthMenu: [[5, 10, 20, -1],[5, 10, 20, "All"]],
			order: [[1, "DESC"]],
			columns: [
			{data: 'id'},
			{data: 'code'},
			{data: 'value'},
			{data: 'is_active'},
			{data: "action"},
			],
			columnDefs: [{"orderable": false,"targets": [4] }],
		});


		$('#addCoupon').on('click', function() {
			$('#addCouponModal #addCouponModalContent').html('');
			$('#addCouponModal #addCouponModalContent').html($('#add_coupon').html());
			$('#addCouponModal').modal('show');
			$('.js-example-basic-multiple').select2();
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

	
	$(document).on('submit', '#post_coupon', function(e) {
		e.preventDefault();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

		$.ajax({
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: new FormData(this),
			processData: false,
			dataType: 'json',
			contentType: false,

			beforeSend: function(data) {
				$(document).find('form span').text('');

			},
			success: function(data) {
				if (data.status == 0) {
					console.log(data);
					$.each(data.error, function(key, value) {
						$('form span.' + key + '_error').text(value[0]);
					});
				} else {
					location.reload();
					toastr.success(data.msg);
				}
			},
		});
	});
</script>

<script type="text/template" id="add_coupon">
	<div class="modal-header">
		<h5 class="modal-title" id="brandModalLabel">Add Coupon</h5>
	</div>

	<div class="modal-body">
		<form method="post" action="{{route('coupons.store')}}" enctype="multipart/form-data"  id="post_coupon">
			@csrf

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div class="mb-3 row">
								<label for="code" class="col-md-2 col-form-label">Code *</label>
								<div class="col-md-10">
									<input class="form-control" type="text" placeholder="Please enter coupon code"
									id="code" name="code" maxlength="200" required>
									<span class="error_txt code_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="name" class="col-md-2 col-form-label">Discount Type *</label>
								<div class="col-md-10">
									<select class="form-select" name="is_percent" required>
										<option value="">Select Type</option>
										<option value="0">Fixed</option>
										<option value="1">Percent</option>		
									</select>
									<span class="error_txt is_percent_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="code" class="col-md-2 col-form-label">Value *</label>
								<div class="col-md-10">
									<input class="form-control" type="number" placeholder="Please discount value"
									id="value" name="value" step="0.02" required>
									<span class="error_txt value_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="code" class="col-md-2 col-form-label">Start Date *</label>
								<div class="col-md-4">
									<input class="form-control" type="date"
									id="start_date" name="start_date" required>
									<span class="error_txt start_date_error"></span>
								</div>

								<label for="code" class="col-md-2 col-form-label">End Date *</label>
								<div class="col-md-4">
									<input class="form-control" type="date"
									id="end_date" name="end_date" required>
									<span class="error_txt end_date_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="code" class="col-md-2 col-form-label">Minimum Spend </label>
								<div class="col-md-4">
									<input class="form-control" type="number"
									id="minimum_spend" name="minimum_spend" step="0.02">
									<span class="error_txt minimum_spend_error"></span>
								</div>

								<label for="code" class="col-md-2 col-form-label">Maximum Spend </label>
								<div class="col-md-4">
									<input class="form-control" type="number"
									id="maximum_spend" name="maximum_spend" step="0.02">
									<span class="error_txt maximum_spend_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="code" class="col-md-2 col-form-label">Usage Limit per Person *</label>
								<div class="col-md-10">
									<input class="form-control" type="number"
									id="usage_limit_of_person" name="usage_limit_of_person" required>
									<span class="error_txt usage_limit_of_person_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="code" class="col-md-2 col-form-label">Usage Limit per Coupon *</label>
								<div class="col-md-10">
									<input class="form-control" type="number"
									id="usage_limit_of_coupon" name="usage_limit_of_coupon" required>
									<span class="error_txt usage_limit_of_coupon_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="products" class="col-md-2 col-form-label">Products </label>
								<div class="col-md-10">
									<select class="js-example-basic-multiple" name="products[]" multiple="multiple">
										@if(!$products->isEmpty())

										@foreach($products as $product)
										<option value="{{$product->id}}">{{$product->name}}</option>
										@endforeach
										@endif
									</select>
									<span class="error_txt products_error"></span>
								</div>
							</div>

							<div class="mb-3 row">
								<label class="col-md-2 col-form-label">Categories </label>
								<div class="col-md-10">
									<select class="js-example-basic-multiple" name="categories[]" multiple="multiple">
										@include('categories_list')
									</select>
									<span class="error_txt categories_error"></span>
								</div>
							</div>

							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active">
								<label class="form-check-label" for="formCheck1">
									Is Active
								</label>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="text-center mt-4">
										<input type="submit" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
									</div>
								</div>

								<div class="col-md-6">
									<div class="text-center mt-4">
										<button type="button" style="float: right;" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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