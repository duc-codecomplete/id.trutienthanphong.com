@extends('layouts.master')
@section('content')
<div class="container-xl">
    <div class="row g-3 mb-4 align-items-center justify-content-between">
        <div class="col-auto">
            <h1 class="app-page-title mb-0">Danh sách giftcode</h1>*Nếu không thấy nhân vật, <a href="/update_char">bấm
                vào đây</a> để cập nhật</small></p>
        </div>
    </div>
</div>

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
    <div class="form-group">
        <label for="exampleInputEmail1">Chọn nhân vật</label>
        <select name="char_id" class="form-control" required>
            <option value="">---Chọn nhân vật---</option>
            @foreach (Auth::user()->chars() as $item)
            <option value="{{ $item['char_id'] }}" @php if ($item["char_id"]==Auth::user()->main_id) {
                echo "selected";
                } @endphp>{{ $item['char_id'] }} - {{ $item->getName() }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Nhập giftcode</label>
        <input name="giftcode" class="form-control" id="exampleInputPassword1" placeholder="" required>
    </div>
    <button type="submit" class="btn btn-success">Nhận giftcode</button>
</form>
<style>
    .btn {
        color: white !important;
    }
</style>
@endsection