@extends('layouts.master')
@section('content')
@php
$account = \Auth::user()->name;
@endphp
<h2>Thanh toán chuyển khoản</h2>
<p style="color:red">Lưu ý: giữ nguyên nội dung chuyển khoản là tên account để quá trình nạp Vg tự động được diễn
    ra</span></p>
<p>Sau khi chuyển khoản thành công, sau 15p mà chưa thấy Vgold thì nhắn tin vào facebook cá nhân của GM</span></p>
<p class="text-center">
    <img style="width:40%"
        src="https://img.vietqr.io/image/TCB-19035476499010-compact2.jpg?amount={{$cash}}&addInfo={{$account}}&accountName=Tru%20Tien%20Than%20Phong"></img>

</p>

<form action="/nap-tien/qrcode/success" method="POST" class="text-center">
    @csrf
    
    <div class="form-group row">
        <div class="col-sm-10">
            <button type="submit" class="btn btn-success">Nhấn xác nhận đã chuyển khoản</button>
        </div>
    </div>
</form>
<div>
    @endsection