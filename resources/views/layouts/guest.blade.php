<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50 dark:bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS System')</title>

    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')
</head>

<body class="h-full">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script>
        // Dark mode initialization
        if (localStorage.getItem('darkMode') === null) {
            // No preference set, enable dark mode by default
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @stack('scripts')
</body>

</html>
