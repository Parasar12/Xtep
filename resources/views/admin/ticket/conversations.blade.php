@extends('layouts.app')

@section('content')

<!-- Begin page -->
<div id="layout-wrapper">
  @include('admin.includes.header')

  @include('admin.includes.left_sidebar')

  <style>
    #chat2 .form-control {
      border-color: transparent;
    }

    #chat2 .form-control:focus  {
      border-color: transparent;
      box-shadow: inset 0px 0px 0px 1px transparent;
    }

    .divider:after,
    .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
    }
  </style>

  <div class="main-content">

    <div class="page-content">
      <div class="container-fluid">
          <section style="background-color: #eee;">
            <div class="container py-5">

              <div class="row d-flex justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">

                  <div class="card" id="chat2">
                    <div class="card-header d-flex justify-content-between align-items-center p-3">
                      <h5 class="mb-0">Chat</h5>
                      
                    </div>
                    <div class="card-body" data-mdb-perfect-scrollbar="true" style="position: relative;">


                      @foreach($tickets as $ticket)
                        @if($ticket->parent_ticket_id == 0)
                        <div class="d-flex flex-row justify-content-start">
                          
                          <div>
                            
                            <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">{{$ticket->description}}</p>
                            
                            
                          </div>
                        </div>
                        

                        <div class="divider d-flex align-items-center mb-4">
                          <p class="text-center mx-3 mb-0" style="color: #a2aab7;">Today</p>
                        </div>
                        @endif

                        @if($ticket->parent_ticket_id == $id)
                        <div class="d-flex flex-row justify-content-end mb-4 pt-1">
                          <div>
                            
                            <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary">{{$ticket->description}}.</p>
                            
                            
                            <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:06</p>
                          </div>
                        </div>
                        @endif

                      @endforeach

                    </div>

                    <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                     
                       <form method="post" action="{{route('ticket.reply.store')}}" id="post_ticket_support_reply">
                          @csrf
                              <input type="hidden" name="ticket_id" value="{{$id}}">
                          <div class="row">
                            <div class="col-md-10">
                              <input type="text" name="message" class="form-control form-control-lg" id="exampleFormControlInput1"
                                placeholder="Type message">

                                <span class="error_txt message_error"></span>
                            </div>

                            <div class="col-md-2">
                              <input type="submit" style="float: right;" class="btn btn-primary waves-effect waves-light" value="Send">
                            </div>
                          </div>
                      </form>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </section>
      </div>
    </div>
  </div>

</div>
@endsection

@section('script')

<script>

  $(document).on('submit', '#post_ticket_support_reply', function(e) {
    e.preventDefault();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });

    $.ajax({
      type: 'POST',
      url: $(this).attr('action'),
      data: new FormData(this),
      processData: false,
      dataType: 'json',
      contentType: false,

      beforeSend: function(data) {
        $(document).find('form span .error_txt').text('');

      },
      success: function(data) {
        if (data.status == 0) {
          $.each(data.error, function(key, value) {
              toastr.success(data.value);
          });
        } else {
          toastr.success(data.msg);
          location.reload();

        }
      },
    });
  });
 
</script>
@endsection