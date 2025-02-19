<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SAKISOLA</title>
        @vite('resources/css/app.css')
    </head>
    <body class="antialiased">
        <div class="min-h-screen max-h-screen overflow-hidden" style="background-color: #73b4f1;">
            <!-- Header -->
            <div class="p-6 flex justify-between items-center">
                <img src="/img/logoVNPT.png" alt="VNPT Logo" class="h-12">
                <a href="{{ route('register') }}" class="text-base text-black hover:text-gray-700 font-medium">đăng ký</a>
            </div>

            <!-- Main content -->
            <div class="h-[calc(100vh-4.5rem)] flex flex-col justify-center items-center px-4">
                <!-- Hero section -->
                <div class="mb-6 text-center">
                    <h1 class="text-3xl font-bold text-white mb-3">Chào mừng đến với Hệ Thống Sáng Kiến: SOKISOLA</h1>
                    <p class="text-lg text-white">Nơi bạn có thể đóng góp, phát triển và biến ý tưởng thành hiện thực!</p>
                </div>

                <!-- Logo -->
                <div class="mb-8">
                    <div class="relative">
                        <img src="/img/logosakisola.png" alt="SOKISOLA Logo" class="w-40 mx-auto">
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-block bg-white text-black px-40 py-3 rounded-full font-medium hover:bg-gray-100 transition-colors text-lg">
                      Gửi ý tưởng ngay
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
