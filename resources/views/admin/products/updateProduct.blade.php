<div class="row">
    <input type="hidden" name="product" value="{{ $product->id }}" readonly required>
    <div class="col-lg-3 col-md-3 col-sm-12">
        <div class="form-group item">
            <label for="Product" class="required-label">Product</label>
            <input type="text" class="form-control" value="{{ $product->name }}" readonly disabled>
        </div>
    </div>
    @if(isset($attribute))
        <input type="hidden" name="attribute" value="{{ $attribute->id }}" readonly required>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group item">
                <label for="Attribute" class="required-label">Attribute</label>
                <input type="text" class="form-control" readonly disabled
                       value="[{{ $attribute->attribute->name }}]--{{ $attribute->value }}">
            </div>
        </div>
    @endif
    <div class="col-lg-3 col-md-3 col-sm-12">
        <div class="form-group item">
            <label for="QTY" class="required-label">QTY</label>
            <input type="number" class="form-control" name="qty" value="{{ $product->productMasters[0]->quantity }}" required>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12">
        <div class="form-group item">
            <label for="Price" class="required-label">Price</label>
            <input type="number" class="form-control" name="price" value="{{ $product->productMasters[0]->price }}" required>
        </div>
    </div>
</div>
