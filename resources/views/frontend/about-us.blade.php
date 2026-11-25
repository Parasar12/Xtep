@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>{{ getSiteTitle() }} - About-us</title>
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
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">About Us</li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>

    <div class="about-us-area fix about-us-img pt-65 pb-75">
        <div class="container">
            <div class="section-title-2 mb-35 wow tmFadeInUp">
                <h2 class="font-size-18 text-center"> {{ $about->title }}</h2>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-4 col-10">
                    <div class="about-us-img wow tmFadeInUp">
                        <img src="{{ asset('uploads/' . $about->featured_image) }}" alt="{{ getSiteTitle() }}"
                            class="w-100">
                    </div>
                </div>
                <div class="col-lg-8 col-12">
                    <div class="about-us-content wow tmFadeInUp">
                        {!! $about->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
