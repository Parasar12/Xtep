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
                                    <h4 class="card-title mb-4">Permission Management</h4>

                                    <div class="table-responsive">
                                        <button type="button" class="btn btn-sm btn-success" title="Click here to refresh"
                                            style="float: right;" id="refreshBtn"><i
                                                class="bx bx-loader-circle bx-spin"></i></button>

                                        <table id="userListTbl" class="table table-bordered nowrap compact" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">S. No.</th>
                                                    <th class="align-middle">Type</th>
                                                    <th class="align-middle">Name</th>
                                                    <th class="align-middle">Email</th>
                                                    <th class="align-middle">Status</th>
                                                    <th class="align-middle edit_brand">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>

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

    <!-- Manage User Nav Permission Modal Start-->
    <div class="modal" tabindex="-1" role="dialog" id="manageNavPermissionModal">
        <div class="modal-dialog modal-lg" role="document" style="width: 1000px !important">
            <div class="modal-content" id="manageNavPermissionModal_content">
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var PermissionModule = {};

        $(document).ready(function() {

            PermissionModule.userListTbl = $('#userListTbl').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{ route('permission.getuser') }}",
                    type: "GET",
                    data: function(postData) {}
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [
                    [10, 20, -1],
                    [10, 20, "All"]
                ],
                order: [
                    [1, "DESC"]
                ],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'is_admin'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'is_active'
                    },
                    {
                        data: "action"
                    },
                ],
                columnDefs: [{
                    "orderable": false,
                    "targets": [4]
                }],
            });

            $('#refreshBtn').on('click', function() {
                PermissionModule.userListTbl.ajax.reload(null, false);
            });


            $('#userListTbl').on('click', '.assign_permission', function() {
                var data = [];
                var userId = $(this).data('userid');
                data.push({
                    name: 'userId',
                    value: userId
                });

                $.get("{{ url('/permission/assign') }}", data, function(responseData) {
                    $('#manageNavPermissionModal .modal-body').html('');
                    $('#manageNavPermissionModal .modal-body').html(responseData.result);
                    $('#manageNavPermissionModal').modal('show');
                });
            });

            $("#manageNavPermissionModal").on('submit', '#userPermissionForm', function() {
                if (validator.checkAll($(this))) {
                    var data = $(this).serializeArray();
                    var url = $(this).attr('action');

                    window.ct.postData(url, data, $("#savePermission")).then(function(responseData) {
                        toastr.success(responseData.msg);
                        $('#manageNavPermissionModal').modal('hide');
                    }, function(error) {
                        toastr.error(responseData.msg);

                        $.each(error.result, function(index, el) {
                            $("#userPermissionForm [name='" + index + "']").parent().append(
                                '<div class="error_msg">' + el + '</div>')
                        });
                    });
                }
                return false;
            });




        });
    </script>
@endsection
