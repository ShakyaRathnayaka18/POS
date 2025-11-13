@extends('layouts.guest')

@section('title', 'Reset Password - POS System')

@section('content')
<div class="w-full max-w-md space-y-8">
    <!-- Logo and Header -->
    <div class="text-center">
        <div class="flex justify-center">
            <div class="w-16 h-16 bg-indigo-600 dark:bg-indigo-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-lock-open text-white text-3xl"></i>
            </div>
        </div>
        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
            Reset your password
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Enter your email and new password below
        </p>
    </div>

    <!-- Reset Password Form -->
    <div class="mt-8 bg-white dark:bg-gray-800 py-8 px-6 shadow-lg rounded-lg sm:px-10">
        <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email address
                </label>
                <div class="mt-1">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="{{ old('email', $request->email) }}"
                        class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('email') border-red-500 @enderror"
                        placeholder="you@example.com"
                    >
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    New Password
                </label>
                <div class="mt-1">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('password') border-red-500 @enderror"
                        placeholder="Enter new password"
                    >
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Confirm Password
                </label>
                <div class="mt-1">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                        placeholder="Confirm new password"
                    >
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors"
                >
                    <i class="fas fa-check mr-2 mt-0.5"></i>
                    Reset Password
                </button>
            </div>

            <!-- Status Message -->
            @if (session('status'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
                </div>
            @endif
        </form>

        <!-- Back to Login -->
        <div class="mt-6">
            <a href="{{ route('login') }}" class="flex items-center justify-center text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to login
            </a>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} POS System. All rights reserved.
    </p>
</div>
@endsection
