@extends('layouts.master')
@section('content')
@php
$data2 = ["50000", "100000", "200000","300000","400000","500000","600000","700000","800000","900000","1000000", "1500000", "2000000"];
@endphp
<h2>Nạp tiền</h2>
<p class="alert alert-danger">Chọn số KNB cần nạp, đi tới trang quét mã thanh toán, chuyển khoản thành công, 5-15p hệ
    thống sẽ tự cập nhật KNB <br>

</p>

<!--<p>Sau khi chuyển khoản thành công thì nhắn tin vào facebook cá nhân của GM</span></p>-->

<!--    <figure class="wp-block-image size-large"><img decoding="async" width="367" height="362" src="/images/bidv.jpeg" alt="" class="wp-image-3364" srcset="/images/bidv.jpeg, /images/bidv.jpeg 300w" sizes="(max-width: 367px) 100vw, 367px"></figure>-->

<div>
    <form action="/nap-tien/qrcode" method="POST">
        @csrf
        @if(Session::has('error'))
        <p class="alert alert-danger" style="text-align: center;color:red">{{ Session::get('error') }}</p>
        @endif
        @if(Session::has('success'))
        <p class="alert alert-success" style="text-align: center;color:red">{{ Session::get('success') }}</p>
        @endif
        <div class="form-group row">
            <label for="inputEmail3" class="col-sm-2 col-form-label">Số tiền nạp</label>
            <div class="col-sm-10">
                <select class="form-control" name="cash" id="exampleFormControlSelect1" name="id">
                    @foreach ($data2 as $item)
                    <option value="{{ $item}}">{{ number_format($item )}} đ</option>
                    @endforeach
                </select>
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Tới trang thanh toán</button>
            </div>
        </div>
    </form>
</div>
<style>
    @-webkit-keyframes my {
        0% {
            color: red;
        }

        50% {
            color: #fff;
        }

        100% {
            color: red;
        }
    }

    @-moz-keyframes my {
        0% {
            color: red;
        }

        50% {
            color: #fff;
        }

        100% {
            color: red;
        }
    }

    @-o-keyframes my {
        0% {
            color: red;
        }

        50% {
            color: #fff;
        }

        100% {
            color: red;
        }
    }

    @keyframes my {
        0% {
            color: red;
        }

        50% {
            color: #fff;
        }

        100% {
            color: red;
        }
    }

    .test {
        font-size: 24px;
        font-weight: bold;
        -webkit-animation: my 700ms infinite;
        -moz-animation: my 700ms infinite;
        -o-animation: my 700ms infinite;
        animation: my 700ms infinite;
    }
</style>
@endsection