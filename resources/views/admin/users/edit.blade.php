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
                            <form method="post" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data"
                                id="update_user">
                                @csrf
                                @method('PATCH')
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3 row">
                                                    <label for="name" class="col-md-2 col-form-label">Name *</label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="text"
                                                            placeholder="Please enter user name" id="name"
                                                            name="name" maxlength="255" value="{{ $user->name }}"
                                                            required>

                                                        @error('name')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="email" class="col-md-2 col-form-label">Email *</label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="email"
                                                            placeholder="Please enter user email" id="email"
                                                            name="email" maxlength="255" value="{{ $user->email }}"
                                                            required>

                                                        @error('email')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="contact" class="col-md-2 col-form-label">Contact Number *</label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="number"
                                                               placeholder="Please enter contact number" id="contact"
                                                               name="contact_number" maxlength="255" value="{{ $user->contact }}"
                                                               required>

                                                        @error('contact_number')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="password" class="col-md-2 col-form-label">Password </label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="password"
                                                            placeholder="Please enter user password" id="password"
                                                            name="password" maxlength="255">

                                                        @error('password')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="text-center mt-4">
                                                            <input type="submit"
                                                                class="btn btn-primary waves-effect waves-light"
                                                                value="Submit">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1">
                                                        <div class="text-center mt-4">
                                                            <a class="btn btn-secondary"
                                                                href="{{ route('users.index') }}">Back</a>
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
        $(document).on('submit', '#update_user',function(){

            if(validator.checkAll($(this))) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                var data = new FormData(this);
                var url = $(this).attr('action');

                window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
                    $('#addUserModal').modal('hide');
                    toastr.success(responseData.msg);

                    location.href = "/users";
                }, function(error){
                    toastr.error(error.msg);
                    window.ct.populateFormError("#update_user", error.result);
                });
            }
            return false;
        });
    </script>
@endsection
