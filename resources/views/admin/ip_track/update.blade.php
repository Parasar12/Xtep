@extends('layouts.app')
@section('content')
    <style>
        input[type=text], input[type=number] {
            height: 30px;
        }
    </style>
    <div id="layout-wrapper">
        @include('admin.includes.header')
        @include('admin.includes.left_sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-right">
                                                <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                                                    <i class="fa fa-backward">Back</i>
                                                </a>
                                            </div>
                                            <h4 class="text-primary">Update Products</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{route('products.sku.update')}}" id="PostProductForm"
                                          novalidate class="eq-section">
                                        @csrf
                                        <div class="row mt-1">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="SKU" class="required-label">SKU</label>
                                                    <select name="sku" id="SKU" required
                                                            class="form-control">
                                                        <option value="" selected disabled>Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div id="ProductDiV"></div>
                                        <div class="row mt-1">
                                            <button type="submit" id="save" class="btn btn-primary btn-rounded">
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- END layout-wrapper -->
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#SKU').select2({
                multiple: false,
                minimumInputLength: 1,
                allowClear: true,
                placeholder: "Search SKU",
                ajax: {
                    delay: 350,
                    transport: function (params, success, failure) {
                        window.ct.getData('{{ route('products.sku.search') }}', [{
                            name: 'keyword',
                            value: params.data.term
                        }], false).then(function (responseData) {
                            success(responseData.result);
                        }, function (error) {
                            $('#select2-SKU-results').html('<li role="treeitem" aria-live="assertive" class="select2-results__option select2-results__message">' + error.msg + '</li>');
                        });
                    }
                }
            })
            $('#SKU').on('change', function () {
                const sku = $(this).val()
                $.ajax({
                    url: "{{ route('product.sku.product.form') }}",
                    type: 'GET',
                    data: {
                        sku
                    },
                    success: function (response) {
                        if (response.response) {
                            $('#ProductDiV').html('')
                            $('#ProductDiV').html(response.result)
                        } else {
                            $('#ProductDiV').html('')
                            toastr.error(response.msg)
                        }
                    }
                })
            })

            $("#PostProductForm").submit(function (event) {
                if (validator.checkAll($(this))) {
                    const data = new FormData(this)
                    const url = $(this).attr('action')

                    event.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });

                    window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                        toastr.success(responseData.msg);
                        location.replace('{{ route('products.index') }}')
                    }, function (error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#PostProductForm", error.result)
                    })
                }
                return false;
            })
        })
    </script>

@endsection
