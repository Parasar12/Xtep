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
                                    <h4 class="card-title mb-4">Inquiry</h4>

                                    <div class="table-responsive">
                                        <table id="MessageTbl" class="table table-bordered dt-responsive nowrap w-100"
                                               style="width:100%">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">S. No.</th>
                                                <th class="align-middle">Name</th>
                                                <th class="align-middle">Email</th>
                                                <th class="align-middle">Message Date</th>
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

    <div class="modal" tabindex="-1" role="dialog" id="MessageModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="MessageModalContent"></div>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        function viewMessage(id) {
            $.ajax({
                url: "{{ route('contact.view') }}",
                type: 'GET',
                data: {
                    id
                },
                success: function (response) {
                    if (response.response) {
                        $('#MessageModal #MessageModalContent').html(response.result);
                        $("#MessageModal").modal('show');
                    } else {
                        toastr.error(response.msg)
                    }
                }
            })
        }

        const MessageModule = {};

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            MessageModule.brandListTbl = $('#MessageTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{route('contact.get')}}",
                    type: "GET",
                    data: function (postData) {
                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [[10, 20, -1], [10, 20, "All"]],
                order: [[1, "DESC"]],
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'created_at'},
                    {data: "action"},
                ],
                columnDefs: [{"orderable": false, "targets": [4]}],
            });
        })
    </script>
@endsection
