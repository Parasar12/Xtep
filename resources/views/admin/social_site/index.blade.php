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
                                    <h4 class="card-title mb-4">Social Site
                                        <button type="button" id="addSite"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                            Add New
                                        </button>
                                    </h4>

                                    <div class="table-responsive">
                                        <table id="siteTbl" class="table table-bordered dt-responsive nowrap w-100"
                                            style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="align-middle">S. No.</th>
                                                    <th class="align-middle">Site Name</th>
                                                    <th class="align-middle">Url</th>
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

    <div class="modal" tabindex="-1" role="dialog" id="addSiteModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="addSiteModalContent"></div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var SiteModule = {};

        $(document).ready(function() {

            SiteModule.siteListTbl = $('#siteTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{ route('social_site.get') }}",
                    type: "GET",
                    data: function(postData) {}
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [
                    [10, 20, -1],
                    [10, 20, "All"]
                ],
                order: [
                    [1, "DESC"]
                ],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'url'
                    },
                    {
                        data: 'is_active'
                    },
                    {
                        data: "action"
                    },
                ],
                columnDefs: [{
                    "orderable": false,
                    "targets": [4]
                }],
            });

            $('#addSite').on('click', function() {
                $('#addSiteModal #addSiteModalContent').html('');
                $('#addSiteModal #addSiteModalContent').html($('#add_site').html());
                $('#addSiteModal').modal('show');
            });
        });
    </script>

    <script type="text/template" id="add_site">
	<div class="modal-header">
		<h5 class="modal-title" id="addSiteModalLabel">Add Social Sites</h5>
	</div>

	<div class="modal-body">
		<form method="post" action="{{ route('social_site.store') }}" enctype="multipart/form-data" id="post_social_site">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Social Site Name *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter site name" id="name" name="name" maxlength="200">
                          <span class="text-danger name_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Url *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter site url" id="url" name="url" maxlength="200">
                          <span class="text-danger url_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Icon Class *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter site url" id="icon_class" name="icon_class" maxlength="200">
                          <span class="text-danger icon_class_error"></span>
                        </div>
                      </div>
					  <div class="row">
                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <input type="submit" class="btn btn-primary waves-effect waves-light" value="Submit">
                          </div>
                        </div>

                        <div class="col-md-1">
                          <div class="text-center mt-4 ml-4">
                            <a class="btn btn-secondary" href="{{ url('social_site') }}">Back</a>
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

    <script>
        $(document).on('submit', '#post_social_site', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: new FormData(this),
                processData: false,
                dataType: 'json',
                contentType: false,

                beforeSend: function(data) {
                    $('form .text-danger').text('');

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
    </script>
@endsection
