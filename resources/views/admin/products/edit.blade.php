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
                                        <h4 class="text-primary">Edit Product</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="post" action="{{route('products.update')}}" novalidate
                                    enctype="multipart/form-data" id="UpdateProductForm" class="eq-section">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group item">
                                                        <label for="ProductName" class="required-label">Product
                                                            Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ $product->name }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group item">
                                                        <label for="ThumbnailImage">
                                                            Thumbnail Image</label>
                                                        <input type="file" class="form-control" name="thum_image">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group item">
                                                        <img src="{{ asset('uploads/products/'. $product->image) }}"
                                                            alt="{{ $product->name }}"
                                                            style="height: 250px; width: 250px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-6 col-md-6 col-sm-12">--}}
                                            {{-- <div class="form-group item">--}}
                                                {{-- <div class="input-images item"></div>--}}
                                                {{-- </div>--}}
                                            {{-- </div>--}}
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group item">
                                                <label for="Vendor" class="required-label">Vendors</label>
                                                <select name="vendor" id="Vendor" required class="form-control">
                                                    <option value="" selected disabled>Select</option>
                                                    @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}" {{ $vendor->id ==
                                                        $product->vendor_id ? 'selected' : '' }}>{{ $vendor->name }}
                                                    </option>
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
                                                    @include('selected_product_category')
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group item">
                                                <label for="subCategory" class="required-label">Sub Category</label>
                                                <select name="subCategory" id="subCategory" required
                                                    class="form-control">

                                                    @foreach($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}" @if ($product->
                                                        sub_category_id ==$subCategory->id)
                                                        selected
                                                        @endif>{{ $subCategory->name }}
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
                                                    @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" <?=$brand->id == $product->brand_id
                                                        ? 'selected' : '' ?> >{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group item">
                                                <label for="ProductSKU" class="required-label">SKU</label>
                                                <input type="text" name="prod_sku" class="form-control"
                                                    value="{{ $product->sku }}" required readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-12">
                                            <div class="form-group item">
                                                <label class="required-label">Description</label>
                                                <textarea class="form-control" required id="Description"
                                                    name="description">
                        {!! $product->description !!}
                      </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row mt-1">
                                        <div class="col-12">
                                            <div class="form-group item">
                                                <label class="required-label">Specifications</label>
                                                <textarea class="form-control" required id="specifications"
                                                    name="specifications">
                        {!! $product->specifications !!}
                      </textarea>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <hr>

                                    <div class="row mt-1">
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" <?=($productAttributes->count() > 0) ? 'checked'
                                                : ''; ?> name="attributeCheckbox"
                                                class="form-check-input"
                                                onchange="showHideEditDiv()" id="EditAttributeCheck">
                                                <label class="form-check-label" for="EditAttributeCheck">
                                                    Add Attributes and
                                                    Value
                                                    <?= $productAttributes->count() ? '<span class="text-danger">(Uncheck delete all product variant)</span>' : '' ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="show_in_home" class="form-check-input"
                                                    id="show_in_home" {{ $product->show_in_home ? 'checked' : '' }}>
                                                <label class="form-check-label ml-3" for="show_in_home">
                                                    <h4> show in home</h4>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="top_rated" class="form-check-input"
                                                    id="top_rated" {{ $product->top_rated ? 'checked' : '' }}>
                                                <label class="form-check-label ml-3" for="top_rated">
                                                    <h4>Is Top Rated</h4>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="is_featured" class="form-check-input"
                                                    id="is_featured" {{ $product->is_featured ? 'checked' : '' }}>
                                                <label class="form-check-label ml-3" for="is_featured">
                                                    <h4>Is Featured</h4>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group ml-4">
                                                <input type="checkbox" name="on_sale" class="form-check-input"
                                                    id="on_sale" {{ $product->on_sale ? 'checked' : '' }}>
                                                <label class="form-check-label ml-3" for="on_sale">
                                                    <h4>Is on Sale</h4>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-1" id="ProductDiv">

                                        <div id="AttributeProduct" class="col-12" style="display: none;">

                                            @if($productAttributes->count())
                                            @foreach($productAttributes as $item)

                                            <div id="attribute_section{{ $item->id }}">
                                                <input type="hidden" value="1" name="attribute"
                                                    class="form-check-input">
                                                <input id="attributeAS{{ $item->id }}" type="hidden"
                                                    name="attributeId[]" value="{{ $item->id }}" readonly required>
                                                <div class="row mt-1">
                                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                                        <div class="form-group">
                                                            <label class="required-label">Attribute</label>
                                                            <select class="form-control AttributeId"
                                                                name="attribute_id[]" required
                                                                id="attributeID{{ $item->id }}">
                                                                @foreach($attributes as $attribute)
                                                                <option <?=$attribute->id == $item->attribute_id ?
                                                                    'selected' : '' ?>
                                                                    value="{{ $attribute->id }}">{{ $attribute->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <label class="required-label">Value</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $item->value }}" name="attribute_value[]"
                                                                id="attributeValue{{ $item->id }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <label class="required-label">SKU</label>
                                                            <input type="text" class="form-control" readonly disabled
                                                                value="{{ $item->sku }}"
                                                                id="attributeSKU{{ $item->id }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <label class="required-label">Cost Price</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $item->price[0]->cost_price ?? 0 }}"
                                                                name="attr_cost_price[]"
                                                                id="attributeCostPrice{{ $item->id }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <label class="required-label">Selling Price</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $item->price[0]->price?? 0 }}"
                                                                name="attr_selling_price[]"
                                                                id="attributeSellingPrice{{ $item->id }}">
                                                        </div>
                                                    </div>


                                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <label class="required-label">Quantity</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $item->price[0]->quantity?? 0 }}"
                                                                name="attr_qty[]" id="attributeQuantity{{ $item->id }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group item">
                                                                    <label for="ThumbnailImage">
                                                                        Attribute Image</label>
                                                                    <input type="file" class="form-control"
                                                                        name="attr_image[]"
                                                                        id="attributeImage{{ $item->id }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6 mb-5">
                                                                <div class="form-group item">
                                                                    <img src="{{ asset('uploads/products/'. $item->featured_image) }}"
                                                                        alt="{{ $product->name }}"
                                                                        style="height: 90px; width: 90px;">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-1 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <input name="attr_status[]" type="checkbox"
                                                                class="form-check-input mt-6 ml-5"
                                                                id="Status-{{ $item->id }}" <?=$item->is_active ?
                                                            'checked' : '' ?>>
                                                            <label class="form-check-label ml-5"
                                                                for="Status-{{ $item->id }}">Is
                                                                Active</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-md-3 col-sm-12">
                                                        <div class="form-group item">
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="deleteAttribute({{ $item->id }})"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>

                                                    {{-- <div class="col-lg-1 col-md-3 col-sm-12">--}}
                                                        {{-- <div class="form-group item">--}}
                                                            {{-- <button type="button" class="btn btn-info"
                                                                onclick="updateAttribute({{ $item->id }})">Update</button>--}}
                                                            {{-- </div>--}}
                                                        {{-- </div>--}}
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif

                                            <div class="row mt-1" id="EditVarientDiv">
                                                <div class="form-group">
                                                    <button type="button" id="newAttributeModal"
                                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                                        Add Variant
                                                    </button>
                                                </div>
                                            </div>
                                        </div>


                                        <div id="DefaultProduct" class="col-12" style="display: none;">
                                            <div class="row">

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group item">
                                                        <label for="Price">Cost Price <span class="text-danger">(Fill if
                                                                you delete all attribute)</span></label>
                                                        <input type="number" class="form-control" name="cost_price"
                                                            value="{{ getCostPrice($product->id) }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group item">
                                                        <label for="Price">Selling Price <span class="text-danger">(Fill
                                                                if you delete all attribute)</span></label>
                                                        <input type="number" class="form-control" name="prod_price"
                                                            value="{{ getProductPrice($product->id) }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group item">
                                                        <label for="QTy" class="">QTY <span class="text-danger">(Fill if
                                                                you delete all attribute)</span></label>
                                                        <input type="number" class="form-control" name="prod_qty"
                                                            value="{{ getProductQuantity($product->id) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mt-1">
                                        <div class="form-group ml-5">
                                            <input name="status" type="checkbox" class="form-check-input" id="Status"
                                                <?=$product->is_active ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="Status">Is Active</label>
                                        </div>
                                    </div>
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
<div class="modal" tabindex="-1" role="dialog" id="addVariantModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" id="addVariantModalContent"></div>
    </div>
</div>
@endsection

@section('script')
<script>
    function showHideEditDiv() {
            const check = $('#EditAttributeCheck').is(':checked');

            if (check) {
                $('#AttributeProduct').fadeIn();

                $('#DefaultProduct').fadeOut();
            } else {
                $('#AttributeProduct').fadeOut();

                $('#DefaultProduct').fadeIn();
            }
        }

        function deleteAttribute(attribute_id){
            $.ajax({
                url:"{{ route('deleteAttribute') }}",
                data:{
                    attribute_id
                },
                success:function (responseData){
                    if(responseData.response){
                        $('#attribute_section'+attribute_id).remove();
                        toastr.success(responseData.msg)
                    }
                }
            })
        }

        function updateAttribute(attribute_id){
            const formData = new FormData($("#attributeUpdateForm"+attribute_id)[0]);

            const url = $("#attributeUpdateForm"+attribute_id).attr('action')

            event.preventDefault();


            window.ct.getData(url, formData, $("#save")).then(function (responseData) {
                toastr.success(responseData.msg);
            }, function (error) {
                toastr.error(error.msg);
                window.ct.populateFormError("#UpdateProductForm", error.result)
            })
        }
        $(document).ready(function () {

            showHideEditDiv();

            $('.input-images').imageUploader()
            ClassicEditor
                .create(document.querySelector('#Description'))
                .catch(error => {
                    console.error(error);
                })
                showHideEditDiv();


ClassicEditor
    .create(document.querySelector('#specifications'))
    .catch(error => {
        console.error(error);
    })
            $('#ProductCategory').select2()
            $('#subCategory').select2()
            $('#Vendor').select2()
            $('#ProductBrand').select2()
            $('.AttributeId').select2()

            $('#newAttributeModal').on('click', function () {
                const product_id = "{{ $product->id }}"
                $.ajax({
                    url: "{{ route('product.variant.form') }}",
                    type: 'GET',
                    data: {
                        product_id
                    },
                    success: function (response) {
                        if (response.response) {
                            $('#addVariantModal #addVariantModalContent').html('');
                            $('#addVariantModal #addVariantModalContent').html(response.result);
                            $('#addVariantModal').modal('show');
                        } else {
                            toastr.error(response.msg)
                            location.reload()
                        }
                    }
                })

            });


            $("#UpdateProductForm").submit(function (event) {
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
                        window.ct.populateFormError("#UpdateProductForm", error.result)
                    })
                }
                return false;
            })
        })
      
</script>

@endsection