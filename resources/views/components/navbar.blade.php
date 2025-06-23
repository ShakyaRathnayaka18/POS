<nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button type="button" class="lg:hidden -ml-2 mr-2 inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500" onclick="toggleSidebar()">
                    <i class="fas fa-bars h-6 w-6"></i>
                </button>
                
                <!-- Logo -->
                <div class="flex flex-shrink-0 items-center">
                    <i class="fas fa-cash-register text-2xl text-primary-600 mr-2"></i>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">POS System</h1>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Dark mode toggle -->
                <button onclick="toggleDarkMode()" class="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>

                <!-- Notifications -->
                <button class="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700 relative">
                    <i class="fas fa-bell"></i>
                    <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-xs text-white flex items-center justify-center">3</span>
                </button>

                <!-- User dropdown -->
                <div class="relative">
                    <button class="flex items-center space-x-2 rounded-md p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=John+Doe&background=3b82f6&color=fff" alt="User">
                        <span class="hidden md:block">John Doe</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
