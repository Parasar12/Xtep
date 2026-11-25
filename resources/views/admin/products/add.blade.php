@extends('layouts.app')
@section('content')
<style>
    input[type=text],
    input[type=number] {
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
                                <form method="post" action="{{route('products.store')}}" enctype="multipart/form-data"
                                    id="PostProductForm" novalidate class="eq-section">
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
                                                    <div class="form-group">
                                                        <label for="ThumbnailImage">
                                                            Thumbnail Image</label>
                                                        <input type="file" class="form-control" name="thum_image">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group item">
                                                <label>Product Images (up to 5)</label>
                                                <small class="d-block text-muted mb-2">Image 1 is used as the thumbnail if no thumbnail is chosen. JPG or PNG, max 2 MB each.</small>
                                                <div class="row">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <div class="col-6 col-md-4 mb-3">
                                                            <label class="product-img-slot w-100 mb-1" for="ProductImage{{ $i }}"
                                                                style="cursor:pointer;border:1px dashed #ced4da;border-radius:6px;background:#f8f9fa;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                                                <img id="ProductImagePreview{{ $i }}" src="" alt=""
                                                                    style="display:none;width:100%;height:100%;object-fit:contain;">
                                                                <span id="ProductImagePlaceholder{{ $i }}" class="text-muted small">Image {{ $i }}</span>
                                                            </label>
                                                            <input type="file" class="form-control form-control-sm product-image-input"
                                                                id="ProductImage{{ $i }}" name="images[]" accept=".jpg,.jpeg,.png"
                                                                data-slot="{{ $i }}">
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group item">
                                                <label for="Vendor" class="required-label">Vendors</label>
                                                <select name="vendor" id="Vendor" required class="form-control">
                                                    <option value="" selected disabled>Select</option>
                                                    @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                                    @endforeach
                                                </select>
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
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group item">
                                                <label for="subCategory" class="required-label">Sub Category</label>
                                                <select name="subCategory" id="subCategory" required
                                                    class="form-control">
                                                    <option value="" selected disabled>Select</option>
                                                    @foreach($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}">{{ $subCategory->name }}
                                                    </option>
                                                    @endforeach
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
                                    {{-- <div class="row mt-1">
                                        <div class="col-12">
                                            <div class="form-group item">
                                                <label class="required-label">Specifications</label>
                                                <textarea class="form-control" required id="specifications"
                                                    name="specifications"></textarea>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <hr>
                                    <div class="row mt-1">
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="attribute" class="form-check-input"
                                                    onchange="showHideDiv()" id="AttributeCheck">
                                                <label class="form-check-label" for="AttributeCheck">
                                                    Add Attributes and Value
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="show_in_home" class="form-check-input"
                                                    id="show_in_home">
                                                <label class="form-check-label ml-3" for="show_in_home">
                                                    <h4> show in home</h4>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="top_rated" class="form-check-input"
                                                    id="top_rated">
                                                <label class="form-check-label ml-3" for="top_rated">
                                                    <h4>Is Top Rated</h4>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="is_featured" class="form-check-input"
                                                    id="is_featured">
                                                <label class="form-check-label ml-3" for="is_featured">
                                                    <h4>Is Featured</h4>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="on_sale" class="form-check-input"
                                                    id="on_sale">
                                                <label class="form-check-label ml-3" for="on_sale">
                                                    <h4>Is on Sale</h4>
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
            $(document).on('change', '.product-image-input', function () {
                const slot = $(this).data('slot')
                const file = this.files && this.files[0]
                if (file) {
                    const reader = new FileReader()
                    reader.onload = e => {
                        $('#ProductImagePreview' + slot).attr('src', e.target.result).show()
                        $('#ProductImagePlaceholder' + slot).hide()
                    }
                    reader.readAsDataURL(file)
                } else {
                    $('#ProductImagePreview' + slot).attr('src', '').hide()
                    $('#ProductImagePlaceholder' + slot).show()
                }
            })
            ClassicEditor
                .create(document.querySelector('#Description'))
                .catch(error => {
                    console.error(error);
                })
                ClassicEditor
                .create(document.querySelector('#specifications'))
                .catch(error => {
                    console.error(error);
                })
            $('#Vendor').select2()
            const div1 = $('#DIV1').html()
            $('#ProductDiv').html(div1)

            $('#ProductCategory').select2()
            $('#subCategory').select2()
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
                    <label for="Price" class="required-label">Cost Price</label>
                    <input type="number" class="form-control" name="prod_cost_price" required>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label for="Price" class="required-label">Selling Price</label>
                    <input type="number" class="form-control" name="prod_selling_price" required>
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
                            <select class="form-control AttributeId" name="attribute_id[]" required>
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
                            <input type="text" class="form-control AttributeValue" name="attribute_value[]" required>
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
                            <label class="required-label">Cost Price</label>
                            <input type="number" name="cost_price[]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group item">
                            <label class="required-label">Selling Price</label>
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
                            <input type="file" class="form-control" name="attribute_image[]">
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
                    <select class="form-control AttributeId" name="attribute_id[]" required>
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
                    <input type="text" class="form-control AttributeValue" name="attribute_value[]" required>
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
                    <label class="required-label">Cost Price</label>
                    <input type="number" name="cost_price[]" class="form-control" required>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">Selling Price</label>
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
                    <input type="file" value="" class="form-control" name="attribute_image[]">
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