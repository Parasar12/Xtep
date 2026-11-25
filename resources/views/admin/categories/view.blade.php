<div class="box box-success">
  <div class="box-header with-border">
    <div class="box-title">
      <h3>Category Detail
        <button style="float: right;" type="button" onclick="performAction('');" class="btn btn-rounded btn-warning" title="Back" ><i class="fa fa-close"></i> Cancel</button>
      </h3>
    </div>
  </div><br>

  <div class="box-body">
    <div class="row form-group item">
      <div class="col-md-10">
        <label>Category Name :&nbsp;</label><?=$category[0]->name?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-10">
        <label>Slug :&nbsp;</label><?=$category[0]->slug?>
      </div>
    </div>
    <div class="row form-group item">
      <div class="col-md-10">
        <label>Parent Category :&nbsp;</label><?=$category[0]->parent_category?>
      </div>
    </div>
    <div class="row form-group">
      <div class="col-md-10">
        <label>Web Image  :&nbsp;</label> <img height="150px" width="200px" src="<?= asset('uploads/categories/'.$category[0]->image) ?>" >
      </div>
    </div>
      <div class="row form-group">
          <div class="col-md-10">
              <label>Mobile Image  :&nbsp;</label> <img height="150px" width="200px" src="<?= asset('uploads/categories/'.$category[0]->mobile_image) ?>" >
          </div>
      </div>
  </div>
</div>
