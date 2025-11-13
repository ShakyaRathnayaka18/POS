@extends('layouts.guest')

@section('title', 'Login - POS System')

@section('content')
<div class="w-full max-w-md space-y-8">
    <!-- Logo and Header -->
    <div class="text-center">
        <div class="flex justify-center">
            <div class="w-16 h-16 bg-indigo-600 dark:bg-indigo-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-cash-register text-white text-3xl"></i>
            </div>
        </div>
        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
            Sign in to POS System
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Enter your credentials to access the system
        </p>
    </div>

    <!-- Login Form -->
    <div class="mt-8 bg-white dark:bg-gray-800 py-8 px-6 shadow-lg rounded-lg sm:px-10">
        <form class="space-y-6" action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Name (Username) -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Username
                </label>
                <div class="mt-1">
                    <input
                        id="name"
                        name="name"
                        type="text"
                        autocomplete="name"
                        required
                        value="{{ old('name') }}"
                        class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('name') border-red-500 @enderror"
                        placeholder="Enter your name"
                    >
                </div>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Password
                </label>
                <div class="mt-1">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('password') border-red-500 @enderror"
                        placeholder="Enter your password"
                    >
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me and Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors"
                >
                    <i class="fas fa-sign-in-alt mr-2 mt-0.5"></i>
                    Sign in
                </button>
            </div>

            <!-- Rate Limit Error -->
            @if (session('status'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
                </div>
            @endif

            @error('throttle')
                <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                    <p class="text-sm text-red-800 dark:text-red-200">{{ $message }}</p>
                </div>
            @enderror
        </form>

        <!-- Test Users Info (Remove in production) -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Development Mode: Test users available
            </p>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} POS System. All rights reserved.
    </p>
</div>
@endsection
