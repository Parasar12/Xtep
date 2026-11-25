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
                <h4 class="card-title mb-4">Shipping Methods
                  <button type="button" id="addShippings" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                    Add New
                  </button>
                </h4>

                <div class="table-responsive">
                  <table id="shippingMethodTbl" class="table table-bordered dt-responsive  nowrap w-100" style="width:100%">
                    <thead class="table-light">
                      <tr>
                        <th class="align-middle">S. No.</th>
                        <th class="align-middle">Title</th>
                        <th class="align-middle">Duration</th>
                        <th class="align-middle">Cost</th>
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

<div class="modal" tabindex="-1" role="dialog" id="addShippingsModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="addShippingsModalContent"></div>
  </div>
</div>


@endsection

@section('script')

<script type="text/javascript">

  var ShippingModule = {};

  $(document).ready(function() {

    ShippingModule.shippingListTbl = $('#shippingMethodTbl').DataTable({
      dom: 'lfrtip',
      destroy: true,
      processing: true,
      language: {
        processing: '<span style="color:white;">Processing...</span>'
      },
      serverSide: true,
      ajax: {
        url: "{{route('shippings.get')}}",
        type: "GET",
        data: function(postData) {}
      },
      scrollY: "50vh",
      scrollX: true,
      lengthMenu: [[10, 20, -1],[10, 20, "All"]],
      order: [[1, "DESC"]],
      columns: [
      {data: 'id'},
      {data: 'title'},
      {data: 'duration'},
      {data: 'cost'},
      {data: 'is_active'},
      {data: "action"},
      ],
      columnDefs: [{"orderable": false,"targets": [5]}],
    });

    $('#addShippings').on('click', function() {
      $('#addShippingsModal #addShippingsModalContent').html('');
      $('#addShippingsModal #addShippingsModalContent').html($('#add_shipping').html());
      $('#addShippingsModal').modal('show');
    });
  });


  $(document).on('submit', '#post_shipping', function(e) {
    e.preventDefault();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });

    $.ajax({
      type: 'POST',
      url: $('this').attr('action'),
      data: new FormData(this),
      processData: false,
      dataType: 'json',
      contentType: false,

      beforeSend: function(data) {
        $(document).find('form span .error_txt').text('');

      },
      success: function(data) {
        if (data.status == 0) {
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

<script type="text/template" id="add_shipping">
  <div class="modal-header">
    <h5 class="modal-title" id="shippingModalLabel">Add Shipping Method</h5>
  </div>

  <div class="modal-body">
    <form method="post" action="{{route('shippings.store')}}" enctype="multipart/form-data" id="post_shipping">
      @csrf
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="mb-3 row">
                <label for="name" class="col-md-2 col-form-label">Title *</label>
                <div class="col-md-10">
                  <input class="form-control" type="text" placeholder="Please enter shipping title"
                  id="title" name="title" maxlength="200" required>
                  <span class="error_txt title_error"></span>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="slug" class="col-md-2 col-form-label">Duration *</label>
                <div class="col-md-10">
                  <input class="form-control" type="text" placeholder="Please enter duration"
                  id="duration" name="duration" maxlength="200" required>
                  <span class="error_txt duration_error"></span>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="name" class="col-md-2 col-form-label">Cost *</label>
                <div class="col-md-10">
                  <input class="form-control" type="number" step="0.01" placeholder="Please enter cost"
                  id="cost" name="cost" maxlength="8" required>
                  <span class="error_txt cost_error"></span>
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
                    <input type="submit" style="float; left;" class="btn btn-primary waves-effect waves-light" value="Submit">
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
        </div> 
      </div>
    </form>
  </div>
</script>

@endsection