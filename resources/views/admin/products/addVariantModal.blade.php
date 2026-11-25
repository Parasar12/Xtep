<div class="modal-header">
    <h5 class="modal-title" id="VariantModalLabel">Add Variant</h5>
</div>

<div class="modal-body">
    <form method="post" action="{{ route('product.variant.form') }}" id="VariantForm" enctype="multipart/form-data" novalidate class="eq-section">
        @csrf
        <input type="hidden" value="{{ $product->id }}" name="product">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">Attribute</label>
                    <select class="form-control AttributeId"
                            name="attribute_id" required>
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
                    <input type="text" class="form-control AttributeValue" name="attribute_value"
                           required>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">SKU</label>
                    <input type="text" name="sku" class="form-control" required>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">Cost Price</label>
                    <input type="number" name="cost_price" class="form-control" required>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">Selling Price</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group item">
                    <label class="required-label">QTY</label>
                    <input type="number" name="qty" class="form-control" required>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="form-group">
                    <label>Attribute Image</label>
                    <input type="file" name="attribute_image" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <button type="submit" id="save" class="btn btn-primary btn-rounded">
                Save
            </button>
            <button type="button" class="btn btn-danger ml-3 btn-rounded" data-bs-dismiss="modal">
                Close
            </button>
        </div>
    </form>
</div>

<script>
    $('.AttributeId').select2()

    $("#VariantForm").submit(function (event) {
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
                location.reload()
            }, function (error) {
                toastr.error(error.msg);
                window.ct.populateFormError("#VariantForm", error.result)
            })
        }
        return false;
    })
</script>
