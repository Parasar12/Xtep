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
                            <form method="post" action="{{route('product.gallary.update')}}"
                                  enctype="multipart/form-data" id="update_product_gallery">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">

											<div class="mb-3 row">
												<label class="col-md-2 col-form-label">Product Name</label>
												<div class="col-md-10">
													<select name="id" id="gallaryProduct" class="form-control" required>
														@foreach($products as $p)
															<option value="{{$p->id}}" {{ $p->id == $id ? 'selected' : '' }}>{{$p->name}}</option>
														@endforeach
													</select>

													
												</div>
											</div>
													<div class="row">
														@if(!$gallary->isEmpty())
															@foreach($gallary as $g)
																<div class="control-group mb-3 row">
																	<div class="col-md-2">
																			<img style="width: 100px; height: 100px;" src="{{asset('uploads/products/'.$g->gallary_images)}}"/>
																	</div>

																	<div class="col-md-1">
																		<div class="input-group-btn">
																			<button class="btn btn-danger btn-delete" onclick="deleteImage({{$id}},{{$g->id}})" type="button"><i class="glyphicon glyphicon-remove"></i> X</button>
																		</div>
																	</div>
																</div>
															@endforeach
														@endif
													</div>
													

                                                <div class="main_div" style="display: none;">
                                                    <div class="control-group mb-3 row">
                                                        <div class="col-md-2">
                                                            <img style="width: 50px; height: 50px;" src=""
                                                                 class="imgshow"/>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <input type="file" name="image[]"
                                                                   class="imgload form-control"
                                                                   onchange="showSelectedImage(event)">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="input-group-btn">
                                                                <button class="btn btn-danger btn-remove" type="button">
                                                                    <i class="glyphicon glyphicon-remove"></i> Remove
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-success btn-add-more" type="button"><i
                                                                class="glyphicon glyphicon-plus"></i>Add Images
                                                        </button>
                                                    </div>
                                                </div>

											<div class="img_div">
														
											</div>

                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="text-center mt-4">
                                                            <input type="submit"
                                                                   class="btn btn-primary waves-effect waves-light"
                                                                   value="Submit">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1">
                                                        <div class="text-center mt-4">
                                                            <a class="btn btn-secondary"
                                                               href="{{route('products.gallary.index')}}">Close</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')

    <script>

        $(document).ready(function () {

            $('#gallaryProduct').on('change', function () {
                window.location.href = "{{ route('product.gallary.edit', '') }}/" + $(this).val();
            });

            $(document).on('submit', '#update_product_gallery', function (e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                $.ajax({
                    type: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    processData: false,
                    dataType: 'json',
                    contentType: false,

                    beforeSend: function (data) {
                        $(document).find('form span .error_txt').text('');

                    },
                    success: function (data) {
                        if (data.status == 0) {
                            $.each(data.error, function (key, value) {
                                toastr.error(value);
                            });
                        } else {

                            toastr.success(data.msg);
                            location.reload();
                        }
                    },
                });
            });
        });

        $(".btn-add-more").click(function () {
            var html = $(".main_div").html();
            $(".img_div").after(html);
        });

        function deleteImage(productId, imageId) {
            $.post("{{ route('product.gallary.image.delete') }}", {
                _token: '{{ csrf_token() }}',
                'productId': productId,
                'id': imageId
            }, function (data) {
                if (data.status == 0) {

                    toastr.error(data.error);

                } else {
                    toastr.success(data.msg);
                    location.reload();
                }
            });
        }

	$("body").on("click", ".btn-remove", function() {
		$(this).parents(".control-group").remove();
	});

	function showSelectedImage(e) {
			if (e.target.files && e.target.files[0]) {
				var reader = new FileReader();
				reader.onload = function(event) {
					$(e.target).parents('.control-group').find('img').attr('src', event.target.result);
				}
				reader.readAsDataURL(e.target.files[0]);
			}
		
	}
	
</script>
@endsection