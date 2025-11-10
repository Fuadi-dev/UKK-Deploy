<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Time Logs Management - Leader Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    @include('components.sidebar')
    <!-- Main Content with sidebar offset -->
    <div class="lg:ml-64 min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
        <!-- Mobile Header Spacer -->
        <div class="lg:hidden h-16"></div>
        
        <div class="container mx-auto px-4 py-8 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-6 py-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Time Logs Management</h1>
                            <p class="text-purple-100">Monitor team productivity and manage time tracking</p>
                        </div>
                        <div class="flex flex-col lg:flex-row items-start lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                                <span class="text-white font-semibold text-sm lg:text-base">Total Logs: {{ $timeLogs->total() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">Today</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ number_format($stats['today_hours'], 1) }}h</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-week text-green-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">This Week</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ number_format($stats['week_hours'], 1) }}h</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar text-purple-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">This Month</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ number_format($stats['month_hours'], 1) }}h</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-list text-orange-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">Total Logs</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ $stats['total_logs'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and View Toggle -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <!-- View Toggle -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">View</label>
                        <select id="viewFilter" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="my" {{ $view == 'my' ? 'selected' : '' }}>My Time Logs</option>
                            <option value="team" {{ $view == 'team' ? 'selected' : '' }}>Team Time Logs</option>
                        </select>
                    </div>
                    
                    <!-- Team Member Filter (only for team view) -->
                    <div class="flex-1" id="memberFilterContainer" style="{{ $view == 'team' ? '' : 'display: none;' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Team Member</label>
                        <select id="memberFilter" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="all" {{ $member == 'all' ? 'selected' : '' }}>All Members</option>
                            @foreach($teamMembers as $teamMember)
                                <option value="{{ $teamMember->id }}" {{ $member == $teamMember->id ? 'selected' : '' }}>{{ $teamMember->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Filter</label>
                        <select id="dateFilter" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                    
                    <div class="lg:flex-shrink-0">
                        <button onclick="timeLogManager.applyFilters()" 
                                class="w-full lg:w-auto bg-purple-600 text-white px-6 py-2 rounded-xl hover:bg-purple-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Time Logs Table -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Team Card Time Logs
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Automatic tracking of all card work activities from team members</p>
                </div>
                
                <!-- Mobile Cards View -->
                <div class="lg:hidden">
                    @forelse($timeLogs as $timeLog)
                    <div class="p-4 border-b border-gray-200 time-log-card">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 mb-1">{{ $timeLog->task_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $timeLog->task_type }}</p>
                                @if($view == 'team')
                                <p class="text-sm text-purple-600 font-medium">{{ $timeLog->user->name }}</p>
                                @endif
                                
                                <!-- Session Count Badge -->
                                @if($timeLog->session_count > 1)
                                <div class="flex items-center mt-1">
                                    <span class="px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                                        <i class="fas fa-redo mr-1"></i>{{ $timeLog->session_count }} sessions
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Estimated vs Actual Hours (Mobile) -->
                                @if($timeLog->estimated_hours > 0)
                                <div class="mt-2">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="text-gray-500">Est: {{ $timeLog->estimated_hours }}h</span>
                                        <span class="{{ $timeLog->is_over_estimated ? 'text-red-600 font-semibold' : 'text-blue-600' }}">
                                            Actual: {{ $timeLog->actual_hours }}h
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
                            <div class="flex space-x-2">
                                @if($timeLog->status === 'running')
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Running</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">{{ $timeLog->formatted_duration }}</span>
                                @endif
                                @if($timeLog->session_count > 1)
                                    <span class="px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                                        {{ $timeLog->session_count }}x
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 mb-3">
                            <p>Started: {{ $timeLog->start_time->format('M d, Y H:i') }}</p>
                            @if($timeLog->end_time)
                            <p>Ended: {{ $timeLog->end_time->format('M d, Y H:i') }}</p>
                            @endif
                            @if($timeLog->description)
                            <p class="mt-1 text-gray-500">{{ $timeLog->description }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="timeLogManager.viewTimeLog({{ $timeLog->id }})" 
                                    class="flex-1 text-blue-600 bg-blue-50 px-3 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Time Logs Found</h3>
                        <p class="text-gray-600 mb-8">{{ $view == 'team' ? 'No team time logs to display for the selected period.' : 'No time logs to display for the selected period.' }}</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                @if($view == 'team')
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                @endif
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="timeLogsTableBody">
                            @forelse($timeLogs as $timeLog)
                            <tr class="time-log-row hover:bg-gray-50">
                                @if($view == 'team')
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold">{{ substr($timeLog->user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $timeLog->user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-{{ $timeLog->task_type === 'Card' ? 'credit-card' : 'tasks' }} text-white text-sm"></i>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $timeLog->task_name }}</div>
                                            @if($timeLog->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($timeLog->description, 30) }}</div>
                                            @endif
                                            
                                            <!-- Session Count Badge (Desktop) -->
                                            @if($timeLog->session_count > 1)
                                            <div class="mt-1">
                                                <span class="px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                                                    <i class="fas fa-redo mr-1"></i>{{ $timeLog->session_count }} work sessions (reject/resubmit)
                                                </span>
                                            </div>
                                            @endif
                                            
                                            <!-- Estimated vs Actual Hours Indicator -->
                                            @if($timeLog->estimated_hours > 0)
                                            <div class="mt-2">
                                                <div class="flex items-center space-x-2 text-xs">
                                                    <span class="text-gray-500">Est: {{ $timeLog->estimated_hours }}h</span>
                                                    <span class="text-gray-400">|</span>
                                                    <span class="{{ $timeLog->is_over_estimated ? 'text-red-600 font-semibold' : 'text-blue-600' }}">
                                                        Actual: {{ $timeLog->actual_hours }}h
                                                    </span>
                                                </div>
                                                <!-- Progress Bar -->
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold bg-{{ $timeLog->task_type === 'Card' ? 'blue' : 'green' }}-100 text-{{ $timeLog->task_type === 'Card' ? 'blue' : 'green' }}-800 rounded-full">
                                        {{ $timeLog->task_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $timeLog->start_time->format('M d, Y') }}
                                    <div class="text-xs text-gray-500">{{ $timeLog->start_time->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($timeLog->end_time)
                                        {{ $timeLog->end_time->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">{{ $timeLog->end_time->format('H:i') }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $timeLog->formatted_duration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($timeLog->status === 'running')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Running</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Completed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="timeLogManager.viewTimeLog({{ $timeLog->id }})" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $view == 'team' ? '8' : '7' }}" class="px-6 py-12">
                                    <div class="text-center">
                                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Time Logs Found</h3>
                                        <p class="text-gray-600 mb-8">{{ $view == 'team' ? 'No team time logs to display for the selected period.' : 'No time logs to display for the selected period.' }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($timeLogs->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $timeLogs->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Time Log Modal -->
    <div id="timeLogModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 rounded-t-3xl">
                    <h3 id="modalTitle" class="text-xl font-semibold text-white">Create Time Log</h3>
                </div>
                <form id="timeLogForm" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="timeLogId" name="time_log_id">
                    <input type="hidden" name="_method" id="methodField">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                        <select name="task_type" id="taskType" required class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Task Type</option>
                            <option value="card">Card</option>
                            <option value="subtask">Subtask</option>
                        </select>
                    </div>
                    
                    <div id="cardSelect" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card</label>
                        <select name="card_id" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Card</option>
                            @foreach($userCards as $card)
                                <option value="{{ $card->id }}">{{ $card->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="subtaskSelect" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtask</label>
                        <select name="subtask_id" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Subtask</option>
                            @foreach($userSubtasks as $subtask)
                                <option value="{{ $subtask->id }}">{{ $subtask->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" required 
                                   class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                            <input type="time" name="start_time" required 
                                   class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date (Optional)</label>
                            <input type="date" name="end_date" 
                                   class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time (Optional)</label>
                            <input type="time" name="end_time" 
                                   class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="3" placeholder="Describe what you worked on..." 
                                  class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"></textarea>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="timeLogManager.closeModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">
                            Save Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Time Log Modal -->
    <div id="viewTimeLogModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-3xl">
                    <h3 class="text-xl font-semibold text-white">Time Log Details</h3>
                </div>
                <div id="viewTimeLogContent" class="p-6">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
    class TimeLogManager {
        constructor() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            this.editMode = false;
            this.init();
        }

        init() {
            // No form handlers needed - view only mode for leaders
        }

        handleTaskTypeChange(e) {
            const taskType = e.target.value;
            const cardSelect = document.getElementById('cardSelect');
            const subtaskSelect = document.getElementById('subtaskSelect');
            
            if (taskType === 'card') {
                cardSelect.classList.remove('hidden');
                subtaskSelect.classList.add('hidden');
                cardSelect.querySelector('select').required = true;
                subtaskSelect.querySelector('select').required = false;
            } else if (taskType === 'subtask') {
                cardSelect.classList.add('hidden');
                subtaskSelect.classList.remove('hidden');
                cardSelect.querySelector('select').required = false;
                subtaskSelect.querySelector('select').required = true;
            } else {
                cardSelect.classList.add('hidden');
                subtaskSelect.classList.add('hidden');
                cardSelect.querySelector('select').required = false;
                subtaskSelect.querySelector('select').required = false;
            }
        }

        handleViewChange(e) {
            const view = e.target.value;
            const memberFilterContainer = document.getElementById('memberFilterContainer');
            
            if (view === 'team') {
                memberFilterContainer.style.display = 'block';
            } else {
                memberFilterContainer.style.display = 'none';
            }
        }

        openCreateModal() {
            this.editMode = false;
            document.getElementById('modalTitle').textContent = 'Create Time Log';
            document.getElementById('methodField').value = '';
            document.getElementById('timeLogId').value = '';
            document.getElementById('timeLogForm').reset();
            document.getElementById('cardSelect').classList.add('hidden');
            document.getElementById('subtaskSelect').classList.add('hidden');
            document.getElementById('timeLogModal').classList.remove('hidden');
        }

        closeModal() {
            document.getElementById('timeLogModal').classList.add('hidden');
            document.getElementById('timeLogForm').reset();
        }

        async editTimeLog(id) {
            this.editMode = true;
            
            try {
                const response = await fetch(`/leader/time-logs/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    const timeLog = result.data.time_log;
                    
                    document.getElementById('modalTitle').textContent = 'Edit Time Log';
                    document.getElementById('methodField').value = 'PUT';
                    document.getElementById('timeLogId').value = timeLog.id;
                    
                    // Fill form with existing data
                    if (timeLog.card_id) {
                        document.getElementById('taskType').value = 'card';
                        document.getElementById('cardSelect').classList.remove('hidden');
                        document.querySelector('[name="card_id"]').value = timeLog.card_id;
                        document.querySelector('[name="card_id"]').required = true;
                    } else if (timeLog.subtask_id) {
                        document.getElementById('taskType').value = 'subtask';
                        document.getElementById('subtaskSelect').classList.remove('hidden');
                        document.querySelector('[name="subtask_id"]').value = timeLog.subtask_id;
                        document.querySelector('[name="subtask_id"]').required = true;
                    }
                    
                    document.querySelector('[name="start_date"]').value = timeLog.start_time.split('T')[0];
                    document.querySelector('[name="start_time"]').value = timeLog.start_time.split('T')[1].substring(0, 5);
                    
                    if (timeLog.end_time) {
                        document.querySelector('[name="end_date"]').value = timeLog.end_time.split('T')[0];
                        document.querySelector('[name="end_time"]').value = timeLog.end_time.split('T')[1].substring(0, 5);
                    }
                    
                    document.querySelector('[name="description"]').value = timeLog.description || '';
                    
                    document.getElementById('timeLogModal').classList.remove('hidden');
                } else {
                    this.showError('Failed to load time log data');
                }
            } catch (error) {
                this.showError('Failed to load time log data');
            }
        }

        async handleSubmitTimeLog(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            // Combine date and time fields
            const startDate = formData.get('start_date');
            const startTime = formData.get('start_time');
            const endDate = formData.get('end_date');
            const endTime = formData.get('end_time');
            
            if (startDate && startTime) {
                formData.set('start_time', `${startDate} ${startTime}`);
            }
            
            if (endDate && endTime) {
                formData.set('end_time', `${endDate} ${endTime}`);
            } else if (endTime && !endDate) {
                formData.set('end_time', `${startDate} ${endTime}`);
            }
            
            const method = this.editMode ? 'PUT' : 'POST';
            const url = this.editMode ? `/leader/time-logs/${formData.get('time_log_id')}` : '/leader/time-logs';
            
            if (this.editMode) {
                formData.append('_method', 'PUT');
            }
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    const action = this.editMode ? 'updated' : 'created';
                    this.showSuccess(`Time log ${action} successfully!`);
                    this.closeModal();
                    window.location.reload();
                } else {
                    this.showError(result.message || 'Failed to submit time log');
                }
            } catch (error) {
                this.showError('Failed to submit time log. Please try again.');
            }
        }

        async viewTimeLog(id) {
            try {
                const response = await fetch(`/leader/time-logs/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    const timeLog = result.time_log;
                    
                    const content = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Name</label>
                                    <p class="text-gray-900 font-semibold">${timeLog.task_name}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Worker</label>
                                    <p class="text-gray-900 font-semibold">${timeLog.worker_name}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                                    <p class="text-gray-900 font-semibold">${timeLog.project_name}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Board</label>
                                    <p class="text-gray-900 font-semibold">${timeLog.board_name}</p>
                                </div>
                            </div>
                            
                            ${timeLog.estimated_hours > 0 ? `
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                                    Time Estimation vs Actual
                                </h4>
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Estimated Hours</label>
                                        <p class="text-lg font-bold text-gray-900">${timeLog.estimated_hours}h</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Actual Hours</label>
                                        <p class="text-lg font-bold ${timeLog.is_over_estimated ? 'text-red-600' : 'text-blue-600'}">${timeLog.actual_hours}h</p>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                    <div class="h-3 rounded-full ${timeLog.is_over_estimated ? 'bg-red-500' : 'bg-blue-500'} transition-all" 
                                         style="width: ${Math.min(timeLog.progress_percentage, 100)}%"></div>
                                </div>
                                ${timeLog.is_over_estimated ? `
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5 mr-2"></i>
                                        <div>
                                            <p class="text-sm font-semibold text-red-800">Time Estimation Exceeded!</p>
                                            <p class="text-xs text-red-600 mt-1">This task has exceeded the estimated time by <strong>${timeLog.overtime_hours} hours</strong>.</p>
                                        </div>
                                    </div>
                                </div>
                                ` : `
                                <div class="flex items-center text-sm text-green-600 mt-2">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>Within estimated time</span>
                                </div>
                                `}
                            </div>
                            ` : ''}
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <p class="text-gray-900">${new Date(timeLog.start_time).toLocaleString()}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <p class="text-gray-900">${timeLog.end_time ? new Date(timeLog.end_time).toLocaleString() : 'In Progress'}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                <p class="text-emerald-600 font-bold text-xl">${timeLog.duration}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <p class="text-gray-900">${timeLog.description}</p>
                            </div>
                            
                            <div class="flex justify-end pt-4">
                                <button onclick="timeLogManager.closeViewModal()" 
                                        class="px-6 py-2 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('viewTimeLogContent').innerHTML = content;
                    document.getElementById('viewTimeLogModal').classList.remove('hidden');
                } else {
                    this.showError('Failed to load time log details');
                }
            } catch (error) {
                this.showError('Failed to load time log details');
            }
        }

        closeViewModal() {
            document.getElementById('viewTimeLogModal').classList.add('hidden');
        }

        async deleteTimeLog(id) {
            if (!confirm('Are you sure you want to delete this time log?')) {
                return;
            }

            try {
                const response = await fetch(`/leader/time-logs/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    this.showSuccess('Time log deleted successfully!');
                    window.location.reload();
                } else {
                    this.showError(result.message || 'Failed to delete time log');
                }
            } catch (error) {
                this.showError('Failed to delete time log. Please try again.');
            }
        }

        applyFilters() {
            const dateFilter = document.getElementById('dateFilter').value;
            const memberFilter = document.getElementById('memberFilter').value;
            
            const params = new URLSearchParams();
            if (dateFilter !== 'all') params.set('filter', dateFilter);
            if (memberFilter !== 'all') params.set('member', memberFilter);
            
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        showSuccess(message) {
            // You can implement a toast notification system here
            alert(message);
        }

        showError(message) {
            // You can implement a toast notification system here
            alert(message);
        }
    }

    // Initialize time log manager
    const timeLogManager = new TimeLogManager();
    </script>
</body>
</html>
