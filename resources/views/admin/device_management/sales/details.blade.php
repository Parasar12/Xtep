<div class="modal-body">
    @if ($device_accessories)
        <div class="py-2 mt-3">
            <h1 class="text-center" style="color: blue" class="font-size-15 fw-bold">Device Accessories</h1>
        </div>
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h4>Name</h4>
                    </div>
                    <div class="col-lg-5">
                        <h4>Image</h4>
                    </div>
                </div>
                <div class="col-lg-12 ml-4">
                    @foreach ($device_accessories as $item)
                        @php
                            $accessories = DB::table('device_accessories_master')
                            
                                ->where('id', $item)
                                ->get();
                            
                        @endphp

                        @foreach ($accessories as $a)
                            <div class="row mt-3">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-7">

                                            <h5>->{{ $a->name }}</h5>

                                        </div>

                                        <div class="col-lg-5">

                                            <img style="height: 100px"
                                                src="{{ asset('uploads/device_management/' . $a->featured_image) }}"
                                                alt="asjkd">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    
    @if ($device_problems)
        <div class="py-2 mt-3">
            <h1  class="text-center" style="color: blue" class="font-size-15 fw-bold">Device Problems</h1>
        </div>
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h4>Problem Name</h4>
                    </div>
                    <div class="col-lg-5">
                        <h4>Image</h4>
                    </div>
                </div>
                <div class="col-lg-12 ml-4">
                    @foreach ($device_problems as $item)
                        @php
                            $problems = DB::table('device_problem_master')
                                ->where('id', $item)
                                ->get();
                            
                        @endphp

                        @foreach ($problems as $a)
                            <div class="row mt-3">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-7">

                                            <h5>->{{ $a->problem_name }}</h5>

                                        </div>

                                        <div class="col-lg-5">

                                            <img style="height: 100px"
                                                src="{{ asset('uploads/device_management/' . $a->featured_image) }}"
                                                alt="asjkd">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    @if ($device_conditions)
        <div class="py-2 mt-3">
            <h1 class="text-center" style="color: blue" class="font-size-15 fw-bold">Device Conditions</h1>
        </div>
        <div class="row">
            <div class="container">
                <div class="col-lg-12 ml-4">
                    @foreach ($device_conditions as $val)
                        @php
                            $data = DB::table('device_condition_master')
                            
                                ->where('id', $val->condition_id)
                                ->get();
                            
                        @endphp
                        @foreach ($data as $key => $value)
                            <div class="row mt-3">
                                <div class="container">
                                    <div class="col-lg-10">
                                        <h5>-> {{ $value->question }}</h5>
                                        <h6>-{{ $value->hint }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <div class="d-print-none">
        <div class="float-end">

            <a class="btn btn-secondary" data-bs-dismiss="modal" style="float: right;">Back</a>

        </div>
    </div>
</div>
