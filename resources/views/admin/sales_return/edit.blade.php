<div class="modal-header">
    <h5 class="modal-title">Add FAQ</h5>
</div>

<div class="modal-body">
    <form method="post" action="{{route('faq.update')}}" class="eq-section" novalidate id="FAQForm">
        <input type="hidden" name="id" value="{{ $faq->id }}" readonly required>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 row">
                            <label for="name" class="col-md-2 col-form-label required-label">Question</label>
                            <div class="col-md-10 item">
                                <textarea class="form-control" required name="question"
                                          rows="5">{{ $faq->question }}</textarea>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="name" class="col-md-2 col-form-label required-label">Answer</label>
                            <div class="col-md-10 item">
                                <textarea class="form-control" id="Answer" required name="answer" rows="5">
                                    {{ $faq->answer }}
                                </textarea>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="form-check item">
                                <input class="form-check-input" name="status" type="checkbox" id="flexCheckStatus"
                                <?= $faq->is_active ? 'checked' : '' ?>
                                <label class="form-check-label" for="flexCheckStatus">
                                    is active
                                </label>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12">
                                <input type="submit" style="float: left;" id="save"
                                       class="btn btn-primary waves-effect waves-light" value="Submit">
                                <button type="button" class="btn btn-danger ml-3"
                                        data-bs-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </form>
</div>

{{--@section('script')--}}
<script>
    $(function () {
        ClassicEditor
            .create(document.querySelector('#Answer'))
            .catch(error => {
                console.error(error);
            });

        $("#FAQForm").submit(function (event) {
            if (validator.checkAll($(this))) {
                const data = new FormData(this)
                const url = $(this).attr('action')

                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });
                window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                    toastr.success(responseData.msg);
                    $('#addFAQModal').modal('hide');
                    FAQModel.faqListTbl.ajax.reload(null, false);
                }, function (error) {
                    toastr.error(error.msg);
                    window.ct.populateFormError("#FAQForm", error.result);
                });
            }
            return false;
        })

    })

</script>
{{--@endsection--}}
