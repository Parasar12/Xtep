{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{-- <div class="container">--}}
    {{-- <div class="row justify-content-center">--}}
        {{-- <div class="col-md-8">--}}
            {{-- <div class="card">--}}
                {{-- <div class="card-header">{{ __('Reset Password fsdfsd') }}</div>--}}

                {{-- <div class="card-body">--}}
                    {{-- <form method="POST" action="{{ route('password.update') }}">--}}
                        {{-- @csrf--}}

                        {{-- <input type="hidden" name="token" value="{{ $token }}">--}}

                        {{-- <div class="form-group row">--}}
                            {{-- <label for="email" --}} {{-- class="col-md-4 col-form-label text-md-right">{{
                                __('E-Mail Address') }}</label>--}}

                            {{-- <div class="col-md-6">--}}
                                {{-- <input id="email" type="email" --}} {{--
                                    class="form-control @error('email') is-invalid @enderror" name="email" --}} {{--
                                    value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>--}}

                                {{-- @error('email')--}}
                                {{-- <span class="invalid-feedback" role="alert">--}}
                                    {{-- <strong>{{ $message }}</strong>--}}
                                    {{-- </span>--}}
                                {{-- @enderror--}}
                                {{-- </div>--}}
                            {{-- </div>--}}

                        {{-- <div class="form-group row">--}}
                            {{-- <label for="password" --}} {{-- class="col-md-4 col-form-label text-md-right">{{
                                __('Password') }}</label>--}}

                            {{-- <div class="col-md-6">--}}
                                {{-- <input id="password" type="password" --}} {{--
                                    class="form-control @error('password') is-invalid @enderror" name="password" --}}
                                    {{-- required autocomplete="new-password">--}}

                                {{-- @error('password')--}}
                                {{-- <span class="invalid-feedback" role="alert">--}}
                                    {{-- <strong>{{ $message }}</strong>--}}
                                    {{-- </span>--}}
                                {{-- @enderror--}}
                                {{-- </div>--}}
                            {{-- </div>--}}

                        {{-- <div class="form-group row">--}}
                            {{-- <label for="password-confirm" --}} {{--
                                class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>--}}

                            {{-- <div class="col-md-6">--}}
                                {{-- <input id="password-confirm" type="password" class="form-control" --}} {{--
                                    name="password_confirmation" required autocomplete="new-password">--}}
                                {{-- </div>--}}
                            {{-- </div>--}}

                        {{-- <div class="form-group row mb-0">--}}
                            {{-- <div class="col-md-6 offset-md-4">--}}
                                {{-- <button type="submit" class="btn btn-primary">--}}
                                    {{-- {{ __('Reset Password') }}--}}
                                    {{-- </button>--}}
                                {{-- </div>--}}
                            {{-- </div>--}}
                        {{-- </form>--}}
                    {{-- </div>--}}
                {{-- </div>--}}
            {{-- </div>--}}
        {{-- </div>--}}
    {{-- </div>--}}
{{--@endsection--}}

@extends('frontend.layouts.master')
@section('content')
<div class="breadcrumb-area breadcrumb-area-padding-2 bg-gray-2">
    <div class="custom-container">
        <div class="breadcrumb-content text-center">
            <ul>
                <li>
                    <a href="{{ route('front.index') }}">Home</a>
                </li>
                <li class="active">Reset Password</li>
            </ul>
        </div>
    </div>
</div>
<div class="login-register-area pt-75 pb-75">
    <div class="container">
        <div class="row">
            <div class="col-8 offset-3">
                <div class="login-register-wrap login-register-gray-bg">
                    <div class="login-register-title">
                        <h1>Login</h1>
                    </div>
                    <div class="login-register-form">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="login-register-input-style input-style input-style-white email">
                                <label>Email address *</label>
                                <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email"
                                    value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="login-register-input-style input-style input-style-white password">
                                <label>Password *</label>
                                <input id="password" type="password" class="@error('password') is-invalid @enderror"
                                    name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="login-register-input-style input-style input-style-white password">
                                <label>Confirm Password *</label>
                                <input id="password-confirm" type="password" name="password_confirmation" required
                                    autocomplete="new-password">
                            </div>
                            <div class="login-register-btn">
                                <button type="submit">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection