<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Cards - User Dashboard</title>
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
                    <span class="font-medium text-indigo-600">My Cards</span>
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
                            My Assigned Cards
                        </h1>
                        <p class="text-gray-600">Manage your assigned tasks and submit them for review</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <!-- Status Summary -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-600 text-sm font-medium">To Do</p>
                                        <p class="text-2xl font-bold text-gray-700">{{ $cards->where('status', 'todo')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-600 text-sm font-medium">In Progress</p>
                                        <p class="text-2xl font-bold text-blue-700">{{ $cards->where('status', 'in_progress')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-600 text-sm font-medium">Review</p>
                                        <p class="text-2xl font-bold text-purple-700">{{ $cards->where('status', 'review')->count() }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-600 text-sm font-medium">Completed</p>
                                        <p class="text-2xl font-bold text-green-700">{{ $cards->where('status', 'done')->count() }}</p>
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

            @if($cards->count() > 0)
                <!-- Cards List -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">My Task List</h2>
                            <div class="text-sm text-gray-600">
                                Click the checkbox to submit tasks for review
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($cards as $card)
                                <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-white/40 hover:shadow-lg transition-all duration-200" data-card-id="{{ $card->id }}">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-start space-x-4 flex-1">
                                            <!-- Checkbox for submitting to review or Start Work button -->
                                            <div class="flex-shrink-0 mt-1">
                                                @if($card->status === 'todo')
                                                    <button class="start-work-btn w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" 
                                                            data-card-id="{{ $card->id }}" title="Start Work">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @elseif($card->status === 'in_progress')
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only submit-review-checkbox" data-card-id="{{ $card->id }}">
                                                        <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-lg flex items-center justify-center hover:border-green-500 transition-colors checkbox-custom">
                                                            <svg class="w-4 h-4 text-white opacity-0 checkmark transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    </label>
                                                @else
                                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center
                                                        @if($card->status === 'review') bg-purple-100 border-2 border-purple-300
                                                        @elseif($card->status === 'done') bg-green-100 border-2 border-green-300
                                                        @endif">
                                                        @if($card->status === 'review')
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @elseif($card->status === 'done')
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $card->card_title }}</h3>
                                                <p class="text-sm text-gray-600 mb-3">Project: {{ $card->board->project->project_name }}</p>
                                                
                                                <div class="flex items-center space-x-3 mb-3">
                                                    <!-- Priority Badge -->
                                                    @if($card->priority)
                                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                                            @if($card->priority === 'high') bg-red-100 text-red-800
                                                            @elseif($card->priority === 'medium') bg-yellow-100 text-yellow-800
                                                            @else bg-green-100 text-green-800
                                                            @endif">
                                                            {{ ucfirst($card->priority) }}
                                                        </span>
                                                    @endif

                                                    <!-- Status Badge -->
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                                        @if($card->status === 'done') bg-green-100 text-green-800
                                                        @elseif($card->status === 'review') bg-purple-100 text-purple-800
                                                        @elseif($card->status === 'in_progress') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        @if($card->status === 'todo') To Do
                                                        @elseif($card->status === 'in_progress') Working
                                                        @elseif($card->status === 'review') Under Review
                                                        @elseif($card->status === 'done') Completed
                                                        @endif
                                                    </span>

                                                    <!-- Board Badge -->
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                                        Board: {{ $card->board->board_name }}
                                                    </span>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                    @if($card->due_date)
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <span>Due: {{ \Carbon\Carbon::parse($card->due_date)->format('M d, Y') }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($card->estimated_hours)
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>{{ $card->estimated_hours }}h estimated</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                                        </svg>
                                                        <span>{{ $card->subtasks->count() }} subtasks</span>
                                                    </div>

                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                        </svg>
                                                        <span>{{ $card->comments->count() }} comments</span>
                                                    </div>
                                                </div>

                                                @if($card->description)
                                                    <div class="mt-3 p-3 bg-gray-50 rounded-xl">
                                                        <p class="text-sm text-gray-700">{{ Str::limit($card->description, 100) }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button onclick="viewCardDetails({{ $card->id }})" 
                                                    class="p-3 bg-blue-100 text-blue-600 rounded-xl hover:bg-blue-200 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                            <button onclick="openCommentsModal({{ $card->id }})" 
                                                    class="relative p-3 bg-green-100 text-green-600 rounded-xl hover:bg-green-200 transition-colors"
                                                    title="Comments">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                @if($card->comments->count() > 0)
                                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg">
                                                        {{ $card->comments->count() }}
                                                    </span>
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 p-6 bg-blue-50 border border-blue-200 rounded-3xl">
                    <div class="flex items-start space-x-4">
                        <div class="w-6 h-6 text-blue-600 mt-1">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-blue-800 text-lg mb-2">How to Submit Tasks for Review</h4>
                            <div class="text-blue-700 text-sm space-y-1">
                                <p><strong>1.</strong> Complete your assigned tasks</p>
                                <p><strong>2.</strong> Click the checkbox next to tasks that are ready for review</p>
                                <p><strong>3.</strong> Tasks will automatically move to the Review board</p>
                                <p><strong>4.</strong> Your project leader will review and approve/reject the tasks</p>
                                <p><strong>5.</strong> Approved tasks will be marked as "Completed"</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actual Hours Info Box -->
                <div class="mt-6 p-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-3xl">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-purple-800 text-lg mb-2">⏱️ Automatic Time Tracking</h4>
                            <div class="text-purple-700 text-sm space-y-1">
                                <p><strong>🎯 Smart Calculation:</strong> Actual hours are automatically calculated when your task is approved by the project leader</p>
                                <p><strong>� Start Tracking:</strong> Time tracking begins when you start working (change status from "To Do" to "In Progress")</p>
                                <p><strong>�📅 Work Hours Only:</strong> System counts working hours (9 AM - 5 PM, Monday-Friday) from start to completion</p>
                                <p><strong>☕ Break Time:</strong> Any logged break time or time logs are automatically subtracted</p>
                                <p><strong>📊 Accurate Tracking:</strong> Get precise insights into your productivity and task completion time</p>
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
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">No Cards Assigned</h3>
                    <p class="text-gray-600 mb-8">You don't have any assigned tasks yet. Your project leader will assign tasks to you when available.</p>
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

    <!-- Comments Modal -->
    <div id="commentsModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="modal-content bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 w-full max-w-4xl max-h-[85vh] overflow-hidden scale-95 opacity-0 transition-all duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-white/20">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Task Comments</h3>
                    <p class="text-sm text-gray-600" id="commentsTaskTitle">Task Title</p>
                </div>
                <button onclick="closeCommentsModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Comments List -->
            <div class="p-6 max-h-[50vh] overflow-y-auto">
                <div id="commentsList" class="space-y-4">
                    <!-- Comments will be loaded here -->
                </div>
            </div>

            <!-- Add Comment Form -->
            <div class="border-t border-white/20 p-6">
                <form id="addCommentForm" class="flex space-x-3">
                    <input type="hidden" id="comment_card_id" name="card_id">
                    <div class="flex-1">
                        <textarea id="comment_text" name="comment_text" rows="3" placeholder="Add a comment..." 
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

    <!-- User Card Management JavaScript -->
    <script>
        /**
         * User Card Management JavaScript
         */
        class UserCardManager {
            constructor() {
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.currentCardId = null;
                this.initializeEventListeners();
            }

            initializeEventListeners() {
                // Checkbox change listeners for submitting to review
                document.querySelectorAll('.submit-review-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', (e) => this.handleSubmitReview(e));
                });

                // Start work button listeners
                document.querySelectorAll('.start-work-btn').forEach(button => {
                    button.addEventListener('click', (e) => this.handleStartWork(e));
                });

                // Comment form submission
                const addCommentForm = document.getElementById('addCommentForm');
                if (addCommentForm) {
                    addCommentForm.addEventListener('submit', (e) => this.handleAddComment(e));
                }

                // Modal background click handlers
                this.setupModalClickHandlers();
            }

            setupModalClickHandlers() {
                const commentsModal = document.getElementById('commentsModal');
                if (commentsModal) {
                    commentsModal.addEventListener('click', (e) => {
                        if (e.target.id === 'commentsModal') {
                            this.closeCommentsModal();
                        }
                    });
                }
            }

            async handleSubmitReview(e) {
                const cardId = e.target.dataset.cardId;
                const checkbox = e.target;
                const checkboxContainer = checkbox.closest('label').querySelector('.checkbox-custom');
                const checkmark = checkbox.closest('label').querySelector('.checkmark');

                if (checkbox.checked) {
                    const result = await Swal.fire({
                        title: 'Submit for Review?',
                        text: 'Are you sure you want to submit this task for review?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, submit it!',
                        cancelButtonText: 'Cancel',
                        background: 'rgba(255, 255, 255, 0.95)',
                        backdrop: 'rgba(0, 0, 0, 0.4)',
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl border border-white/20'
                        }
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/my-card/${cardId}/review`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                    'Accept': 'application/json',
                                },
                            });

                            const data = await response.json();

                            if (response.ok) {
                                // Update UI to show submitted state
                                checkboxContainer.classList.add('bg-green-500', 'border-green-500');
                                checkmark.classList.add('opacity-100');
                                checkbox.disabled = true;

                                this.showSuccess('Task submitted for review successfully!');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                checkbox.checked = false;
                                this.showError(data.message || 'Failed to submit task for review');
                            }
                        } catch (error) {
                            checkbox.checked = false;
                            this.showError('Failed to submit task for review. Please try again.');
                        }
                    } else {
                        checkbox.checked = false;
                    }
                }
            }

            async handleStartWork(e) {
                const cardId = e.target.closest('button').dataset.cardId;
                const button = e.target.closest('button');
                
                // Show confirmation dialog
                const result = await Swal.fire({
                    title: 'Start Working',
                    text: 'Are you ready to start working on this task?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Start Work!',
                    cancelButtonText: 'Cancel',
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-white/20'
                    }
                });

                if (result.isConfirmed) {
                    try {
                        // Disable button during request
                        button.disabled = true;
                        button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';

                        const response = await fetch(`/my-card/${cardId}/start`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.status === 'success') {
                            this.showSuccess('Work started successfully!');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            // Re-enable button
                            button.disabled = false;
                            button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                            this.showError(data.message || 'Failed to start work');
                        }
                    } catch (error) {
                        console.error('Start work error:', error);
                        // Re-enable button
                        button.disabled = false;
                        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                        this.showError('Failed to start work. Please try again.');
                    }
                }
            }

            async openCommentsModal(cardId) {
                this.currentCardId = cardId;
                document.getElementById('comment_card_id').value = cardId;
                await this.loadCardComments(cardId);
                await this.loadCardTitle(cardId);
                this.openModal('commentsModal');
            }

            closeCommentsModal() {
                this.closeModal('commentsModal');
            }

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

            async loadCardTitle(cardId) {
                try {
                    const response = await fetch(`/my-card/${cardId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        document.getElementById('commentsTaskTitle').textContent = data.data.card.card_title;
                    }
                } catch (error) {
                    console.error('Failed to load card title:', error);
                }
            }

            async loadCardComments(cardId) {
                try {
                    const response = await fetch(`/my-card/${cardId}/comments`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const commentsList = document.getElementById('commentsList');
                        
                        if (data.data.comments.length === 0) {
                            commentsList.innerHTML = `
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">No comments yet</h3>
                                    <p class="text-gray-600">Be the first to comment on this task!</p>
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

            async handleAddComment(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const cardId = formData.get('card_id');
                
                try {
                    const response = await fetch(`/my-card/${cardId}/comments`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (response.ok) {
                        document.getElementById('comment_text').value = '';
                        await this.loadCardComments(cardId);
                        this.showSuccess('Comment added successfully!');
                    } else {
                        this.showError(result.message || 'Failed to add comment');
                    }
                } catch (error) {
                    this.showError('Failed to add comment. Please try again.');
                }
            }

            async viewCardDetails(cardId) {
                try {
                    const response = await fetch(`/my-card/${cardId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const card = data.data.card;
                        
                        let detailsHtml = `
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-2">Description</h4>
                                    <p class="text-gray-600">${card.description || 'No description available'}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-1">Priority</h4>
                                        <p class="text-gray-600">${card.priority ? card.priority.charAt(0).toUpperCase() + card.priority.slice(1) : 'Not set'}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-1">Status</h4>
                                        <p class="text-gray-600">${card.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        Swal.fire({
                            title: card.card_title,
                            html: detailsHtml,
                            icon: 'info',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#6366f1',
                            background: 'rgba(255, 255, 255, 0.95)',
                            backdrop: 'rgba(0, 0, 0, 0.4)',
                            customClass: {
                                popup: 'rounded-3xl shadow-2xl border border-white/20'
                            }
                        });
                    }
                } catch (error) {
                    this.showError('Failed to load card details');
                }
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
        function openCommentsModal(cardId) {
            window.userCardManager.openCommentsModal(cardId);
        }

        function closeCommentsModal() {
            window.userCardManager.closeCommentsModal();
        }

        function viewCardDetails(cardId) {
            window.userCardManager.viewCardDetails(cardId);
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.userCardManager = new UserCardManager();
        });
    </script>

    <style>
        .submit-review-checkbox:checked + .checkbox-custom {
            background-color: #10b981;
            border-color: #10b981;
        }

        .submit-review-checkbox:checked + .checkbox-custom .checkmark {
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
