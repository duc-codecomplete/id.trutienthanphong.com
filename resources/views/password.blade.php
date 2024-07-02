@extends('layouts.master')
@section('content')
<div class="container-xl">
  <div class="row g-3 mb-4 align-items-center justify-content-between">
    <div class="col-auto">
      <h1 class="app-page-title mb-0">Thay đổi mật khẩu</h1>
    </div>
  </div>
  @if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span>{{ $errors->first() }}</span>
  </div>
  @endif
  @if(Session::has('error'))
  <div class="alert alert-danger fade show" role="alert">
    <span>{{ Session::get('error') }}</span>
  </div>
  @endif
  @if(Session::has('success'))
  <div class="alert alert-success fade show" role="alert">
    <span>{{ Session::get('success') }}</span>
  </div>
  @endif
  <form action="" method="POST">
    @csrf
    <div class="mb-3">
      <input type="password" required class="form-control" name="old">
    </div>
    <div class="mb-3">
      <input type="password" required class="form-control" name="new">
    </div>
    <div class="mb-3">
      <input type="password" required class="form-control" name="newcf">
    </div>
    <button type="submit" class="btn btn-primary" style="color:white">Cập nhật</button>
  </form>
  <br>
  <!--//row-->

  <div class="row g-4">
  </div>
</div>
@endsection