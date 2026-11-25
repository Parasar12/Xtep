<div class="box box-success">
  <div class="box-header with-border">
    <div class="box-title">
      <h3>Menu Detail
        <button style="float: right;" type="button" onclick="performAction('');" class="btn btn-rounded btn-warning" title="Back" ><i class="fa fa-close"></i> Cancel</button>
      </h3>
    </div>
  </div><br>

  <div class="box-body">
    <div class="row form-group item">
      <div class="col-md-10">
        <label>Code :&nbsp;</label><?=$menu[0]->menu_code?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-10">
        <label>Title :&nbsp;</label><?=$menu[0]->menu_name?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-6">
        <label>Type :&nbsp;</label><?=ucwords($menu[0]->menu_type);?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-10">
        <label>Route :&nbsp;</label><?=$menu[0]->route?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-6">
        <label>Icon Class :&nbsp;</label><?=$menu[0]->icon_class?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-6">
        <label>Position :&nbsp;</label><?=$menu[0]->menu_index?>
      </div>
    </div>
    <div class="row form-group">
      <div class="col-md-10">
        <label>Parent Menu :&nbsp;</label><?=$menu[0]->parent_menu?>
      </div>
    </div>
  </div>
</div>
