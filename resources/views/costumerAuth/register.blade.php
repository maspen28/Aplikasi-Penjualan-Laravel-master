@extends('layouts.layout')

@section('title')
    Register
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset("assets/vendors/linericon/style.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/vendors/nouislider/nouislider.min.css") }}">
@endsection

@section('main')
    @php
        $hideNavBar = true;
        $hideFooter = true;
    @endphp
    <section class="register_box_area section-margin">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="register_box_img">
                        <div class="hover">
                            <h4>Already have an account?</h4>
                            <p>Log in to access your account and continue shopping.</p>
                            <a class="button button-account" href="{{ route('costumer.login') }}">Log In</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="register_form_inner">
                        <h3>Create an Account</h3>
                            <form class="row register_form" method="POST" action="{{ route('costumer.register.post') }}" id="contactForm">
                                @csrf
                                <div class="col-md-12 form-group">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="{{ old('username') }}" required>
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{ old('address') }}" required>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="Confirm Password" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <button type="submit" value="submit" class="button button-register w-100">Register</button>
                                </div>
                            </form>
                            <div class="text-center mt-4">
                                <a href="{{ route('costumer.login') }}">Already have an account? Log in</a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
