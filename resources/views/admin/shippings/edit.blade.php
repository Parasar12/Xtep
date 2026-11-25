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
            <form method="post" action="{{route('shippings.update', $shipping->id)}}" enctype="multipart/form-data" id="update_shipping">
              @csrf
              @method('PATCH')
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Title *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter shipping title" id="title" name="title" value="{{$shipping->title}}" maxlength="255" required>
                          <span class="error_txt title_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Duration *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter duration" id="duration" name="duration" value="{{$shipping->duration}}" maxlength="255" required>
                          <span class="error_txt duration_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Cost *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="number" step="0.01" placeholder="Please enter cost" id="cost" name="cost" value="{{$shipping->cost}}" maxlength="8" required>
                          <span class="error_txt cost_error"></span>
                        </div>
                      </div>

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" @if($shipping->is_active == 1) checked @endif>
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
                            <a type="button" style="float: right;" class="btn btn-secondary" href="{{route('shippings.index')}}">Back</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> 
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

  $(document).on('submit', '#update_shipping', function(e) {
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
@endsection