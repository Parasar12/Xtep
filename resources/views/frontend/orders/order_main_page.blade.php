<div class="table-content table-responsive">
    <div class="container">
        <div class="row">
            @if($orders->count())
                @php
                    $total_products = 0;
                @endphp
                @foreach ($orders as $order)

                    @php
    $total = 0;
                        $total_order = \App\Models\Order::where([
                            ['user_id',\auth()->user()->id],
                            ['created_at',$order->created_at]
                        ])->get();
                        foreach ($total_order as $item){
                            $total += $item->orderDetails->count();
                        }
                    @endphp
    
                    <div class="col-12 mb-3">
                        <div class="card p-3 bg-light align-middle">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <h4 class="mb-0 fw-bold text-dark">{{ date('Y-m-d', strtotime($order->created_at)) }}</h4>
                                </div>
                                
                                <div class="col-md-3">
                                    <h4 class="mb-0">Total Order:</h4>
                                </div>
                                
                                <div class="col-md-3">
                                    <span class="fw-bold ">{{ $total }}</span>
                                </div>
                                
                                <div class="col-md-3 text-end">
                                    <a href="javascript:void(0);" onclick="viewOrders('{{ $order->created_at }}')" class="btn btn-outline-primary btn-sm">
                                        View Orders
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
    
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        No Order's Found
                    </div>
                </div>
            @endif
        </div>
    </div>
    
</div>