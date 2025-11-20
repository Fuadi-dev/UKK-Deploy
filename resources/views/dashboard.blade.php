<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Project Management</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-5 pointer-events-none">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgb(99 102 241) 1px, transparent 0); background-size: 20px 20px;"></div>
    </div>

    <!-- Include Sidebar Component -->
    @include('components.sidebar')

    <!-- Main Content Area -->
    <div class="lg:ml-64">
        <!-- Top Header Bar -->
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <!-- Breadcrumb - Hidden on mobile -->
                <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600">
                    <span>Home</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Dashboard</span>
                </div>
                
                <!-- Mobile: Show simple title -->
                <h2 class="md:hidden text-lg font-bold text-gray-800">Dashboard</h2>
                
                <!-- Notification and Profile -->
                <div class="flex items-center space-x-2 lg:space-x-4">
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(Auth::user()->role === 'leader' && isset($cardsNeedingReview) && $cardsNeedingReview->count() > 0)
                            <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">{{ $cardsNeedingReview->count() }}</span>
                        @endif
                    </button>

                    <div class="relative">
                        <button class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                            <x-user-avatar :user="Auth::user()" size="sm" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Dashboard Content -->
        <main class="p-3 lg:p-6">
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-5 backdrop-blur-sm">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Welcome Section -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-8 mb-4 lg:mb-8">
                <div class="flex items-start justify-between mb-4 lg:mb-6">
                    <div>
                        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-1 lg:mb-2">
                            Welcome, {{ Auth::user()->name }}!
                        </h1>
                        <p class="text-gray-600 text-sm lg:text-lg hidden md:block">
                            @if(Auth::user()->role === 'admin')
                                Here's an overview of the entire system.
                            @elseif(Auth::user()->role === 'leader')
                                Here's what's happening with your projects and team today.
                            @else
                                Here's your progress and tasks for today.
                            @endif
                        </p>
                    </div>
                    <!-- Date - Hidden on mobile -->
                    <div class="text-right hidden lg:block">
                        <p class="text-sm text-gray-500">{{ date('l, F j, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ date('g:i A') }}</p>
                    </div>
                </div>

                <!-- Admin Dashboard Stats -->
                @if(Auth::user()->role === 'admin')
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 lg:gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-xs lg:text-sm font-medium">Total Projects</p>
                                    <p class="text-xl lg:text-2xl font-bold text-blue-800">{{ $totalProjects ?? 0 }}</p>
                                    <p class="text-xs text-blue-500 mt-1 hidden md:block">{{ $activeProjects ?? 0 }} active</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-xs lg:text-sm font-medium">Completed</p>
                                    <p class="text-xl lg:text-2xl font-bold text-emerald-800">{{ $completedProjects ?? 0 }}</p>
                                    <p class="text-xs text-emerald-500 mt-1 hidden md:block">{{ $expiredProjects ?? 0 }} expired</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-rose-50 to-pink-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-rose-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-rose-600 text-xs lg:text-sm font-medium">Users</p>
                                    <p class="text-xl lg:text-2xl font-bold text-rose-800">{{ ($leadersCount ?? 0) + ($developersCount ?? 0) + ($designersCount ?? 0) }}</p>
                                    <p class="text-xs text-rose-500 mt-1 hidden md:block">{{ $availableUsers ?? 0 }} available</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-rose-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-amber-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-600 text-xs lg:text-sm font-medium">Roles</p>
                                    <p class="text-xl lg:text-2xl font-bold text-amber-800">{{ ($leadersCount ?? 0) + ($developersCount ?? 0) + ($designersCount ?? 0) }}</p>
                                    <p class="text-xs text-amber-500 mt-1 hidden md:block">{{ $leadersCount ?? 0 }} leaders, {{ $developersCount ?? 0 }} devs</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Leader Dashboard Stats -->
                @elseif(Auth::user()->role === 'leader')
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 lg:gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-xs lg:text-sm font-medium">Projects</p>
                                    <p class="text-xl lg:text-2xl font-bold text-blue-800">{{ $myProjectsCount ?? 0 }}</p>
                                    <p class="text-xs text-blue-500 mt-1 hidden md:block">{{ $activeProjectsCount ?? 0 }} active</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-amber-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-600 text-xs lg:text-sm font-medium">Progress</p>
                                    <p class="text-xl lg:text-2xl font-bold text-amber-800">{{ $inProgressCards ?? 0 }}</p>
                                    <p class="text-xs text-amber-500 mt-1 hidden md:block">{{ $todoCards ?? 0 }} to do</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-xs lg:text-sm font-medium">Review</p>
                                    <p class="text-xl lg:text-2xl font-bold text-purple-800">{{ $cardsNeedingReview->count() ?? 0 }}</p>
                                    <p class="text-xs text-purple-500 mt-1 hidden md:block">Pending approval</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-xs lg:text-sm font-medium">Team</p>
                                    <p class="text-xl lg:text-2xl font-bold text-emerald-800">{{ $teamMembersCount ?? 0 }}</p>
                                    <p class="text-xs text-emerald-500 mt-1 hidden md:block">{{ $doneCards ?? 0 }} done</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- User (Developer/Designer) Dashboard Stats -->
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 lg:gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-xs lg:text-sm font-medium">Card</p>
                                    <p class="text-xl lg:text-2xl font-bold text-blue-800">{{ $currentCard ? 1 : 0 }}</p>
                                    <p class="text-xs text-blue-500 mt-1 hidden md:block">
                                        @if($currentCard)
                                            {{ ucfirst($currentCard->status) }}
                                        @else
                                            No active card
                                        @endif
                                    </p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-amber-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-600 text-xs lg:text-sm font-medium">Subtasks</p>
                                    <p class="text-xl lg:text-2xl font-bold text-amber-800">{{ $completedSubtasks ?? 0 }}/{{ $totalSubtasks ?? 0 }}</p>
                                    <p class="text-xs text-amber-500 mt-1 hidden md:block">
                                        @if($totalSubtasks > 0)
                                            {{ round(($completedSubtasks / $totalSubtasks) * 100) }}% done
                                        @else
                                            No subtasks
                                        @endif
                                    </p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-xs lg:text-sm font-medium">Today</p>
                                    <p class="text-xl lg:text-2xl font-bold text-emerald-800">{{ $timeToday ?? '0h' }}</p>
                                    <p class="text-xs text-emerald-500 mt-1 hidden md:block">{{ $timeThisWeek ?? '0h' }} this week</p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-xs lg:text-sm font-medium">Month</p>
                                    <p class="text-xl lg:text-2xl font-bold text-purple-800">{{ $timeThisMonth ?? '0h' }}</p>
                                    <p class="text-xs text-purple-500 mt-1 hidden md:block">
                                        @if($runningTimer)
                                            <span class="inline-flex items-center">
                                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-1"></span>
                                                Timer running
                                            </span>
                                        @else
                                            No active timer
                                        @endif
                                    </p>
                                </div>
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Role-specific Content Sections -->
            @if(Auth::user()->role === 'admin')
                <!-- Admin: Expiring Projects Section - Hidden on mobile -->
                @if(isset($expiringProjects) && $expiringProjects->count() > 0)
                    <div class="hidden md:block bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-amber-800">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Projects Expiring Soon
                            </h2>
                            <a href="{{ route('projects') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View all →</a>
                        </div>
                        <div class="space-y-3">
                            @foreach($expiringProjects as $project)
                                <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl border border-amber-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $project->name }}</h4>
                                            <p class="text-sm text-gray-600">Leader: {{ $project->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-amber-700">Expires: {{ $project->deadline->format('M d, Y') }}</p>
                                        <p class="text-xs text-amber-600">{{ $project->deadline->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Admin: Recent Projects & Pending Permissions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-8">
                    <!-- Recent Projects -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Recent Projects</h3>
                        <div class="space-y-2 lg:space-y-3">
                            @forelse($recentProjects ?? [] as $project)
                                <div class="flex items-center space-x-2 lg:space-x-4 p-2 lg:p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-semibold text-xs lg:text-sm">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 text-sm lg:text-base truncate">{{ $project->name }}</h4>
                                        <p class="text-xs lg:text-sm text-gray-600 hidden md:block">{{ $project->user->name }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap
                                        {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $project->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">{{ ucfirst($project->status) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent projects</p>
                            @endforelse
                        </div>
                        <a href="{{ route('projects') }}" class="mt-3 lg:mt-4 hidden md:block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">View all projects →</a>
                    </div>

                    <!-- Registered Users -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Registered Users</h3>
                        <div class="space-y-2 lg:space-y-4">
                            <div class="flex items-center justify-between p-3 lg:p-4 rounded-xl bg-blue-50">
                                <div class="flex items-center space-x-2 lg:space-x-3">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm lg:text-base">Project Leaders</h4>
                                        <p class="text-xs lg:text-sm text-gray-600 hidden md:block">Managing projects</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl lg:text-2xl font-bold text-blue-600">{{ $leadersCount ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 lg:p-4 rounded-xl bg-emerald-50">
                                <div class="flex items-center space-x-2 lg:space-x-3">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm lg:text-base">Developers</h4>
                                        <p class="text-xs lg:text-sm text-gray-600 hidden md:block">Building features</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl lg:text-2xl font-bold text-emerald-600">{{ $developersCount ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 lg:p-4 rounded-xl bg-purple-50">
                                <div class="flex items-center space-x-2 lg:space-x-3">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm lg:text-base">Designers</h4>
                                        <p class="text-xs lg:text-sm text-gray-600 hidden md:block">Creating designs</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl lg:text-2xl font-bold text-purple-600">{{ $designersCount ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="mt-3 lg:mt-4 p-2 lg:p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center justify-between text-xs lg:text-sm">
                                    <span class="text-gray-600">Available</span>
                                    <span class="font-semibold text-green-600">{{ $availableUsers ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs lg:text-sm mt-2">
                                    <span class="text-gray-600">Assigned</span>
                                    <span class="font-semibold text-amber-600">{{ $busyUsers ?? 0 }} users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(Auth::user()->role === 'leader')
                <!-- Leader: Cards Needing Review - Hidden on mobile -->
                @if(isset($cardsNeedingReview) && $cardsNeedingReview->count() > 0)
                    <div class="hidden md:block bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-purple-800">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Cards Waiting for Review
                            </h2>
                            <span class="text-purple-600 text-sm font-medium">{{ $cardsNeedingReview->count() }} pending</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($cardsNeedingReview as $card)
                                <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl border border-purple-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $card->card_title }}</h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $card->board->project->name }}
                                                @if($card->user)
                                                    - {{ $card->user->name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-purple-600 font-medium">Ready for Review</p>
                                        <a href="{{ route('projects.show', $card->board->project->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800">View Card →</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Leader: Team Performance (Fastest & Slowest Workers) -->
                @if((isset($fastestWorker) && $fastestWorker) || (isset($slowestWorker) && $slowestWorker))
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6 mb-4 lg:mb-8">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4 flex items-center">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Team Performance
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:gap-4">
                            <!-- Fastest Worker -->
                            @if(isset($fastestWorker) && $fastestWorker)
                                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-4 lg:p-5 rounded-xl border-2 border-emerald-300">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-emerald-600 font-semibold">🏆 FASTEST WORKER</p>
                                            <p class="text-sm text-emerald-700">Most Efficient</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 mb-3">
                                        <x-user-avatar :user="$fastestWorker['user']" size="md" />
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-gray-800 text-base lg:text-lg truncate">{{ $fastestWorker['user']->name }}</h4>
                                            <p class="text-xs text-gray-600">{{ ucfirst($fastestWorker['user']->role) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 mt-3">
                                        <div class="bg-white/80 rounded-lg p-2">
                                            <p class="text-xs text-gray-600">Avg Time/Card</p>
                                            <p class="text-lg font-bold text-emerald-600">{{ round($fastestWorker['avg_time'] / 60, 1) }}h</p>
                                        </div>
                                        <div class="bg-white/80 rounded-lg p-2">
                                            <p class="text-xs text-gray-600">Completed</p>
                                            <p class="text-lg font-bold text-emerald-600">{{ $fastestWorker['completed_cards'] }} cards</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 p-2 bg-emerald-100 rounded-lg">
                                        <p class="text-xs text-emerald-800 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Total: {{ $fastestWorker['total_hours'] }} hours worked
                                        </p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Slowest Worker or Placeholder -->
                            @if(isset($slowestWorker) && $slowestWorker && (!isset($fastestWorker) || $slowestWorker['user']->id !== $fastestWorker['user']->id))
                                <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-4 lg:p-5 rounded-xl border-2 border-amber-300">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-amber-600 font-semibold">📊 NEEDS ATTENTION</p>
                                            <p class="text-sm text-amber-700">Longer Completion Time</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 mb-3">
                                        <x-user-avatar :user="$slowestWorker['user']" size="md" />
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-gray-800 text-base lg:text-lg truncate">{{ $slowestWorker['user']->name }}</h4>
                                            <p class="text-xs text-gray-600">{{ ucfirst($slowestWorker['user']->role) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 mt-3">
                                        <div class="bg-white/80 rounded-lg p-2">
                                            <p class="text-xs text-gray-600">Avg Time/Card</p>
                                            <p class="text-lg font-bold text-amber-600">{{ round($slowestWorker['avg_time'] / 60, 1) }}h</p>
                                        </div>
                                        <div class="bg-white/80 rounded-lg p-2">
                                            <p class="text-xs text-gray-600">Completed</p>
                                            <p class="text-lg font-bold text-amber-600">{{ $slowestWorker['completed_cards'] }} cards</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 p-2 bg-amber-100 rounded-lg">
                                        <p class="text-xs text-amber-800 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                            Total: {{ $slowestWorker['total_hours'] }} hours worked
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 lg:p-5 rounded-xl border-2 border-blue-300 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700 mb-1">Need More Data</p>
                                        <p class="text-xs text-gray-500">Waiting for more team members<br>to complete their cards</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Leader: Top Performing Users -->
                @if(isset($topPerformingUsers) && count($topPerformingUsers) > 0)
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6 mb-4 lg:mb-8">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4 flex items-center">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            Top Performing Team Members
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                            @foreach($topPerformingUsers as $index => $performer)
                                <div class="bg-gradient-to-br 
                                    {{ $index === 0 ? 'from-purple-50 to-indigo-50 border-purple-300' : '' }}
                                    {{ $index === 1 ? 'from-blue-50 to-cyan-50 border-blue-300' : '' }}
                                    {{ $index === 2 ? 'from-teal-50 to-emerald-50 border-teal-300' : '' }}
                                    p-4 lg:p-5 rounded-xl border-2">
                                    
                                    <!-- Rank Badge -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 
                                                {{ $index === 0 ? 'bg-gradient-to-br from-purple-500 to-indigo-500' : '' }}
                                                {{ $index === 1 ? 'bg-gradient-to-br from-blue-500 to-cyan-500' : '' }}
                                                {{ $index === 2 ? 'bg-gradient-to-br from-teal-500 to-emerald-500' : '' }}
                                                rounded-full flex items-center justify-center mr-3">
                                                @if($index === 0)
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @else
                                                    <span class="text-white font-bold text-lg">#{{ $index + 1 }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs 
                                                    {{ $index === 0 ? 'text-purple-600' : '' }}
                                                    {{ $index === 1 ? 'text-blue-600' : '' }}
                                                    {{ $index === 2 ? 'text-teal-600' : '' }}
                                                    font-semibold">
                                                    {{ $index === 0 ? '⭐ RANK #1' : 'RANK #' . ($index + 1) }}
                                                </p>
                                                <p class="text-xs text-gray-600">Performance Score</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold 
                                                {{ $index === 0 ? 'text-purple-600' : '' }}
                                                {{ $index === 1 ? 'text-blue-600' : '' }}
                                                {{ $index === 2 ? 'text-teal-600' : '' }}">
                                                {{ $performer['performance_score'] }}
                                            </p>
                                            <p class="text-xs text-gray-500">out of 100</p>
                                        </div>
                                    </div>
                                    
                                    <!-- User Info -->
                                    <div class="flex items-center space-x-3 mb-3 pb-3 border-b border-gray-200">
                                        <x-user-avatar :user="$performer['user']" size="md" />
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-gray-800 text-base truncate">{{ $performer['user']->name }}</h4>
                                            <p class="text-xs text-gray-600">{{ ucfirst($performer['user']->role) }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Metrics -->
                                    <div class="space-y-2">
                                        <!-- Completed Cards -->
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Completed Cards
                                            </span>
                                            <span class="font-bold text-gray-800">{{ $performer['completed_cards'] }}</span>
                                        </div>
                                        
                                        <!-- On-Time Rate -->
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                On-Time Delivery
                                            </span>
                                            <span class="font-bold {{ $performer['on_time_rate'] >= 80 ? 'text-green-600' : 'text-amber-600' }}">
                                                {{ $performer['on_time_rate'] }}%
                                            </span>
                                        </div>
                                        
                                        <!-- Estimation Accuracy -->
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Estimate Accuracy
                                            </span>
                                            <span class="font-bold {{ $performer['estimate_accuracy_rate'] >= 80 ? 'text-green-600' : 'text-amber-600' }}">
                                                {{ $performer['estimate_accuracy_rate'] }}%
                                            </span>
                                        </div>
                                        
                                        <!-- Avg Time & Hours -->
                                        <div class="mt-3 pt-2 border-t border-gray-200 flex items-center justify-between text-xs">
                                            <div>
                                                <p class="text-gray-500">Avg Time/Card</p>
                                                <p class="font-bold text-gray-800">{{ $performer['avg_time_per_card'] }}h</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-gray-500">Total Hours</p>
                                                <p class="font-bold text-gray-800">{{ $performer['total_hours'] }}h</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Performance Badge -->
                                    <div class="mt-3 p-2 rounded-lg text-center
                                        {{ $index === 0 ? 'bg-purple-100' : '' }}
                                        {{ $index === 1 ? 'bg-blue-100' : '' }}
                                        {{ $index === 2 ? 'bg-teal-100' : '' }}">
                                        <p class="text-xs font-semibold
                                            {{ $index === 0 ? 'text-purple-700' : '' }}
                                            {{ $index === 1 ? 'text-blue-700' : '' }}
                                            {{ $index === 2 ? 'text-teal-700' : '' }}">
                                            @if($performer['performance_score'] >= 80)
                                                🏆 Excellent Performance
                                            @elseif($performer['performance_score'] >= 60)
                                                ⭐ Great Work
                                            @else
                                                💪 Good Progress
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Info Footer -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-600 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Performance score is calculated based on: Cards completed (30%), On-time delivery (35%), Estimation accuracy (35%)
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Leader: Team Overview & Recent Time Logs -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-8">
                    <!-- Team Permissions -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Team Permissions</h3>
                        <div class="space-y-2 lg:space-y-3">
                            @forelse($teamPermissions ?? [] as $permission)
                                <div class="flex items-center justify-between p-2 lg:p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-2 lg:space-x-3 min-w-0 flex-1">
                                        <x-user-avatar :user="$permission->user" size="sm" />
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm lg:text-base truncate">{{ $permission->user->name }}</h4>
                                            <p class="text-xs lg:text-sm text-gray-600 hidden md:block">{{ $permission->type }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap ml-2
                                        {{ $permission->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $permission->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $permission->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ ucfirst($permission->status) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No team permissions</p>
                            @endforelse
                        </div>
                        <a href="{{ route('leader.permissions') }}" class="mt-3 lg:mt-4 hidden md:block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">View all permissions →</a>
                    </div>

                    <!-- Team Time Logs -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Team Activity</h3>
                        <div class="space-y-2 lg:space-y-3">
                            @forelse($teamTimeLogs ?? [] as $log)
                                <div class="flex items-center justify-between p-2 lg:p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-2 lg:space-x-3 min-w-0 flex-1">
                                        <x-user-avatar :user="$log->user" size="sm" />
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm lg:text-base truncate">{{ $log->user->name }}</h4>
                                            <p class="text-xs lg:text-sm text-gray-600 truncate">{{ $log->subtask->card->card_title }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right ml-2">
                                        <p class="text-xs lg:text-sm font-medium text-indigo-600">{{ floor($log->duration_minutes / 60) }}h {{ $log->duration_minutes % 60 }}m</p>
                                        <p class="text-xs text-gray-500 hidden md:block">{{ $log->created_at->format('M d') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent activity</p>
                            @endforelse
                        </div>
                        <a href="{{ route('leader.time-logs') }}" class="mt-3 lg:mt-4 hidden md:block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">View all time logs →</a>
                    </div>
                </div>

            @else
                <!-- User (Developer/Designer): Current Card & Recent Subtasks -->
                @if($currentCard)
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-8 mb-4 lg:mb-8">
                        <div class="flex items-center justify-between mb-4 lg:mb-6">
                            <h2 class="text-lg lg:text-2xl font-bold text-indigo-800">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6 inline-block mr-1 lg:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                 
                            </h2>
                            <a href="{{ route('user.my-card') }}" class="text-indigo-600 hover:text-indigo-800 text-xs lg:text-sm font-medium whitespace-nowrap">View →</a>
                        </div>
                        <div class="p-3 lg:p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                            <div class="flex items-start justify-between mb-3 lg:mb-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg lg:text-xl font-bold text-gray-800 truncate">{{ $currentCard->card_title }}</h3>
                                    <p class="text-gray-600 text-sm lg:text-base mt-1 hidden md:block">{{ $currentCard->board->project->name }}</p>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full
                                    {{ $currentCard->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $currentCard->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $currentCard->status === 'review' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $currentCard->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                ">{{ ucfirst(str_replace('_', ' ', $currentCard->status)) }}</span>
                            </div>
                            @if($currentCard->description)
                                <p class="text-gray-700 text-sm mb-3 lg:mb-4 hidden md:block">{{ $currentCard->description }}</p>
                            @endif
                            <div class="flex items-center justify-between text-xs lg:text-sm">
                                <div class="flex items-center space-x-2 lg:space-x-4">
                                    <span class="text-gray-600 flex items-center">
                                        <svg class="w-3 h-3 lg:w-4 lg:h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        <span class="truncate">{{ $completedSubtasks ?? 0 }}/{{ $totalSubtasks ?? 0 }}</span>
                                    </span>
                                </div>
                                <span class="text-indigo-600 font-medium hidden md:inline truncate ml-2">{{ $currentCard->board->name }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- User: Recent Subtasks & Time Logs -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-8">
                    <!-- Recent Subtasks -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Recent Subtasks</h3>
                        <div class="space-y-2 lg:space-y-3">
                            @forelse($recentSubtasks ?? [] as $subtask)
                                <div class="flex items-center justify-between p-2 lg:p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-2 lg:space-x-3 min-w-0 flex-1">
                                        <div class="w-6 h-6 lg:w-8 lg:h-8 rounded-full flex items-center justify-center flex-shrink-0
                                            {{ $subtask->is_completed ? 'bg-green-100' : 'bg-gray-100' }}">
                                            @if($subtask->is_completed)
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-semibold text-gray-800 text-sm lg:text-base truncate">{{ $subtask->subtask_title }}</h4>
                                            <p class="text-xs lg:text-sm text-gray-600 truncate hidden md:block">{{ $subtask->card->card_title }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}
                                    ">{{ $subtask->status === 'done' ? 'Done' : 'In Progress' }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent subtasks</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- My Permissions -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">My Permissions</h3>
                        <div class="space-y-2 lg:space-y-3">
                            @forelse($myPermissions ?? [] as $permission)
                                <div class="flex items-center justify-between p-2 lg:p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-800 text-sm lg:text-base">{{ $permission->type }}</h4>
                                        <p class="text-xs lg:text-sm text-gray-600">{{ $permission->start_date->format('M d') }} - {{ $permission->end_date->format('M d, Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap ml-2
                                        {{ $permission->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $permission->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $permission->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ ucfirst($permission->status) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No permissions</p>
                            @endforelse
                        </div>
                        <a href="{{ route('user.permissions') }}" class="mt-3 lg:mt-4 block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs lg:text-sm py-2 lg:py-2.5 rounded-lg transition-colors">Request Permission</a>
                    </div>
                </div>
            @endif

        </main>
    </div>
</body>
</html>
