<li class="nav-item">
    <a class="nav-link" data-toggle="tab"
       id="Pending"
       href="#pending">Pending({{ getTotalPending() }}
        )</a>
</li>
<li class="nav-item">
    <a class="nav-link " id="Processing" data-toggle="tab" href="#processing">Processing({{ getTotalProcessing() }}
        )</a>
</li>
<li class="nav-item">
    <a class="nav-link" id="Delivered" data-toggle="tab"
       href="#delivered">Delivered({{ getTotalDelivered() }})</a>
</li>
<li class="nav-item">
    <a class="nav-link" id="Returned" data-toggle="tab"
       href="#returned">Returned({{ getTotalReturned() }})</a>
</li>
<li class="nav-item">
    <a class="nav-link" id="Cancelled" data-toggle="tab"
       href="#cancelled">Cancelled({{ getTotalCancelled() }})</a>
</li>

<script>
    $(function (){
        changeTab('{{ $status }}')
        $('#myTab a').click(function (e) {
            $(this).tab('show');
            const fiscal_year = $('#FiscalYear').val()
            const val = $(this).text()
            const status = val.split('(')[0]
            datatable(fiscal_year, status)
        });

    })

    function changeTab(status) {

        console.log(status)
        $(".nav-link").removeClass('active')
        if (status === "Pending") {
            $("#Pending").addClass("active")
        }

        if (status === "Delivered") {
            $("#Delivered").addClass("active")
        }

        if (status === "Cancelled") {
            $("#Cancelled").addClass("active")
        }

        if (status === "Returned") {
            $("#Returned").addClass("active")
        }
        if (status === "Processing") {
            $("#Processing").addClass("active")
        }
    }
</script>
