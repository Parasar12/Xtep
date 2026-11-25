<div class="box box-success">
  <div class="box-header with-border">
    <div class="box-title">
      <h3>Edit Category
        <button style="float: right;" type="button" onclick="performAction('');" class="btn btn-rounded btn-warning" title="Back" ><i class="fa fa-close"></i> Cancel</button>
      </h3>
    </div>
  </div><br>
  <!-- /.box-header -->

  <!-- form start -->
  <form action="{{ route('category.update') }}" method="post" enctype="multipart/form-data" novalidate class="eq-section" id="categoryUpdateForm">
    @csrf

    <div class="box-body">
      <div class="row form-group item">
        <div class="col-md-10">
          <label class="required-label">Category Name </label>
          <input type="text" class="form-control" name="name" placeholder="Category Name" required value="<?=$category->name?>">
        </div>
      </div>

      <input type="hidden" name="id" value="{{ $category->id }}">

      <div class="row">
        <div class="col-md-10">
          <div class="form-group item">
            <label class="required-label">Slug </label>
            <input type="text" class="form-control" name="slug" placeholder="Slug" required value="<?=$category->slug?>">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="form-group item">
            <label>Image </label>
            <input class="form-control" type="file" id="imgload" name="image" onclick="showSelectedImage(event)">
          </div>
        </div>
          <div class="col-lg-6">
              <div class="form-group item">
                  <img style="width: 200px; height: 200px;" src="@if($category->image) {{asset('uploads/categories/'.$category->image)}} @endif" id="imgshow" />
              </div>
          </div>
      </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group item">
                    <label>Mobile Image </label>
                    <input class="form-control" type="file" id="mobileImgload" name="mobile_image" onclick="showSelectedMobileImage(event)">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group item">
                    <img style="width: 200px; height: 200px;" src="{{ $category->mobile_image ? asset('uploads/categories/'.$category->mobile_image) : '' }}" id="mobileImgshow" />
                </div>
            </div>
        </div>


      <div class="row form-group">
        <div class="col-md-6">
          <label class="required-label">Parent Category </label>
          <select name="parent_category" class="form-control" id="edit_parent_category" required  data-placeholder="Select Parent Menu">
            <option value="<?=urlencode(base64_encode(0))?>">N/A</option>
          </select>
        </div>
          <div class="col-lg-3">
              <label class="required-label"> Order </label>
              <input type="number" class="form-control" name="order" placeholder="order" required value="{{ $category->ordering }}" >
          </div>
          <div class="col-lg-3 mt-4">
              <input type="checkbox" name="show_in_home" {{ $category->show_in_home ? 'checked' : '' }}>
              <label> Show in home </label>
          </div>

      </div>



    </div>
    <div class="box-footer">
      <div class="row form-group">
        <button type="submit" class="btn btn-success btn-rounded" name="save" id="save" >Save Category</button>
      </div>
    </div>
  </form>
</div>



<script type="text/javascript">

  var select2Datasource = [];

  $(document).ready(function() {

    formatOuputSelect2(categoryModule.categoryList, '');

    $('#edit_parent_category').select2({
      data:select2Datasource,
    }).val('<?=urlencode(base64_encode($category->parent_id));?>').trigger('change');


    $("#categoryUpdateForm").submit(function(event) {
      event.preventDefault();
      if ($("#categoryUpdateForm #save").is(":disabled")) { return false; }
      if(validator.checkAll($(this))) {
        var data = new FormData(this);
        var url = $(this).attr('action');

        event.preventDefault();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        });

        var $saveBtn = $("#categoryUpdateForm #save");
        var savingRequest = window.ct.postDataMultiPartForm(url, data, $saveBtn);
        // show spinner while saving (button is disabled by postDataMultiPartForm and restored when the request finishes)
        $saveBtn.html('<i class="bx bx-loader-circle bx-spin font-size-16 align-middle me-1"></i> Saving...');
        savingRequest.then(function(responseData){
          toastr.success(responseData.msg);
          performAction('');
          loadCategoryTree();
        }, function(error){
          window.ct.populateFormError("#categoryUpdateForm", error.result);
          toastr.error(error.msg);
        });
      }
      return false;
    });

  });


  /*function to prepare structure for select2 converts deep nested array into single dimension array*/
  function formatOuputSelect2(data, level){
    $.each(data, function(index, el) {
      select2Datasource.push({id:el.enc_id,text:level+el.name});
      if(el.child){
        var newLevel = ' '+level+"-";
        formatOuputSelect2(el.child, newLevel);
      }
    });
  }

  function showSelectedImage(e) {
    $("#imgload").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#imgshow').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  function showSelectedMobileImage(e) {
      $("#mobileImgload").change(function() {
          if (this.files && this.files[0]) {
              var reader = new FileReader();
              reader.onload = function(e) {
                  $('#mobileImgshow').attr('src', e.target.result);
              }
              reader.readAsDataURL(this.files[0]);
          }
      });
  }

</script>
