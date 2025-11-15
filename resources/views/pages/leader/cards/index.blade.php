<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cards Management - Leader Dashboard</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
        
        <div class="container mx-auto px-3 py-4 lg:px-8 lg:py-8">
            @if($project)
                <!-- Header Section -->
                <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-gray-100 mb-4 lg:mb-8 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-4 py-4 lg:px-8 lg:py-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 lg:gap-4">
                            <div>
                                <h1 class="text-lg lg:text-3xl font-bold text-white mb-1 lg:mb-2 truncate">{{ $project->project_name }} - Cards</h1>
                                <p class="text-purple-100 text-xs lg:text-base">Create and manage task assignments for your team</p>
                            </div>
                            <div class="flex items-center space-x-2 lg:space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl lg:rounded-2xl px-3 py-1.5 lg:px-4 lg:py-2">
                                    <span class="text-white font-semibold text-xs lg:text-base">Total: {{ $cards->count() }}</span>
                                </div>
                                <button onclick="openCreateCardModal()" 
                                        class="bg-white text-purple-600 px-3 py-1.5 lg:px-6 lg:py-3 rounded-xl lg:rounded-2xl font-semibold hover:bg-gray-50 transition-all duration-200 shadow-lg text-xs lg:text-base">
                                    <i class="fas fa-plus mr-1 lg:mr-2"></i><span class="hidden sm:inline">New </span>Task
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 lg:gap-4 mb-4 lg:mb-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-blue-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Total Cards</p>
                                    <p class="text-2xl font-bold text-blue-700">{{ $cards->count() }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-600 text-[10px] lg:text-sm font-medium">To Do</p>
                                    <p class="text-lg lg:text-2xl font-bold text-gray-700">{{ $cards->where('status', 'todo')->count() }}</p>
                                </div>
                                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-gray-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-yellow-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-600 text-[10px] lg:text-sm font-medium">Progress</p>
                                    <p class="text-lg lg:text-2xl font-bold text-orange-700">{{ $cards->where('status', 'in_progress')->count() }}</p>
                                </div>
                                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-orange-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-purple-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-[10px] lg:text-sm font-medium">Review</p>
                                    <p class="text-lg lg:text-2xl font-bold text-purple-700">{{ $cards->where('status', 'review')->count() }}</p>
                                </div>
                                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-purple-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-green-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-600 text-[10px] lg:text-sm font-medium">Done</p>
                                    <p class="text-lg lg:text-2xl font-bold text-green-700">{{ $cards->where('status', 'done')->count() }}</p>
                                </div>
                                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($cards->count() > 0)
                    <!-- Cards List -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-xl border border-white/20">
                        <div class="p-3 lg:p-6">
                            <div class="space-y-3 lg:space-y-4">
                                @foreach($cards as $card)
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl lg:rounded-2xl p-3 lg:p-6 border border-white/40 hover:shadow-lg transition-all duration-200">
                                        <div class="flex items-start justify-between gap-2 lg:gap-4 mb-3 lg:mb-4">
                                            <div class="flex items-start space-x-2 lg:space-x-4 flex-1 min-w-0">
                                                <div class="flex-shrink-0">
                                                    <x-user-avatar :user="$card->assignedUser" size="md" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-sm lg:text-lg font-semibold text-gray-800 mb-1 truncate">{{ $card->card_title }}</h3>
                                                    <p class="text-xs lg:text-sm text-gray-600 mb-2 lg:mb-3 truncate">Assigned to {{ $card->assignedUser->name }}</p>
                                                    
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
                                                        <span class="px-2 py-0.5 lg:px-3 lg:py-1 text-[10px] lg:text-xs font-medium rounded-full
                                                            @if($card->status === 'done') bg-green-100 text-green-800
                                                            @elseif($card->status === 'review') bg-purple-100 text-purple-800
                                                            @else bg-blue-100 text-blue-800
                                                            @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex items-start space-x-1 lg:space-x-2 flex-shrink-0">
                                                <button onclick="openEditCardModal({{ $card->id }})" 
                                                        class="p-1.5 lg:p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors relative" 
                                                        title="Edit Card">
                                                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <button onclick="openCommentsModal({{ $card->id }})" 
                                                        class="p-1.5 lg:p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors relative" 
                                                        title="Comments">
                                                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    @if($card->comments->count() > 0)
                                                        <span class="absolute -top-1 -right-1 bg-purple-500 text-white text-[10px] lg:text-xs rounded-full w-4 h-4 lg:w-5 lg:h-5 flex items-center justify-center">
                                                            {{ $card->comments->count() }}
                                                        </span>
                                                    @endif
                                                </button>
                                                
                                                <button onclick="deleteCard({{ $card->id }})" 
                                                        class="p-1.5 lg:p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                        title="Delete Card">
                                                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 lg:gap-4 text-xs lg:text-sm text-gray-600">
                                            <div class="flex items-center space-x-1.5 lg:space-x-2">
                                                <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                <span>Board: {{ $card->board->board_name }}</span>
                                            </div>
                                            
                                            @if($card->due_date)
                                                <div class="flex items-center space-x-1.5 lg:space-x-2">
                                                    <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>Due: {{ \Carbon\Carbon::parse($card->due_date)->format('M d, Y') }}</span>
                                                </div>
                                            @endif

                                            @if($card->estimated_hours)
                                                <div class="flex items-center space-x-1.5 lg:space-x-2">
                                                    <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Est: {{ $card->estimated_hours }}h</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($card->description)
                                            <p class="mt-3 lg:mt-4 text-xs lg:text-sm text-gray-600 bg-gray-50 rounded-lg p-2 lg:p-3 line-clamp-3">{{ $card->description }}</p>
                                        @endif

                                        <!-- Subtasks Preview -->
                                        @if($card->subtasks->count() > 0)
                                            <div class="mt-3 lg:mt-4 pt-3 lg:pt-4 border-t border-gray-100">
                                                <div class="flex items-center text-xs lg:text-sm text-gray-600">
                                                    <svg class="w-3 h-3 lg:w-4 lg:h-4 mr-1.5 lg:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                    {{ $card->subtasks->count() }} subtask{{ $card->subtasks->count() > 1 ? 's' : '' }}
                                                    <span class="ml-2 text-green-600">
                                                        ({{ $card->subtasks->where('status', 'done')->count() }} completed)
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Cards State -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-6 lg:p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-r from-purple-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6">
                                <svg class="w-8 h-8 lg:w-12 lg:h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-800 mb-3 lg:mb-4">No Tasks Yet</h3>
                            <p class="text-sm lg:text-base text-gray-600 mb-4 lg:mb-6">
                                Start organizing your project by creating tasks for your team members.
                            </p>
                            <button onclick="openCreateCardModal()" class="bg-gradient-to-r from-purple-500 to-pink-600 text-white px-4 py-2 lg:px-6 lg:py-3 rounded-xl hover:from-purple-600 hover:to-pink-700 transition-all duration-200 flex items-center space-x-2 mx-auto text-sm lg:text-base">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Create Your First Task</span>
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <!-- No Project State -->
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-6 lg:p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6">
                            <svg class="w-8 h-8 lg:w-12 lg:h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-800 mb-3 lg:mb-4">No Project Assigned</h3>
                        <p class="text-sm lg:text-base text-gray-600 mb-4 lg:mb-6">
                            You need to be assigned to a project before you can manage cards.
                        </p>
                        <a href="{{ route('dashboard') }}" 
                           class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 lg:px-6 lg:py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2 mx-auto w-fit text-sm lg:text-base">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v1H8V5z"></path>
                            </svg>
                            <span>Go to Dashboard</span>
                        </a>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Create Card Modal -->
    <div id="createCardModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-screen w-full p-4">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Modal Header with Gradient -->
                <div class="relative bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 rounded-t-3xl p-6 flex-shrink-0">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Create New Task</h3>
                                <p class="text-white/80 text-sm">Assign a new task to your team member</p>
                            </div>
                        </div>
                        <!-- Close Button -->
                        <button onclick="closeCreateCardModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <svg class="w-5 h-5 text-white group-hover:text-white/80 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body - Scrollable -->
                <div class="flex-1 overflow-y-auto p-8">
                    <form id="createCardForm">
                        <div class="mb-6">
                            <label for="card_title" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span>Task Title</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="card_title" name="card_title" required
                                       class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md"
                                       placeholder="Enter task title...">
                            </div>
                        </div>

                        <!-- Info Note about Auto Board Assignment -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="flex items-start space-x-3">
                                <div class="w-5 h-5 text-blue-600 mt-0.5">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-800 text-sm">Auto Board Assignment</h4>
                                    <p class="text-blue-700 text-xs mt-1">
                                        New cards will automatically be placed in the <strong>"In Progress"</strong> board. 
                                        Cards will move between boards automatically based on their status changes.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="assigned_user" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Assign to</span>
                            </label>
                            
                            <!-- Info Note -->
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                <p class="text-xs text-blue-600">Search for team members by name or email to assign this task</p>
                            </div>
                            
                            <div class="relative group">
                                <input type="text" id="userSearch" placeholder="Search team members..."
                                       class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md">
                                
                                <!-- Search Icon -->
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Loading Spinner -->
                                <div id="searchLoading" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                    <div class="w-5 h-5 border-2 border-purple-300 border-t-purple-600 rounded-full animate-spin"></div>
                                </div>
                            </div>
                            
                            <!-- Search Results Dropdown -->
                            <div id="userSearchResults" class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl hidden transform scale-95 opacity-0 transition-all duration-200 max-h-64 overflow-y-auto">
                                <div id="searchResultsList" class="p-2">
                                    <!-- Search results will be populated here -->
                                </div>
                                <div id="noResults" class="p-4 text-center text-gray-500 hidden">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm">No team members found</p>
                                    <p class="text-xs text-gray-400">Only project team members can be assigned</p>
                                </div>
                            </div>
                            
                            <!-- Selected User Display -->
                            <div id="selectedUserDisplay" class="mt-4 hidden">
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-2xl">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <img id="selectedUserAvatar" src="" alt="" class="w-10 h-10 rounded-full object-cover hidden">
                                            <div id="selectedUserInitialContainer" class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                                <span id="selectedUserInitial" class="text-white font-semibold text-sm"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <p id="selectedUserName" class="font-semibold text-gray-800"></p>
                                            <p id="selectedUserEmail" class="text-sm text-gray-600"></p>
                                        </div>
                                    </div>
                                    <button type="button" id="clearSelection" class="p-2 hover:bg-white/50 rounded-xl transition-colors">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" id="selectedUserId" name="user_id" value="">
                        </div>

                        <div class="mb-6">
                            <label for="card_description" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                <span>Description (Optional)</span>
                            </label>
                            <div class="relative group">
                                <textarea id="card_description" name="description" rows="3"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md resize-none"
                                          placeholder="Describe the task details..."></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="card_priority" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span>Priority</span>
                                </label>
                                <div class="relative group">
                                    <select id="card_priority" name="priority"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md">
                                        <option value="">Select priority...</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="estimated_hours" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Est. Hours</span>
                                </label>
                                <div class="relative group">
                                    <input type="number" id="estimated_hours" name="estimated_hours" min="0" step="0.5"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md"
                                           placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="due_date" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                <span>Due Date (Optional)</span>
                            </label>
                            <div class="relative group">
                                <input type="date" id="due_date" name="due_date"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer - Fixed -->
                <div class="flex items-center justify-end space-x-4 p-6 pt-0 flex-shrink-0">
                    <button type="button" onclick="closeCreateCardModal()" 
                            class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform">
                        Cancel
                    </button>
                    <button type="submit" form="createCardForm"
                            class="px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform shadow-lg">
                        Create Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include External JavaScript -->
    <script src="{{ asset('js/card-management.js') }}"></script>
    
    <!-- Cards User Search Script -->
    <script>
        let searchTimeout;
        let editSearchTimeout;

        document.addEventListener('DOMContentLoaded', function() {
            // Create Card Form User Search
            const userSearch = document.getElementById('userSearch');
            const userSearchResults = document.getElementById('userSearchResults');
            const searchResultsList = document.getElementById('searchResultsList');
            const noResults = document.getElementById('noResults');
            const searchLoading = document.getElementById('searchLoading');
            const selectedUserId = document.getElementById('selectedUserId');
            const selectedUserDisplay = document.getElementById('selectedUserDisplay');
            const clearSelection = document.getElementById('clearSelection');

            // Edit Card Form User Search
            const editUserSearch = document.getElementById('editUserSearch');
            const editUserSearchResults = document.getElementById('editUserSearchResults');
            const editSearchResultsList = document.getElementById('editSearchResultsList');
            const editNoResults = document.getElementById('editNoResults');
            const editSearchLoading = document.getElementById('editSearchLoading');
            const editSelectedUserId = document.getElementById('editSelectedUserId');
            const editSelectedUserDisplay = document.getElementById('editSelectedUserDisplay');
            const editClearSelection = document.getElementById('editClearSelection');

            // Show/hide loading for create form
            function showLoading() {
                searchLoading.classList.remove('hidden');
                userSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'none';
            }

            function hideLoading() {
                searchLoading.classList.add('hidden');
                userSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'block';
            }

            // Show/hide loading for edit form
            function showEditLoading() {
                editSearchLoading.classList.remove('hidden');
                editUserSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'none';
            }

            function hideEditLoading() {
                editSearchLoading.classList.add('hidden');
                editUserSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'block';
            }

            // User search for create form
            userSearch.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                clearTimeout(searchTimeout);
                
                if (searchTerm.length === 0) {
                    userSearchResults.classList.add('hidden');
                    hideLoading();
                    return;
                }

                if (searchTerm.length < 2) {
                    userSearchResults.classList.add('hidden');
                    hideLoading();
                    return;
                }

                showLoading();
                searchTimeout = setTimeout(() => {
                    searchUsers(searchTerm, false);
                }, 150);
            });

            // User search for edit form
            editUserSearch.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                clearTimeout(editSearchTimeout);
                
                if (searchTerm.length === 0) {
                    editUserSearchResults.classList.add('hidden');
                    hideEditLoading();
                    return;
                }

                if (searchTerm.length < 2) {
                    editUserSearchResults.classList.add('hidden');
                    hideEditLoading();
                    return;
                }

                showEditLoading();
                editSearchTimeout = setTimeout(() => {
                    searchUsers(searchTerm, true);
                }, 150);
            });

            // Search users function
            function searchUsers(searchTerm, isEdit) {
                const url = new URL('/project-members', window.location.origin);
                url.searchParams.append('search', searchTerm);

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (isEdit) {
                        hideEditLoading();
                        displaySearchResults(data.data.members, true);
                    } else {
                        hideLoading();
                        displaySearchResults(data.data.members, false);
                    }
                })
                .catch(error => {
                    console.error('Error searching users:', error);
                    if (isEdit) {
                        hideEditLoading();
                    } else {
                        hideLoading();
                    }
                });
            }

            // Display search results
            function displaySearchResults(members, isEdit) {
                const resultsList = isEdit ? editSearchResultsList : searchResultsList;
                const noResultsDiv = isEdit ? editNoResults : noResults;
                const resultsContainer = isEdit ? editUserSearchResults : userSearchResults;
                
                resultsList.innerHTML = '';
                noResultsDiv.classList.add('hidden');
                
                if (members.length === 0) {
                    noResultsDiv.classList.remove('hidden');
                } else {
                    resultsList.innerHTML = members.map(member => `
                        <div class="user-result p-3 hover:bg-gray-50 rounded-xl cursor-pointer transition-colors flex items-center space-x-3"
                             data-user-id="${member.user.id}"
                             data-user-name="${member.user.name}"
                             data-user-email="${member.user.email}"
                             data-user-avatar="${member.user.avatar || ''}"
                             data-is-edit="${isEdit}">
                            <div class="relative flex-shrink-0">
                                ${member.user.avatar 
                                    ? `<img src="${member.user.avatar}" alt="${member.user.name}" class="w-10 h-10 rounded-full object-cover">`
                                    : `<div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                         <span class="text-white font-semibold text-sm">${member.user.name.charAt(0).toUpperCase()}</span>
                                       </div>`
                                }
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">${member.user.name}</p>
                                <p class="text-sm text-gray-600">${member.user.email}</p>
                                <p class="text-xs text-blue-600 font-medium">${member.role}</p>
                            </div>
                        </div>
                    `).join('');
                }
                
                resultsContainer.classList.remove('hidden');
                setTimeout(() => {
                    resultsContainer.classList.remove('scale-95', 'opacity-0');
                    resultsContainer.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Add click events to results
                document.querySelectorAll('.user-result').forEach(result => {
                    result.addEventListener('click', function() {
                        selectUser(this);
                    });
                });
            }

            // Select user function
            function selectUser(element) {
                const userId = element.dataset.userId;
                const userName = element.dataset.userName;
                const userEmail = element.dataset.userEmail;
                const userAvatar = element.dataset.userAvatar;
                const isEdit = element.dataset.isEdit === 'true';

                if (isEdit) {
                    // Edit form
                    editSelectedUserId.value = userId;
                    document.getElementById('editSelectedUserName').textContent = userName;
                    document.getElementById('editSelectedUserEmail').textContent = userEmail;
                    document.getElementById('editSelectedUserInitial').textContent = userName.charAt(0).toUpperCase();
                    
                    if (userAvatar) {
                        document.getElementById('editSelectedUserAvatar').src = userAvatar;
                        document.getElementById('editSelectedUserAvatar').classList.remove('hidden');
                        document.getElementById('editSelectedUserInitialContainer').style.display = 'none';
                    } else {
                        document.getElementById('editSelectedUserAvatar').classList.add('hidden');
                        document.getElementById('editSelectedUserInitialContainer').style.display = 'flex';
                    }
                    
                    editSelectedUserDisplay.classList.remove('hidden');
                    editUserSearchResults.classList.add('hidden');
                    editUserSearch.value = userName;
                } else {
                    // Create form
                    selectedUserId.value = userId;
                    document.getElementById('selectedUserName').textContent = userName;
                    document.getElementById('selectedUserEmail').textContent = userEmail;
                    document.getElementById('selectedUserInitial').textContent = userName.charAt(0).toUpperCase();
                    
                    if (userAvatar) {
                        document.getElementById('selectedUserAvatar').src = userAvatar;
                        document.getElementById('selectedUserAvatar').classList.remove('hidden');
                        document.getElementById('selectedUserInitialContainer').style.display = 'none';
                    } else {
                        document.getElementById('selectedUserAvatar').classList.add('hidden');
                        document.getElementById('selectedUserInitialContainer').style.display = 'flex';
                    }
                    
                    selectedUserDisplay.classList.remove('hidden');
                    userSearchResults.classList.add('hidden');
                    userSearch.value = userName;
                }
            }

            // Clear selection - Create form
            if (clearSelection) {
                clearSelection.addEventListener('click', function() {
                    selectedUserId.value = '';
                    selectedUserDisplay.classList.add('hidden');
                    userSearch.value = '';
                    document.getElementById('selectedUserAvatar').classList.add('hidden');
                    document.getElementById('selectedUserInitialContainer').style.display = 'flex';
                    userSearch.focus();
                });
            }

            // Clear selection - Edit form
            if (editClearSelection) {
                editClearSelection.addEventListener('click', function() {
                    editSelectedUserId.value = '';
                    editSelectedUserDisplay.classList.add('hidden');
                    editUserSearch.value = '';
                    document.getElementById('editSelectedUserAvatar').classList.add('hidden');
                    document.getElementById('editSelectedUserInitialContainer').style.display = 'flex';
                    editUserSearch.focus();
                });
            }

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!userSearch?.contains(e.target) && !userSearchResults?.contains(e.target)) {
                    userSearchResults?.classList.add('hidden');
                }
                if (!editUserSearch?.contains(e.target) && !editUserSearchResults?.contains(e.target)) {
                    editUserSearchResults?.classList.add('hidden');
                }
            });

            // Escape key to close search results
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    userSearchResults?.classList.add('hidden');
                    editUserSearchResults?.classList.add('hidden');
                }
            });
        });
    </script>

    <!-- Edit Card Modal -->
    <div id="editCardModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-screen w-full p-4">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Modal Header -->
                <div class="relative bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 rounded-t-3xl p-6 flex-shrink-0">
                    <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Edit Task</h3>
                                <p class="text-white/80 text-sm">Update task information</p>
                            </div>
                        </div>
                        <button onclick="closeEditCardModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <svg class="w-5 h-5 text-white group-hover:text-white/80 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body - Scrollable -->
                <div class="flex-1 overflow-y-auto p-8">
                    <form id="editCardForm">
                        <input type="hidden" id="edit_card_id" name="card_id">
                        
                        <div class="mb-6">
                            <label for="edit_card_title" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span>Task Title</span>
                            </label>
                            <input type="text" id="edit_card_title" name="card_title" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                        </div>

                        <div class="mb-6">
                            <label for="edit_assigned_user" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Assign to</span>
                            </label>
                            
                            <!-- Info Note -->
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                <p class="text-xs text-blue-600">Search for team members by name or email to reassign this task</p>
                            </div>
                            
                            <div class="relative group">
                                <input type="text" id="editUserSearch" placeholder="Search team members..."
                                       class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                                
                                <!-- Search Icon -->
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Loading Spinner -->
                                <div id="editSearchLoading" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                    <div class="w-5 h-5 border-2 border-blue-300 border-t-blue-600 rounded-full animate-spin"></div>
                                </div>
                            </div>
                            
                            <!-- Search Results Dropdown -->
                            <div id="editUserSearchResults" class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl hidden transform scale-95 opacity-0 transition-all duration-200 max-h-64 overflow-y-auto">
                                <div id="editSearchResultsList" class="p-2">
                                    <!-- Search results will be populated here -->
                                </div>
                                <div id="editNoResults" class="p-4 text-center text-gray-500 hidden">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm">No team members found</p>
                                    <p class="text-xs text-gray-400">Only project team members can be assigned</p>
                                </div>
                            </div>
                            
                            <!-- Selected User Display -->
                            <div id="editSelectedUserDisplay" class="mt-4 hidden">
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-2xl">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <img id="editSelectedUserAvatar" src="" alt="" class="w-10 h-10 rounded-full object-cover hidden">
                                            <div id="editSelectedUserInitialContainer" class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                                <span id="editSelectedUserInitial" class="text-white font-semibold text-sm"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <p id="editSelectedUserName" class="font-semibold text-gray-800"></p>
                                            <p id="editSelectedUserEmail" class="text-sm text-gray-600"></p>
                                        </div>
                                    </div>
                                    <button type="button" id="editClearSelection" class="p-2 hover:bg-white/50 rounded-xl transition-colors">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" id="editSelectedUserId" name="user_id" value="">
                        </div>

                        <div class="mb-6">
                            <label for="edit_card_description" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                <span>Description</span>
                            </label>
                            <textarea id="edit_card_description" name="description" rows="3"
                                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="edit_card_priority" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <span>Priority</span>
                                </label>
                                <select id="edit_card_priority" name="priority"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none">
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_estimated_hours" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Est. Hours</span>
                                </label>
                                <input type="number" id="edit_estimated_hours" name="estimated_hours" step="0.5" min="0"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="edit_card_status" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Status</span>
                                </label>
                                <select id="edit_card_status" name="status"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none">
                                    <option value="todo">To Do</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="review">Review</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_due_date" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Due Date</span>
                                </label>
                                <input type="date" id="edit_due_date" name="due_date"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 p-6 pt-0 flex-shrink-0">
                    <button type="button" onclick="closeEditCardModal()" 
                            class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform">
                        Cancel
                    </button>
                    <button type="submit" form="editCardForm"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform shadow-lg">
                        Update Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Modal -->
    <div id="commentsModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50" style="display: none;">
        <div class="flex items-center justify-center min-h-screen w-full p-4">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-2xl border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Modal Header -->
                <div class="relative bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 rounded-t-3xl p-6 flex-shrink-0">
                    <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Task Comments</h3>
                                <p class="text-white/80 text-sm" id="commentsTaskTitle">Loading...</p>
                            </div>
                        </div>
                        <button onclick="closeCommentsModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <svg class="w-5 h-5 text-white group-hover:text-white/80 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Comments List - Scrollable -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div id="commentsList" class="space-y-4">
                        <!-- Comments will be loaded here -->
                    </div>
                </div>

                <!-- Add Comment Form - Fixed -->
                <div class="border-t border-gray-100 p-6 flex-shrink-0">
                    <form id="addCommentForm" class="flex space-x-4">
                        <input type="hidden" id="comment_card_id" name="card_id">
                        <div class="flex-1">
                            <textarea id="comment_text" name="comment_text" rows="3" required
                                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200 resize-none"
                                      placeholder="Write a comment..."></textarea>
                        </div>
                        <div class="flex flex-col justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
