<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SAKISOLA</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen max-h-screen flex flex-col" style="background-color: #73b4f1;">
            <!-- Header -->
            <div class="p-8 flex justify-between items-center px-16">
                <img src="/img/logo-mb.png" alt="VNPT Logo" class="h-12">
                <div class="flex gap-4">
                    <a href="{{ route('register') }}" class="text-lg text-black hover:text-gray-700 font-medium">đăng ký</a>
                    <a href="{{ route('login') }}" class="inline-block bg-white text-black px-8 py-2 rounded-full font-medium hover:bg-gray-100 transition-colors">Admin</a>
                </div>
            </div>
            <!-- Main content -->
            <div class="flex-1 flex items-center justify-end px-4 mt-2">
                <!-- Left side - Logo -->
                <div class="flex justify-center items-center pr-2">
                    <div class="relative">
                        <img src="/img/logosakisola.png" alt="SOKISOLA Logo" class="w-80">
                    </div>
                </div>

                <!-- Right side - Text content -->
                <div class="w-1/2 flex flex-col items-center pl-2">
                    <div class="mb-6 text-center">
                        <h1 class="text-2xl font-bold text-white mb-3">Chào mừng đến với Hệ Thống Sáng Kiến:</h1>
                        <h2 class="text-3xl font-bold text-white mb-3">SAKISOLA</h2>
                        <p class="text-lg text-white">Nơi bạn có thể đóng góp, phát triển và biến ý tưởng thành hiện thực!</p>
                    </div>

                    <a href="{{ route('login') }}" class="inline-block bg-white text-black px-8 py-2 rounded-full font-medium hover:bg-gray-100 transition-colors">
                        Gửi ý tưởng ngay !
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="w-full py-4 text-center text-white" style="background-color: #506ed1;">
                Số điện thoại Hỗ trợ Email/ Xác thực tập trung là: 18001555 Nhánh 1
            </div>
        </div>
    </body>
</html>
