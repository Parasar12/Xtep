<div class="modal-header">
    <h5 class="modal-title" id="MessageModalLabel">Inquiry By {{ $message->name }}</h5>
</div>

<div class="modal-body">
    <div class="card">
        <div class="row mt-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="Name">Name</label>
                    <input type="text" class="form-control" value="{{ $message->name }}" readonly disabled>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="Name">Email</label>
                    <input type="email" class="form-control" disabled value="{{ $message->email }}" readonly
                           required>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="Name">Name</label>
                    <input type="text" class="form-control" value="{{ $message->phone }}" readonly disabled>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="Name">Name</label>
                    <input type="text" class="form-control" value="{{ $message->subject }}" readonly disabled>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="Name">Message</label>
                    <textarea class="form-control" rows="5" readonly disabled>{{ $message->message }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <button type="button" class="btn btn-danger" id="CloseModel">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        $('#CloseModel').on('click', function () {
            $("#MessageModal").modal('hide');
        })
    })
</script>
