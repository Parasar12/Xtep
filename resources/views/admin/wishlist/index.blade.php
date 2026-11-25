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
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Product In Wishlist
                </h4>

                <div class="table-responsive">
                  <table id="wishListTbl" class="table table-bordered dt-responsive nowrap w-100" style="width:100%">
                    <thead class="table-light">
                      <tr>
                        <th class="align-middle">S. No.</th>
                        <th class="align-middle">Product Name</th>
                        <th class="align-middle">Total In Wishlist</th>
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
@endsection

@section('script')

<script type="text/javascript">

  var WishlistModule = {};

  $(document).ready(function() {

    WishlistModule.wishlistListTbl = $('#wishListTbl').DataTable({
      dom: 'lfrtip',
      destroy: true,
      processing: true,
      language: {
        processing: '<span style="color:white;">Processing...</span>'
      },
      serverSide: true,
      ajax: {
        url: "{{route('product.wishlist.get')}}",
        type: "GET",
        data: function(postData) {}
      },
      scrollY: "50vh",
      scrollX: true,
      lengthMenu: [[10, 20, -1],[10, 20, "All"]],
      order: [[1, "DESC"]],
      columns: [
      {data: 'id'},
      {data: 'name'},
      {data: 'total'},
      ],
      columnDefs: [{"orderable": false,"targets": [2]}],
    });
  });
</script>

@endsection