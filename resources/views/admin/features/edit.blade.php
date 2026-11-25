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
            <form method="post" action="{{route('features.update')}}" enctype="multipart/form-data" novalidate class="eq-section" id="update_feature">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">

                      @foreach($features as $feature)
                      <div class="mb-3 row">
                        <label class="required-label col-md-2 col-form-label">Feature {{$feature->feature}} Title </label>
                        <div class="col-md-10 item">
                          <input class="form-control" type="text" placeholder="Please enter feature title" name="title[]" maxlength="200" value="{{$feature->title}}" required>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label class="required-label col-md-2 col-form-label">Sub Title </label>
                        <div class="col-md-10 item">
                          <input class="form-control" type="text" placeholder="Please enter feature subtitle" name="sub_title[]" maxlength="200" value="{{$feature->sub_title}}" required>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label class="required-label col-md-2 col-form-label">Span Class </label>
                        <div class="col-md-10 item">
                          <input class="form-control" type="text" name="span[]" maxlength="200" value="{{$feature->span}}" required>
                        </div>
                      </div>

                      <div class="mb-3 row">
                        <label class="required-label col-md-2 col-form-label">Icon Class </label>
                        <div class="col-md-10 item">
                          <input class="form-control" type="text" name="icon[]" maxlength="200" value="{{$feature->icon}}" required>
                        </div>
                      </div>

                      <br><hr>
                      @endforeach

                      <div class="row">
                        <div class="col-md-1">
                          <div class="text-center mt-4">
                            <input type="submit" class="btn btn-primary waves-effect waves-light" value="Submit">
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

<script type="text/javascript">

  $(document).ready(function(){

    $('#update_feature').on('submit', function(){
      if(validator.checkAll($(this))) {

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        });

        var data = new FormData(this);        
        var url = $(this).attr('action');

        window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){ 
          toastr.success(responseData.msg);
          location.reload();
        }, function(error){
          toastr.error(error.msg);          
          window.ct.populateFormError("#update_feature", error.result);
        });
      }
      return false;
    });

  });

</script>

@endsection