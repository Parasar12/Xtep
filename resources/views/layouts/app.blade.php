<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="UTech IT Professionals Pvt. Ltd." name="description"/>
    <meta content="UTech IT Professionals Pvt. Ltd." name="author"/>

    <title>{{ getSiteTitle() }}</title>

    @php $siteFavicon = getSiteFavicon(); @endphp
    <link rel="icon" type="{{ $siteFavicon && strtolower(pathinfo($siteFavicon, PATHINFO_EXTENSION)) === 'png' ? 'image/png' : 'image/x-icon' }}" href="{{ $siteFavicon ? asset('uploads/favicon/' . $siteFavicon) : asset('favicon.ico') }}">

    <script src="{{ asset('dashboard/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/validator.js') }}"></script>
    <script src="{{ asset('dashboard/app/app.js') }}"></script>

    <link href="{{ asset('dashboard/css/layout.css') }}" rel="stylesheet" type="text/css"/>


    <!-- Bootstrap Css -->
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('dashboard/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css"/>


    <!-- Tree View  -->
    <link href="{{ asset('dashboard/plugins/jstree/style.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('dashboard/plugins/jstree/jstree.min.js') }}"></script>

    <!-- Toast -->
    <link href="{{ asset('dashboard/plugins/toast/build/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('dashboard/plugins/toast/build/toastr.min.js') }}"></script>

    <!-- Image Uploader -->
    <link href="{{ asset('dashboard/drag-drop-image-uploader/dist/image-uploader.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <script src="{{ asset('dashboard/drag-drop-image-uploader/dist/image-uploader.min.js') }}"></script>

    <link href="{{ asset('dashboard/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


</head>

<body data-sidebar="dark">
<div class="overlay"></div>

<div id="loader"></div>

@yield('content')

<!-- JAVASCRIPT -->
<script src="{{ asset('dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('dashboard/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('dashboard/libs/node-waves/waves.min.js') }}"></script>

<!-- DataTable -->
<link rel="stylesheet" type="text/css"
      href="{{ asset('dashboard/plugins/datatable/table/css/dataTables.bootstrap4.min.css') }}">
<script src="{{ asset('dashboard/plugins/datatable/table/js/jquery.dataTables.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('dashboard/plugins/datatable/table/js/dataTables.bootstrap4.min.js') }}"
        type="text/javascript"></script>


<link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="{{ asset('dashboard/css/tagin.min.css') }}">
<script src="{{ asset('dashboard/js/app.js') }}"></script>

<!-- bootbox -->
<script src="{{ asset('dashboard/js/bootbox/bootbox.min.js') }}"></script>
<script src="{{ asset('dashboard/js/bootbox/bootbox.locales.min.js') }}"></script>
<!-- bootbox -->

<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>

<script src="{{ asset('dashboard/js/select2.min.js') }}"></script>
<link href="{{ asset('dashboard/css/select2.min.css') }}" rel="stylesheet"/>

<script src="{{ asset('dashboard/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('dashboard/libs/chart.js/Chart.bundle.min.js') }}"></script>

@yield('script')

<script type="text/javascript">

    $(document).ready(function () {

        init_validator();
        validator.defaults.alerts = false;

        $('.modal').modal({
            show: false,
            backdrop: 'static',
            keyboard: false,
        });

        toastr.options.timeOut = 5000;
        @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}');
        @elseif(Session::has('success'))
        toastr.success('{{ Session::get('success') }}');
        @endif

    });

    function printOrderInAdmin() {
        printElement(document.getElementById("printThis"));
    }

    function printElement(elem) {
        var domClone = elem.cloneNode(true);

        var $printSection = document.getElementById("printSection");

        if (!$printSection) {
            var $printSection = document.createElement("div");
            $printSection.id = "printSection";
            document.body.appendChild($printSection);
        }

        $printSection.innerHTML = "";
        $printSection.appendChild(domClone);
        window.print();
    }

    function manage(e) {
        var formId = e.id;
        if (formId != '') {
            $('#' + formId + ' :input[type=submit]').attr('disabled', true);
            $('#' + formId).css('opacity', 0.5);
            $('div.overlay').addClass('show');
            return true;
        }
        return false;
    }

</script>
</body>

</html>
