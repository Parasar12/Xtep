@extends('layouts.app')

@section('content')

<style>
  .select2-dropdown {
    z-index: 99999;
  }
</style>

<!-- Begin page -->
<div id="layout-wrapper">

  @include('admin.includes.header')

  @include('admin.includes.left_sidebar')

  <div class="main-content">

    <div class="page-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Flash Sales
                  <button type="button" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2" id="addFlashSale">
                    Add New
                  </button>
                </h4>

                <div class="table-responsive">
                  <table id="flashSaleTbl" class="table table-bordered dt-responsive nowrap w-100" width="100%">
                    <thead class="table-light">
                      <tr>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Start Date</th>
                        <th class="align-middle">End Date</th>
                        <th class="align-middle">Status</th>
                        <th class="align-middle edit_brand">Action</th>
                      </tr>
                    </thead>
                  </table>
                </div>
                <!-- end table-responsive -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<!-- END layout-wrapper -->



<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- Brand Modal -->
<div class="modal" tabindex="-1" id="addFlashSaleModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="addFlashSaleModalContent">
    </div>
  </div>
</div>
<!-- end modal -->

@endsection

@section('script')

<script type="text/javascript">

  var FlashSaleModule = {};

  function addProduct(e) {

    var elem = document.querySelector('.main_div .mainSet');
    var clone = elem.cloneNode(true);

    $(".cloning_div").append(clone);
    $('.cloning_div .js-example-basic-single').select2({
      dropdownParent: $("#addFlashSaleModal .modal-content")
    });
  }

  $("body").on("click", ".btn-remove", function() {
    $(this).parents(".control-group").remove();
  });


  $(document).ready(function() {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });

    FlashSaleModule.flashSaleListTbl = $('#flashSaleTbl').DataTable({
      dom: 'lfrtip',
      destroy: true,
      processing: true,
      language: {
        processing: '<span style="color:white;">Processing...</span>'
      },
      serverSide: true,
      ajax: {
        url: "{{route('flashSales.get')}}",
        type: "POST",        
        data: function(postData) {}
      },
      scrollY: "50vh",
      scrollX: true,
      lengthMenu: [[5, 10, 20, -1],[5, 10, 20, "All"]],
      order: [[1, "DESC"]],
      columns: [
      {data: 'name'},
      {data: 'start_date'},
      {data: 'end_date'},
      {data: 'status'},
      {data: "action"},
      ],
      columnDefs: [{"orderable": false, "targets": [2]}],
    });


    $('#flashSaleTbl').on('click', '.delete_flash_sales', function(){
			var saleId = $(this).data('id');

			bootbox.confirm({
				title: "Delete Flash Sales ?",
				message: "Do you want to delete this Flash Sales ?",
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i> Cancel'
					},
					confirm: {
						label: '<i class="fa fa-check"></i> Confirm'
					}
				},
				callback: function (result) {
					if (result) {
						var data = [];
						data.push({name:'saleId', value:saleId});
						var url = "{{ route('flash_sales.delete') }}";

						window.ct.postData(url ,data).then(function(responseData){
							FlashSaleModule.flashSaleListTbl.ajax.reload(null, false);
							toastr.success(responseData.msg);
						},
						function(error){
							toastr.error(error.msg);          
						});
					}
					else{
						FlashSaleModule.flashSaleListTbl.ajax.reload(null, false);
					}
				}
			});
		});

    $('#addFlashSale').on('click', function() {
      $('#addFlashSaleModal #addFlashSaleModalContent').html('');
      $('#addFlashSaleModal #addFlashSaleModalContent').html($('#add_flash_sale').html());
      $('#addFlashSaleModal').modal('show');
    });
  });

  function getSellingPriceAndQuantity(e) {
    var id = $(e).find('option:selected').val();
    $(e).closest('.control-group').find('.selling_price').text('');
    $(e).closest('.control-group').find('.quantity').text('');

    $.get("{{ url('/product/price_quantity/')}}" + '/' + id, function(data) {
      $(e).closest('.control-group').find('.selling_price').text('Selling price is Rs.' + data.price);
      $(e).closest('.control-group').find('.quantity').text('Available quantity is ' + data.quantity);
    });

  }
</script>

<script type="text/template" id="add_flash_sale">
  <div class="modal-header">
    <h5 class="modal-title" id="flashModalLabel">Add Flash Sales</h5>
  </div>

  <div class="modal-body">
    <form action="{{route('flash-sales.store')}}" method="post" id="post_flash" novalidate class="eq-section">
      @csrf

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group item">
                    <label class="required-label">Campaign Name </label>
                    <input class="form-control" type="text" placeholder="Please enter campaign name" name="name" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group item">
                    <label class="required-label">Campaign Start Date </label>
                    <input class="form-control" type="date" placeholder="Campaign Start Date" name="start_date" id="start_date" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group item">
                    <label class="required-label">Campaign End Date </label>
                    <input class="form-control" type="date" placeholder="Campaign End Date" name="end_date" id="end_date" required>
                  </div>
                </div>
              </div>

              <div class="cloning_div"></div>

              <div class="main_div" style="display:none">

                <div class="control-group mainSet" style="margin-top:10px">
                  <hr>
                  <div class="mb-3 row">
                    <div class="input-group-btn"> 
                      <button class="btn btn-danger btn-remove" style="float: right;" type="button"><i class="fa fa-trash"></i> Remove</button>
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="products" class="col-md-2 required-label">Product </label>
                    <div class="col-md-10 item">
                      <select class="js-example-basic-single" name="products[]" onchange="getSellingPriceAndQuantity(this)" required>
                        <option value="" selected="selected" disabled="disabled">Select</option>
                        @if(!$products->isEmpty())
                        @foreach($products as $product)
                        <option value="{{$product->id}}">{{$product->name}}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <label for="price" class="col-md-2 required-label">Price </label>
                    <div class="col-md-4 item">
                      <div class="selling_price"></div>
                      <input class="form-control" type="number"  placeholder="Please enter price" name="price[]" required>
                    </div>

                    <label for="quantity" class="col-md-2 required-label">Quantity </label>
                    <div class="col-md-4 item">
                      <div class="quantity"></div>
                      <input class="form-control" type="number" required placeholder="Please enter quantity" name="quantity[]">
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-3 row">
                <div class="col-md-4">
                  <div class="input-group control-group form-group" >
                    <div class="input-group-btn"> 
                      <button class="btn btn-success btn-add-more" onclick="addProduct(event)" type="button"><i class="glyphicon glyphicon-plus"></i>Add Product</button>
                    </div>
                  </div>
                </div>
              </div>

              
              <div class="row">
                <div class="col-md-6">
                  <div class="text-center mt-4">
                    <button style="float: left;" id="save" type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="text-center mt-4">
                    <button type="button" style="float: right;" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div> 
      </div>  
    </form>                          	 
  </div>
</script>

<script>

  $(document).on('submit', '#post_flash', function(e) {
e.preventDefault();
    if(validator.checkAll($(this))) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      });

      var data = new FormData(this);        
      var url = $(this).attr('action');

      window.ct.postDataMultiPartForm(url, data, $("#save")).then(function(responseData){
        $('#addFlashSaleModal').modal('hide'); 
        toastr.success(responseData.msg);
        FlashSaleModule.flashSaleListTbl.ajax.reload(null, false);
      }, function(error){
        toastr.error(error.msg);          
        window.ct.populateFormError("#addSliderForm", error.result);
      });
    }
    return false;
  });

</script>
@endsection