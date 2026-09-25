@php
$setting = \App\Helpers\Helpers::setting();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->application_name }}</title>
    <link rel="icon" href="{{ asset('upload/setting/'.$setting->small_icon) }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f7fafc;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* --- SISI KIRI: BACKGROUND ANIMASI --- */
        .bg-side {
            flex: 1;
            position: relative;
            background: linear-gradient(135deg, #f32121 0%,  #d4750063 100%), url({{ asset('storage/upload/setting/'.$setting->background_login) }}) no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
            overflow: hidden;
        }

        /* Efek Zoom Lambat pada Background saat Load */
        .bg-side::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            z-index: -1;
            transform: scale(1.1);
            animation: slowZoom 10s ease forwards;
        }

        .bg-side h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateX(-30px);
            animation: slideInText 0.8s 0.2s ease forwards;
        }

        .bg-side p {
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 500px;
            color: #e2e8f0;
            opacity: 0;
            transform: translateX(-30px);
            animation: slideInText 0.8s 0.4s ease forwards;
        }

        /* --- SISI KANAN: FORM LOGIN --- */
        .form-side {
            width: 450px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
            z-index: 10;
            overflow-y: auto;

            /* Animasi Form Masuk */
            opacity: 0;
            transform: translateX(50px);
            animation: slideInForm 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .form-header {
            margin-bottom: 25px;
        }

        .form-header h2 {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #718096;
            font-size: 14px;
        }

        /* --- STYLING CUSTOM UNTUK UTILITY ALERT (PERBAIKAN UTAMA) --- */
        .alert {
            position: relative;
            padding: 15px 40px 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            animation: slideDownAlert 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1) forwards;
        }

        /* Variasi Warna Alert */
        .bg-success {
            background-color: #e8f5e9;
            /* Green 50 */
            border-left: 5px solid #4caf50;
            /* Green 500 */
            color: #1b5e20;
            /* Green 900 */
        }

        .bg-danger {
            background-color: #ffebee;
            /* Red 50 */
            border-left: 5px solid #f44336;
            /* Red 500 */
            color: #b71c1c;
            /* Red 900 */
        }

        .bg-warning {
            background-color: #fffde7;
            /* Yellow 50 */
            border-left: 5px solid #fbc02d;
            /* Yellow 700 (lebih kontras dari 500) */
            color: #f57f17;
            /* Yellow 900 */
        }

        .alert h4 {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
        }

        .alert span {
            font-size: 13px;
            line-height: 1.4;
        }

        /* Tombol Close Sila (X) */
        .btn-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .btn-icon:hover {
            opacity: 1;
        }

        /* Menyesuaikan warna icon SVG agar mengikuti tema alert */
        .bg-success .svg-icon svg {
            fill: #0e9f6e;
        }

        .bg-danger .svg-icon svg {
            fill: #f98080;
        }

        .bg-warning .svg-icon svg {
            fill: #eab308;
        }

        /* Input Group dengan Floating Label */
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 12px;
            border: 2px solid #e2e8f0;
            background: transparent;
            border-radius: 8px;
            outline: none;
            font-size: 16px;
            color: #2d3748;
            transition: all 0.3s ease;
        }

        .input-group input:focus,
        .input-group input:valid {
            border-color: #ea6666ff;
        }

        .input-group label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus~label,
        .input-group input:valid~label {
            top: 0px;
            left: 8px;
            font-size: 12px;
            padding: 0 5px;
            background: #fff;
            color: #ea6666ff;
            font-weight: 600;
        }

        /* Opsi & Tombol */
        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #4a5568;
        }

        .options label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .options a {
            color: #2196f3;
            text-decoration: none;
            font-weight: 500;
        }

        .options a:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #F44336 0%, #c11205 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(234, 102, 102, 0.3);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(234, 102, 102, 0.5);
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #718096;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        /* --- KEYFRAMES ANIMASI --- */
        @keyframes slowZoom {
            0% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes slideInText {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInForm {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideDownAlert {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsif untuk Layar HP */
        @media (max-width: 850px) {
            .bg-side {
                display: none;
            }

            .form-side {
                width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="bg-side">
            <img src="{{ asset('storage/upload/setting/'.$setting->large_icon) }}" alt="Logo" style="width: 700px; margin-bottom: 15px;">
        </div>

        <div class="form-side">
            <div class="form-header">
                <center>
                    <h2> LOGIN</h2>
                    <p>Silakan masukkan detail akun Anda</p>
                </center>
            </div>

            <form class="text-left" method="POST" action="{{ url('login') }}" enctype="multipart/form-data">
                @csrf


                @if ($message = Session::get('status'))
                <div class="alert alert-dismissible bg-success">
                    <div>
                        <h4>Berhasil!</h4>
                        <span>{{ $message }}</span>
                    </div>
                    <button type="button" class="btn-icon" onclick="this.parentElement.style.display='none'">
                        <span class="svg-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                        </span>
                    </button>
                </div>
                @endif

                @if ($message = Session::get('status2'))
                <div class="alert alert-dismissible bg-danger">
                    <div>
                        <h4>Gagal!</h4>
                        <span>{{ $message }}</span>
                    </div>
                    <button type="button" class="btn-icon" onclick="this.parentElement.style.display='none'">
                        <span class="svg-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                        </span>
                    </button>
                </div>
                @endif

                @if ($message = Session::get('status3'))
                <div class="alert alert-dismissible bg-warning">
                    <div>
                        <h4>Menunggu!</h4>
                        <span>{{ $message }}</span>
                    </div>
                    <button type="button" class="btn-icon" onclick="this.parentElement.style.display='none'">
                        <span class="svg-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                        </span>
                    </button>
                </div>
                @endif

                <div class="input-group">
                    <input type="text" name="name" required>
                    <label>Username / Email</label>
                </div>

                <div class="input-group">
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>

                <div class="options">
                    <label>
                        <input type="checkbox" name="remember" id="remember"> Ingat saya
                    </label>
                </div>

                    <div class="mb-12 text-center" style="text-align:center;margin-bottom:20px;">

                        <div class="cf-turnstile"
                            data-sitekey="{{ config('services.turnstile.site_key') }}">
                        </div>

                        @error('captcha')
                        <small class="text-danger d-block mt-2">
                            {{ $message }}
                        </small>
                        @enderror

                        @error('cf-turnstile-response')
                        <small class="text-danger d-block mt-2">
                            {{ $message }}
                        </small>
                        @enderror

                    </div>

                <button type="submit" class="btn-login">Masuk Aplikasi</button>

            </form>
        </div>

    </div>

</body>

</html>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>