<div class="box box-success">
  <div class="box-header with-border">
    <div class="box-title">
      <h3>Edit Menu
        <button style="float: right;" type="button" onclick="performAction('');" class="btn btn-rounded btn-warning" title="Back" ><i class="fa fa-close"></i> Cancel</button>
      </h3>
    </div>
  </div><br>
  <!-- /.box-header -->

  <!-- form start -->
  <form action="{{ route('menu.update') }}" method="post" novalidate class="eq-section" id="menuUpdateForm">

    <div class="box-body">
      <div class="row form-group item">
        <div class="col-md-10">
          <label class="required-label">Menu Code </label>
          <input type="text" class="form-control" name="menu_code" placeholder="ClassName-functionName" required maxlength="80" value="<?=$menu[0]->menu_code?>">
        </div>
      </div>

      <input type="hidden" name="menuId" value="<?=$menu[0]->menu_id?>">

      <div class="row">
        <div class="col-md-8">
          <div class="form-group item">
            <label class="required-label">Title </label>
            <input type="text" class="form-control" name="menu_name" placeholder="Title" required maxlength="50" value="<?=$menu[0]->menu_name?>">
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group item">
            <label class="required-label">Type </label>
            <select name="menu_type" id="menu_type" class="form-control input-sm" required data-placeholder="Select Menu Type">
              <option value="outer">Outer</option>
            <option value="inner">Inner</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row form-group item" style="display: none;">
        <div class="col-md-10">
          <label class="required-label">Route </label>
          <input type="text" class="form-control" name="route" placeholder="ClassName/functionName" maxlength="80" value="<?=$menu[0]->route?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group item">
            <label>Icon Class </label>
            <input type="text" class="form-control" name="icon_class" placeholder="font-awesome icon class" maxlength="50" value="<?=$menu[0]->icon_class?>">
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group item">
            <label class="required-label">Menu Position :</label>
            <input type="number" name="menu_index" class="form-control" placeholder="Position" required value="<?=$menu[0]->menu_index?>"/>
          </div>
        </div>
      </div>

      <div class="row form-group">
        <div class="col-md-6">
          <label class="required-label">Parent Menu </label>
          <select name="parent_menu" class="form-control" id="parent_menu" required  data-placeholder="Select Parent Menu">
            <option value="<?=urlencode(base64_encode(0))?>">N/A</option>
          </select>
        </div>
      </div>

      <div class="row form-group item">
        <div class="col-md-10">
          <label class="required-label">Modified Remarks </label>
          <input type="text" name="modifiedRemarks" placeholder="Remarks" class="form-control" required min="5" max="255">
        </div>
      </div>
    </div>
    <div class="box-footer">
      <div class="row form-group">
        <button type="submit" class="btn btn-success btn-rounded" name="save" id="save" >Save Menu</button>
      </div>
    </div>
  </form>
</div>



<script type="text/javascript">

  var select2Datasource = [];

  $(document).ready(function() {

    onMenuTypeChange("<?=$menu[0]->menu_type?>");

    $("#menu_type").select2().val("<?=$menu[0]->menu_type?>").trigger("change");

    formatOuputSelect2(menuModule.menuList, '');

    $('#parent_menu').select2({
     data:select2Datasource,
   }).val('<?=urlencode(base64_encode($menu[0]->pre_menu_id));?>').trigger('change');


    /*fires on change event of menu type*/
    $("#menu_type").on('change', function(){
      onMenuTypeChange($(this).val());
    });  


    $("#menuUpdateForm").submit(function(event) {
      if(validator.checkAll($(this))) {
        var data = $(this).serializeArray();
        var url = $(this).attr('action');

        event.preventDefault();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        });

        window.ct.postData(url, data, $("#save")).then(function(responseData){
          toastr.success(responseData.msg);          
          performAction('');
          loadMenuTree();
        }, function(error){
          window.ct.populateFormError("#menuUpdateForm", error.result);
          toastr.error(error.msg);                    
        });
      }
      return false;
    });

  });

 
  function onMenuTypeChange(value){
    if("outer"==value){
      $('[name="route"]').attr('required', 'required');
      $('[name="route"]').parent().parent().show();
    }else{
      $('[name="route"]').removeAttr('required', 'required');
      $('[name="route"]').parent().parent().hide();
      $('[name="route"]').parent().parent().removeClass('bad');
    }
  }

  /*function to prepare structure for select2 converts deep nested array into single dimension array*/
  function formatOuputSelect2(data, level){
    $.each(data, function(index, el) {
      select2Datasource.push({id:el.enc_menu_id,text:level+el.menu_name});
      if(el.child){
        var newLevel = ' '+level+"-";
        formatOuputSelect2(el.child, newLevel);
      }
    });
  } 
</script>