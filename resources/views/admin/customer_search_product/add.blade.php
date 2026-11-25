@extends('layouts.app')
@section('content')
    <style>
        input[type=text], input[type=number] {
            height: 30px;
        }

        .ck-editor__editable_inline {
            min-height: 200px;
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
                                            <h4 class="text-primary">New Products</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{route('products.store')}}"
                                          enctype="multipart/form-data" id="PostProductForm" novalidate
                                          class="eq-section">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group item">
                                                            <label for="ProductName" class="required-label">Product
                                                                Name</label>
                                                            <input type="text" class="form-control" name="name"
                                                                   placeholder="Product Name" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group item">
                                                            <label for="ThumbnailImage" class="required-label">
                                                                Thumbnail Image</label>
                                                            <input type="file" class="form-control" name="thum_image"
                                                                   required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group item">
                                                    <div class="input-images item"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="ProductCategory" class="required-label">Category</label>
                                                    <select name="category" id="ProductCategory" required
                                                            class="form-control">
                                                        <option value="" selected disabled>Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="ProductBrand">Brand</label>
                                                    <select name="brand" id="ProductBrand" class="form-control">
                                                        <option value="" selected disabled>Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="ProductSKU" class="required-label">SKU</label>
                                                    <input type="text" name="prod_sku" class="form-control"
                                                           placeholder="Unique Product SKU" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <div class="form-group item">
                                                    <label class="required-label">Description</label>
                                                    <textarea class="form-control" required id="Description"
                                                              name="description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <div class="form-group ml-4">
                                                    <input type="checkbox" name="attribute" class="form-check-input"
                                                           onchange="showHideDiv()" id="AttributeCheck">
                                                    <label class="form-check-label" for="AttributeCheck">
                                                        Add Attributes and Value
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-1" id="ProductDiv"></div>

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
        function showHideDiv() {
            const check = $('#AttributeCheck').is(':checked')
            if (check) {
                const html = $('#DIV2').html()
                $('#DefaultProduct').remove()
                $('#ProductDiv').html(html)
                init()
            } else {
                const product = $('#DIV1').html()
                $('#AttributeProduct').remove()
                $('#ProductDiv').html(product)
            }
        }

        function init() {
            $('.AttributeId').select2()
            $('.AttributeValue').select2({
                multiple: true,
                allowClear: true,
                placeholder: "Attribute Value",
                tags: true,
                tokenSeparators: [','],
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (term === '')
                        return null;

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    }
                },
            })
        }

        function addNewDiv() {
            const div = $('#NewAttributeDiv').html()
            $('#AppendAttributeDiv').append(div)
            init()
        }


        $(document).ready(function () {
            $('.input-images').imageUploader()
            ClassicEditor
                .create(document.querySelector('#Description'))
                .catch(error => {
                    console.error(error);
                })

            const div1 = $('#DIV1').html()
            $('#ProductDiv').html(div1)

            $('#ProductCategory').select2({
                multiple: false,
                minimumInputLength: 1,
                allowClear: true,
                placeholder: "Search Category",
                ajax: {
                    delay: 350,
                    transport: function (params, success, failure) {
                        window.ct.getData('/search-category', [{
                            name: 'keyword',
                            value: params.data.term
                        }], false).then(function (responseData) {
                            success(responseData.result);
                        }, function (error) {
                            $('#select2-ProductCategory-results').html('<li role="treeitem" aria-live="assertive" class="select2-results__option select2-results__message">' + error.msg + '</li>');
                        });
                    }
                }
            })
            $('#ProductBrand').select2({
                multiple: false,
                minimumInputLength: 1,
                allowClear: true,
                placeholder: "Search Brand",
                ajax: {
                    delay: 350,
                    transport: function (params, success, failure) {
                        window.ct.getData('/search-brand', [{
                            name: 'keyword',
                            value: params.data.term
                        }], false).then(function (responseData) {
                            success(responseData.result);
                        }, function (error) {
                            $('#select2-ProductBrand-results').html('<li role="treeitem" aria-live="assertive" class="select2-results__option select2-results__message">' + error.msg + '</li>');
                        });
                    }
                }
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

            $(document).on('click', '#removeAttributeDiv', function () {
                $(this).parent().parent().parent().parent().remove()
            })
        })
    </script>

    <script type="text/html" id="DIV1">
        <div id="DefaultProduct" class="col-12">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label for="Price" class="required-label">Price</label>
                        <input type="number" class="form-control" name="prod_price" required>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label for="QTy" class="required-label">QTY</label>
                        <input type="number" class="form-control" name="prod_qty" required>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/html" id="DIV2">
        <div id="AttributeProduct" class="col-12">
            <div class="row" id="AppendAttributeDiv">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group item">
                                <label class="required-label">Attribute</label>
                                <select class="form-control AttributeId"
                                        name="attribute_id[]" required>
                                    <option value="" selected disabled>Select</option>
                                    @foreach($attributes as $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group item">
                                <label class="required-label">Value</label>
                                <input type="text" class="form-control AttributeValue" name="attribute_value[]"
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group item">
                                <label class="required-label">SKU</label>
                                <input type="text" name="sku[]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group item">
                                <label class="required-label">Price</label>
                                <input type="number" name="price[]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group item">
                                <label class="required-label">QTY</label>
                                <input type="number" name="qty[]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group item">
                                <label for="ThumbnailImage" class="required-label">
                                    Attribute Image</label>
                                <input type="file" class="form-control" name="attribute_image[]"
                                       >
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <button type="button" onclick="addNewDiv()" class="btn btn-sm">
                        <i class="fa fa-plus-circle"></i>
                        Add more attributes
                    </button>
                </div>
            </div>
        </div>
    </script>

    <script type="text/html" id="NewAttributeDiv">
        <div class="col-12">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label class="required-label">Attribute</label>
                        <select class="form-control AttributeId"
                                name="attribute_id[]" required>
                            <option value="" selected disabled>Select</option>
                            @foreach($attributes as $attribute)
                                <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label class="required-label">Value</label>
                        <input type="text" class="form-control AttributeValue" name="attribute_value[]"
                               required>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label class="required-label">SKU</label>
                        <input type="text" name="sku[]" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label class="required-label">Price</label>
                        <input type="number" name="price[]" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label class="required-label">QTY</label>
                        <input type="number" name="qty[]" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12">
                    <div class="form-group item">
                        <label for="ThumbnailImage" class="required-label">
                            Attribute Image</label>
                        <input type="file" value="" class="form-control" name="attribute_image[]"
                        >
                    </div>
                </div>
                <div class="col-lg-1 col-md-2 col sm-12">
                    <div class="form-group">
                        <button type="button" id="removeAttributeDiv" class="btn" style="color: #e20e0e">
                            <i class="fa fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </script>
@endsection
