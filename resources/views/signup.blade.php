@extends('layouts.master')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">ĐĂNG KÝ</h3>
                    <form action="" method="POST">
                        @csrf
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(Session::has('error'))
                        <p class="alert alert-danger">{{ Session::get('error') }}</p>
                        @endif
                        @if(Session::has('success'))
                        <p class="alert alert-success">{{ Session::get('success') }}</p>
                        @endif
                        <div class="form-group">
                            <label for="username">* Tên đăng nhập</label>
                            <input value="{{ old('login') }}" type="text" required class="form-control" name="login" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label for="password">* Mật khẩu</label>
                            <input type="password" required class="form-control" name="passwd" placeholder="Enter password">
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">* Xác nhận mật khẩu</label>
                            <input type="password" required class="form-control" name="passwdConfirm"
                                placeholder="Confirm password">
                        </div>
                        <div class="form-group">
                            <label for="email">* Email</label>
                            <input value="{{ old('email') }}" type="email" required class="form-control" name="email" placeholder="Enter email">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection