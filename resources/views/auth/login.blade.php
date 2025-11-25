@extends('layouts.guest')

@section('title', 'Login - POS System')

@section('content')
<div class="min-h-screen w-full flex">
    <!-- Left Side - Floating POS Illustration with Blue Gradient -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-900 via-blue-950 to-slate-950 relative overflow-hidden">
        <!-- Subtle Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>

        <!-- Content Container -->
        <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
            <!-- Floating POS Illustration -->
            <div class="mb-8 floating-animation">
                <img
                    src="{{ asset('images/login.png') }}"
                    alt="POS System"
                    class="h-[28rem] w-auto drop-shadow-2xl"
                >
            </div>

            <!-- Hero Text -->
            <div class="text-center max-w-lg">
                <h1 class="text-4xl font-bold mb-6 drop-shadow-lg flex items-center justify-center gap-3">
                    Welcome to
                    <img src="{{ asset('images/VPOS.png') }}" alt="VPOS" class="h-12 w-auto">
                    
                </h1>

            </div>

            <!-- Feature Highlights -->
            <div class="mt-12 grid grid-cols-3 gap-6 w-full max-w-md">
                <div class="flex flex-col items-center group cursor-default">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-white/30 transition-all duration-300 p-3">
                        <img src="{{ asset('images/analytics.png') }}" alt="Analytics" class="w-full h-full object-contain">
                    </div>
                    <span class="text-sm font-medium text-white/90">Analytics</span>
                </div>
                <div class="flex flex-col items-center group cursor-default">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-white/30 transition-all duration-300 p-3">
                        <img src="{{ asset('images/accounting.png') }}" alt="Accounting" class="w-full h-full object-contain">
                    </div>
                    <span class="text-sm font-medium text-white/90">Accounting</span>
                </div>
                <div class="flex flex-col items-center group cursor-default">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-white/30 transition-all duration-300 p-3">
                        <img src="{{ asset('images/payroll.png') }}" alt="Payroll" class="w-full h-full object-contain">
                    </div>
                    <span class="text-sm font-medium text-white/90">Payroll</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <!-- <div class="lg:hidden text-center mb-8">
                <img src="{{ asset('images/h_mart.png') }}" alt="H-Mart" class="h-32 w-auto mx-auto mb-4">
            </div> -->

            <!-- Header with H-Mart Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center mb-4">
                    <img
                        src="{{ asset('images/h_mart.png') }}"
                        alt="H-Mart"
                        class="h-40 w-auto"
                    >
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Sign In
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Enter your credentials to access the system
                </p>
            </div>

            <!-- Login Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 space-y-6 border border-gray-100 dark:border-gray-700">
                <form class="space-y-5" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Username Input -->
                    <div class="group">
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                autocomplete="name"
                                required
                                value="{{ old('name') }}"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('name') border-red-500 ring-2 ring-red-500 @enderror"
                                placeholder="Enter your username"
                            >
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('password') border-red-500 ring-2 ring-red-500 @enderror"
                                placeholder="Enter your password"
                            >
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                Remember me
                            </label>
                        </div>

                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>

                    <!-- Status Messages -->
                    @if (session('status'))
                        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-2"></i>
                                <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- @error('throttle')
                        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-2"></i>
                                <p class="text-sm text-red-800 dark:text-red-200">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror -->
                </form>

                <!-- Test Users Info (Development Mode) -->
                @if (config('app.env') === 'local')
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-center text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Development Mode</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} VertexCoreAI. All rights reserved.
            </p>
        </div>
    </div>
</div>

<!-- Custom Animations -->
<style>
    /* Floating Animation for POS Illustration */
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        25% {
            transform: translateY(-20px) rotate(1deg);
        }
        50% {
            transform: translateY(-15px) rotate(-1deg);
        }
        75% {
            transform: translateY(-25px) rotate(0.5deg);
        }
    }

    .floating-animation {
        animation: float 6s ease-in-out infinite;
        filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.3));
    }

    /* Subtle pulse animation for feature icons */
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        50% {
            box-shadow: 0 0 20px 5px rgba(255, 255, 255, 0.2);
        }
    }
</style>
@endsection
