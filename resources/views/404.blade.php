@extends('frontend.layouts.master')

@section('content')
<div class="main-wrapper">
    <div class="error-area">
        <div class="container">
            <div class="row align-items-center height-100vh">
                <div class="col-lg-8 ml-auto mr-auto">
                    <div class="error-content text-center">
                        <div class="error-logo">
                            <a href="{{ route('front.index') }}"><img src="{{ asset('uploads/logo/'.getSiteLogo()) }}" alt="logo"></a>
                        </div>
                        <div class="error-img">
                            <img src="assets/images/banner/page-404-image.jpg" alt="">
                        </div>
                        <h2> Oops! That Product can’t be found.</h2>
                        <p> It looks like nothing was found . Maybe try one of the links below or a search?</p>
                        <div class="search-style-4">
                            <form  method="get" action="{{ route('search') }}">
                                <input type="text" name="product" placeholder="Search…">
                                <button type="submit"> Search </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection