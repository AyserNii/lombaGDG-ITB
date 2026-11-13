<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#edf1f7">
    <title>500 · Gangguan pada server — BARENG</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 32px 20px;
            color: #1e293b;
            background:
                radial-gradient(ellipse at 50% 34%, rgba(65, 84, 232, 0.12), transparent 42%),
                #edf1f7;
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
        }

        .error-page {
            width: min(100%, 620px);
            text-align: center;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 28px;
            color: #4154e8;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-decoration: none;
        }

        .brand-mark {
            display: grid;
            width: 30px;
            height: 30px;
            place-items: center;
            border-radius: 10px;
            color: #fff;
            background: #4154e8;
            font-size: 15px;
            letter-spacing: 0;
        }

        .error-code {
            margin: 0;
            color: #4154e8;
            font-size: clamp(68px, 17vw, 112px);
            font-weight: 800;
            letter-spacing: -0.09em;
            line-height: 1;
        }

        h1 {
            margin: 16px 0 10px;
            font-size: clamp(22px, 5vw, 30px);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .description {
            max-width: 470px;
            margin: 0 auto;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
        }

        .home-link {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            margin-top: 28px;
            padding: 0 22px;
            border-radius: 14px;
            color: #fff;
            background: #4154e8;
            box-shadow: 0 10px 24px rgba(65, 84, 232, 0.24);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .home-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(65, 84, 232, 0.3);
        }

        .home-link:focus-visible {
            outline: 3px solid #a5b4fc;
            outline-offset: 4px;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="error-page">
        <a class="brand" href="{{ url('/') }}" aria-label="BARENG — kembali ke beranda">
            <span class="brand-mark" aria-hidden="true">B</span>
            BARENG
        </a>

        <p class="error-code" aria-hidden="true">500</p>
        <h1>Terjadi gangguan pada server</h1>
        <p class="description">
            Error 500 berarti server mengalami masalah internal saat memproses permintaanmu.
            Penyebabnya bisa berupa bug aplikasi atau layanan yang sedang bermasalah.
            Ini bukan kesalahan di perangkatmu. Detail teknis tidak ditampilkan demi keamanan;
            silakan coba lagi beberapa saat.
        </p>
        <a class="home-link" href="{{ url('/') }}">Kembali ke beranda</a>
    </main>
</body>
</html>
