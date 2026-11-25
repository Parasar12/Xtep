@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} - Contact Us</title>
@endsection
@section('content')
<!-- breadcrumb -->
<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{route('front.index')}}">Home</a>
                    </li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Contact
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<!-- End breadcrumb -->
<div class="mb-8">
    <iframe
        src="{{ $mapLink }}"
        width="100%" height="514" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
</div>

<div class="container">
    <div class="row mb-10">
        <div class="col-md-8 col-xl-9">
            <div class="mr-xl-6">
                <div class="border-bottom border-color-1 mb-5">
                    <h3 class="section-title mb-0 pb-2 font-size-25">Leave us a Message</h3>
                </div>
                <p class="max-width-830-xl text-gray-90">You can call us in working time or visit our office. All mails
                    will get the response within 24 hours. Love to hear from you!</p>
                @if (Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{ Session::get('success') }}</li>
                    </ul>
                </div>
                @endif

                <form action="{{ route('contact.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="js-form-message mb-4">
                                <label class="form-label">
                                    Your Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="name" placeholder="Your Name" required
                                    value="{{ old('name') }}" data-msg="Please enter your name."
                                    data-error-class="u-has-error" data-success-class="u-has-success"
                                    autocomplete="off">

                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="js-form-message mb-4">
                                <label class="form-label">
                                    Your Email
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" name="email" placeholder="Your Email" required
                                    value="{{ old('email') }}" data-msg="Please enter a valid email."
                                    data-error-class="u-has-error" data-success-class="u-has-success">
                                @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="js-form-message mb-4">
                                <label class="form-label">
                                    Your Phone
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" name="phone" placeholder="Your Phone" required
                                    value="{{ old('phone') }}" data-msg="Please enter a valid phone number."
                                    data-error-class="u-has-error" data-success-class="u-has-success">
                                @error('phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="col-md-6">
                            <div class="js-form-message mb-4">
                                <label class="form-label">
                                    Subject
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="subject" placeholder="Subject" required
                                    value="{{ old('subject') }}" data-msg="Please enter the subject."
                                    data-error-class="u-has-error" data-success-class="u-has-success">
                                @error('subject')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="col-md-12">
                            <div class="js-form-message mb-4">
                                <label class="form-label">
                                    Your Message
                                </label>
                                <div class="input-group">
                                    <textarea class="form-control p-5" rows="4" name="message"
                                        placeholder="Your Message" required>{{ old('message') }}</textarea>
                                </div>
                                @error('message')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Recaptcha -->
                        <div class="col-md-12">
                            <div class="input-style mb-5">
                                <div class="g-recaptcha" data-sitekey="6LeegNghAAAAAJ2N64ZbcBNFq2hqIm1o57BezCXs"></div>
                                @error('g-recaptcha-response')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary-dark-w px-5">Send Message</button>
                    </div>
                </form>


            </div>
        </div>
        <div class="col-md-4 col-xl-3">
            <div class="border-bottom border-color-1 mb-5">
                <h3 class="section-title mb-0 pb-2 font-size-25">Our Store</h3>
            </div>
            <div class="mr-xl-6">
                <address class="mb-6">
                    {{ getAddress() }}
                </address>
                <h5 class="font-size-14 font-weight-bold mb-3">Contact</h5>

                <p class="text-gray-90">Email
                    us: <a class="text-blue text-decoration-on" href="mailto:{{ getEmail() }}">{{ getEmail() }}</a></p>
                <p class="text-gray-90">Call us: <a class="text-blue text-decoration-on">{{ getPhoneNumber() }}</a></p>
            </div>
        </div>
    </div>

</div>

@endsection