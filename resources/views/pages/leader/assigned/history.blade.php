<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assignment History - Leader Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <span class="font-medium text-indigo-600">Assignment History</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                            Assignment History
                        </h1>
                        <p class="text-gray-600">View all card assignment records and their completion status</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <!-- Summary Stats -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-yellow-600 text-sm font-medium">Pending Review</p>
                                        <p class="text-2xl font-bold text-yellow-700">{{ $assignments->where('assignment_status', 'pending_confirmation')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 border border-orange-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-orange-600 text-sm font-medium">Rework</p>
                                        <p class="text-2xl font-bold text-orange-700">{{ $assignments->where('assignment_status', 'in_progress')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-600 text-sm font-medium">Completed</p>
                                        <p class="text-2xl font-bold text-green-700">{{ $assignments->where('assignment_status', 'assigned')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($project && $assignments->count() > 0)
                <!-- Filter Section -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-lg border border-white/20 p-6 mb-6">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Filter by Status:</label>
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="all">All Assignments</option>
                            <option value="pending_confirmation">Pending Review</option>
                            <option value="in_progress">Rejected - Rework</option>
                            <option value="assigned">Completed</option>
                        </select>
                    </div>
                </div>

                <!-- Assignments Table -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Card Title</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Assigned To</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Board</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Submitted At</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="assignmentsTableBody">
                                @foreach($assignments as $assignment)
                                @if($assignment->card && $assignment->user && $assignment->card->board && $assignment->card->board->project)
                                <tr class="hover:bg-gray-50 transition-colors assignment-row" data-status="{{ $assignment->assignment_status }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ $assignment->card->card_title }}</div>
                                                <div class="text-xs text-gray-500">{{ $assignment->card->board->project->project_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <x-user-avatar :user="$assignment->user" size="sm" />
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $assignment->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $assignment->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                            {{ $assignment->card->board->board_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($assignment->assignment_status === 'pending_confirmation')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Pending Review
                                            </span>
                                        @elseif($assignment->assignment_status === 'in_progress')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Rejected - Rework
                                            </span>
                                        @elseif($assignment->assignment_status === 'assigned')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Completed
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst(str_replace('_', ' ', $assignment->assignment_status)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($assignment->created_at)
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $assignment->created_at->format('M d, Y') }}</span>
                                                <span class="text-xs text-gray-500">{{ $assignment->created_at->format('H:i A') }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-500 italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($assignment->card && $assignment->card->actual_hours > 0)
                                            @php
                                                $actualHours = $assignment->card->actual_hours;
                                                $days = floor($actualHours / 8); // 8 working hours per day
                                                $remainingHours = $actualHours - ($days * 8);
                                            @endphp
                                            <div class="flex flex-col">
                                                <span class="font-medium text-green-600">
                                                    @if($days > 0)
                                                        {{ $days }}d {{ number_format($remainingHours, 1) }}h
                                                    @else
                                                        {{ number_format($actualHours, 1) }}h
                                                    @endif
                                                </span>
                                                <span class="text-xs text-gray-500">Actual work time</span>
                                            </div>
                                        @elseif($assignment->assignment_status === 'assigned')
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-500">0h</span>
                                                <span class="text-xs text-gray-500">No time logged</span>
                                            </div>
                                        @else
                                            <div class="flex flex-col">
                                                <span class="font-medium text-blue-600">
                                                    <i class="fas fa-spinner fa-spin text-xs mr-1"></i>In progress
                                                </span>
                                                <span class="text-xs text-gray-500">Working on it</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <button onclick="viewAssignmentDetails({{ $assignment->id }})" 
                                                class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-3xl">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-blue-800 text-lg mb-2">Assignment Status Flow</h4>
                            <div class="text-blue-700 text-sm space-y-2">
                                <p>• <strong>Pending Review (Yellow):</strong> User submitted card for review, waiting for leader's approval</p>
                                <p>• <strong>Rejected - Rework (Orange):</strong> Leader rejected the submission, user needs to rework and resubmit</p>
                                <p>• <strong>Completed (Green):</strong> Leader approved the card, work completed successfully</p>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p class="font-semibold mb-1">Workflow:</p>
                                    <p class="text-xs">User Submit → <span class="text-yellow-700 font-medium">Pending Review</span> → Leader Review</p>
                                    <p class="text-xs ml-4">↳ If Approved → <span class="text-green-700 font-medium">Completed</span></p>
                                    <p class="text-xs ml-4">↳ If Rejected → <span class="text-orange-700 font-medium">Rework</span> → User can resubmit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">No Assignment History</h3>
                    <p class="text-gray-600 mb-8">There are no assignment records yet. Assignment records are created when team members submit cards for review.</p>
                    <a href="{{ route('leader.assigned.cards') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        View Assigned Cards
                    </a>
                </div>
            @endif
        </main>
    </div>

    <!-- Assignment Details Modal -->
    <div id="assignmentDetailsModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">Assignment Details</h3>
                        <button onclick="closeAssignmentModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <svg class="w-5 h-5 text-white group-hover:text-white/80 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="assignmentDetailsContent" class="p-6">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter functionality
        document.getElementById('statusFilter')?.addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('.assignment-row');
            
            rows.forEach(row => {
                const status = row.dataset.status;
                if (filterValue === 'all' || status === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // View assignment details
        async function viewAssignmentDetails(assignmentId) {
            const assignments = @json($assignments);
            const assignment = assignments.find(a => a.id === assignmentId);
            
            if (!assignment) {
                alert('Assignment not found');
                return;
            }

            let statusBadge = '';
            if (assignment.assignment_status === 'pending_confirmation') {
                statusBadge = '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Review</span>';
            } else if (assignment.assignment_status === 'in_progress') {
                statusBadge = '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Rejected - Rework</span>';
            } else if (assignment.assignment_status === 'assigned') {
                statusBadge = '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>';
            } else {
                statusBadge = `<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">${assignment.assignment_status}</span>`;
            }

            let duration = 'Ongoing';
            let durationClass = 'text-blue-600';
            
            // Get actual_hours from card
            if (assignment.card && assignment.card.actual_hours > 0) {
                const actualHours = assignment.card.actual_hours;
                const days = Math.floor(actualHours / 8); // 8 working hours per day
                const remainingHours = actualHours - (days * 8);
                
                if (days > 0) {
                    duration = `${days}d ${remainingHours.toFixed(1)}h`;
                } else {
                    duration = `${actualHours.toFixed(1)}h`;
                }
                
                if (assignment.assignment_status === 'assigned') {
                    durationClass = 'text-green-600';
                } else {
                    durationClass = 'text-blue-600';
                }
            } else if (assignment.assignment_status === 'assigned') {
                duration = '0h';
                durationClass = 'text-gray-500';
            } else {
                duration = '<span class="text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i>In progress</span>';
            }

            const content = `
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-4 border border-indigo-100">
                        <h4 class="font-semibold text-gray-800 mb-2">Card Information</h4>
                        <p class="text-lg font-bold text-gray-900 mb-1">${assignment.card.card_title}</p>
                        <p class="text-sm text-gray-600">${assignment.card.description || 'No description'}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Assigned To</h4>
                            <p class="text-gray-900 font-medium">${assignment.user.name}</p>
                            <p class="text-sm text-gray-600">${assignment.user.email}</p>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Status</h4>
                            ${statusBadge}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Board</h4>
                            <p class="text-gray-900">${assignment.card.board.board_name}</p>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Project</h4>
                            <p class="text-gray-900">${assignment.card.board.project.project_name}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Timeline</h4>
                        <div class="space-y-2 text-sm">
                            ${assignment.assigned_at ? `<p><span class="font-medium">Assigned:</span> ${new Date(assignment.assigned_at).toLocaleString()}</p>` : ''}
                            ${assignment.started_at ? `<p><span class="font-medium">Started:</span> ${new Date(assignment.started_at).toLocaleString()}</p>` : ''}
                            ${assignment.completed_at ? `<p><span class="font-medium">Completed:</span> ${new Date(assignment.completed_at).toLocaleString()}</p>` : ''}
                            <p><span class="font-medium">Submitted for Review:</span> ${new Date(assignment.created_at).toLocaleString()}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-100">
                        <h4 class="font-semibold text-gray-800 mb-2">Work Duration (Working Hours Only)</h4>
                        <p class="text-2xl font-bold ${durationClass}">${duration}</p>
                        <p class="text-xs text-gray-600 mt-1">Calculated from time logs (Mon-Fri, 08:00-16:00)</p>
                    </div>

                    <div class="pt-4">
                        <button onclick="closeAssignmentModal()" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl hover:from-indigo-700 hover:to-purple-700 transition-colors font-semibold">
                            Close
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('assignmentDetailsContent').innerHTML = content;
            openAssignmentModal();
        }

        function openAssignmentModal() {
            const modal = document.getElementById('assignmentDetailsModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAssignmentModal() {
            const modal = document.getElementById('assignmentDetailsModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('assignmentDetailsModal')?.addEventListener('click', function(e) {
            if (e.target.id === 'assignmentDetailsModal') {
                closeAssignmentModal();
            }
        });
    </script>
</body>
</html>
