<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Subtasks - Todo List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
                    <span class="font-medium text-indigo-600">My Subtasks</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Add New Subtask Button -->
                    @if($userCard)
                        <button onclick="openCreateSubtaskModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Subtask
                        </button>
                    @endif
                    
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
                            My Subtasks - Todo List
                        </h1>
                        <p class="text-gray-600">Manage your subtasks and track your progress</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <!-- Status Summary -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-600 text-sm font-medium">In Progress</p>
                                        <p class="text-2xl font-bold text-blue-700">{{ $subtasks->where('status', 'in_progress')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-600 text-sm font-medium">Completed</p>
                                        <p class="text-2xl font-bold text-green-700">{{ $subtasks->where('status', 'done')->count() }}</p>
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

            @if($userCard && $subtasks->count() > 0)
                <!-- Filter and Sort Options -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-lg border border-white/20 p-6 mb-6">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-semibold text-gray-800">Filter & Sort</h3>
                            <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="all">All Status</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Completed</option>
                            </select>
                            <div class="text-sm text-gray-600">
                                Card: <span class="font-medium text-purple-600">{{ $userCard->card_title }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Total:</span>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                                {{ $subtasks->count() }} subtasks
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Subtasks List -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">My Todo List</h2>
                            <div class="text-sm text-gray-600">
                                Drag to reorder, click to mark complete
                            </div>
                        </div>
                        
                        <div id="subtasksList" class="space-y-4">
                            @foreach($subtasks as $subtask)
                                <div class="subtask-item bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-white/40 hover:shadow-lg transition-all duration-200 {{ $subtask->status === 'done' ? 'opacity-75' : '' }}" 
                                     data-subtask-id="{{ $subtask->id }}" 
                                     data-status="{{ $subtask->status }}">
                                    <div class="flex items-start space-x-4">
                                        <!-- Checkbox -->
                                        <div class="flex-shrink-0 mt-1">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only subtask-checkbox" 
                                                       data-subtask-id="{{ $subtask->id }}" 
                                                       {{ $subtask->status === 'done' ? 'checked' : '' }}>
                                                <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-lg flex items-center justify-center hover:border-green-500 transition-colors checkbox-custom {{ $subtask->status === 'done' ? 'bg-green-500 border-green-500' : '' }}">
                                                    <svg class="w-4 h-4 text-white opacity-0 checkmark transition-opacity {{ $subtask->status === 'done' ? 'opacity-100' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-semibold text-gray-800 mb-1 {{ $subtask->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                        {{ $subtask->subtask_title }}
                                                    </h3>
                                                    <p class="text-sm text-gray-600 mb-2">Project: {{ $subtask->card->board->project->project_name }}</p>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div class="flex items-center space-x-2 ml-4">
                                                    <button onclick="openSubtaskCommentsModal({{ $subtask->id }})" 
                                                            class="relative p-2 bg-green-100 text-green-600 rounded-xl hover:bg-green-200 transition-colors"
                                                            title="Comments">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                        </svg>
                                                        @if($subtask->comments->count() > 0)
                                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center">
                                                                {{ $subtask->comments->count() }}
                                                            </span>
                                                        @endif
                                                    </button>
                                                    
                                                    <button onclick="editSubtask({{ $subtask->id }})" 
                                                            class="p-2 bg-blue-100 text-blue-600 rounded-xl hover:bg-blue-200 transition-colors"
                                                            title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    
                                                    <button onclick="deleteSubtask({{ $subtask->id }})" 
                                                            class="p-2 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition-colors"
                                                            title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            @if($subtask->description)
                                                <div class="mb-3 p-3 bg-gray-50 rounded-xl">
                                                    <p class="text-sm text-gray-700">{{ $subtask->description }}</p>
                                                </div>
                                            @endif

                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <!-- Status Badge -->
                                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                                    {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $subtask->status === 'done' ? 'Completed' : 'In Progress' }}
                                                </span>

                                                <!-- Comments Count -->
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <span>{{ $subtask->comments->count() }} comments</span>
                                                </div>

                                                @if($subtask->estimated_hours)
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span>{{ $subtask->estimated_hours }}h estimated</span>
                                                    </div>
                                                @endif

                                                @if($subtask->actual_hours)
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span>{{ $subtask->actual_hours }}h actual</span>
                                                    </div>
                                                @endif

                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>{{ $subtask->created_at->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Progress Section -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Overall Progress -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-lg border border-white/20 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Overall Progress</h3>
                        @php
                            $totalSubtasks = $subtasks->count();
                            $completedSubtasks = $subtasks->where('status', 'done')->count();
                            $progressPercentage = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">{{ $completedSubtasks }} of {{ $totalSubtasks }} completed</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>

                    <!-- Time Tracking -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-lg border border-white/20 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Time Tracking</h3>
                        @php
                            $totalEstimated = $subtasks->sum('estimated_hours');
                            $totalActual = $subtasks->sum('actual_hours');
                        @endphp
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Estimated Hours:</span>
                                <span class="font-semibold text-blue-600">{{ $totalEstimated }}h</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Actual Hours:</span>
                                <span class="font-semibold text-green-600">{{ $totalActual }}h</span>
                            </div>
                            @if($totalEstimated > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Efficiency:</span>
                                    <span class="font-semibold {{ $totalActual <= $totalEstimated ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $totalEstimated > 0 ? round(($totalEstimated / max($totalActual, 1)) * 100) : 0 }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                @if(!$userCard)
                    <!-- No Card State -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Card Assigned</h3>
                        <p class="text-gray-600 mb-8">You don't have any assigned card yet. Please contact your project leader to get a card assignment.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                @elseif($subtasks->count() == 0)
                    <!-- Empty Subtasks State -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Subtasks Yet</h3>
                        <p class="text-gray-600 mb-8">Start organizing your work by creating your first subtask for <strong>{{ $userCard->card_title }}</strong>.</p>
                        <button onclick="openCreateSubtaskModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Your First Subtask
                        </button>
                    </div>
                @endif
            @endif
        </main>
    </div>

    @if($userCard)
        <!-- Create Subtask Modal -->
        <div id="createSubtaskModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="modal-content bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 w-full max-w-4xl max-h-[90vh] overflow-hidden scale-95 opacity-0 transition-all duration-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-white/20">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Create New Subtask</h3>
                        <p class="text-sm text-gray-600">Add a new item to your todo list</p>
                    </div>
                    <button onclick="closeCreateSubtaskModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="createSubtaskForm" class="p-6 space-y-6">
                    <!-- Current Card Info -->
                    @if($userCard)
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-4 border border-purple-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-purple-800">{{ $userCard->card_title }}</h4>
                                    <p class="text-sm text-purple-600">{{ $userCard->board->project->project_name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Subtask Title -->
                    <div>
                        <label for="subtask_title" class="block text-sm font-medium text-gray-700 mb-2">Subtask Title</label>
                        <input type="text" id="subtask_title" name="subtask_title" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter subtask title...">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                  placeholder="Describe the subtask..."></textarea>
                    </div>

                    <!-- Estimated Hours -->
                    <div>
                        <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours (Optional)</label>
                        <input type="number" id="estimated_hours" name="estimated_hours" step="0.5" min="0"
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="0.0">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button type="button" onclick="closeCreateSubtaskModal()" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Create Subtask
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

                </div>
        </div>
    @endif

    @if($userCard)
        <!-- Edit Subtask Modal -->
    <div id="editSubtaskModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="modal-content bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 w-full max-w-4xl max-h-[90vh] overflow-hidden scale-95 opacity-0 transition-all duration-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-white/20">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Edit Subtask</h3>
                        <p class="text-sm text-gray-600">Update your subtask details</p>
                    </div>
                    <button onclick="closeEditSubtaskModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="editSubtaskForm" class="p-6 space-y-6">
                    <input type="hidden" id="edit_subtask_id" name="subtask_id">
                    
                    <!-- Subtask Title -->
                    <div>
                        <label for="edit_subtask_title" class="block text-sm font-medium text-gray-700 mb-2">Subtask Title</label>
                        <input type="text" id="edit_subtask_title" name="subtask_title" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter subtask title...">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="edit_description" name="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                  placeholder="Describe the subtask..."></textarea>
                    </div>

                    <!-- Estimated Hours -->
                    <div>
                        <label for="edit_estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours (Optional)</label>
                        <input type="number" id="edit_estimated_hours" name="estimated_hours" step="0.5" min="0"
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="0.0">
                    </div>

                    <!-- Actual Hours Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-100">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-blue-800 mb-1">Actual Hours - Auto Calculated</h4>
                                <p class="text-sm text-blue-700">
                                    Actual hours are automatically calculated when you complete a subtask. 
                                    The system counts working hours (8 AM - 4 PM, Mon-Fri) from creation time 
                                    to completion time, minus any logged break time.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button type="button" onclick="closeEditSubtaskModal()" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Update Subtask
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Subtask Comments Modal -->
    <div id="subtaskCommentsModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="modal-content bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 w-full max-w-4xl max-h-[85vh] overflow-hidden scale-95 opacity-0 transition-all duration-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-white/20">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Subtask Comments</h3>
                        <p class="text-sm text-gray-600" id="commentsSubtaskTitle">Subtask Title</p>
                    </div>
                    <button onclick="closeSubtaskCommentsModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Comments List -->
                <div class="p-6 max-h-[50vh] overflow-y-auto">
                    <div id="subtaskCommentsList" class="space-y-4">
                        <!-- Comments will be loaded here -->
                    </div>
                </div>

                <!-- Add Comment Form -->
                <div class="border-t border-white/20 p-6">
                    <form id="addSubtaskCommentForm" class="flex space-x-3">
                        <input type="hidden" id="comment_subtask_id" name="subtask_id">
                        <div class="flex-1">
                            <textarea id="subtask_comment_text" name="comment_text" rows="3" placeholder="Add a comment..." 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-2xl hover:from-purple-600 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Subtask Management JavaScript -->
    <script>
        class SubtaskManager {
            constructor() {
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.currentSubtaskId = null;
                this.initializeEventListeners();
            }

            initializeEventListeners() {
                // Form submissions
                const createForm = document.getElementById('createSubtaskForm');
                const editForm = document.getElementById('editSubtaskForm');
                const addCommentForm = document.getElementById('addSubtaskCommentForm');
                
                if (createForm) {
                    createForm.addEventListener('submit', (e) => this.handleCreateSubtask(e));
                }
                
                if (editForm) {
                    editForm.addEventListener('submit', (e) => this.handleEditSubtask(e));
                }

                if (addCommentForm) {
                    addCommentForm.addEventListener('submit', (e) => this.handleAddSubtaskComment(e));
                }

                // Checkbox changes
                document.querySelectorAll('.subtask-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', (e) => this.handleStatusChange(e));
                });

                // Filter changes
                const statusFilter = document.getElementById('statusFilter');
                
                if (statusFilter) {
                    statusFilter.addEventListener('change', () => this.applyFilters());
                }

                // Modal background clicks
                this.setupModalClickHandlers();
            }

            setupModalClickHandlers() {
                const modals = ['createSubtaskModal', 'editSubtaskModal', 'subtaskCommentsModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.addEventListener('click', (e) => {
                            if (e.target.id === modalId) {
                                this.closeModal(modalId);
                            }
                        });
                    }
                });
            }

            async handleCreateSubtask(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                
                try {
                    const response = await fetch('/subtasks', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (response.ok) {
                        this.showSuccess('Subtask created successfully!');
                        this.closeCreateSubtaskModal();
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.showError(result.message || 'Failed to create subtask');
                    }
                } catch (error) {
                    this.showError('Failed to create subtask. Please try again.');
                }
            }

            async handleEditSubtask(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const subtaskId = formData.get('subtask_id');
                
                const data = {};
                for (let [key, value] of formData.entries()) {
                    if (value !== '' && key !== 'subtask_id') {
                        data[key] = value;
                    }
                }
                
                try {
                    const response = await fetch(`/subtasks/${subtaskId}`, {
                        method: 'PUT',
                        body: JSON.stringify(data),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (response.ok) {
                        this.showSuccess('Subtask updated successfully!');
                        this.closeEditSubtaskModal();
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.showError(result.message || 'Failed to update subtask');
                    }
                } catch (error) {
                    this.showError('Failed to update subtask. Please try again.');
                }
            }

            async handleStatusChange(e) {
                const subtaskId = e.target.dataset.subtaskId;
                const isChecked = e.target.checked;
                const newStatus = isChecked ? 'done' : 'in_progress';
                
                const checkbox = e.target;
                const checkboxContainer = checkbox.closest('label').querySelector('.checkbox-custom');
                const checkmark = checkbox.closest('label').querySelector('.checkmark');
                const subtaskItem = checkbox.closest('.subtask-item');
                
                try {
                    const response = await fetch(`/subtasks/${subtaskId}/status`, {
                        method: 'PATCH',
                        body: JSON.stringify({ status: newStatus }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Update UI
                        if (isChecked) {
                            checkboxContainer.classList.add('bg-green-500', 'border-green-500');
                            checkmark.classList.add('opacity-100');
                            subtaskItem.classList.add('opacity-75');
                            subtaskItem.querySelector('h3').classList.add('line-through', 'text-gray-500');
                        } else {
                            checkboxContainer.classList.remove('bg-green-500', 'border-green-500');
                            checkmark.classList.remove('opacity-100');
                            subtaskItem.classList.remove('opacity-75');
                            subtaskItem.querySelector('h3').classList.remove('line-through', 'text-gray-500');
                        }
                        
                        this.showSuccess(`Subtask marked as ${newStatus === 'done' ? 'completed' : 'in progress'}!`);
                        
                        // Update data attributes
                        subtaskItem.dataset.status = newStatus;
                        
                        // Refresh page after a delay to update counters
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        // Revert checkbox state
                        checkbox.checked = !isChecked;
                        this.showError(result.message || 'Failed to update subtask status');
                    }
                } catch (error) {
                    // Revert checkbox state
                    checkbox.checked = !isChecked;
                    this.showError('Failed to update subtask status. Please try again.');
                }
            }

            async loadSubtaskData(subtaskId) {
                try {
                    const response = await fetch(`/subtasks/${subtaskId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const subtask = data.data.subtask;
                        
                        // Populate edit form
                        document.getElementById('edit_subtask_id').value = subtask.id;
                        document.getElementById('edit_subtask_title').value = subtask.subtask_title;
                        document.getElementById('edit_description').value = subtask.description || '';
                        document.getElementById('edit_estimated_hours').value = subtask.estimated_hours || '';
                    } else {
                        this.showError('Failed to load subtask data');
                    }
                } catch (error) {
                    console.error('Failed to load subtask data:', error);
                    this.showError('Failed to load subtask data');
                }
            }

            async deleteSubtask(subtaskId) {
                const result = await Swal.fire({
                    title: 'Delete Subtask?',
                    text: 'This action cannot be undone. The subtask will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-white/20'
                    }
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/subtasks/${subtaskId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.showSuccess('Subtask deleted successfully!');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.showError(data.message || 'Failed to delete subtask');
                        }
                    } catch (error) {
                        this.showError('Failed to delete subtask. Please try again.');
                    }
                }
            }

            // Subtask Comments Management
            async openSubtaskCommentsModal(subtaskId) {
                this.currentSubtaskId = subtaskId;
                document.getElementById('comment_subtask_id').value = subtaskId;
                await this.loadSubtaskComments(subtaskId);
                await this.loadSubtaskTitle(subtaskId);
                this.openModal('subtaskCommentsModal');
            }

            closeSubtaskCommentsModal() {
                this.closeModal('subtaskCommentsModal');
            }

            async loadSubtaskTitle(subtaskId) {
                try {
                    const response = await fetch(`/subtasks/${subtaskId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        document.getElementById('commentsSubtaskTitle').textContent = data.data.subtask.subtask_title;
                    }
                } catch (error) {
                    console.error('Failed to load subtask title:', error);
                }
            }

            async loadSubtaskComments(subtaskId) {
                console.log('Loading comments for subtask:', subtaskId);
                try {
                    const response = await fetch(`/subtasks/${subtaskId}/comments`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    console.log('Comments response status:', response.status);
                    if (response.ok) {
                        const data = await response.json();
                        console.log('Comments data:', data);
                        const commentsList = document.getElementById('subtaskCommentsList');
                        
                        if (data.data.comments.length === 0) {
                            commentsList.innerHTML = `
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">No comments yet</h3>
                                    <p class="text-gray-600">Be the first to comment on this subtask!</p>
                                </div>
                            `;
                        } else {
                            commentsList.innerHTML = data.data.comments.map(comment => `
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold">${comment.user.name.charAt(0).toUpperCase()}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-semibold text-gray-800 text-sm">${comment.user.name}</span>
                                                <span class="text-gray-500 text-xs">${new Date(comment.created_at).toLocaleDateString()}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm leading-relaxed">${comment.comment_text}</p>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        }
                    }
                } catch (error) {
                    console.error('Failed to load comments:', error);
                }
            }

            async handleAddSubtaskComment(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const subtaskId = formData.get('subtask_id');
                
                console.log('Submitting comment for subtask:', subtaskId);
                console.log('Form data:', Object.fromEntries(formData));
                
                try {
                    const response = await fetch(`/subtasks/${subtaskId}/comments`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    console.log('Response status:', response.status);
                    const result = await response.json();
                    console.log('Response data:', result);

                    if (response.ok) {
                        document.getElementById('subtask_comment_text').value = '';
                        await this.loadSubtaskComments(subtaskId);
                        this.updateCommentBadge(subtaskId);
                        this.showSuccess('Comment added successfully!');
                    } else {
                        console.error('Error response:', result);
                        this.showError(result.message || 'Failed to add comment');
                    }
                } catch (error) {
                    console.error('Catch error:', error);
                    this.showError('Failed to add comment. Please try again.');
                }
            }

            applyFilters() {
                const statusFilter = document.getElementById('statusFilter').value;
                const subtaskItems = document.querySelectorAll('.subtask-item');

                subtaskItems.forEach(item => {
                    const status = item.dataset.status;
                    
                    let showItem = true;
                    
                    if (statusFilter !== 'all' && status !== statusFilter) {
                        showItem = false;
                    }
                    
                    item.style.display = showItem ? 'block' : 'none';
                });
            }

            updateCommentBadge(subtaskId) {
                // Find the comment button for this subtask
                const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
                if (!subtaskItem) return;

                const commentButton = subtaskItem.querySelector('button[onclick*="openSubtaskCommentsModal"]');
                if (!commentButton) return;

                // Get current comments count from modal
                const commentsList = document.getElementById('subtaskCommentsList');
                const commentsCount = commentsList ? commentsList.children.length : 0;

                // Update or create badge
                let badge = commentButton.querySelector('.absolute');
                if (commentsCount > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center';
                        commentButton.appendChild(badge);
                    }
                    badge.textContent = commentsCount;
                } else if (badge) {
                    badge.remove();
                }
            }

            // Modal Management
            openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    
                    // Ensure proper centering
                    modal.style.alignItems = 'center';
                    modal.style.justifyContent = 'center';
                    
                    const modalContent = modal.querySelector('.modal-content');
                    if (modalContent) {
                        setTimeout(() => {
                            modalContent.classList.remove('scale-95', 'opacity-0');
                            modalContent.classList.add('scale-100', 'opacity-100');
                        }, 10);
                    }
                }
            }

            closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const modalContent = modal.querySelector('.modal-content');
                    if (modalContent) {
                        modalContent.classList.remove('scale-100', 'opacity-100');
                        modalContent.classList.add('scale-95', 'opacity-0');
                    }
                    
                    setTimeout(() => {
                        modal.style.display = 'none';
                        document.body.style.overflow = '';
                        
                        const form = modal.querySelector('form');
                        if (form) {
                            form.reset();
                        }
                    }, 200);
                }
            }

            openCreateSubtaskModal() {
                this.openModal('createSubtaskModal');
            }

            closeCreateSubtaskModal() {
                this.closeModal('createSubtaskModal');
            }

            async openEditSubtaskModal(subtaskId) {
                this.currentSubtaskId = subtaskId;
                await this.loadSubtaskData(subtaskId);
                this.openModal('editSubtaskModal');
            }

            closeEditSubtaskModal() {
                this.closeModal('editSubtaskModal');
            }

            showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message,
                    showConfirmButton: false,
                    timer: 1500,
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-white/20'
                    }
                });
            }

            showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-white/20'
                    }
                });
            }
        }

        // Global functions
        function openCreateSubtaskModal() {
            window.subtaskManager.openCreateSubtaskModal();
        }

        function closeCreateSubtaskModal() {
            window.subtaskManager.closeCreateSubtaskModal();
        }

        function editSubtask(subtaskId) {
            window.subtaskManager.openEditSubtaskModal(subtaskId);
        }

        function closeEditSubtaskModal() {
            window.subtaskManager.closeEditSubtaskModal();
        }

        function deleteSubtask(subtaskId) {
            window.subtaskManager.deleteSubtask(subtaskId);
        }

        function openSubtaskCommentsModal(subtaskId) {
            window.subtaskManager.openSubtaskCommentsModal(subtaskId);
        }

        function closeSubtaskCommentsModal() {
            window.subtaskManager.closeSubtaskCommentsModal();
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.subtaskManager = new SubtaskManager();
        });
    </script>

    <style>
        .subtask-checkbox:checked + .checkbox-custom {
            background-color: #10b981;
            border-color: #10b981;
        }

        .subtask-checkbox:checked + .checkbox-custom .checkmark {
            opacity: 1;
        }
        
        /* Force modal width */
        .modal-content {
            min-width: 800px !important;
            max-width: 1000px !important;
        }
        
        @media (max-width: 1024px) {
            .modal-content {
                min-width: 90vw !important;
                max-width: 95vw !important;
            }
        }
    </style>
</body>
</html>
