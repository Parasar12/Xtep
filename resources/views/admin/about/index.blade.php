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
                            <form method="post" novalidate class="eq-section" action="{{ route('aboutus.update') }}"
                                enctype="multipart/form-data" id="PostForm">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="id" value="{{ $about->id }}">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3 row">
                                                    <label for="name"
                                                        class="col-md-2 col-form-label required-label">Title</label>
                                                    <div class="col-md-10 item">
                                                        <input class="form-control" type="text"
                                                            value="{{ $about->title }}" name="title" maxlength="255"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="mb-3 row">
                                                    <label for="name"
                                                        class="col-md-2 col-form-label required-label">Description</label>
                                                    <div class="col-md-10 item">
                                                        <textarea name="description" id="Description" cols="30" required rows="10" class="form-control">
                                                            {{ $about->description }}
                                                        </textarea>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label class="col-md-2 col-form-label">Image </label>
                                                    <div class="col-md-10 item">
                                                      <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)">
                                                    </div>
                                                  </div>
                                
                                
                                                  <div class="mb-3 row">
                                                    <label class="col-md-2 col-form-label"></label>
                                                    <div class="col-md-10">
                                                      <img src="@if($about->featured_image) {{ asset('uploads/'.$about->featured_image) }} @endif" id="imgshow" style="width: 200px; max-height: 200px"/>
                                                    </div>
                                                  </div>

                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="text-center mt-4">
                                                            <input type="submit" id="save"
                                                                class="btn btn-primary waves-effect waves-light"
                                                                value="Submit">
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
        $(document).ready(function() {
            ClassicEditor
                .create(document.querySelector('#Description'))
                .catch(error => {
                    console.error(error);
                });

            $("#PostForm").submit(function(event) {
                if (validator.checkAll($(this))) {
                    const data = new FormData(this)
                    const url = $(this).attr('action')

                    event.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });
                    window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData) {
                        toastr.success(responseData.msg);
                        location.reload()
                    }, function(error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#PostForm", error.result);
                    });
                }
                return false;
            });
        })

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
@endsection
