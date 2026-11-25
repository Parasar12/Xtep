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
                                        <div class="col-12">
                                            <div class="float-right">
                                                <button type="button" id="addFAQ"
                                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                                    Add FAQ
                                                </button>
                                            </div>
                                            <h4 class="card-title mb-4">FAQ </h4>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="FAQTbl" class="table table-bordered dt-responsive nowrap w-100"
                                               style="width:100%">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">S. No.</th>
                                                <th class="align-middle">Question</th>
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

    <div class="modal" tabindex="-1" role="dialog" id="addFAQModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="addFAQModalContent"></div>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        function edit(id) {
            $.ajax({
                url: "{{ route('faq.edit') }}",
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

        const FAQModel = {};

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            FAQModel.faqListTbl = $('#FAQTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{route('faq.get')}}",
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
                    {data: 'question'},
                    {data: 'is_active'},
                    {data: "action"},
                ],
                columnDefs: [{"orderable": false, "targets": [3]}],
            });

            $('#addFAQ').on('click', function () {
                $.ajax({
                    url: "{{ route('faq.add') }}",
                    type: 'GET',
                    data: {},
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
            });
        });

    </script>
@endsection
