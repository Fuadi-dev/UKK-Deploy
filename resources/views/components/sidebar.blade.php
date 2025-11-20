<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white/80 backdrop-blur-lg shadow-2xl border-r border-white/20 flex flex-col" id="sidebar">
    <!-- Logo Section -->
    <div class="flex items-center justify-center p-6 border-b border-white/20 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl blur opacity-75"></div>
                <div class="relative bg-white p-2 rounded-xl">
                    <img src="{{ asset('asset/logo2.png') }}" alt="Logo" class="h-8 w-auto">
                </div>
            </div>
            <div>
                <h1 class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    ProjectHub
                </h1>
                <p class="text-xs text-gray-500">Management System</p>
            </div>
        </div>
    </div>

    <!-- User Profile Section -->
    <div class="p-4 border-b border-white/20 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <x-user-avatar :user="Auth::user()" size="md" />
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 rounded-full capitalize">
                        {{ Auth::user()->role }}
                    </span>
                    @if(Auth::user()->google_id)
                        <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full">
                            Google
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu - Scrollable -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto"
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-800 transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-800 shadow-lg' : '' }}">
            <div class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} transition-colors">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v1H8V5z"></path>
                </svg>
            </div>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Projects -->
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('projects') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-800 transition-all duration-200 group {{ request()->routeIs('projects*') || request()->routeIs('projects.create') || request()->routeIs('projects.edit') || request()->routeIs('projects.detail') ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('projects*') || request()->routeIs('projects.create') || request()->routeIs('projects.edit') || request()->routeIs('projects.detail') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="font-medium">Projects</span>
            </a>
        @endif

        <!-- My Projects -->
        @if(auth()->user()->role === 'leader')
            <a href="{{ route('leader.projects') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-800 transition-all duration-200 group {{ request()->routeIs('leader.projects*') ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.projects*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="font-medium">My Project</span>
            </a>
        @endif

        @if(auth()->user()->role === 'leader')
            <!-- Board -->
            <a href="{{ route('leader.boards') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-800 transition-all duration-200 group {{ request()->routeIs('leader.boards*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.boards*') ? 'text-amber-600' : 'text-gray-400 group-hover:text-amber-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <span class="font-medium">Boards</span>
            </a>
        @endif

        @if(auth()->user()->role === 'leader')
            <!-- Cards Management -->
            <a href="{{ route('leader.cards') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-800 transition-all duration-200 group {{ request()->routeIs('leader.cards*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.cards*') ? 'text-amber-600' : 'text-gray-400 group-hover:text-amber-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <span class="font-medium">Cards</span>
            </a>
        @endif

        @if(auth()->user()->role === 'leader')
            <!-- Assigned Projects -->
            <a href="{{ route('leader.assigned.cards') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-800 transition-all duration-200 group {{ request()->routeIs('leader.assigned.cards') ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.assigned.cards') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }} transition-colors">
                    <!-- Icon berbeda untuk Assigned Cards (Review/Approve) -->
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium">Assigned Cards</span>
            </a>
        @endif

        @if(auth()->user()->role === 'leader')
            <!-- Assignment History -->
            <a href="{{ route('leader.assignment.history') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 hover:text-pink-800 transition-all duration-200 group {{ request()->routeIs('leader.assignment.history') ? 'bg-gradient-to-r from-pink-50 to-rose-50 text-pink-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.assignment.history') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <span class="font-medium">Assignment History</span>
            </a>
        @endif

        @if(auth()->user()->role === 'user')
            <!-- My Card -->
            <a href="{{ route('user.my-card') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-800 transition-all duration-200 group {{ request()->routeIs('user.my-card*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('user.my-card*') ? 'text-amber-600' : 'text-gray-400 group-hover:text-amber-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <span class="font-medium">My Card</span>
            </a>
        @endif

        @if(auth()->user()->role === 'user')
            <!-- My Subtasks -->
            <a href="{{ route('user.subtasks') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 hover:text-purple-800 transition-all duration-200 group {{ request()->routeIs('user.subtasks*') ? 'bg-gradient-to-r from-purple-50 to-pink-50 text-purple-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('user.subtasks*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <span class="font-medium">My Subtasks</span>
            </a>
        @endif

        @if(auth()->user()->role === 'user')
            <!-- My Permissions -->
            <a href="{{ route('user.permissions') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 hover:text-green-800 transition-all duration-200 group {{ request()->routeIs('user.permissions*') ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('user.permissions*') ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <span class="font-medium">My Permissions</span>
            </a>
        @endif

        @if(auth()->user()->role === 'user')
            <!-- My Time Log -->
            <a href="{{ route('user.time-logs') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 hover:text-emerald-800 transition-all duration-200 group {{ request()->routeIs('user.time-logs*') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('user.time-logs*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium">Time Log</span>
            </a>
        @endif


        @if(auth()->user()->role === 'leader')
            <!-- Card Time Logs -->
            <a href="{{ route('leader.time-logs') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 hover:text-emerald-800 transition-all duration-200 group {{ request()->routeIs('leader.time-logs*') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.time-logs*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium">Card Time Logs</span>
            </a>
        @endif

        @if(auth()->user()->role === 'leader')
            <!-- Permission Management -->
            <a href="{{ route('leader.permissions') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 hover:text-indigo-800 transition-all duration-200 group {{ request()->routeIs('leader.permissions*') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('leader.permissions*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <span class="font-medium">Permission</span>
            </a>
        @endif


        <!-- Admin Only Features -->
        @if(auth()->user()->role === 'admin')
            <!-- User Management -->
            <a href="{{ route('users') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-teal-50 hover:to-cyan-50 hover:text-teal-800 transition-all duration-200 group {{ request()->routeIs('users*') ? 'bg-gradient-to-r from-teal-50 to-cyan-50 text-teal-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('users*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-teal-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="font-medium">User Management</span>
            </a>

            <!-- Project Review -->
            <a href="{{ route('review') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 hover:text-purple-800 transition-all duration-200 group {{ request()->routeIs('review*') ? 'bg-gradient-to-r from-purple-50 to-pink-50 text-purple-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('review*') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <span class="font-medium">Project Review</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('reports') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-violet-50 hover:to-purple-50 hover:text-violet-800 transition-all duration-200 group {{ request()->routeIs('reports*') ? 'bg-gradient-to-r from-violet-50 to-purple-50 text-violet-800 shadow-lg' : '' }}">
                <div class="w-5 h-5 {{ request()->routeIs('reports*') ? 'text-violet-600' : 'text-gray-400 group-hover:text-violet-600' }} transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="font-medium">Reports</span>
            </a>
        @endif

    </nav>

    <!-- Bottom Section -->
    <div class="p-4 border-t border-white/20 flex-shrink-0">
        <!-- Profile -->
        <a href="{{ route('profile') }}" class="w-full flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-gray-800 transition-all duration-200 group mb-2 {{ request()->routeIs('profile*') ? 'bg-gradient-to-r from-gray-50 to-gray-100 text-gray-800 shadow-lg' : '' }}">
            <div class="w-5 h-5 {{ request()->routeIs('profile*') ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600' }} transition-colors">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="font-medium">Profile</span>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 px-3 py-3 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:text-red-800 transition-all duration-200 group">
                <div class="w-5 h-5 text-gray-400 group-hover:text-red-600 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm lg:hidden hidden transition-all duration-300" id="sidebar-overlay"></div>

<!-- Mobile Menu Button -->
<button class="fixed top-4 left-4 z-50 lg:hidden bg-white/80 backdrop-blur-lg p-2 rounded-xl shadow-lg border border-white/20" id="mobile-menu-button">
    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const mobileButton = document.getElementById('mobile-menu-button');

    // Mobile menu toggle
    mobileButton.addEventListener('click', function() {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Make sidebar responsive
    function handleResize() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Call on page load
});
</script>

<style>
#sidebar {
    transition: transform 0.3s ease-in-out;
}

/* Custom scrollbar untuk navigation */
#sidebar nav::-webkit-scrollbar {
    width: 6px;
}

#sidebar nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

#sidebar nav::-webkit-scrollbar-thumb {
    background: rgba(139, 92, 246, 0.3);
    border-radius: 10px;
}

#sidebar nav::-webkit-scrollbar-thumb:hover {
    background: rgba(139, 92, 246, 0.5);
}

@media (max-width: 1023px) {
    #sidebar {
        transform: translateX(-100%);
    }
    
    #sidebar:not(.-translate-x-full) {
        transform: translateX(0);
    }
}
</style>
