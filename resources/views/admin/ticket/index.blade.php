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
						<div class="card">
							<div class="card-body">
								<h4 class="card-title mb-4">Support Tickets</h4>
								
								<div class="table-responsive">
									<table id="ticketTbl" class="table table-bordered dt-responsive nowrap w-100" style="width:100%">
										<thead class="table-light">
											<tr>
												<th class="align-middle">S. No.</th>
												<th class="align-middle">Ticket No.</th>
												<th class="align-middle">Issue Category</th>
												<th class="align-middle">Created Date</th>
												<th class="align-middle">Status</th>
												<th class="align-middle edit_ticket">Action</th>
											</tr>
										</thead>
									</table>
								</div>
								<!-- end table-responsive -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<!-- END layout-wrapper -->



<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

@endsection

@section('script')

<script type="text/javascript">
	var TicketModule = {};

	$(document).ready(function() {

		TicketModule.ticketListTbl = $('#ticketTbl').DataTable({
	      dom: 'lfrtip',
	      destroy: true,
	      processing: true,
	      language: {
	        processing: '<span style="color:white;">Processing...</span>'
	      },
	      serverSide: true,
	      ajax: {
	        url: "{{route('ticket.get')}}",
	        type: "GET",
	        data: function(postData) {}
	      },
	      scrollY: "50vh",
	      scrollX: true,
	      lengthMenu: [[5, 10, 20, -1],[5, 10, 20, "All"]],
	      order: [[1, "DESC"]],
	      columns: [
	      {data: 'id'},
	      {data: 'ticket_no'},
	      {data: 'ticName'},
	      {data: 'tDate'},
	      {data: 'is_active'},
	      {data: "action"},
	      ],
	      columnDefs: [{"orderable": false, "targets": [5]}],
	    });
	});

</script>


@endsection