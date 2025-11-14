<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assigned Cards for Review - Project Management</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-sans antialiased lg:overflow-hidden">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 lg:flex">
        <!-- Sidebar -->
        <x-sidebar />
        
        <!-- Main content -->
        <div class="flex-1 lg:ml-64">
            <!-- Header Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 mb-8 overflow-hidden mx-4 mt-4 lg:mx-8 lg:mt-8">
                <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-6 py-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Cards for Review</h1>
                            <p class="text-purple-100">Review and approve cards submitted by team members</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($project)
                                <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                                    <span class="text-white font-semibold text-sm lg:text-base">Project: {{ $project->project_name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if(!$project)
                    <!-- No Project State -->
                    <div class="text-center py-16">
                        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Project Assigned</h3>
                        <p class="text-gray-600 mb-6">You are not assigned to any project yet.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            Go to Dashboard
                        </a>
                    </div>
                @else
                    @if($reviewCards->count() > 0)
                        <!-- Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($reviewCards as $card)
                                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 hover:shadow-xl transition-all duration-200 overflow-hidden">
                                    <!-- Card Header -->
                                    <div class="p-6 border-b border-gray-100">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $card->title }}</h3>
                                                <p class="text-sm text-gray-600 line-clamp-2">{{ $card->description }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Status Badge -->
                                        <div class="flex items-center justify-between">
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 rounded-full">
                                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                                                Under Review
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $card->updated_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Details -->
                                    <div class="p-6 space-y-4">
                                        <!-- Board Info -->
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            Board: {{ $card->board->board_name }}
                                        </div>

                                        <!-- Assigned User -->
                                        @if($card->assignedUser)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <x-user-avatar :user="$card->assignedUser" size="sm" />
                                                <span class="ml-2">{{ $card->assignedUser->name }}</span>
                                                <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full capitalize">
                                                    {{ $card->assignedUser->role }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Priority -->
                                        @if($card->priority)
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-2 {{ $card->priority === 'high' ? 'text-red-500' : ($card->priority === 'medium' ? 'text-yellow-500' : 'text-green-500') }}" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                                </svg>
                                                Priority: <span class="capitalize font-medium {{ $card->priority === 'high' ? 'text-red-600' : ($card->priority === 'medium' ? 'text-yellow-600' : 'text-green-600') }}">{{ $card->priority }}</span>
                                            </div>
                                        @endif

                                        <!-- Subtasks -->
                                        @if($card->subtasks->count() > 0)
                                            <div class="text-sm text-gray-600">
                                                <div class="flex items-center mb-2">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                    </svg>
                                                    Subtasks ({{ $card->subtasks->where('is_completed', true)->count() }}/{{ $card->subtasks->count() }})
                                                </div>
                                                <div class="pl-6 space-y-1">
                                                    @foreach($card->subtasks->take(3) as $subtask)
                                                        <div class="flex items-center text-xs">
                                                            <span class="w-2 h-2 {{ $subtask->is_completed ? 'bg-green-400' : 'bg-gray-300' }} rounded-full mr-2"></span>
                                                            <span class="{{ $subtask->is_completed ? 'line-through text-gray-500' : '' }}">{{ $subtask->title }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if($card->subtasks->count() > 3)
                                                        <div class="text-xs text-gray-500 pl-4">
                                                            +{{ $card->subtasks->count() - 3 }} more...
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                                        <div class="grid grid-cols-2 gap-3">
                                            <!-- Approve Button (Centang) -->
                                            <button onclick="approveCard({{ $card->id }})" class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl group">
                                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approve
                                            </button>

                                            <!-- Reject Button (Silang) -->
                                            <button onclick="rejectCard({{ $card->id }})" class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white font-medium rounded-xl hover:from-red-600 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl group">
                                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- No Cards State -->
                        <div class="text-center py-16">
                            <div class="w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clipboard-list text-2xl lg:text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Cards for Review</h3>
                            <p class="text-gray-600 mb-6">All cards are either in progress or already completed.</p>
                            <a href="{{ route('leader.cards') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-tasks mr-2"></i>
                                Manage Cards
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl p-8 max-w-sm mx-4 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600 font-medium" id="loadingText">Processing...</p>
            </div>
        </div>
    </div>


    <script>
    function showLoading(text = 'Processing...') {
        document.getElementById('loadingText').textContent = text;
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    function showToast(type, message) {
        const toast = document.getElementById(type + 'Toast');
        const messageElement = document.getElementById(type + 'Message');
        messageElement.textContent = message;
        
        toast.classList.remove('translate-x-full');
        
        setTimeout(() => {
            toast.classList.add('translate-x-full');
        }, 3000);
    }

    function approveCard(cardId) {
        Swal.fire({
            title: 'Approve Card?',
            text: 'Are you sure you want to approve this card? This will mark it as completed.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading('Approving card...');

                fetch(`/cards/${cardId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to approve card',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while approving the card',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }

    function rejectCard(cardId) {
        Swal.fire({
            title: 'Reject Card?',
            text: 'Are you sure you want to reject this card? This will send it back to in progress.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading('Rejecting card...');

                fetch(`/cards/${cardId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to reject card',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while rejecting the card',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }
    </script>
</body>
</html>
