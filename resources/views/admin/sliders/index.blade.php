@extends('layouts.app')

@section('content')
<?php $categories = \App\Models\Category::where('parent_id', 0)->with('children')->get(); ?>

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
								<h4 class="card-title mb-4">Sliders
									<button type="button" id="addSlider" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">Add New Slider</button>
								</h4>

								<div class="table-responsive">
									<table id="sliderTbl" class="table table-bordered dt-responsive  nowrap w-100" width="100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">Main Title</th>
												<th class="align-middle">Category</th>
												<th class="align-middle">Status</th>
												<th class="align-middle edit_slider">Action</th>
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

<div class="modal" tabindex="-1" role="dialog" id="addSliderModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" id="addSliderModalContent"></div>
	</div>
</div>


@endsection

@section('script')

<script type="text/javascript">

	var SliderModule = {};

	$(document).ready(function(){

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

		SliderModule.sliderListTbl = $('#sliderTbl').DataTable({
			dom:'lfrtip',
			destroy:true,
			processing:true,
			language: {
				processing: '<span style="color:white;">Processing...</span>'
			},
			serverSide: true,
			ajax:{
				url: "{{route('sliders.get')}}",
				type: "POST",
				data: function(postData){
				}
			},
			scrollY: "50vh",
			scrollX: true,
			lengthMenu: [[10, 20, -1], [10, 20, "All"]],
			order: [[ 1, "DESC" ]],
			columns: [
			{ data: 'main_title' },
			{ data: 'category_name' },
			{ data: 'status' },
			{data:"action"},
			],
			columnDefs: [{ "orderable": false, "targets": [2] }],
		});

		$('#sliderTbl').on('click', '.delete_slider', function(){
			var sliderId = $(this).data('id');

			bootbox.confirm({
				title: "Delete Slider ?",
				message: "Do you want to delete this slider ?",
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
						data.push({name:'sliderId', value:sliderId});
						var url = "{{ route('sliders.delete') }}";

						window.ct.postData(url ,data).then(function(responseData){
							SliderModule.sliderListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);
						});
					}
					else{
						SliderModule.sliderListTbl.ajax.reload(null, false);
					}
				}
			});
		});


		$('#addSlider').on('click', function(){
			$('#addSliderModal #addSliderModalContent').html('');
			$('#addSliderModal #addSliderModalContent').html($('#add_slider').html());
			$('#addSliderModal').modal('show');
		});


		$('#addSliderModal').on('submit', '#addSliderForm',function(){
			if(validator.checkAll($(this))) {

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				});

				var data = new FormData(this);
				var url = $(this).attr('action');

				window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
					$('#addSliderModal').modal('hide');
					toastr.success(responseData.msg);
					SliderModule.sliderListTbl.ajax.reload(null, false);
				}, function(error){
					toastr.error(error.msg);
					window.ct.populateFormError("#addSliderForm", error.result);
				});
			}
			return false;
		});

	});

	function showSelectedImage(e){
		$("#imgload").change(function () {
			if (this.files && this.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#imgshow').attr('src', e.target.result);
				}
				reader.readAsDataURL(this.files[0]);
			}
		});
	}

    function showSelectedMobileImage(e){
        $("#mobileImgload").change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#mobileImgshow').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
</script>

<script type="text/template" id="add_slider">
	<div class="modal-header">
		<h5 class="modal-title">Add Slider</h5>
	</div>

	<div class="modal-body">
		<form method="post" action="{{route('sliders.store')}}" enctype="multipart/form-data" id="addSliderForm" novalidate class="eq-section">
			@csrf

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div class="mb-3 row">
								<label class="required-label col-md-2 col-form-label">Category </label>
								<div class="col-md-10 item">
									<select class="form-select form-control" name="category_id" required>
										<option value="" selected="selected" disabled="disabled">Select Type</option>
										@include('categories_list')
									</select>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="name" class="col-md-2 required-label col-form-label">Main Title </label>
								<div class="col-md-10 item">
									<input class="form-control" type="text" placeholder="Please enter image title" id="main_title" name="main_title" maxlength="200" required>
								</div>
							</div>

							<div class="mb-3 row">
								<label for="name" class="col-md-2 col-form-label">Title </label>
								<div class="col-md-10 item">
									<input class="form-control" type="text" placeholder="Please enter image title" id="title" name="title" maxlength="200">
								</div>
							</div>
							<div class="mb-3 row">
								<label for="slug" class="col-md-2 required-label col-form-label">Web Slider Image </label>
								<div class="col-md-6 item">
									<input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)" required>
								</div>
                                <div class="col-md-4">
                                    <img src="" id="imgshow" style="width: 200px; max-height: 200px"/>
                                </div>
							</div>

                            <div class="mb-3 row">
                                <label for="slug" class="col-md-2 required-label col-form-label">Mobile Slider Image </label>
                                <div class="col-md-6 item">
                                    <input class="form-control" type="file" id="mobileImgload" name="mobile_image" onclick="showSelectedMobileImage(event)" required>
                                </div>
                                <div class="col-md-4">
                                    <img src="" id="mobileImgshow" style="width: 200px; max-height: 200px"/>
                                </div>
                            </div>

							<div class="mb-3 row">
								<label class="col-md-2 col-form-label"></label>

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
