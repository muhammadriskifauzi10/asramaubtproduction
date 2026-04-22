<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="images/ubt-logo.webp">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- ✅ Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* LOADER */
        .loader {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            transition: 0.5s;
        }

        .loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3b82f6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        main.hidden {
            opacity: 0;
        }

        main.visible {
            opacity: 1;
            transition: opacity 0.6s ease;
        }

        /* LEFT */
        #left-content {
            opacity: 0;
            transform: translateX(-50px);
        }

        .animate-left {
            animation: slideLeft 0.8s ease forwards;
        }

        @keyframes slideLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* FLOAT */
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        /* CARD */
        #login-card {
            opacity: 0;
            transform: translateY(-80px) scale(0.95);
        }

        .animate-drop {
            animation: dropIn 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes dropIn {
            0% {
                opacity: 0;
                transform: translateY(-80px) scale(0.95);
            }

            60% {
                opacity: 1;
                transform: translateY(10px) scale(1.02);
            }

            100% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <!-- LOADER -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
    </div>

    <main id="main-content"
        class="hidden min-h-screen w-screen relative flex items-center justify-center overflow-hidden px-6 py-10">

        <!-- BACKGROUND -->
        <div class="absolute inset-0">
            <img src="/images/wallpaper-ubt-ghibli.webp" class="w-full h-full object-cover brightness-70">

            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <!-- CONTENT -->
        <div
            class="relative z-10 w-full max-w-6xl
            grid grid-cols-1 md:grid-cols-2
            gap-6 md:gap-10 items-center">

            <!-- LEFT -->
            <div id="left-content" class="text-white text-center md:text-left">

                <img src="images/ubt-logo.webp"
                    class="w-20 md:w-32 mx-auto md:mx-0 mb-4 md:mb-6 drop-shadow-xl animate-float" />

                <h1 class="text-3xl md:text-5xl font-bold leading-tight drop-shadow">
                    ASRAMA UBT
                </h1>

                <p class="mt-3 md:mt-4 text-sm md:text-lg text-blue-100 max-w-md mx-auto md:mx-0">
                    Sistem Informasi Asrama Universitas Bunda Thamrin
                    untuk pengelolaan data asrama Roemah54
                    secara modern, cepat, dan terintegrasi.
                </p>

                <div class="mt-4 md:mt-6 flex justify-center md:justify-start gap-2">
                    <div class="h-1 w-10 md:w-12 bg-purple-500 rounded"></div>
                    <div class="h-1 w-5 md:w-6 bg-blue-400 rounded"></div>
                    <div class="h-1 w-3 md:w-4 bg-red-500 rounded"></div>
                </div>
            </div>

            <!-- RIGHT -->
            <div id="login-card">
                <div
                    class="w-full max-w-md mx-auto
                    backdrop-blur-2xl bg-white/10 border border-white/20
                    rounded-2xl md:rounded-[2rem]
                    p-6 md:p-8 text-white
                    shadow-[0_20px_60px_rgba(0,0,0,0.35)]">

                    <h2 class="text-xl md:text-2xl font-semibold text-center mb-2">
                        Selamat Datang
                    </h2>

                    <p class="text-xs md:text-sm text-blue-100 text-center mb-5 md:mb-6">
                        Silakan login menggunakan akun SSO UBT
                    </p>

                    @if (session('error'))
                        <div
                            class="mb-4 bg-red-500/20 border border-red-400
                            text-red-200 px-4 py-3 rounded-lg text-sm text-center">
                            {{ session('error') }}
                        </div>
                    @else
                        <a href="{{ \App\Http\Middleware\Sso::getLoginLink() }}"
                            class="block text-center bg-gradient-to-r from-blue-600 to-blue-700
                            hover:scale-105 transition duration-300
                            rounded-xl px-5 py-2.5 md:px-6 md:py-3
                            text-base md:text-lg font-semibold shadow-lg">
                            Login SSO UBT
                        </a>
                    @endif

                    <!-- FOOTER -->
                    <div class="pt-4 md:pt-5 border-t border-white/10 mt-5 md:mt-6">
                        <p class="text-[10px] md:text-[11px] text-center leading-relaxed text-blue-100/70">
                            ©{{ now()->year }}
                            <span class="font-semibold text-yellow-400">ASRAMA</span><br>
                            Designed & Developed by
                            <span class="font-medium text-blue-100/90">
                                Muhammad Riski Fauzi |
                                <span class="font-semibold text-yellow-400">
                                    Universitas Bunda Thamrin
                                </span>
                            </span>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = document.getElementById('loader');
                const main = document.getElementById('main-content');
                const left = document.getElementById('left-content');
                const card = document.getElementById('login-card');

                loader.classList.add('hidden');

                main.classList.remove('hidden');
                main.classList.add('visible');

                setTimeout(() => {
                    left.classList.add('animate-left');
                }, 100);

                setTimeout(() => {
                    card.classList.add('animate-drop');
                }, 400);

            }, 300);
        });
    </script>
</body>

</html>
