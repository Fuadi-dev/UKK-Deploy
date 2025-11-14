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
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span>Home</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Dashboard</span>
                </div>
                
                <!-- Notification and Profile -->
                <div class="flex items-center space-x-4">
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
        <main class="p-6">
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
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                            Welcome back, {{ Auth::user()->name }}!
                        </h1>
                        <p class="text-gray-600 text-lg">
                            @if(Auth::user()->role === 'admin')
                                Here's an overview of the entire system.
                            @elseif(Auth::user()->role === 'leader')
                                Here's what's happening with your projects and team today.
                            @else
                                Here's your progress and tasks for today.
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ date('l, F j, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ date('g:i A') }}</p>
                    </div>
                </div>

                <!-- Admin Dashboard Stats -->
                @if(Auth::user()->role === 'admin')
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Total Projects</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ $totalProjects ?? 0 }}</p>
                                    <p class="text-xs text-blue-500 mt-1">{{ $activeProjects ?? 0 }} active</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-4 rounded-2xl border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-sm font-medium">Completed Projects</p>
                                    <p class="text-2xl font-bold text-emerald-800">{{ $completedProjects ?? 0 }}</p>
                                    <p class="text-xs text-emerald-500 mt-1">{{ $expiredProjects ?? 0 }} expired</p>
                                </div>
                                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-rose-50 to-pink-100 p-4 rounded-2xl border border-rose-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-rose-600 text-sm font-medium">Total Users</p>
                                    <p class="text-2xl font-bold text-rose-800">{{ ($leadersCount ?? 0) + ($developersCount ?? 0) + ($designersCount ?? 0) }}</p>
                                    <p class="text-xs text-rose-500 mt-1">{{ $availableUsers ?? 0 }} available</p>
                                </div>
                                <div class="w-10 h-10 bg-rose-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-4 rounded-2xl border border-amber-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-600 text-sm font-medium">User Roles</p>
                                    <p class="text-2xl font-bold text-amber-800">{{ ($leadersCount ?? 0) + ($developersCount ?? 0) + ($designersCount ?? 0) }}</p>
                                    <p class="text-xs text-amber-500 mt-1">{{ $leadersCount ?? 0 }} leaders, {{ $developersCount ?? 0 }} devs</p>
                                </div>
                                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Leader Dashboard Stats -->
                @elseif(Auth::user()->role === 'leader')
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">My Projects</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ $myProjectsCount ?? 0 }}</p>
                                    <p class="text-xs text-blue-500 mt-1">{{ $activeProjectsCount ?? 0 }} active</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-4 rounded-2xl border border-amber-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-600 text-sm font-medium">In Progress</p>
                                    <p class="text-2xl font-bold text-amber-800">{{ $inProgressCards ?? 0 }}</p>
                                    <p class="text-xs text-amber-500 mt-1">{{ $todoCards ?? 0 }} to do</p>
                                </div>
                                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-4 rounded-2xl border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-sm font-medium">Need Review</p>
                                    <p class="text-2xl font-bold text-purple-800">{{ $cardsNeedingReview->count() ?? 0 }}</p>
                                    <p class="text-xs text-purple-500 mt-1">Pending approval</p>
                                </div>
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-4 rounded-2xl border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-sm font-medium">Team Members</p>
                                    <p class="text-2xl font-bold text-emerald-800">{{ $teamMembersCount ?? 0 }}</p>
                                    <p class="text-xs text-emerald-500 mt-1">{{ $doneCards ?? 0 }} done</p>
                                </div>
                                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- User (Developer/Designer) Dashboard Stats -->
                @else
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-2xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Current Card</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ $currentCard ? 1 : 0 }}</p>
                                    <p class="text-xs text-blue-500 mt-1">
                                        @if($currentCard)
                                            {{ ucfirst($currentCard->status) }}
                                        @else
                                            No active card
                                        @endif
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-4 rounded-2xl border border-amber-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-600 text-sm font-medium">Subtasks Progress</p>
                                    <p class="text-2xl font-bold text-amber-800">{{ $completedSubtasks ?? 0 }}/{{ $totalSubtasks ?? 0 }}</p>
                                    <p class="text-xs text-amber-500 mt-1">
                                        @if($totalSubtasks > 0)
                                            {{ round(($completedSubtasks / $totalSubtasks) * 100) }}% done
                                        @else
                                            No subtasks
                                        @endif
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-4 rounded-2xl border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-sm font-medium">Time Today</p>
                                    <p class="text-2xl font-bold text-emerald-800">{{ $timeToday ?? '0h' }}</p>
                                    <p class="text-xs text-emerald-500 mt-1">{{ $timeThisWeek ?? '0h' }} this week</p>
                                </div>
                                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-4 rounded-2xl border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-sm font-medium">Time This Month</p>
                                    <p class="text-2xl font-bold text-purple-800">{{ $timeThisMonth ?? '0h' }}</p>
                                    <p class="text-xs text-purple-500 mt-1">
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
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <!-- Admin: Expiring Projects Section -->
                @if(isset($expiringProjects) && $expiringProjects->count() > 0)
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Projects -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Projects</h3>
                        <div class="space-y-3">
                            @forelse($recentProjects ?? [] as $project)
                                <div class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ $project->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $project->user->name }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $project->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">{{ ucfirst($project->status) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent projects</p>
                            @endforelse
                        </div>
                        <a href="{{ route('projects') }}" class="mt-4 block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">View all projects →</a>
                    </div>

                    <!-- Registered Users -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Registered Users</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-xl bg-blue-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Project Leaders</h4>
                                        <p class="text-sm text-gray-600">Managing projects</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-blue-600">{{ $leadersCount ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Developers</h4>
                                        <p class="text-sm text-gray-600">Building features</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-emerald-600">{{ $developersCount ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-xl bg-purple-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Designers</h4>
                                        <p class="text-sm text-gray-600">Creating designs</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-purple-600">{{ $designersCount ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Available for Assignment</span>
                                    <span class="font-semibold text-green-600">{{ $availableUsers ?? 0 }} users</span>
                                </div>
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-gray-600">Currently Assigned</span>
                                    <span class="font-semibold text-amber-600">{{ $busyUsers ?? 0 }} users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(Auth::user()->role === 'leader')
                <!-- Leader: Cards Needing Review -->
                @if(isset($cardsNeedingReview) && $cardsNeedingReview->count() > 0)
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
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

                <!-- Leader: Team Overview & Recent Time Logs -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Team Permissions -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Team Permissions</h3>
                        <div class="space-y-3">
                            @forelse($teamPermissions ?? [] as $permission)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <x-user-avatar :user="$permission->user" size="sm" />
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $permission->user->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $permission->type }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $permission->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $permission->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $permission->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ ucfirst($permission->status) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No team permissions</p>
                            @endforelse
                        </div>
                        <a href="{{ route('leader.permissions') }}" class="mt-4 block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">View all permissions →</a>
                    </div>

                    <!-- Team Time Logs -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Team Activity</h3>
                        <div class="space-y-3">
                            @forelse($teamTimeLogs ?? [] as $log)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <x-user-avatar :user="$log->user" size="sm" />
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $log->user->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $log->subtask->card->card_title }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-indigo-600">{{ floor($log->duration_minutes / 60) }}h {{ $log->duration_minutes % 60 }}m</p>
                                        <p class="text-xs text-gray-500">{{ $log->created_at->format('M d') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent activity</p>
                            @endforelse
                        </div>
                        <a href="{{ route('leader.time-logs') }}" class="mt-4 block text-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">View all time logs →</a>
                    </div>
                </div>

            @else
                <!-- User (Developer/Designer): Current Card & Recent Subtasks -->
                @if($currentCard)
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-indigo-800">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Current Card
                            </h2>
                            <a href="{{ route('projects.show', $currentCard->board->project->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Details →</a>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">{{ $currentCard->card_title }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $currentCard->board->project->name }}</p>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full
                                    {{ $currentCard->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $currentCard->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $currentCard->status === 'review' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $currentCard->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                ">{{ ucfirst(str_replace('_', ' ', $currentCard->status)) }}</span>
                            </div>
                            @if($currentCard->description)
                                <p class="text-gray-700 text-sm mb-4">{{ $currentCard->description }}</p>
                            @endif
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-600">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        {{ $completedSubtasks ?? 0 }}/{{ $totalSubtasks ?? 0 }} subtasks
                                    </span>
                                </div>
                                <span class="text-indigo-600 font-medium">{{ $currentCard->board->name }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- User: Recent Subtasks & Time Logs -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Subtasks -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Subtasks</h3>
                        <div class="space-y-3">
                            @forelse($recentSubtasks ?? [] as $subtask)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center
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
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $subtask->subtask_title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $subtask->card->card_title }}</p>
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
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">My Permissions</h3>
                        <div class="space-y-3">
                            @forelse($myPermissions ?? [] as $permission)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $permission->type }}</h4>
                                        <p class="text-sm text-gray-600">{{ $permission->start_date->format('M d') }} - {{ $permission->end_date->format('M d, Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $permission->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $permission->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $permission->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ ucfirst($permission->status) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No permissions</p>
                            @endforelse
                        </div>
                        <a href="{{ route('user.permissions') }}" class="mt-4 block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm py-2 rounded-lg transition-colors">Request Permission</a>
                    </div>
                </div>
            @endif

        </main>
    </div>
</body>
</html>
