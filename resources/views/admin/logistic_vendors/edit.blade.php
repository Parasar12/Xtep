<div class="modal-header">
    <h5 class="modal-title" id="brandModalLabel">Edit Logistic</h5>
</div>

<div class="modal-body">
    <form method="post" action="{{route('logistic_vendors.update')}}" novalidate class="eq-section" enctype="multipart/form-data" id="post_brand">
        @csrf
        <input type="hidden" name="id" value="{{ $vendor->id }}">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">


                        <div class="mb-3 row">
                            <label for="name" class="col-md-2 required-label col-form-label">Name </label>
                            <div class="col-md-10 item">
                                <input class="form-control" type="text" placeholder="Please enter logistic vendor Name" id="name" value="{{ $vendor->name }}" name="name" maxlength="200" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="address" class="col-md-2 required-label col-form-label">Address </label>
                            <div class="col-md-10 item">
                                <input class="form-control" type="text" placeholder="Please enter logistic vendor address" id="address" name="address" value="{{ $vendor->address }}" maxlength="200" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="api" class="col-md-2  col-form-label">Api Base Url </label>
                            <div class="col-md-10 item">
                                <input class="form-control" type="text" placeholder="Please enter logistic vendor Api base url" id="api" value="{{ $vendor->api_base_url }}" name="api_url" maxlength="200" >
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center mt-4">
                                    <input type="submit" id="save" style="float: left;" class="btn btn-primary waves-effect waves-light" value="Submit">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-secondary" style="float: right;" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </form>
</div>
