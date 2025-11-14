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
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Home</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Reports</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <button onclick="printReport()" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Print Report</span>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Reports Content -->
        <main class="p-6">
            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                            Project Management Reports
                        </h1>
                        <p class="text-gray-600 text-lg">
                            Comprehensive analytics and insights for your projects
                        </p>
                    </div>
                </div>

                <!-- Date Filter -->
                <form method="GET" action="{{ route('reports') }}" class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" 
                                   class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" 
                                   class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div class="self-end">
                            <button type="submit" class="px-6 py-2 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition-colors">
                                Update Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Projects</p>
                            <p class="text-3xl font-bold text-indigo-600">{{ $data['projectStats']['total_projects'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-green-600">+{{ $data['projectStats']['projects_created_in_period'] }} this period</span>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Projects</p>
                            <p class="text-3xl font-bold text-green-600">{{ $data['projectStats']['active_projects'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-600">Currently in progress</span>
                    </div>
                </div>

                <!-- Completed Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed Projects</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $data['projectStats']['completed_projects'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-green-600">+{{ $data['projectStats']['projects_completed_in_period'] }} this period</span>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $data['userStats']['total_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-blue-600">{{ $data['userStats']['working_users'] }} working</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Project Status Distribution Chart -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Project Status Distribution</h3>
                    <div class="relative h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Trend Chart -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Monthly Project Creation Trend</h3>
                    <div class="relative h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Top Performers -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Top Performers</h3>
                    <div class="space-y-3">
                        @forelse($data['topPerformers'] as $performer)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <x-user-avatar :user="$performer" size="sm" />
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $performer->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $performer->role }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-indigo-600">{{ $performer->completed_projects }}</p>
                                    <p class="text-xs text-gray-600">projects</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 text-center py-4">No data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Overdue Projects -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Overdue Projects</h3>
                    <div class="space-y-3">
                        @forelse($data['overdueProjects']->take(5) as $project)
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $project->project_name }}</p>
                                        <p class="text-sm text-gray-600">by {{ $project->user->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-red-600">
                                            {{ $project->deadline ? $project->deadline->diffForHumans() : 'No deadline' }}
                                        </p>
                                        <p class="text-xs text-gray-600">{{ $project->members->count() }} members</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600 text-center py-4">No overdue projects</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Workload -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">User Workload Distribution</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($data['userWorkload'] as $workload)
                        <div class="p-4 border rounded-xl 
                            @if($workload['workload_status'] === 'free') border-green-200 bg-green-50
                            @elseif($workload['workload_status'] === 'overloaded') border-red-200 bg-red-50
                            @else border-blue-200 bg-blue-50 @endif">
                            <div class="flex items-center space-x-3">
                                <x-user-avatar :user="$workload['user']" size="sm" />
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $workload['user']->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $workload['active_projects'] }} active projects</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
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
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Activities</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($data['recentActivities'] as $activity)
                        <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-gray-800">{{ $activity['description'] }}</p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 text-center py-4">No recent activities</p>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript for Charts -->
    <script>
        // Project Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($data['statusDistribution'])),
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
                        position: 'bottom'
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
                    fill: true
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Print function
        function printReport() {
            const currentUrl = new URL(window.location);
            currentUrl.pathname = currentUrl.pathname.replace('/reports', '/reports/print');
            window.open(currentUrl.toString(), '_blank');
        }
    </script>
</body>
</html>