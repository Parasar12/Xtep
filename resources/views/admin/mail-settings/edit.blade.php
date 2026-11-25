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
        <form method="post" action="{{route('mail.store')}}"  id="update_mail">
          @csrf

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="mb-3 row">
                    <label for="name" class="col-md-2 col-form-label">Mail From Address *</label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" placeholder="Please enter email"
                      id="mail_from" name="mail_from" maxlength="250" value="@if($config){{$config->email_from}} @endif" required>

                      @error('mail_from')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="name" class="col-md-2 col-form-label">Mail From Name *</label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" placeholder="Please enter name"
                      id="name" name="name" maxlength="250" value="@if($config){{$config->name}} @endif" required>

                      @error('name')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="host" class="col-md-2 col-form-label">Mail Host *</label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" placeholder="Please enter mail host"
                      id="host" name="host" value="@if($config){{$config->mail_host}} @endif" required>

                      @error('host')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="port" class="col-md-2 col-form-label">Mail Port *</label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" placeholder="Please enter mail port"
                      id="port" name="port" value="@if($config){{$config->port}} @endif" required>

                      @error('port')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="username" class="col-md-2 col-form-label">Mail Username *</label>
                    <div class="col-md-10">
                      <input class="form-control" type="text" placeholder="Please enter mail username"
                      id="username" name="username" value="@if($config){{$config->username}} @endif" required>

                      @error('username')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="password" class="col-md-2 col-form-label">Mail Password *</label>
                    <div class="col-md-10">
                      <input class="form-control" type="password" placeholder="Please enter mail password"
                      id="password" name="password" value="@if($config){{$config->password}} @endif" required>

                      @error('password')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="password" class="col-md-2 col-form-label">Mail Encryption *</label>
                    <div class="col-md-10">
                      <select class="form-select" name="encryption" required>
                        <option value="">Select</option>
                        <option value="ssl" @if($config)@if($config->encryption == 'ssl') selected @endif @endif>SSL</option>
                        <option value="tls" @if($config)@if($config->encryption == 'tls') selected @endif @endif>TLS</option>
                      </select>

                      @error('encryption')
                      <div class="alert alert-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-md-1">
                      <div class="text-center mt-4">
                        <input type="submit" class="btn btn-primary waves-effect waves-light" value="Submit">
                      </div>
                    </div>

                    <div class="col-md-1">
                      <div class="text-center mt-4">
                        <a class="btn btn-secondary" href="{{route('mail.index')}}">Back</a>
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

  $(document).on('submit', '#update_mail', function(e) {
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
          toastr.success(data.msg);
        }
      },
    });
  });
</script>

@endsection
