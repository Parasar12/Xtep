<div class="modal-header bg-soft-info p-3">
    <h5 class="modal-title" id="exampleModalLabel"></h5>

</div>
<div class="modal-body" >
    <input type="hidden" id="id-field" />
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    <div class="position-absolute bottom-0 end-0">
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div>
                <label for="companyname-field" class="form-label">Customer Name : {{ $user->name }}</label>
            </div>
        </div>
        <div class="col-lg-4">
            <div>
                <label for="owner-field" class="form-label">Customer Email: {{ $user->email }}</label>
            </div>
        </div>
        <div class="col-lg-4">
            <div>
                <label for="location-field" class="form-label">Customer Contact Number: {{ $user->contact }}</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="item">
                <label for="location-field" class="form-label">Customer Secondary Contact Number: {{ $user->secondary_contact }}</label>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="item">
                <label for="location-field" class="form-label">Logistic Vendor code: {{ $orderLogs['orderid'] ?? '' }}</label>
            </div>
        </div>
    </div>
    @if($orderLogs)
        <div class="row mt-3">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">S.N.</th>
                    <th scope="col">Status</th>
                    <th scope="col">Added Time</th>
                    <th scope="col">Vendor Return</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orderLogs as $detail)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>
                            {{ $detail['status'] }}
                        </td>
                        <td>{{ $detail['added_time']}}</td>
                        <td>{{ $detail['vendor_return']}}</td>
                    </tr>

                @endforeach

                </tbody>
            </table>

        </div>
    @endif
</div>
<div class="modal-footer">
    <div class="hstack gap-2 justify-content-end">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
    </div>
</div>

