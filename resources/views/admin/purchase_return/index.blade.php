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
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="form-group item">
                                                <label for="FiscalYear" class="required-label">Fiscal Year</label>
                                                <select name="fiscal_year" id="FiscalYear" required
                                                        class="form-control">
                                                    <option value="" selected disabled>Select</option>
                                                    @foreach($fiscalYear as $year)
                                                        <option value="{{ $year->id }}" >{{ $year->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-sm-12 mt-3">
                                            <button type="button" id="search" class="btn btn-outline-success">Search</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="float-right">
                                                <button type="button" id="addFAQ"
                                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                                    Add Purchase Return
                                                </button>
                                            </div>
                                            <h4 class="card-title mb-4">Purchase Return </h4>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="FAQTbl" class="table table-bordered dt-responsive nowrap w-100"
                                               style="width:100%">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">S. No.</th>
                                                <th class="align-middle">Fiscal Year</th>
                                                <th class="align-middle">Product Name</th>
                                                <th class="align-middle">Grand Total</th>
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

    <div class="modal" tabindex="-1" role="dialog" id="addFAQModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="addFAQModalContent"></div>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        function viewPurchaseReturn(id) {

            $.ajax({
                url: "{{ route('purchase_return.view') }}",
                type: 'GET',
                data: {
                    id
                },
                success: function (response) {
                    if (response.response) {
                        $('#addFAQModal #addFAQModalContent').html('');
                        $('#addFAQModal #addFAQModalContent').html(response.result);
                        $('#addFAQModal').modal('show');
                    } else {
                        toastr.error(response.msg)
                    }
                }
            })
        }

        function datatable(fiscal_year =null){
            $('#FAQTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{route('purchase_return.get')}}",
                    type: "GET",
                    data:  {
                        fiscal_year
                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [[10, 20, -1], [10, 20, "All"]],
                order: [[1, "DESC"]],
                columns: [
                    {data: 'id'},
                    {data: 'fiscal_year_id'},
                    {data: 'product_id'},
                    {data: 'total_price'},
                    {data: "action"},
                ],
                columnDefs: [{"orderable": false, "targets": [4]}],
            });
        }

        const FAQModel = {};

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            datatable();

            $('#FiscalYear').select2()

            $('#search').on('click',function (){
                const fiscal_year = $('#FiscalYear').val()
                datatable(fiscal_year)
            })


            $('#addFAQ').on('click', function () {
                $('#addFAQModal #addFAQModalContent').html('');
                $('#addFAQModal #addFAQModalContent').html($('#purchase_return_template').html());
                $('#addFAQModal').modal('show');
            });
        });

    </script>
    <template id="purchase_return_template">
        <div class="modal-header">
            <h5 class="modal-title">Add Purchase Return</h5>
        </div>

        <div class="modal-body">
            <form method="post" action="{{route('purchase_return.store')}}" class="eq-section" novalidate id="FAQForm">@csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group item">
                                            <label for="SKU" class="required-label">SKU</label>
                                            <select name="sku" id="SKU" required
                                                    class="form-control">
                                                <option value="" selected disabled>Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="product_details">

                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="submit" style="float: left;" id="save"
                                               class="btn btn-primary waves-effect waves-light" value="Submit">
                                        <button type="button" class="btn btn-danger ml-3"
                                                data-bs-dismiss="modal">Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
            </form>
        </div>

        {{--@section('script')--}}
        <script>
            $(function () {
                $('#SKU').select2({
                    multiple: false,
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent:$('#addFAQModal'),
                    placeholder: "Search SKU",
                    ajax: {
                        delay: 350,
                        transport: function (params, success, failure) {
                            window.ct.getData('{{ route('products.sku.search') }}', [{
                                name: 'keyword',
                                value: params.data.term
                            }], false).then(function (responseData) {
                                success(responseData.result);
                            }, function (error) {
                                $('#select2-SKU-results').html('<li role="treeitem" aria-live="assertive" class="select2-results__option select2-results__message">' + error.msg + '</li>');
                            });
                        }
                    }
                })

                $('#SKU').on('change',function (){

                    $.ajax({
                        url:"{{ route('getProductDetailsBySku') }}",
                        method :"GET",
                        data:{
                            'sku':$(this).val()
                        },
                        success:function (responseData){
                            if(responseData.response)
                                $('#product_details').html(responseData.result)
                        }
                    })
                })
                $("#FAQForm").submit(function (event) {
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
                            $('#addFAQModal').modal('hide');
                            datatable()
                        }, function (error) {
                            toastr.error(error.msg);
                            window.ct.populateFormError("#FAQForm", error.result);
                        });
                    }
                    return false;
                })

            })

        </script>
        {{--@endsection--}}

    </template>
@endsection
