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

    <!-- Toastr Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @stack('styles')
</head>

<body class="h-full">
    <div class="min-h-full">
        @include('components.navbar')


        <div class="flex ml-64 pt-16 min-h-screen">
            @include('components.sidebar')

            <!-- Main content -->
            <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 min-h-screen">
                <main class="flex-1 py-6 flex flex-col">
                    <div class="mx-auto w-full max-w px-4 sm:px-6 lg:px-8 flex-1">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

    </div>

    <!-- jQuery (required for toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Scripts -->
    <script>
        // Dark mode toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Initialize dark mode
        if (localStorage.getItem('darkMode') === null) {
            // No preference set, enable dark mode by default
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
    </script>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
                @if(session('error'))
                    toastr.error("{{ session('error') }}");
                @endif
                @if($errors->any())
                    toastr.error(`{!! implode('<br>', $errors->all()) !!}`);
                @endif
                                        });
        </script>
    @endpush

    @stack('scripts')
</body>

</html>
