<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIPUS-Del' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            --del-purple: #4C1D95;
            --del-purple-light: #6D28D9;
        }

        body {
            background-color: #F5F3FF;
            color: #1F2937;
            font-family: Arial, sans-serif;
        }

        .navbar-del {
            background-color: var(--del-purple);
        }

        .navbar-brand,
        .navbar-del .nav-link {
            color: white;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .navbar-del .nav-link:hover {
            color: #DDD6FE;
        }

        .page-header {
            color: var(--del-purple);
        }

        .btn-del {
            background-color: var(--del-purple);
            color: white;
        }

        .btn-del:hover {
            background-color: var(--del-purple-light);
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        footer {
            color: #6B7280;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-del">
        <div class="container">
            <a class="navbar-brand" href="{{ route('bukus.index') }}">
                SIPUS-Del
            </a>

            <button class="navbar-toggler bg-light" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarMenu"
                    aria-controls="navbarMenu"
                    aria-expanded="false"
                    aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="{{ route('bukus.index') }}">
                        Daftar Buku
                    </a>
                    <a class="nav-link" href="{{ route('kategoris.index') }}">
                        Kategori
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup"></button>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="container text-center py-4">
        <small>SIPUS-Del &copy; {{ date('Y') }} — Sistem Informasi Perpustakaan</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
