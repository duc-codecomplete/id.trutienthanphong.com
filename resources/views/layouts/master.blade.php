<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tru Tiên Thần Phong</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/"><img src="/assets/logo.png" style="width: 25%" alt="Logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Trang chủ</a>
                </li>
                @if(!Auth::check())
                <li class="nav-item">
                    <a class="nav-link" href="/dang-nhap">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dang-ky">Đăng ký</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="/giftcodes">Giftcode</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/nap-tien">Nạp tiền</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/">Lịch sử nạp tiền</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/doi-mat-khau">Đổi mật khẩu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Thoát</a>
                </li>
                @endif
            </ul>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="container p-4">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>