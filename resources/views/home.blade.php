@extends('layouts.app')

@section('content')
<!-- Begin page -->
<div id="layout-wrapper">
  @include('admin.includes.header')

  @include('admin.includes.left_sidebar')

  <div class="main-content">

    <div class="page-content">
      <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
          <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            </div>
          </div>
        </div>
        <!-- end page title -->

        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-md-3">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Pending</p>
                        <h4 class="mb-0">{{$pendingCount}}</h4>
                      </div>

                      <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                          <span class="avatar-title">
                            <i class="bx bx-copy-alt font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Processing</p>
                        <h4 class="mb-0">{{$processingCount}}</h4>
                      </div>

                      <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                          <span class="avatar-title rounded-circle bg-primary">
                            <i class="bx bx-purchase-tag-alt font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Delivered</p>
                        <h4 class="mb-0">{{$deliveredCount}}</h4>
                      </div>

                      <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                          <span class="avatar-title rounded-circle bg-primary">
                            <i class="bx bx-purchase-tag-alt font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Cancelled</p>
                        <h4 class="mb-0">{{$cancelledCount}}</h4>
                      </div>
                      <div class="flex-shrink-0 align-self-center ">
                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                          <span class="avatar-title rounded-circle bg-primary">
                            <i class="bx bx-archive-in font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Returned</p>
                        <h4 class="mb-0">{{$returnedCount}}</h4>
                      </div>

                      <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                          <span class="avatar-title rounded-circle bg-primary">
                            <i class="bx bx-purchase-tag-alt font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- end row -->
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4>Admin wallet</h4>
            <div class="d-sm-flex flex-wrap">

              <div class="col-md-3">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Revenue</p>
                        <h4 class="mb-0">Rs. {{$revenue}}</h4>
                      </div>

                      <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                          <span class="avatar-title">
                            <i class="bx bx-copy-alt font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="card mini-stats-wid">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Delivery Charge Earnings</p>
                        <h4 class="mb-0">Rs. {{$shippingEarnings}}</h4>
                      </div>

                      <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                          <span class="avatar-title">
                            <i class="bx bx-copy-alt font-size-24"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <div id="stacked-column-chart" class="apex-charts" dir="ltr"></div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Most Ordered Products</h4>
                <div class="table-responsive">
                  <table class="table align-middle table-nowrap mb-0">
                    <thead class="table-light">
                      <tr>

                        <th class="align-middle">Name</th>
                        <th class="align-middle">Image</th>
                        <th class="align-middle">Quantity</th>
                        <th class="align-middle">View Details</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($mostOrderedProducts as $product)
                      <tr>
                        <td>{{$product->name}}</td>
                        <td>
                          @if($product->image)
                          <img src="{{asset('uploads/products/'.$product->image)}}" width="50px" height="50px" />
                          @endif
                        </td>
                        <td>
                          {{$product->total}}
                        </td>

                        <td>
                          <!-- Button trigger modal -->
                          <a href="{{url('product/'.$product->slug)}}" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">
                            View Details
                          </a>
                        </td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
                </div>
                <!-- end table-responsive -->
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Top Customers</h4>
                <div class="table-responsive">
                  <table class="table align-middle table-nowrap mb-0">
                    <thead class="table-light">
                      <tr>

                        <th class="align-middle">Name</th>
                        <th class="align-middle">Email</th>
                        <th class="align-middle">Ordered Amount</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($topUsers as $user)
                      <tr>
                        <td>{{$user->name}}</td>
                        <td>
                          {{$user->email}}
                        </td>

                        <td>
                          Rs. {{$user->total}}
                        </td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
                </div>
                <!-- end table-responsive -->
              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- container-fluid -->
    </div>
    <!-- End Page-content -->


    <footer class="footer">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <script>
              // document.write(new Date().getFullYear())
            </script>
            {{getFooterText()}}
          </div>
          <div class="col-sm-6">
            <div class="text-sm-end d-none d-sm-block">

            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>

</div>
<!-- END layout-wrapper -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>
</body>
@endsection