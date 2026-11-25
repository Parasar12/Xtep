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
            <form method="post" action="{{route('social_site.update')}}" enctype="multipart/form-data" id="update_social_site">
              @csrf
              <input type="hidden" name="id" value="{{$socialSite->id}}">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Social Site Name *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter site name" id="name" name="name" maxlength="200" value="{{$socialSite->name}}">
                          <span class="error_txt name_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Url *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter site url" id="url" name="url" value="{{$socialSite->url}}" maxlength="200">
                          <span class="error_txt url_error"></span>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label for="slug" class="col-md-2 col-form-label">Icon Class *</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" placeholder="Please enter site url" id="icon_class" name="icon_class" value="{{$socialSite->icon_class}}" maxlength="200">
                          <span class="error_txt icon_class_error"></span>
                        </div>
                      </div>

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" @if($socialSite->is_active == 1) checked @endif>
                        <label class="form-check-label" for="formCheck1">
                          Is Active
                        </label>
                      </div>

                      <div class="row">
                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <input type="submit" class="btn btn-primary waves-effect waves-light" value="Submit">
                          </div>
                        </div>

                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <a class="btn btn-secondary" href="{{route('social_site.index')}}">Back</a>
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
  $(document).on('submit', '#update_social_site', function(e) {
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
        $('form .error_txt').text('');

      },
      success: function(data) {
        if (data.status == 0) {
          $.each(data.error, function(key, value) {
            $('form span.' + key + '_error').text(value[0]);
          });
        } else {
          toastr.success(data.msg);
          location.href = "/social_site";
        }
      },
    });
  });
</script>
@endsection