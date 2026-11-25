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
                <h4 class="card-title mb-4">Category Banner
                  <button type="button" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2" id="addBanner">
                    Add New
                  </button>
                </h4>

                <div class="table-responsive">
                  <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 show_data" style="width:100%">
                    <thead class="table-light">
                      <tr>
                        <th class="align-middle">S. No.</th>
                        <th class="align-middle">Heading</th>
                        <th class="align-middle">Title</th>
                        <th class="align-middle">Status</th>
                        <th class="align-middle edit_banner">Action</th>
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
<div class="modal" id="addBannerModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="addBannerModalContent">
    </div>
  </div>
</div>
<!-- end modal -->

@endsection

@section('script')

<script type="text/javascript">
  var BannerModule = {};

  $(document).ready(function() {

    $.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
		});

    BannerModule.bannerListTbl = $('.show_data').DataTable({
      dom: 'lfrtip',
      destroy: true,
      processing: true,
      language: {
        processing: '<span style="color:white;">Processing...</span>'
      },
      serverSide: true,
      ajax: {
        url: "{{route('banners.get')}}",
        type: "GET",
        data: function(postData) {}
      },
      scrollY: "50vh",
      scrollX: true,
      lengthMenu: [[5, 10, 20, -1],[5, 10, 20, "All"]],
      order: [[1, "DESC"]],
      columns: [
      {data: 'id'},
      {data: 'heading'},
      {data: 'title'},
      {data: 'is_active'},
      {data: "action"},
      ],
      columnDefs: [{"orderable": false, "targets": [2] }],
    });



    $('#datatable').on('click', '.delete_brand', function(){

			var bannerId = $(this).data('id');

			bootbox.confirm({
				title: "Delete Categry Banner ?",
				message: "Do you want to delete this Category Banner ?",
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
						data.push({name:'bannerId', value:bannerId});
						var url = "{{ route('banners.delete') }}";

						window.ct.postData(url ,data).then(function(responseData){
							BannerModule.bannerListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);
						});
					}
					else{
						BannerModule.bannerListTbl.ajax.reload(null, false);
					}
				}
			});
		});

    $('#addBanner').on('click', function() {
          $('#addBannerModal #addBannerModalContent').html('');
          $('#addBannerModal #addBannerModalContent').html($('#add_banner').html());
          $('#addBannerModal').modal('show');
    });


    $(document).on('submit', '#post_banner', function(e) {
      e.preventDefault();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      });

      $.ajax({
        type: 'POST',
        url: "{{ route('banners.store') }}",
        data: new FormData(this),
        processData: false,
        dataType: 'json',
        contentType: false,

        beforeSend: function(data) {
          $('form .error_txt').text('');
        },
        success: function(data) {
          if (data.status == 0) {
            $.each(data.error, function(key, value) {
              $('form span.' + key + '_error').text(value[0]);
            });
          } else {
            toastr.success(data.msg);
            location.reload();
          }
        },
      });
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

<script type="text/template" id="add_banner">
  <div class="modal-header">
    <h5 class="modal-title" id="brandModalLabel">Add Banner</h5>
  </div>

  <div class="modal-body">
    <form method="post" action="{{route('banners.store')}}" enctype="multipart/form-data" id="post_banner">
      @csrf

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <div class="mb-3 row">
                <label for="name" class="col-md-2 col-form-label">Category *</label>
                <div class="col-md-10">
                  <select class="form-select form-control" name="category_id">
                    <option value="">Select Type</option>
                    @include('categories_list')
                  </select>
                  <span class="error_txt category_id_error"></span>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="name" class="col-md-2 col-form-label">Heading *</label>
                <div class="col-md-10">
                  <input class="form-control" type="text" placeholder="Please enter banner heading" id="heading" name="heading" maxlength="200">
                  <span class="error_txt heading_error"></span>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="name" class="col-md-2 col-form-label">Title *</label>
                <div class="col-md-10">
                  <input class="form-control" type="text" placeholder="Please enter banner title" id="title" name="title" maxlength="200">
                  <span class="error_txt title_error"></span>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="name" class="col-md-2 col-form-label">Sub Title *</label>
                <div class="col-md-10">
                  <input class="form-control" type="text" placeholder="Please enter banner sub title" id="sub_title" name="sub_title" maxlength="200">
                  <span class="error_txt sub_title_error"></span>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="slug" class="col-md-2 col-form-label">Banner Image </label>
                <div class="col-md-10">
                  <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)">
                  <span class="error_txt image_error"></span>
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
                    <input type="submit" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
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
