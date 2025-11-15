<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports - Admin Panel</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Prevent horizontal overflow on mobile */
        body {
            overflow-x: hidden;
        }
        
        /* Ensure date inputs don't overflow */
        input[type="date"] {
            max-width: 100%;
        }
        
        /* Better mobile date picker styling */
        @media (max-width: 640px) {
            input[type="date"]::-webkit-calendar-picker-indicator {
                font-size: 12px;
            }
        }
    </style>
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
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Home</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Reports</span>
                </div>
                
                <!-- Mobile: Simple Title -->
                <h2 class="md:hidden text-base font-bold text-gray-800">📊 Reports</h2>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-2 lg:space-x-4">
                    <button onclick="printReport()" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-3 py-2 lg:px-4 lg:py-2 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 flex items-center space-x-2 shadow-lg text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span class="hidden sm:inline">Print Report</span>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                            <x-user-avatar :user="Auth::user()" size="sm" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Reports Content -->
        <main class="p-3 lg:p-6">
            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-3 lg:p-8 mb-3 lg:mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-3 lg:mb-6">
                    <div class="w-full">
                        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-1 lg:mb-2">
                            📊 Project Management Reports
                        </h1>
                        <p class="text-gray-600 text-xs lg:text-lg">
                            Comprehensive analytics and insights for your projects
                        </p>
                    </div>
                </div>

                <!-- Date Filter -->
                <form method="GET" action="{{ route('reports') }}" class="mt-4 lg:mt-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-indigo-100">
                        <div class="flex items-center mb-3 lg:mb-4">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-sm lg:text-base font-semibold text-gray-800">Filter Report Period</h3>
                        </div>
                        
                        <div class="mb-3 lg:mb-4 grid grid-cols-4 gap-1.5 lg:gap-2">
                            <button type="button" onclick="setDateRange('today')" class="px-2 py-1.5 lg:px-3 bg-white border border-indigo-200 text-indigo-600 rounded-md lg:rounded-lg text-xs font-medium hover:bg-indigo-50 transition-colors">
                                Today
                            </button>
                            <button type="button" onclick="setDateRange('week')" class="px-2 py-1.5 lg:px-3 bg-white border border-indigo-200 text-indigo-600 rounded-md lg:rounded-lg text-xs font-medium hover:bg-indigo-50 transition-colors">
                                Week
                            </button>
                            <button type="button" onclick="setDateRange('month')" class="px-2 py-1.5 lg:px-3 bg-white border border-indigo-200 text-indigo-600 rounded-md lg:rounded-lg text-xs font-medium hover:bg-indigo-50 transition-colors">
                                Month
                            </button>
                            <button type="button" onclick="setDateRange('year')" class="px-2 py-1.5 lg:px-3 bg-white border border-indigo-200 text-indigo-600 rounded-md lg:rounded-lg text-xs font-medium hover:bg-indigo-50 transition-colors">
                                Year
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2 lg:gap-4">
                                <div>
                                    <label for="date_from" class="block text-xs lg:text-sm font-medium text-gray-700 mb-1 lg:mb-2">
                                        From Date
                                    </label>
                                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" 
                                           class="w-full px-2 py-1.5 lg:px-4 lg:py-2.5 bg-white border border-gray-300 rounded-lg lg:rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs lg:text-sm shadow-sm transition-all hover:border-indigo-400">
                                </div>
                                
                                <div>
                                    <label for="date_to" class="block text-xs lg:text-sm font-medium text-gray-700 mb-1 lg:mb-2">
                                        To Date
                                    </label>
                                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" 
                                           class="w-full px-2 py-1.5 lg:px-4 lg:py-2.5 bg-white border border-gray-300 rounded-lg lg:rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs lg:text-sm shadow-sm transition-all hover:border-indigo-400">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full px-4 py-2 lg:px-6 lg:py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg lg:rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 text-sm lg:text-base font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Update Report</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 lg:gap-6 mb-3 lg:mb-8">
                <!-- Total Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-lg lg:rounded-2xl shadow-lg border border-white/20 p-2.5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-600 truncate">Total</p>
                            <p class="text-lg lg:text-3xl font-bold text-indigo-600">{{ $data['projectStats']['total_projects'] }}</p>
                        </div>
                        <div class="w-8 h-8 lg:w-12 lg:h-12 bg-indigo-100 rounded-lg lg:rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 lg:w-6 lg:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 lg:mt-4">
                        <span class="text-xs text-green-600">+{{ $data['projectStats']['projects_created_in_period'] }}</span>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-lg lg:rounded-2xl shadow-lg border border-white/20 p-2.5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-600 truncate">Active</p>
                            <p class="text-lg lg:text-3xl font-bold text-green-600">{{ $data['projectStats']['active_projects'] }}</p>
                        </div>
                        <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg lg:rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 lg:mt-4">
                        <span class="text-xs text-gray-600">Progress</span>
                    </div>
                </div>

                <!-- Completed Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-lg lg:rounded-2xl shadow-lg border border-white/20 p-2.5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-600 truncate">Done</p>
                            <p class="text-lg lg:text-3xl font-bold text-blue-600">{{ $data['projectStats']['completed_projects'] }}</p>
                        </div>
                        <div class="w-8 h-8 lg:w-12 lg:h-12 bg-blue-100 rounded-lg lg:rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 lg:mt-4">
                        <span class="text-xs text-green-600">+{{ $data['projectStats']['projects_completed_in_period'] }}</span>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white/80 backdrop-blur-lg rounded-lg lg:rounded-2xl shadow-lg border border-white/20 p-2.5 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-600 truncate">Users</p>
                            <p class="text-lg lg:text-3xl font-bold text-purple-600">{{ $data['userStats']['total_users'] }}</p>
                        </div>
                        <div class="w-8 h-8 lg:w-12 lg:h-12 bg-purple-100 rounded-lg lg:rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 lg:mt-4">
                        <span class="text-xs text-blue-600">{{ $data['userStats']['working_users'] }} work</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8 mb-4 lg:mb-8">
                <!-- Project Status Distribution Chart -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                    <h3 class="text-base lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Project Status Distribution</h3>
                    <div class="relative h-56 lg:h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Trend Chart -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                    <h3 class="text-base lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">Monthly Project Creation Trend</h3>
                    <div class="relative h-56 lg:h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8 mb-4 lg:mb-8">
                <!-- Top Performers -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                    <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">🏆 Top Performers</h3>
                    <div class="space-y-2 lg:space-y-3">
                        @forelse($data['topPerformers'] as $performer)
                            <div class="flex items-center justify-between gap-2 lg:gap-3 p-3 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 lg:gap-3 min-w-0 flex-1">
                                    <x-user-avatar :user="$performer" size="sm" class="flex-shrink-0" />
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 text-sm lg:text-base truncate">{{ $performer->name }}</p>
                                        <p class="text-xs lg:text-sm text-gray-600 truncate">{{ ucfirst($performer->role) }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-indigo-600 text-lg lg:text-xl">{{ $performer->completed_projects }}</p>
                                    <p class="text-xs text-gray-600">projects</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 text-center py-4 text-sm">No data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Overdue Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                    <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">⚠️ Overdue Projects</h3>
                    <div class="space-y-2 lg:space-y-3">
                        @forelse($data['overdueProjects']->take(5) as $project)
                            <div class="p-3 bg-gradient-to-br from-red-50 to-orange-50 border border-red-200 rounded-xl hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between gap-2 lg:gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-800 text-sm lg:text-base truncate">{{ $project->project_name }}</p>
                                        <p class="text-xs lg:text-sm text-gray-600 truncate">by {{ $project->user->name }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-xs lg:text-sm font-medium text-red-600 whitespace-nowrap">
                                            {{ $project->deadline ? $project->deadline->diffForHumans() : 'No deadline' }}
                                        </p>
                                        <p class="text-xs text-gray-600 whitespace-nowrap">{{ $project->members->count() }} members</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 text-center py-4 text-sm">No overdue projects 🎉</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Workload -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6 mb-4 lg:mb-8">
                <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">👥 User Workload Distribution</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                    @foreach($data['userWorkload'] as $workload)
                        <div class="p-3 lg:p-4 border rounded-xl 
                            @if($workload['workload_status'] === 'free') border-green-200 bg-gradient-to-br from-green-50 to-emerald-50
                            @elseif($workload['workload_status'] === 'overloaded') border-red-200 bg-gradient-to-br from-red-50 to-orange-50
                            @else border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 @endif
                            hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-2 lg:gap-3">
                                <x-user-avatar :user="$workload['user']" size="sm" class="flex-shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate text-sm lg:text-base">{{ $workload['user']->name }}</p>
                                    <p class="text-xs lg:text-sm text-gray-600 truncate">{{ $workload['active_projects'] }} active projects</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap flex-shrink-0
                                    @if($workload['workload_status'] === 'free') bg-green-100 text-green-800
                                    @elseif($workload['workload_status'] === 'overloaded') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($workload['workload_status']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-6">
                <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-3 lg:mb-4">📋 Recent Activities</h3>
                <div class="space-y-2 lg:space-y-3 max-h-96 overflow-y-auto">
                    @forelse($data['recentActivities'] as $activity)
                        <div class="flex items-start gap-2 lg:gap-3 p-3 hover:bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl transition-colors">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-800 text-sm lg:text-base break-words">{{ $activity['description'] }}</p>
                                <p class="text-xs lg:text-sm text-gray-600 truncate">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 text-center py-4 text-sm">No recent activities</p>
                    @endforelse
                </div>
            </div>

        </main>
    </div>

    <!-- JavaScript for Charts -->
    <script>
        // Quick Date Range Selection
        function setDateRange(range) {
            const today = new Date();
            let fromDate = new Date();
            let toDate = new Date();
            
            switch(range) {
                case 'today':
                    fromDate = today;
                    toDate = today;
                    break;
                case 'week':
                    fromDate.setDate(today.getDate() - today.getDay());
                    toDate.setDate(fromDate.getDate() + 6);
                    break;
                case 'month':
                    fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'year':
                    fromDate = new Date(today.getFullYear(), 0, 1);
                    toDate = new Date(today.getFullYear(), 11, 31);
                    break;
            }
            
            document.getElementById('date_from').value = formatDate(fromDate);
            document.getElementById('date_to').value = formatDate(toDate);
        }
        
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        // Project Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_map('ucfirst', array_keys($data['statusDistribution']))),
                datasets: [{
                    data: @json(array_values($data['statusDistribution'])),
                    backgroundColor: [
                        '#10B981', // active - green
                        '#3B82F6', // completed - blue
                        '#EF4444', // cancelled - red
                        '#F59E0B', // on_hold - yellow
                        '#8B5CF6'  // expired - purple
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Monthly Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendData = @json($data['monthlyTrend']);
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(item => item.month_name),
                datasets: [{
                    label: 'Projects Created',
                    data: trendData.map(item => item.count),
                    borderColor: '#6366F1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#6366F1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Print function
        function printReport() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const url = '{{ route("reports.print") }}?date_from=' + dateFrom + '&date_to=' + dateTo;
            window.open(url, '_blank');
        }
    </script>
</body>
</html>
