<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $project->project_name }} - Project Details</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
                    <a href="{{ route('projects') }}" class="hover:text-indigo-600 transition-colors">Projects</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">{{ $project->project_name }}</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->role === 'admin' || $project->user_id === auth()->user()->id)
                        <a href="{{ route('projects.edit', $project->slug) }}" class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-4 py-2 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit Project</span>
                        </a>
                        
                        <form action="{{ route('projects.delete', $project->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 flex items-center space-x-2 shadow-lg delete-btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Delete</span>
                            </button>
                        </form>
                    @endif
                    
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Project Detail Content -->
        <main class="p-6">
            <!-- SweetAlert Success/Error Messages -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end',
                            background: '#f0fdf4',
                            color: '#166534',
                            iconColor: '#22c55e'
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end',
                            background: '#fef2f2',
                            color: '#dc2626',
                            iconColor: '#ef4444'
                        });
                    });
                </script>
            @endif

            <!-- Project Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-start space-x-6">
                        <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-3xl flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-2xl">{{ strtoupper(substr($project->project_name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                                {{ $project->project_name }}
                            </h1>
                            <p class="text-gray-600 text-lg leading-relaxed mb-4">
                                {{ $project->description ?: 'No description available for this project.' }}
                            </p>
                            <div class="flex items-center space-x-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-gray-600">Created by <span class="font-medium text-gray-800">{{ $project->user->name }}</span></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h.01M3 7h18v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
                                    </svg>
                                    <span class="text-gray-600">Created on {{ $project->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <!-- Project Status Display -->
                        <div class="flex flex-col items-end space-y-2">
                            @php
                                $statusConfig = [
                                    'active' => ['bg-green-100', 'text-green-800', 'Active'],
                                    'completed' => ['bg-blue-100', 'text-blue-800', 'Completed'],
                                    'cancelled' => ['bg-red-100', 'text-red-800', 'Cancelled'],
                                    'expired' => ['bg-red-100', 'text-red-800', 'Expired'],
                                    'on_hold' => ['bg-yellow-100', 'text-yellow-800', 'On Hold']
                                ];
                                $currentStatus = $statusConfig[$project->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($project->status)];
                            @endphp
                            
                            <span class="px-4 py-2 text-sm font-medium {{ $currentStatus[0] }} {{ $currentStatus[1] }} rounded-full">
                                {{ $currentStatus[2] }}
                            </span>
                            
                            <!-- Deadline Status -->
                            @if($project->deadline)
                                @if($project->deadline < now() && $project->status === 'active')
                                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Overdue</span>
                                @elseif($project->deadline <= now()->addDays(7) && $project->status === 'active')
                                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Due Soon</span>
                                @endif
                            @endif
                            
                            <!-- Status Management Buttons -->
                            @if(Auth::user()->role === 'admin' || $project->user_id === Auth::id())
                                <div class="flex space-x-2 mt-2">
                                    @if($project->status === 'active')
                                        <button onclick="updateProjectStatus('completed')" class="px-3 py-1 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                            Complete
                                        </button>
                                        <button onclick="updateProjectStatus('cancelled')" class="px-3 py-1 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            Cancel
                                        </button>
                                    @elseif(in_array($project->status, ['expired', 'cancelled', 'on_hold']))
                                        <button onclick="updateProjectStatus('active')" class="px-3 py-1 text-xs bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                            Reactivate
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Project Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-2xl border border-blue-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-blue-800 font-semibold">Project Info</h3>
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if($project->deadline)
                                <p class="text-sm text-blue-700">
                                    <span class="font-medium">Deadline:</span> {{ $project->deadline->format('M d, Y') }}
                                </p>
                            @else
                                <p class="text-sm text-blue-700">
                                    <span class="font-medium">Deadline:</span> Not set
                                </p>
                            @endif
                            <p class="text-sm text-blue-700">
                                <span class="font-medium">Status:</span> 
                                @if($project->deadline && $project->deadline < now())
                                    Overdue
                                @elseif($project->deadline && $project->deadline <= now()->addDays(7))
                                    Due Soon
                                @else
                                    Active
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-6 rounded-2xl border border-emerald-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-emerald-800 font-semibold">Team</h3>
                            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-emerald-700">
                                <span class="font-medium">Members:</span> {{ $project->members->count() }}
                            </p>
                            <p class="text-sm text-emerald-700">
                                <span class="font-medium">Leaders:</span> {{ $project->members->where('role', 'Project Manager')->count() }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-6 rounded-2xl border border-amber-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-amber-800 font-semibold">Boards</h3>
                            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-amber-700">
                                <span class="font-medium">Total Boards:</span> {{ $project->boards->count() }}
                            </p>
                            <p class="text-sm text-amber-700">
                                <span class="font-medium">Total Cards:</span> {{ $project->boards->sum(function($board) { return $board->cards->count(); }) }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-100 p-6 rounded-2xl border border-purple-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-purple-800 font-semibold">Card Status</h3>
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            @php
                                $allCards = collect();
                                foreach($project->boards as $board) {
                                    $allCards = $allCards->merge($board->cards);
                                }
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600">To Do:</span>
                                <span class="text-xs font-medium text-gray-800">{{ $allCards->where('status', 'todo')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-blue-600">In Progress:</span>
                                <span class="text-xs font-medium text-blue-800">{{ $allCards->where('status', 'in_progress')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-purple-600">Review:</span>
                                <span class="text-xs font-medium text-purple-800">{{ $allCards->where('status', 'review')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-green-600">Done:</span>
                                <span class="text-xs font-medium text-green-800">{{ $allCards->where('status', 'done')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members Section -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Team Members</h2>
                    @if(auth()->user()->role === 'admin' || $project->user_id === auth()->user()->id)
                        <button id="addMemberBtn" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Member</span>
                        </button>
                    @endif
                </div>

                @if($project->members->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($project->members as $member)
                            <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl border border-gray-200 hover:shadow-lg transition-all duration-300">
                                <div class="flex items-center space-x-4">
                                    <x-user-avatar :user="$member->user" size="lg" />
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-800 truncate">{{ $member->user->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $member->user->email }}</p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full capitalize">
                                                {{ $member->role }}
                                            </span>
                                            @if($member->user->role)
                                                <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-full capitalize">
                                                    {{ $member->user->role }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($member->joined_at)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Joined: {{ \Carbon\Carbon::parse($member->joined_at)->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    @if(auth()->user()->role === 'admin' || $project->user_id === auth()->user()->id)
                                        <div class="relative">
                                            <button class="p-1 rounded-lg hover:bg-gray-100 transition-colors member-options-btn" data-member-id="{{ $member->user->id }}">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                </svg>
                                            </button>
                                            <div class="member-dropdown absolute right-0 top-8 bg-white rounded-lg shadow-lg border border-gray-200 py-2 w-32 z-10 hidden">
                                                <button class="flex items-center block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 remove-member-btn" data-member-id="{{ $member->user->id }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Team Members</h3>
                        <p class="text-sm text-gray-500 mb-4">This project doesn't have any team members yet.</p>
                        @if(auth()->user()->role === 'admin' || $project->user_id === auth()->user()->id)
                            <button id="addFirstMemberBtn" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium">
                                Add First Member
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Boards Section -->
            @if($project->boards->count() > 0)
                <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Project Boards</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($project->boards as $board)
                            <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl border border-gray-200 hover:shadow-lg transition-all duration-300">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="font-semibold text-gray-800">{{ $board->board_name }}</h3>
                                    <div class="relative">
                                        <button class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">{{ $board->description ?: 'No description available.' }}</p>
                                
                                <!-- Total Cards -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                                        <span class="text-sm font-medium text-indigo-800">Total Cards</span>
                                        <span class="text-lg font-bold text-indigo-900">{{ $board->cards->count() }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm border-t border-gray-200 pt-3">
                                    <span class="text-gray-500">Created: {{ $board->created_at->format('M d, Y') }}</span>
                                    @if($board->cards->count() > 0)
                                        <div class="flex items-center space-x-1">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-xs text-gray-500">Active</span>
                                        </div>
                                    @else
                                        <div class="flex items-center space-x-1">
                                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            <span class="text-xs text-gray-500">Empty</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden" style="display: none;">
        <div class="flex items-center justify-center min-h-screen w-full p-4 overflow-y-auto">
            <!-- Modal Container with Animation -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content my-8 mx-auto relative">
                <!-- Modal Header with Gradient -->
                <div class="relative bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-t-3xl p-6">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Icon -->
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <!-- Title -->
                            <div>
                                <h3 class="text-xl font-bold text-white">Add Team Member</h3>
                                <p class="text-white/80 text-sm">Invite someone to join this project</p>
                            </div>
                        </div>
                        <!-- Close Button -->
                        <button id="closeModalBtn" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <svg class="w-5 h-5 text-white group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="p-8">

                <form id="addMemberForm">
                    <!-- Search User Section -->
                    <div class="mb-8">
                        <label for="userSearch" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Search User</span>
                        </label>
                        
                        <!-- Info Note -->
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-700">
                                    <span class="font-medium">Note:</span> Only users with "User" role can be added as team members. Admin and Leader roles cannot be added to projects. Users already working on other projects will appear as "working" and cannot be recruited.
                                </p>
                            </div>
                        </div>
                        
                        <div class="relative group">
                            <input type="text" 
                                   id="userSearch" 
                                   class="w-full px-5 py-4 pr-12 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300 placeholder-gray-400"
                                   placeholder="Search users (User role only)..."
                                   autocomplete="off">
                            
                            <!-- Search icon with animation -->
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <!-- Loading indicator with pulse animation -->
                            <div id="searchLoading" class="absolute inset-y-0 right-0 pr-4 items-center pointer-events-none hidden">
                                <div class="animate-spin rounded-full h-5 w-5 border-2 border-indigo-500 border-t-transparent"></div>
                            </div>
                            
                            <!-- Enhanced Search results dropdown -->
                            <div id="userSearchResults" class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-lg border-2 border-gray-200 rounded-2xl shadow-2xl mt-2 max-h-72 overflow-y-auto z-30 hidden transform transition-all duration-200 scale-95 opacity-0">
                                <!-- No results message with better styling -->
                                <div id="noResults" class="p-6 text-center text-gray-500 text-sm hidden">
                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-gray-700 mb-1">No users found</p>
                                    <p class="text-xs text-gray-400">Try searching with different keywords</p>
                                </div>
                                
                                <!-- Search results will be populated here -->
                                <div id="searchResultsList"></div>
                            </div>
                        </div>
                        
                        <!-- Enhanced Selected user display -->
                        <div id="selectedUserDisplay" class="mt-5 hidden">
                            <div class="relative overflow-hidden">
                                <div class="flex items-center p-5 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-2 border-indigo-200 rounded-2xl group hover:shadow-lg transition-all duration-300">
                                    <!-- Animated background -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    
                                    <div class="relative w-12 h-12 rounded-2xl flex items-center justify-center mr-4 shadow-lg overflow-hidden" id="selectedUserAvatarContainer">
                                        <!-- Avatar image (hidden by default) -->
                                        <img id="selectedUserAvatar" class="w-full h-full object-cover rounded-2xl hidden" alt="User Avatar" onerror="this.style.display='none'; document.getElementById('selectedUserInitial').parentElement.style.display='flex';">
                                        <!-- Fallback initials -->
                                        <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center" id="selectedUserInitialContainer">
                                            <span id="selectedUserInitial" class="text-white font-bold text-lg"></span>
                                        </div>
                                    </div>
                                    <div class="relative flex-1">
                                        <div id="selectedUserName" class="font-semibold text-gray-900 text-lg"></div>
                                        <div id="selectedUserEmail" class="text-sm text-gray-600 mb-1"></div>
                                        <div class="flex items-center space-x-2">
                                            <span id="selectedUserRole" class="px-3 py-1 text-xs font-bold bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-full uppercase tracking-wide"></span>
                                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                        </div>
                                    </div>
                                    <button type="button" id="clearSelection" class="relative ml-3 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Selection with enhanced styling -->
                    <div class="mb-8">
                        <label for="memberRole" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Role Assignment</span>
                        </label>
                        <div class="relative">
                            <select id="memberRole" class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300 appearance-none cursor-pointer">
                                <option value="Developer">👨‍💻 Developer</option>
                                <option value="Designer">🎨 Designer</option>
                            </select>
                            <!-- Custom dropdown arrow -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="selectedUserId" value="">

                    <!-- Enhanced Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-100">
                        <button type="button" id="cancelAddBtn" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl transition-all duration-200 font-semibold hover:scale-105 transform">
                            Cancel
                        </button>
                        <button type="submit" id="confirmAddBtn" class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-semibold disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none hover:scale-105 transform shadow-lg hover:shadow-xl flex items-center space-x-2" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add Member</span>
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for member management -->
    <script>
        let searchTimeout;
        const projectSlug = '{{ $project->slug }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Modal controls
            const modal = document.getElementById('addMemberModal');
            const addMemberBtn = document.getElementById('addMemberBtn');
            const addFirstMemberBtn = document.getElementById('addFirstMemberBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelAddBtn = document.getElementById('cancelAddBtn');
            const userSearch = document.getElementById('userSearch');
            const userSearchResults = document.getElementById('userSearchResults');
            const searchResultsList = document.getElementById('searchResultsList');
            const noResults = document.getElementById('noResults');
            const searchLoading = document.getElementById('searchLoading');
            const addMemberForm = document.getElementById('addMemberForm');
            const confirmAddBtn = document.getElementById('confirmAddBtn');
            const selectedUserId = document.getElementById('selectedUserId');
            const selectedUserDisplay = document.getElementById('selectedUserDisplay');
            const clearSelection = document.getElementById('clearSelection');

            // Open modal with animation
            function openModal() {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Ensure proper centering
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                
                // Animate modal entrance
                const modalContent = modal.querySelector('.modal-content');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                setTimeout(() => userSearch.focus(), 200);
            }

            // Close modal with animation
            function closeModal() {
                const modalContent = modal.querySelector('.modal-content');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    resetForm();
                }, 200);
            }

            // Reset form
            function resetForm() {
                userSearch.value = '';
                selectedUserId.value = '';
                userSearchResults.classList.add('hidden');
                searchResultsList.innerHTML = '';
                selectedUserDisplay.classList.add('hidden');
                noResults.classList.add('hidden');
                confirmAddBtn.disabled = true;
                document.getElementById('memberRole').value = 'Developer';
                
                // Reset avatar display
                const selectedUserAvatar = document.getElementById('selectedUserAvatar');
                const selectedUserInitialContainer = document.getElementById('selectedUserInitialContainer');
                if (selectedUserAvatar) {
                    selectedUserAvatar.classList.add('hidden');
                    selectedUserAvatar.src = '';
                }
                if (selectedUserInitialContainer) {
                    selectedUserInitialContainer.style.display = 'flex';
                }
                
                hideLoading();
            }

            // Show/hide loading
            function showLoading() {
                searchLoading.classList.remove('hidden');
                searchLoading.style.display = 'flex';
                userSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'none';
            }

            function hideLoading() {
                searchLoading.classList.add('hidden');
                searchLoading.style.display = 'none';
                userSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'block';
            }

            // Event listeners for opening modal
            if (addMemberBtn) {
                addMemberBtn.addEventListener('click', openModal);
            }
            if (addFirstMemberBtn) {
                addFirstMemberBtn.addEventListener('click', openModal);
            }

            // Event listeners for closing modal
            closeModalBtn.addEventListener('click', closeModal);
            cancelAddBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });

            // Clear selection
            clearSelection.addEventListener('click', function() {
                selectedUserId.value = '';
                selectedUserDisplay.classList.add('hidden');
                confirmAddBtn.disabled = true;
                userSearch.value = '';
                
                // Reset avatar display
                const selectedUserAvatar = document.getElementById('selectedUserAvatar');
                const selectedUserInitialContainer = document.getElementById('selectedUserInitialContainer');
                if (selectedUserAvatar) {
                    selectedUserAvatar.classList.add('hidden');
                    selectedUserAvatar.src = '';
                }
                if (selectedUserInitialContainer) {
                    selectedUserInitialContainer.style.display = 'flex';
                }
                
                userSearch.focus();
            });

            // User search with real-time response (every keystroke)
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
                
                // Real-time search - instant response on every keystroke
                searchTimeout = setTimeout(() => {
                    searchUsers(searchTerm);
                }, 150); // Very short delay for real-time feel
            });

            // Search users function
            function searchUsers(searchTerm) {
                const url = new URL('{{ route("users.search") }}');
                url.searchParams.append('search', searchTerm);
                url.searchParams.append('project_id', '{{ $project->id }}');
                url.searchParams.append('role', 'user'); // Only search for users with 'user' role

                fetch(url)
                    .then(response => response.json())
                    .then(users => {
                        hideLoading();
                        displaySearchResults(users);
                    })
                    .catch(error => {
                        console.error('Error searching users:', error);
                        hideLoading();
                        searchResultsList.innerHTML = '<div class="p-3 text-sm text-red-600 text-center">Error searching users</div>';
                        userSearchResults.classList.remove('hidden');
                    });
            }

            // Display search results with enhanced styling
            function displaySearchResults(users) {
                searchResultsList.innerHTML = '';
                noResults.classList.add('hidden');
                
                // Filter users to only include 'user' role (additional frontend validation)
                const filteredUsers = users.filter(user => user.role === 'user');
                
                if (filteredUsers.length === 0) {
                    noResults.classList.remove('hidden');
                    // Update no results message for role restriction
                    noResults.innerHTML = `
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-red-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <p class="font-medium text-gray-700 mb-1">No eligible users found</p>
                        <p class="text-xs text-gray-400">Only users with "User" role can be added as team members</p>
                    `;
                } else {
                    searchResultsList.innerHTML = filteredUsers.map(user => `
                        <div class="p-4 ${user.is_working ? 'bg-gray-50 cursor-not-allowed opacity-60' : 'hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 cursor-pointer'} border-b border-gray-100 last:border-b-0 user-result transition-all duration-200 group" 
                             data-user-id="${user.id}" 
                             data-user-name="${user.name}" 
                             data-user-email="${user.email}"
                             data-user-role="${user.role}"
                             data-user-avatar="${user.avatar_url || ''}"
                             data-user-initials="${user.initials}"
                             data-user-working="${user.is_working || false}"
                             data-user-status="${user.status || 'available'}">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg ${user.is_working ? '' : 'group-hover:shadow-xl'} transition-shadow duration-200 overflow-hidden">
                                        ${user.avatar_url ? 
                                            `<img src="${user.avatar_url}" alt="${user.name}" class="w-full h-full object-cover rounded-2xl" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                             <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center" style="display: none;">
                                                 <span class="text-white text-sm font-bold">${user.initials}</span>
                                             </div>` :
                                            `<div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                                                 <span class="text-white text-sm font-bold">${user.initials}</span>
                                             </div>`
                                        }
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 ${user.is_working ? 'bg-orange-400' : 'bg-green-400'} rounded-full border-2 border-white shadow-sm"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 truncate ${user.is_working ? '' : 'group-hover:text-indigo-600'} transition-colors duration-200">${user.name}</div>
                                    <div class="text-sm text-gray-600 truncate mb-1">${user.email}</div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-bold bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-full uppercase tracking-wide">${user.role}</span>
                                        <span class="px-2 py-1 text-xs font-medium ${user.is_working ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'} rounded-full capitalize">${user.status || 'available'}</span>
                                    </div>
                                </div>
                                ${user.is_working ? 
                                    `<div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-300 rounded-xl flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            </svg>
                                        </div>
                                    </div>` :
                                    `<div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <div class="w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                    </div>`
                                }
                            </div>
                        </div>
                    `).join('');
                }
                
                // Animate dropdown appearance
                userSearchResults.classList.remove('hidden');
                setTimeout(() => {
                    userSearchResults.classList.remove('scale-95', 'opacity-0');
                    userSearchResults.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Add click events to results
                document.querySelectorAll('.user-result').forEach(result => {
                    result.addEventListener('click', function() {
                        const userId = this.dataset.userId;
                        const userName = this.dataset.userName;
                        const userEmail = this.dataset.userEmail;
                        const userRole = this.dataset.userRole;
                        const userAvatar = this.dataset.userAvatar;
                        const userInitials = this.dataset.userInitials;
                        const userWorking = this.dataset.userWorking === 'true';
                        const userStatus = this.dataset.userStatus;

                        // Check if user is already working on another project
                        if (userWorking) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'User Already Working!',
                                text: 'This user is already assigned to another project and cannot be added.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#f59e0b'
                            });
                            return;
                        }

                        // Additional validation - only allow 'user' role
                        if (userRole !== 'user') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Role Not Allowed!',
                                text: 'Only users with the "User" role can be added as project members.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#f59e0b'
                            });
                            return;
                        }

                        // Set selected user
                        selectedUserId.value = userId;
                        
                        // Show selected user display with avatar
                        const selectedUserAvatar = document.getElementById('selectedUserAvatar');
                        const selectedUserInitialContainer = document.getElementById('selectedUserInitialContainer');
                        const selectedUserInitial = document.getElementById('selectedUserInitial');
                        
                        if (userAvatar && userAvatar !== 'null' && userAvatar !== '') {
                            selectedUserAvatar.src = userAvatar;
                            selectedUserAvatar.classList.remove('hidden');
                            selectedUserInitialContainer.style.display = 'none';
                        } else {
                            selectedUserAvatar.classList.add('hidden');
                            selectedUserInitialContainer.style.display = 'flex';
                            selectedUserInitial.textContent = userInitials || userName.charAt(0).toUpperCase();
                        }
                        
                        document.getElementById('selectedUserName').textContent = userName;
                        document.getElementById('selectedUserEmail').textContent = userEmail;
                        document.getElementById('selectedUserRole').textContent = userRole;
                        
                        selectedUserDisplay.classList.remove('hidden');
                        userSearchResults.classList.add('hidden');
                        userSearch.value = '';
                        confirmAddBtn.disabled = false;
                    });
                });
            }

            // Handle form submission
            addMemberForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const userId = selectedUserId.value;
                const role = document.getElementById('memberRole').value;

                if (!userId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Silakan pilih user terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                // Disable button during request
                confirmAddBtn.disabled = true;
                confirmAddBtn.textContent = 'Adding...';

                fetch(`/projects/${projectSlug}/members`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        role: role
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Member added successfully',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload(); // Reload to show new member
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.error || 'Error adding member',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menambahkan member',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc2626'
                    });
                })
                .finally(() => {
                    confirmAddBtn.disabled = false;
                    confirmAddBtn.textContent = 'Add Member';
                });
            });

            // Handle member removal
            document.querySelectorAll('.member-options-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const memberId = this.dataset.memberId;
                    const dropdown = this.parentElement.querySelector('.member-dropdown');
                    
                    // Close all other dropdowns
                    document.querySelectorAll('.member-dropdown').forEach(dd => {
                        if (dd !== dropdown) dd.classList.add('hidden');
                    });
                    
                    dropdown.classList.toggle('hidden');
                });
            });

            // Handle remove member
            document.querySelectorAll('.remove-member-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const memberId = this.dataset.memberId;
                    
                    Swal.fire({
                        title: 'Hapus Member?',
                        text: "Member ini akan dihapus dari project",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/projects/${projectSlug}/members`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    user_id: memberId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Member removed successfully',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    }).then(() => {
                                        location.reload(); // Reload to remove member from view
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: data.error || 'Error removing member',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#dc2626'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghapus member',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc2626'
                                });
                            });
                        }
                    });
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                document.querySelectorAll('.member-dropdown').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!userSearch.contains(e.target) && !userSearchResults.contains(e.target)) {
                    userSearchResults.classList.add('hidden');
                }
            });

            // Escape key to close modal and search results
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    userSearchResults.classList.add('hidden');
                    if (!modal.classList.contains('hidden')) {
                        closeModal();
                    }
                }
            });
        });

        // Handle delete confirmation for project
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This project will be permanently deleted along with all associated data!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Delete!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    background: '#fff',
                    customClass: {
                        confirmButton: 'font-medium',
                        cancelButton: 'font-medium'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus Project...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading()
                            }
                        });
                        
                        // Submit form
                        form.submit();
                    }
                });
            });
        });

        // Project Status Management Functions
        function updateProjectStatus(status) {
            const statusMessages = {
                'completed': 'Are you sure you want to mark this project as completed? All team members will be freed.',
                'cancelled': 'Are you sure you want to cancel this project? All team members will be freed.',
                'active': 'Are you sure you want to reactivate this project? Team members will be set to working status.'
            };

            Swal.fire({
                title: 'Update Project Status',
                text: statusMessages[status] || 'Are you sure you want to update the project status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'cancelled' ? '#dc2626' : status === 'completed' ? '#2563eb' : '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: status === 'cancelled' ? 'Yes, Cancel Project' : status === 'completed' ? 'Yes, Complete Project' : 'Yes, Reactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/projects/{{ $project->id }}/status`;
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Add method override
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    form.appendChild(methodField);
                    
                    // Add status field
                    const statusField = document.createElement('input');
                    statusField.type = 'hidden';
                    statusField.name = 'status';
                    statusField.value = status;
                    form.appendChild(statusField);
                    
                    // Submit form
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

</body>
</html>
