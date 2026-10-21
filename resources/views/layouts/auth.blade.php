<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi — RanggaMotret.id')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-square.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .clean-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .btn-dark {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-dark:hover {
            background-color: #1e293b;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50">
    <div class="w-full max-w-md">
        <!-- Logo Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center">
                <img src="{{ asset('images/logo.png') }}" alt="RanggaMotret.id" class="w-48 h-auto object-contain mx-auto">
            </a>
        </div>

        @yield('content')
    </div>
</body>
</html>
