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
            <form method="post" action="{{route('ticket.issue.update')}}" enctype="multipart/form-data" id="update_issue_category">
              @csrf
                  <input type="hidden" name="id" value="{{$issueCategory->id}}">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body">
                          <div class="mb-3 row">
                            <label for="name" class="col-md-2 col-form-label">Issue Category *</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" placeholder="Please enter issue category name"
                              id="name" name="name" value="{{$issueCategory->name}}" maxlength="200">
                              <span class="error_txt name_error"></span>
                            </div>
                          </div>


                          <div class="mb-3 row">
                            <div class="col-md-2"></div>
                            <div class="form-check mb-3">
                              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" @if($issueCategory->is_active == 1) checked @endif>
                              <label class="form-check-label" for="formCheck1">
                                Is Active
                              </label>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="text-center mt-4">
                                <input type="submit" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
                              </div>
                            </div>

                            <div class="col-md-6 pull-right">
                              <div class="text-center mt-4">
                                <a type="button" style="float: right;" class="btn btn-secondary" href="{{route('ticket.issue.index')}}">Back</a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> <!-- end col -->
                  </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('script')

<script>

  $(document).on('submit', '#update_issue_category', function(e) {
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
        $(this).find('form .error_txt').text('');

      },
      success: function(data) {
        if (data.status == 0) {
          $.each(data.error, function(key, value) {
            $('form span.' + key + '_error').text(value[0]);
          });
        } else {
          toastr.success(data.msg);
        }
      },
    });
  });
</script>
@endsection