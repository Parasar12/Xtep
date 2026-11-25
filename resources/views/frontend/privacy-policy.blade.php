@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} - Privacy Policy</title>
@endsection
@section('content')

<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Privacy Policy
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<div class="privacy-policy-area pt-75 pb-35">
    <div class="mb-12 text-center">
        <h1>Privacy Policy</h1>
    </div>
    <div class="container">
        <div class="row flex-row-reverse">
            <div class="col-lg-12">
                <p>This Privacy Policy is applicable for {{ getSiteTitle() }}.</p>
                {!! $data->value !!}
            </div>

        </div>
    </div>
</div>
@endsection