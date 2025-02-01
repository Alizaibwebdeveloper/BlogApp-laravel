@extends('back.layout.auth-layout')
@section('PageTitle', isset($pageTitle) ? $pageTitle : 'PageTitle Here')

@section('content')
<div class="login-box bg-white box-shadow border-radius-10">
    <div class="login-title">
        <h2 class="text-center text-primary">Login</h2>
    </div>
    <form method="POST" action="{{ route('admin.login-handler') }}">

        @csrf
        <x-form-alert />
        <div class="input-group custom">
            <input type="text" name="login_id" value="{{old('login_id')}}" class="form-control form-control-lg"
                placeholder="Username">
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
            </div>

        </div>
        @error('login_id')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <div class="input-group custom">
            <input type="password" name="password" class="form-control form-control-lg" value="{{old('password')}}"
                placeholder="**********">
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
            </div>
        </div>

        @error('password')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <div class="row pb-30">
            <div class="col-6">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">Remember</label>
                </div>
            </div>
            <div class="col-6">
                <div class="forgot-password">
                    <a href="forgot-password.html">Forgot Password</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="input-group mb-0">

                    <input class="btn btn-primary btn-lg btn-block" type="submit" value="Sign In">
                </div>

            </div>
        </div>
    </form>
</div>
@endsection