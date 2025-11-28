<nav
    class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="mx-auto max-w px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button type="button"
                    class="lg:hidden -ml-2 mr-2 inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500"
                    onclick="toggleSidebar()">
                    <i class="fas fa-bars h-6 w-6"></i>
                </button>

                <!-- Logo -->
                <div class="flex flex-shrink-0 items-center">
                    <a href="{{ route('cashier.dashboard') }}" class="flex items-center">
                        <img src="{{ asset('images/h_mart.png') }}" alt="POS Logo" class="h-14 w-auto mr-1 ml-2" />

                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Dark mode toggle -->
                <button onclick="toggleDarkMode()"
                    class="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>

                <!-- Notifications -->
                <button
                    class="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700 relative">
                    <i class="fas fa-bell"></i>
                    <span
                        class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-xs text-white flex items-center justify-center">3</span>
                </button>

                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center space-x-2 rounded-md p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <img class="h-8 w-8 rounded-full"
                            src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff"
                            alt="{{ auth()->user()->name }}">
                        <div class="hidden md:flex flex-col items-start">
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->roles->first())
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ auth()->user()->roles->first()->name }}
                                </span>
                            @endif
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" x-transition
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                <div class="font-medium">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
                            </div>

                            <!-- <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user-circle mr-2"></i> Profile
                            </a>

                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a> -->

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
