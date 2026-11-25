<script src="{{ asset('dashboard/plugins/treejs/treejs.js') }}" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('dashboard/plugins/treejs/treejs.css') }}">

<style type="text/css">
    .treeview_custom li.outer-menu {
        font-weight: bolder;
        font-size: 14px;
    }

    .treeview_custom li.inner-menu {
        font-weight: lighter;
        font-size: 12px;
    }

    .parent {
        margin-right: 1.4%;
    }
</style>

<?php
function formatOutput(array $data, array $permission, string $level)
{
    $output = '';

    foreach ($data as $key => $row) {
        $id = $level . ($key + 1);

        $output .= '<li class="' . $row['menu_type'] . '-menu" title="' . $row['menu_name'] . '"> <i class="treejs-icon treejs-ocl" role="presentation"></i> ';

        /*checking user has already this permission*/
        if (in_array($row['menu_id'], $permission)) {
            $output .= '<input type="checkbox" checked="checked" data-id="' . $row['menu_id'] . '"  name="menu_id[]" id="' . $id . '"   value="' . $row['enc_menu_id'] . '" >';
            $output .= '<label for="' . $id . '" class=""><i class="' . $row['icon_class'] . '"></i> ' . $row['menu_name'] . '</label>';
        } else {
            $output .= '<input type="checkbox" name="menu_id[]" data-id="' . $row['menu_id'] . '" id="' . $id . '" value="' . $row['enc_menu_id'] . '" >';
            $output .= '<label for="' . $id . '" class="custom-unchecked"> <i class="' . $row['icon_class'] . '"></i> ' . $row['menu_name'] . '</label>';
        }

        if (isset($row['child'])) {
            $output .= '&nbsp;&nbsp;<i class="fa fa-minus-square parent"></i>';
            $output .= '<ul>' . formatOutput($row['child'], $permission, $id . '-') . '</ul>';
        }
        $output .= '</li>';
    }

    return $output;
}
?>


<!-- general form elements -->
<div class="row">
    <div class="col-md-12">
        <div class="card-header">
            <h3>User Permission (Navigation Access Control)</h3>
        </div><br>
        <div class="box-header with-border" style="padding-bottom: 0px;">
            <table class="table table-bordered nowrap table-striped" style="margin-bottom: 5px;margin-top: -5px;">
                <tr>
                    <td><label>Full Name:</label><br><?= $user->name ?></td>
                    <td><label>Email:</label><br><?= $user->email ?></td>
                </tr>
            </table>
        </div>

        <!-- form start -->
        <form action="{{ route('permission.save') }}" method="post" novalidate class="eq-section"
            id="userPermissionForm">
            @csrf

            <input type="hidden" name="userId" value="<?= $userId ?>">

            <div class="box-body" style="max-height: 90vh; overflow: scroll;">
                <div class="nav-permission">
                    <ul class="treeview_custom"><?php echo formatOutput($menu, $userPermission, ''); ?></ul>
                </div>
            </div><br>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-rounded btn-success" id="savePermission" name="save">Save
                    Permission</button>
                <button style="float: right;" type="button" class="btn btn-danger btn-rounded"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
</div>
<!-- /.box -->

<script type="text/javascript">
    $(document).ready(function() {

        /*binding function to checkbox change event*/
        $('input[type="checkbox"]').change(treejs.checkboxChanged);

        /*Fires on page load to detemines checked elements*/
        (function() {
            $('input[type="checkbox"]').each(function(index, el) {
                $(el).trigger('change');
            });
            treejs.offFirstLoadFlag(0);
        })();

        /*Hiding all the child element*/
        $(".parent").each(function(index, element) {
            var parent = $(element).parents('li');
            var child = $(parent[0]).children('ul');
            $(child).toggle("fast", function(e) {
                if ($(this).is(":visible")) {
                    $(element).removeClass('fa-plus-square').addClass('fa-minus-square');
                } else {
                    $(element).removeClass('fa-minus-square').addClass('fa-plus-square');
                }
            });
        });

        /*Binding function to on click event of show/hide icon*/
        $(".parent").on('click', function(e) {
            var element = $(this);
            var parent = $(element).parents('li');
            var child = $(parent[0]).children('ul');
            $(child).toggle("fast", function(e) {
                if ($(this).is(":visible")) {
                    $(element).removeClass('fa-plus-square').addClass('fa-minus-square');
                } else {
                    $(element).removeClass('fa-minus-square').addClass('fa-plus-square');
                }
            });
        });
    });
</script>
