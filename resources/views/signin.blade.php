@extends('layouts.master')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">ĐĂNG NHẬP</h3>
                    <form action="" method="POST">
                        @csrf
                        @if(Session::has('error'))
                            <p class="alert alert-danger" style="text-align: center;color:red">{{ Session::get('error') }}
                        </p>
                        @endif
                        <div class="form-group">
                            <label for="username">* Tên đăng nhập</label>
                            <input required type="text" class="form-control" name="login" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label for="password">* Mật khẩu</label>
                            <input required="password" class="form-control" name="password" placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection