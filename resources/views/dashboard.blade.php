<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Project Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <!-- Top Header Bar (for breadcrumb and notifications) -->
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span>Home</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Dashboard</span>
                </div>
                
                <!-- Notification and Quick Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5a1.5 1.5 0 010-2.121l.707-.707a1 1 0 011.414 0L21 12.414V10a8 8 0 10-16 0v2.414l2.379 2.379a1 1 0 011.414 0l.707.707a1.5 1.5 0 010 2.121L6 21h5m4-4v4m0-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Profile Dropdown Trigger -->
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
                        <p class="text-gray-600 text-lg">Here's what's happening with your projects today.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ date('l, F j, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ date('g:i A') }}</p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-2xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-sm font-medium">Active Projects</p>
                                <p class="text-2xl font-bold text-blue-800">3</p>
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
                                <p class="text-amber-600 text-sm font-medium">Pending Tasks</p>
                                <p class="text-2xl font-bold text-amber-800">7</p>
                            </div>
                            <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-4 rounded-2xl border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-600 text-sm font-medium">Completed</p>
                                <p class="text-2xl font-bold text-emerald-800">12</p>
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
                                <p class="text-rose-600 text-sm font-medium">Team Members</p>
                                <p class="text-2xl font-bold text-rose-800">8</p>
                            </div>
                            <div class="w-10 h-10 bg-rose-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-specific Content -->
            @if(Auth::user()->role === 'designer')
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                    <div class="flex items-start space-x-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM7 3H5a2 2 0 00-2 2v12a4 4 0 004 4h2V3zM13 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 13h6m-6-4h6m4 0l4-4m0 0l4 4m-4-4v8"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-purple-800 mb-3">Designer Workspace</h2>
                            <p class="text-purple-700 leading-relaxed mb-6">
                                Welcome to your creative space! Access your design tools, collaborate with your team, and bring amazing ideas to life.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                                    <h4 class="font-semibold text-purple-800 mb-2">Design Assets</h4>
                                    <p class="text-sm text-purple-600">Manage your design files and resources</p>
                                </div>
                                <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                                    <h4 class="font-semibold text-purple-800 mb-2">Wireframes</h4>
                                    <p class="text-sm text-purple-600">Create and edit wireframes</p>
                                </div>
                                <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                                    <h4 class="font-semibold text-purple-800 mb-2">Style Guide</h4>
                                    <p class="text-sm text-purple-600">Maintain design consistency</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->role === 'developer')
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                    <div class="flex items-start space-x-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-emerald-800 mb-3">Developer Workspace</h2>
                            <p class="text-emerald-700 leading-relaxed mb-6">
                                Welcome to your development environment! Manage your repositories, track issues, and build amazing features.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-200">
                                    <h4 class="font-semibold text-emerald-800 mb-2">Code Repository</h4>
                                    <p class="text-sm text-emerald-600">Manage your source code</p>
                                </div>
                                <div class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-200">
                                    <h4 class="font-semibold text-emerald-800 mb-2">Testing Suite</h4>
                                    <p class="text-sm text-emerald-600">Run and manage tests</p>
                                </div>
                                <div class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-200">
                                    <h4 class="font-semibold text-emerald-800 mb-2">Deployment</h4>
                                    <p class="text-sm text-emerald-600">Deploy and monitor apps</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Projects</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">P1</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">E-commerce Platform</h4>
                                <p class="text-sm text-gray-600">Updated 2 hours ago</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                        </div>
                        
                        <div class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">P2</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Mobile App Design</h4>
                                <p class="text-sm text-gray-600">Updated 1 day ago</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Review</span>
                        </div>

                        <div class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">P3</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Dashboard Redesign</h4>
                                <p class="text-sm text-gray-600">Updated 3 days ago</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Planning</span>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Tasks -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Upcoming Tasks</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Review homepage mockups</h4>
                                <p class="text-sm text-gray-600">Due: Today, 5:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Implement user authentication</h4>
                                <p class="text-sm text-gray-600">Due: Tomorrow, 2:00 PM</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Team meeting preparation</h4>
                                <p class="text-sm text-gray-600">Due: Friday, 10:00 AM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
