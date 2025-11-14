<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Boards Management - Leader Dashboard</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Sortable.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    @include('components.sidebar')
    <!-- Main Content with sidebar offset -->
    <div class="lg:ml-64 min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
        <!-- Mobile Header Spacer -->
        <div class="lg:hidden h-16"></div>
        
        <div class="container mx-auto px-4 py-8 lg:px-8">
            @if($project)
                <!-- Header Section -->
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-6 py-6 lg:px-8">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">{{ $project->project_name }} - Boards</h1>
                                <p class="text-purple-100">Manage your project boards and tasks</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                                    <span class="text-white font-semibold text-sm lg:text-base">Total Boards: {{ $boards->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($boards->count() > 0)
                    <!-- Boards Container -->
                    <div class="flex space-x-6 overflow-x-auto pb-6" id="boards-container">
                        @foreach($boards as $board)
                            <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 min-w-80 max-w-80 flex-shrink-0" data-board-id="{{ $board->id }}">
                                <!-- Board Header -->
                                <div class="p-6 border-b border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-xl font-bold text-gray-800">{{ $board->board_name }}</h3>
                                    </div>
                                    @if($board->description)
                                        <p class="text-sm text-gray-600 mb-4">{{ $board->description }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">{{ $board->cards->count() }} cards</span>
                                        <a href="{{ route('leader.cards') }}" 
                                           class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-lg text-sm transition-colors flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>Manage Tasks</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Cards Container -->
                                <div class="p-6 space-y-4 max-h-96 overflow-y-auto" id="cards-container-{{ $board->id }}">
                                    @forelse($board->cards as $card)
                                        <div class="bg-gradient-to-r from-gray-50 to-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-all duration-200" 
                                             data-card-id="{{ $card->id }}">
                                            <!-- Card Header -->
                                            <div class="flex items-start justify-between mb-3">
                                                <h4 class="font-semibold text-gray-800 flex-1">{{ $card->card_title }}</h4>
                                                @php
                                                    $priorityClasses = [
                                                        'high' => 'bg-red-100 text-red-700',
                                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                                        'low' => 'bg-green-100 text-green-700'
                                                    ];
                                                    $priority = $card->priority ?? 'low';
                                                @endphp
                                                <span class="px-2 py-1 text-xs font-medium rounded-full ml-2 {{ $priorityClasses[$priority] }}">
                                                    {{ ucfirst($priority) }}
                                                </span>
                                            </div>

                                            <!-- Card Description -->
                                            @if($card->description)
                                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $card->description }}</p>
                                            @endif

                                            <!-- Assigned User -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <x-user-avatar :user="$card->assignedUser" size="sm" />
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">{{ $card->assignedUser->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $card->assignedUser->role }}</p>
                                                    </div>
                                                </div>
                                                
                                                @if($card->due_date)
                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-500">Due</p>
                                                        <p class="text-xs font-medium {{ $card->due_date->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                                            {{ $card->due_date->format('M j') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Progress Info -->
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-500">{{ $card->subtasks->count() }} subtasks</span>
                                                    <span class="text-gray-500">{{ $card->actual_hours }}h / {{ $card->estimated_hours }}h</span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8">
                                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="text-sm text-gray-500">No cards yet</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Boards State -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 bg-gradient-to-r from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">No Boards Yet</h3>
                            <p class="text-gray-600 mb-6">
                                Boards will be automatically created when projects are set up.
                            </p>
                        </div>
                    </div>
                @endif
            @else
                <!-- No Project State -->
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Project Assigned</h3>
                        <p class="text-gray-600 mb-6">
                            You need to be assigned to a project before you can manage boards.
                        </p>
                        <a href="{{ route('dashboard') }}" 
                           class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2 mx-auto w-fit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="flex items-center justify-center min-h-screen w-full p-4 overflow-y-auto">
            <!-- Modal Container with Animation -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content my-8 mx-auto relative">
                <!-- Modal Header with Gradient -->
                <div class="relative bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 rounded-t-3xl p-6">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Create New Card</h3>
                                <p class="text-white/80 text-sm">Add a new task to your board</p>
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
                
                <!-- Modal Body -->
                <div class="p-8">
                    <form id="createCardForm">
                        <input type="hidden" id="card_board_id">
                        
                        <div class="mb-6">
                            <label for="card_title" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span>Card Title</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="card_title" name="card_title" required
                                       class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md"
                                       placeholder="Enter task title...">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="assigned_user" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Assign to</span>
                            </label>
                            <div class="relative group">
                                <select id="assigned_user" name="user_id" required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md">
                                    <option value="">Select team member...</option>
                                </select>
                            </div>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                                    </svg>
                                    <span>Priority</span>
                                </label>
                                <div class="relative group">
                                    <select id="card_priority" name="priority"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md">
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
                                    <span>Hours</span>
                                </label>
                                <div class="relative group">
                                    <input type="number" id="estimated_hours" name="estimated_hours" min="0" step="0.5"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md"
                                           placeholder="0.0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="due_date" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Due Date (Optional)</span>
                            </label>
                            <div class="relative group">
                                <input type="date" id="due_date" name="due_date"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-gray-50/50 hover:bg-white group-hover:shadow-md">
                            </div>
                        </div>

                        <!-- Enhanced Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-100">
                            <button type="button" onclick="closeCreateCardModal()" 
                                    class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform shadow-lg">
                                Create Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include External JavaScript -->
    <script>
        // Simple script for board interactions - no card creation here
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Boards view loaded - use Cards Management for task creation');
        });
    </script>
</body>
</html>
