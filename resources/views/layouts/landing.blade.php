<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="images/main-app-logo.webp" type="image/x-icon">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ===== Loader ===== */
        .loader {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.8);
            z-index: 9999;
            transition: opacity .5s ease-out;
        }
        .loader.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===== Main ===== */
        main.hidden { opacity: 0; }
        main.visible {
            opacity: 1;
            transition: opacity .5s ease-in;
        }

        .bg-overlay {
            background: rgba(230,230,250,.45);
        }

        .login-card {
            background: rgba(0,0,0,.8);
            border-radius: 0 1.5rem 0 1.5rem;
            color: #fff;
        }

        .text-lavender { color: #e6e6fa; }
    </style>
</head>

<body>
    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
    </div>

    <!-- Main -->
    <main id="main-content"
          class="hidden min-vh-100 d-flex align-items-center justify-content-center position-relative px-2"
          style="background:url('/images/wallpaper-ubt-ghibli.webp') center/cover no-repeat;">

        <div class="position-absolute top-0 start-0 w-100 h-100 bg-overlay"></div>

        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-24 col-sm-8 col-xl-4">
                    <div class="login-card text-center py-4 px-3">

                        <img src="images/ubt-logo.webp" width="150" height="150" class="mb-3">

                        <div class="fs-2 fw-semibold text-lavender mt-3">
                            PRESENSI UBT
                        </div>

                        <div class="fs-6 fst-italic text-lavender mb-3">
                            Aplikasi Pencatatan Presensi Universitas Bunda Thamrin Akademik UBT
                        </div>

                        @if (session('error'))
                            <div class="alert bg-light text-danger fw-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 256 256">
                                    <path fill="red"
                                          d="M128 112a28 28 0 0 0-8 54.83V184a8 8 0 0 0 16 0v-17.17a28 28 0 0 0-8-54.83m0 40a12 12 0 1 1 12-12a12 12 0 0 1-12 12m80-72h-32V56a48 48 0 0 0-96 0v24H48a16 16 0 0 0-16 16v112a16 16 0 0 0 16 16h160a16 16 0 0 0 16-16V96a16 16 0 0 0-16-16M96 56a32 32 0 0 1 64 0v24H96Zm112 152H48V96h160z"/>
                                </svg>
                                <div class="mt-2">{{ session('error') }}</div>
                            </div>
                        @else
                            <a href="{{ \App\Http\Middleware\Sso::getLoginLink() }}"
                               class="btn btn-primary btn-lg px-5 my-4 fw-semibold"
                               style="transition:.3s">
                                Login SSO UBT
                            </a>
                        @endif

                        <p class="text-lavender mt-3 mb-0">
                            © IT PT. Thamrin Sinar Surya<br>
                            Universitas Bunda Thamrin<br>
                            2025
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loader').classList.add('hidden');
                document.getElementById('main-content').classList.remove('hidden');
                document.getElementById('main-content').classList.add('visible');
            }, 300);
        });
    </script>
</body>
</html>
