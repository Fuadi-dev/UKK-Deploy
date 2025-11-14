<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Work Activity Log</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Home</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Work Activity Log</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-600 hover:text-gray-800 p-2 rounded-lg hover:bg-white/50 transition-colors">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="lg:hidden fixed inset-0 z-40 hidden">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleMobileMenu()"></div>
            <div class="fixed left-0 top-0 h-full w-64">
                @include('components.sidebar')
            </div>
        </div>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                            My Subtask Work Log
                        </h1>
                        <p class="text-gray-600">Automatic tracking of your subtask activities</p>
                    </div>
                </div>
            </div>

            @if($timeLogs->count() > 0)
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-blue-500/20 text-blue-600 mr-4">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Total Time</p>
                                <p class="text-gray-800 text-xl font-bold">{{ $totalTime ?? '0h 0m' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-green-500/20 text-green-600 mr-4">
                                <i class="fas fa-calendar text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">This Week</p>
                                <p class="text-gray-800 text-xl font-bold">{{ $weekTime ?? '0h 0m' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-purple-500/20 text-purple-600 mr-4">
                                <i class="fas fa-tasks text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Total Logs</p>
                                <p class="text-gray-800 text-xl font-bold">{{ $totalLogs ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-600 mr-4">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Avg/Day</p>
                                <p class="text-gray-800 text-xl font-bold">{{ $averageTime ?? '0h 0m' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity in Progress Card -->
                @if($runningTimer ?? false)
                <div id="runningTimerCard" class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6 mb-6 border-l-4 border-l-emerald-500">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-600 mr-4">
                                <i class="fas fa-play text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-gray-800 font-semibold text-lg">Activity in Progress</h3>
                                <p class="text-gray-600">{{ $runningTimer->task_name ?? 'Working...' }}</p>
                                <p class="text-emerald-600 font-mono text-lg" id="timerDisplay">{{ $runningTimer->duration ?? '00:00:00' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Time Logs List -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                            <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-4 lg:mb-0">Activity History</h2>
                        </div>
                        <!-- Mobile Cards View -->
                        <div class="lg:hidden space-y-4">
                            @foreach($timeLogs as $timeLog)
                            <div class="bg-white/60 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center">
                                        @if($timeLog->card_id)
                                            <div class="p-2 rounded-lg bg-blue-500/20 text-blue-600 mr-3">
                                                <i class="fas fa-sticky-note text-sm"></i>
                                            </div>
                                        @else
                                            <div class="p-2 rounded-lg bg-purple-500/20 text-purple-600 mr-3">
                                                <i class="fas fa-list-check text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-gray-800 font-semibold">{{ $timeLog->task_name }}</h3>
                                            <p class="text-gray-600 text-sm">{{ $timeLog->project_name }}</p>
                                            
                                            <!-- Estimated vs Actual for Subtask -->
                                            @if($timeLog->estimated_hours > 0)
                                            <div class="mt-2">
                                                <div class="flex items-center space-x-2 text-xs">
                                                    <span class="text-gray-500">Est: {{ $timeLog->estimated_hours }}h</span>
                                                    <span class="text-gray-400">|</span>
                                                    <span class="{{ $timeLog->is_over_estimated ? 'text-red-600 font-semibold' : 'text-blue-600' }}">
                                                        Actual: {{ $timeLog->actual_hours }}h
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="h-1.5 rounded-full {{ $timeLog->is_over_estimated ? 'bg-red-500' : 'bg-blue-500' }}" 
                                                         style="width: {{ min($timeLog->progress_percentage, 100) }}%"></div>
                                                </div>
                                                @if($timeLog->is_over_estimated)
                                                <div class="flex items-center mt-1 text-xs text-red-600">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Over by {{ $timeLog->overtime_hours }}h
                                                </div>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-emerald-600 font-bold">{{ $timeLog->duration }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                    <span>{{ \Carbon\Carbon::parse($timeLog->start_time)->format('M d, Y H:i') }}</span>
                                    @if($timeLog->end_time)
                                        <span class="text-green-600">Completed</span>
                                    @else
                                        <span class="text-yellow-600">In Progress</span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button onclick="timeLogManager.viewDetails({{ $timeLog->id }})" 
                                            class="flex-1 px-3 py-2 bg-blue-500/20 text-blue-600 rounded-lg hover:bg-blue-500/30 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Task</th>
                                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Project</th>
                                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Start Time</th>
                                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Duration</th>
                                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Status</th>
                                        <th class="text-left py-3 px-4 text-gray-600 font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeLogs as $timeLog)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                @if($timeLog->card_id)
                                                    <div class="p-2 rounded-lg bg-blue-500/20 text-blue-600 mr-3">
                                                        <i class="fas fa-sticky-note text-sm"></i>
                                                    </div>
                                                @else
                                                    <div class="p-2 rounded-lg bg-purple-500/20 text-purple-600 mr-3">
                                                        <i class="fas fa-list-check text-sm"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-gray-800 font-medium">{{ $timeLog->task_name }}</p>
                                                    <p class="text-gray-500 text-sm">{{ $timeLog->card_id ? 'Card' : 'Subtask' }}</p>
                                                    
                                                    @if($timeLog->estimated_hours > 0)
                                                    <div class="mt-2 w-48">
                                                        <div class="flex items-center justify-between text-xs mb-1">
                                                            <span class="text-gray-500">Est: {{ $timeLog->estimated_hours }}h</span>
                                                            <span class="{{ $timeLog->is_over_estimated ? 'text-red-600 font-semibold' : 'text-blue-600' }}">
                                                                Act: {{ $timeLog->actual_hours }}h
                                                            </span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="h-1.5 rounded-full {{ $timeLog->is_over_estimated ? 'bg-red-500' : 'bg-blue-500' }}" 
                                                                 style="width: {{ min($timeLog->progress_percentage, 100) }}%"></div>
                                                        </div>
                                                        @if($timeLog->is_over_estimated)
                                                        <div class="flex items-center mt-1 text-xs text-red-600">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            Over by {{ $timeLog->overtime_hours }}h
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-gray-800">{{ $timeLog->project_name }}</td>
                                        <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($timeLog->start_time)->format('M d, Y H:i') }}</td>
                                        <td class="py-4 px-4">
                                            <span class="text-emerald-600 font-bold">{{ $timeLog->duration }}</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($timeLog->end_time)
                                                <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Completed</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">In Progress</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <button onclick="timeLogManager.viewDetails({{ $timeLog->id }})" 
                                                    class="px-3 py-1.5 bg-blue-500/20 text-blue-600 rounded-lg hover:bg-blue-500/30 transition-colors text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($timeLogs->hasPages())
                        <div class="mt-6">
                            {{ $timeLogs->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">No Activity Logs Yet</h3>
                    <p class="text-gray-600 mb-8">You don't have any subtask activity logs yet. Your work on subtasks will be automatically tracked when you start working on them.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            @endif
        </main>
    </div>

<!-- View Time Log Modal -->
<div id="viewTimeLogModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="modal-content rounded-3xl shadow-2xl border border-white/20 w-full max-w-2xl max-h-[90vh] overflow-hidden scale-95 opacity-0 transition-all duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-white/20">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Activity Details</h3>
                    <p class="text-sm text-gray-600">View work activity information</p>
                </div>
                <button onclick="timeLogManager.closeViewModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Type</label>
                        <p id="viewTaskType" class="text-gray-900 font-semibold">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Name</label>
                        <p id="viewTaskName" class="text-gray-900 font-semibold">-</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <p id="viewProject" class="text-gray-900 font-semibold">-</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                        <p id="viewStartTime" class="text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <p id="viewEndTime" class="text-gray-900">-</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                    <p id="viewDuration" class="text-gray-900 font-bold text-lg">-</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p id="viewDescription" class="text-gray-900">-</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    class TimeLogManager {
        constructor() {
            // Only keep view functionality
        }

        viewDetails(timeLogId) {
            fetch(`/user/time-log/${timeLogId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.populateViewModal(data.time_log);
                        this.showViewModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load time log details.'
                    });
                });
        }

        populateViewModal(timeLog) {
            document.getElementById('viewTaskType').textContent = 'Subtask';
            document.getElementById('viewTaskName').textContent = timeLog.task_name;
            document.getElementById('viewProject').textContent = timeLog.project_name;
            document.getElementById('viewStartTime').textContent = new Date(timeLog.start_time).toLocaleString();
            document.getElementById('viewEndTime').textContent = timeLog.end_time ? new Date(timeLog.end_time).toLocaleString() : 'In Progress';
            document.getElementById('viewDuration').textContent = timeLog.duration;
            
            // Add estimated vs actual warning if applicable
            let descriptionHtml = timeLog.description || 'Automatic tracking - No description';
            
            if (timeLog.estimated_hours > 0) {
                const progressBar = `
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Estimated: ${timeLog.estimated_hours}h</span>
                            <span class="text-sm font-semibold ${timeLog.is_over_estimated ? 'text-red-600' : 'text-blue-600'}">Actual: ${timeLog.actual_hours}h</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full ${timeLog.is_over_estimated ? 'bg-red-500' : 'bg-blue-500'}" style="width: ${Math.min(timeLog.progress_percentage, 100)}%"></div>
                        </div>
                        ${timeLog.is_over_estimated ? `
                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-700 flex items-start">
                            <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                            <div>
                                <strong>Warning:</strong> You have exceeded the estimated time by <strong>${timeLog.overtime_hours} hours</strong>. Consider reviewing your work pace or updating the estimation.
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;
                descriptionHtml = progressBar + descriptionHtml;
            }
            
            document.getElementById('viewDescription').innerHTML = descriptionHtml;
        }

        showViewModal() {
            const modal = document.getElementById('viewTimeLogModal');
            const content = modal.querySelector('.modal-content');
            modal.style.display = 'flex';
            setTimeout(() => {
                content.style.transform = 'scale(1)';
                content.style.opacity = '1';
            }, 10);
        }

        closeViewModal() {
            const modal = document.getElementById('viewTimeLogModal');
            const content = modal.querySelector('.modal-content');
            content.style.transform = 'scale(0.95)';
            content.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }
    }

    const timeLogManager = new TimeLogManager();

    // Mobile menu toggle
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }

    // Auto close mobile menu when clicking outside or on sidebar links
    document.addEventListener('click', function(event) {
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuButton = event.target.closest('[onclick="toggleMobileMenu()"]');
        
        if (!mobileMenuButton && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.add('hidden');
        }
    });
</script>

</body>
</html>