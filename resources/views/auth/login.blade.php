<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAKISOLA - Đăng nhập</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #73b4f1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
        }

        .vnpt-logo {
            width: 60px; /* Giảm kích thước logo VNPT */
            height: auto;
        }

        .header-text {
            color: white;
            font-size: 20px;
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin-bottom: 60px;
        }

        .login-container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-section {
            flex: 2; /* Tăng tỷ lệ cho phần logo */
            text-align: center;
            padding-right: 50px;
        }

        .logo-section img {
            width: 400px; /* Tăng kích thước logo Sakisola */
            height: auto;
        }

        .login-form-container {
            flex: 1;
            max-width: 350px; /* Giới hạn độ rộng form đăng nhập */
        }

        .help-text {
            text-align: center;
            color: black;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: black;
            font-size: 16px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background: #000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            background: #0077b6;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('img/logoVNPT.png') }}" alt="VNPT Logo" class="vnpt-logo">
        <div class="header-text">TẬP ĐOÀN VIỄN THÔNG VIỆT NAM</div>
    </div>

    <div class="main-content">
        <div class="login-container">
            <div class="logo-section">
                <img src="{{ asset('img/logosakisola.png') }}" alt="Sakisola Logo">
            </div>

            <div class="login-form-container">
                <div class="help-text">
                    Nếu bạn chưa có tài khoản, vui lòng liên hệ: 0123456789
                </div>

                {{-- Display error messages if any --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label>TÊN ĐĂNG NHẬP</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>MẬT KHẨU</label>
                        <input type="password" name="password" required>
                    </div>

                    <button type="submit" class="login-button">ĐĂNG NHẬP</button>
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        Số điện thoại Hỗ trợ Email/ Xác thực tập trung là: 18001555 Nhánh 1
    </div>
</body>
</html>
