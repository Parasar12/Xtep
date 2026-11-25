@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>{{ getSiteTitle() }} - Faq</title>
@endsection
@section('content')
    {{--    FAQ--}}
    <div class="breadcrumb-area breadcrumb-area-padding-2 bg-gray-2">
        <div class="custom-container">
            <div class="breadcrumb-content text-center">
                <ul>
                    <li>
                        <a href="{{ route('front.index') }}">Home</a>
                    </li>
                    <li class="active">purchase guide</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="purchase-guide-area pt-75 pb-35">
        <div class="container">
            <div class="row flex-row-reverse">
                <div class="col-lg-8">
                    <div class="medizin-common-style-wrap">
                        @foreach($faqs as $faq)
                            <div class="medizin-common-paragraph-style">
                                <h2>{{ $faq->question }}</h2>
                                <p>{!! $faq->answer !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar-wrapper sidebar-wrapper-mr1 sidebar-wrapper-mt-dec">
                        <div class="sidebar-widget sidebar-widget-wrap mb-20">
                            <div class="widget-menu-style">
                                <nav>
                                    <ul>
                                        <li><a href="{{ route('front.about') }}">About Us</a></li>
                                        <li><a href="{{ route('front.contact') }}">Contact</a></li>
                                        <li><a href="{{ route('front.terms_conditions') }}">Terms of Service</a></li>
                                        <li><a href="{{ route('front.privacy') }}">Privacy Policy</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
