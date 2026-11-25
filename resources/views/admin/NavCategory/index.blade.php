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
                        <div class="col-12">
                            <h3>Web NavBar Category</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <form method="post" novalidate class="eq-section" action="{{route('nav.category.index')}}"
                                  id="PostForm">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3 row">
                                                    <label for="Category1"
                                                           class="col-md-2 col-form-label required-label">Category</label>
                                                    <div class="col-md-10 item">
                                                        <select name="category_1" class="form-control Category"
                                                                required>
                                                            <option value="" disabled>Select</option>
                                                            @foreach($categories as $category)
                                                                <option
                                                                    value="{{ $category->id }}" <?= $category->id == $data->category_1 ? 'selected' : '' ?>>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3 row">
                                                    <label for="Category2"
                                                           class="col-md-2 col-form-label required-label">Category</label>
                                                    <div class="col-md-10 item">
                                                        <select name="category_2" class="form-control Category"
                                                                required>
                                                            <option value="" disabled>Select</option>
                                                            @foreach($categories as $category)
                                                                <option
                                                                    value="{{ $category->id }}" <?= $category->id == $data->category_2 ? 'selected' : '' ?>>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3 row">
                                                    <label for="Category3"
                                                           class="col-md-2 col-form-label required-label">Category</label>
                                                    <div class="col-md-10 item">
                                                        <select name="category_3" class="form-control Category"
                                                                required>
                                                            <option value="" disabled>Select</option>
                                                            @foreach($categories as $category)
                                                                <option
                                                                    value="{{ $category->id }}" <?= $category->id == $data->category_3 ? 'selected' : '' ?>>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3 row">
                                                    <label for="Category4"
                                                           class="col-md-2 col-form-label required-label">Category</label>
                                                    <div class="col-md-10 item">
                                                        <select name="category_4" class="form-control Category"
                                                                required>
                                                            <option value="" disabled>Select</option>
                                                            @foreach($categories as $category)
                                                                <option
                                                                    value="{{ $category->id }}" <?= $category->id == $data->category_4 ? 'selected' : '' ?>>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
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
        $(document).ready(function () {

            $('.Category').select2()

            $("#PostForm").submit(function (event) {
                if (validator.checkAll($(this))) {
                    const data = $(this).serialize()
                    const url = $(this).attr('action')

                    event.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });
                    window.ct.postData(url, data, $("#save")).then(function (responseData) {
                        toastr.success(responseData.msg);
                        location.reload()
                    }, function (error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#PostForm", error.result);
                    });
                }
                return false;
            });
        })
    </script>
@endsection
