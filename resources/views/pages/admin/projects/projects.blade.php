<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Projects - Project Management</title>
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
                    <span>Home</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Projects</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'leader')
                        <a href="{{ route('projects.create') }}" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>New Project</span>
                        </a>
                    @endif
                    
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Projects Content -->
        <main class="p-6">
            <!-- SweetAlert Success/Error Messages -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succsess!',
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

            @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let errorMessages = @json($errors->all());
                        let errorText = errorMessages.join('\n• ');
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            html: '<div style="text-align: left;">• ' + errorMessages.join('<br>• ') + '</div>',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626',
                            background: '#fef2f2',
                            color: '#dc2626'
                        });
                    });
                </script>
            @endif

            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                            @if(auth()->user()->role === 'admin')
                                All Projects
                            @elseif(auth()->user()->role === 'leader')
                                My Projects
                            @else
                                Assigned Projects
                            @endif
                        </h1>
                        <p class="text-gray-600 text-lg">
                            @if(auth()->user()->role === 'admin')
                                Manage and oversee all projects in the organization
                            @elseif(auth()->user()->role === 'leader')
                                Projects you're leading and managing
                            @else
                                Projects where you're assigned as a team member
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Project Stats -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-4 rounded-2xl border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-600 text-sm font-medium">Active</p>
                                <p class="text-2xl font-bold text-emerald-800">{{ $projects->where('status', 'active')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-50 to-yellow-100 p-4 rounded-2xl border border-amber-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-amber-600 text-sm font-medium">On Hold</p>
                                <p class="text-2xl font-bold text-amber-800">{{ $projects->where('status', 'on_hold')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-2xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-sm font-medium">Completed</p>
                                <p class="text-2xl font-bold text-blue-800">{{ $projects->where('status', 'completed')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-rose-100 p-4 rounded-2xl border border-red-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-600 text-sm font-medium">Cancelled</p>
                                <p class="text-2xl font-bold text-red-800">{{ $projects->where('status', 'cancelled')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-100 p-4 rounded-2xl border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-600 text-sm font-medium">Total</p>
                                <p class="text-2xl font-bold text-purple-800">{{ $projects->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6 mb-8">
                <form method="GET" action="{{ route('projects') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search projects..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center space-x-4">
                        <select name="sort_by" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="project_name" {{ $sortBy === 'project_name' ? 'selected' : '' }}>Sort by Name</option>
                            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                            <option value="deadline" {{ $sortBy === 'deadline' ? 'selected' : '' }}>Sort by Deadline</option>
                        </select>

                        <select name="sort_dir" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="asc" {{ $sortDir === 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ $sortDir === 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>

                        <button type="submit" class="p-3 bg-indigo-500 text-white border border-indigo-500 rounded-xl hover:bg-indigo-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    <!-- Project Card -->
                    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-6 hover:shadow-3xl hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($project->project_name, 0, 1)) }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Status Badge -->
                                @if($project->status === 'active')
                                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                @elseif($project->status === 'on_hold')
                                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">On Hold</span>
                                @elseif($project->status === 'completed')
                                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Completed</span>
                                @elseif($project->status === 'cancelled')
                                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Cancelled</span>
                                @elseif($project->status === 'expired')
                                    <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Expired</span>
                                @elseif($project->deadline && $project->deadline < now())
                                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Overdue</span>
                                @elseif($project->deadline && $project->deadline <= now()->addDays(7))
                                    <span class="px-3 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">Due Soon</span>
                                @endif
                                
                                @if(auth()->user()->role === 'admin' || $project->user_id === auth()->user()->id)
                                    <div class="relative group/menu">
                                        <button class="p-1 rounded-lg hover:bg-gray-100 transition-colors dropdown-toggle" data-project-id="{{ $project->id }}">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <div class="dropdown-menu absolute right-0 top-8 bg-white rounded-lg shadow-lg border border-gray-200 py-2 w-48 z-10 hidden">
                                            <a href="{{ route('projects.edit', $project->slug) }}" class="flex items-center space-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                <span>Edit Project</span>
                                            </a>
                                            
                                            <!-- Status Actions -->
                                            @if($project->status === 'on_hold')
                                            <form action="{{ route('projects.updateStatus', $project) }}" method="POST" class="status-action-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Activate Project</span>
                                                </button>
                                            </form>
                                            @elseif($project->status === 'active')
                                            <form action="{{ route('projects.updateStatus', $project) }}" method="POST" class="status-action-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="on_hold">
                                                <button type="submit" class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Put On Hold</span>
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <form action="{{ route('projects.delete', $project->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 delete-btn">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    <span>Delete Project</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition-colors">{{ $project->project_name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-3">{{ $project->description ?: 'No description available.' }}</p>

                        <!-- Project Info -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Person Responsible</span>
                                <span class="font-medium text-gray-800">{{ $project->user->name }}</span>
                            </div>
                            
                            @if($project->deadline)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Deadline</span>
                                    <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($project->deadline)->format('M d, Y') }}</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Created At</span>
                                <span class="font-medium text-gray-800">{{ $project->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            @if($project->members->count() > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Team</span>
                                    <div class="flex -space-x-2">
                                        @foreach($project->members->take(3) as $member)
                                            <x-user-avatar :user="$member->user" size="xs" class="border-2 border-white" />
                                        @endforeach
                                        @if($project->members->count() > 3)
                                            <div class="w-6 h-6 bg-gray-400 rounded-full border-2 border-white flex items-center justify-center">
                                                <span class="text-white text-xs font-semibold">+{{ $project->members->count() - 3 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Team</span>
                                    <span class="text-gray-400 text-xs">No members yet</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('projects.show', $project->slug) }}" class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 text-sm font-medium text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- No Projects Message -->
                    <div class="col-span-full bg-white/40 backdrop-blur-lg rounded-3xl shadow-lg border-2 border-dashed border-gray-300 p-12 text-center">
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Projects Found</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            @if(auth()->user()->role === 'admin')
                                No projects have been created yet. Start by creating your first project.
                            @elseif(auth()->user()->role === 'leader')
                                You haven't created any projects yet. Start by creating your first project.
                            @else
                                You haven't been assigned to any projects yet. Contact your project manager.
                            @endif
                        </p>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'leader')
                            <button class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium">
                                Create First Project
                            </button>
                        @endif
                    </div>
                @endforelse

                <!-- Add new project card (only for admin/leader) -->
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'leader')
                    <a href="{{ route('projects.create') }}" class="bg-white/40 backdrop-blur-lg rounded-3xl shadow-lg border-2 border-dashed border-gray-300 p-6 hover:border-indigo-400 hover:bg-white/60 transition-all duration-300 group cursor-pointer block">
                        <div class="flex flex-col items-center justify-center h-full text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center mb-4 group-hover:from-indigo-200 group-hover:to-purple-200 transition-all duration-300">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2 group-hover:text-indigo-700 transition-colors">Create New Project</h3>
                            <p class="text-sm text-gray-500 group-hover:text-gray-600 transition-colors">Start a new project and invite your team members</p>
                        </div>
                    </a>
                @endif
            </div>

            <!-- Pagination -->
            @if($projects->hasPages())
                <div class="mt-12">
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                        {{ $projects->links() }}
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- JavaScript for dropdown and delete confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle dropdown toggles
            document.querySelectorAll('.dropdown-toggle').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.parentElement.querySelector('.dropdown-menu');
                    
                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        if (menu !== dropdown) {
                            menu.classList.add('hidden');
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdown.classList.toggle('hidden');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            });

            // Handle delete confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Project ini akan dihapus secara permanen beserta semua data yang terkait!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
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
            
            // Handle status change confirmation
            document.querySelectorAll('.status-action-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const status = this.querySelector('input[name="status"]').value;
                    
                    let title, text, confirmText, icon;
                    
                    if (status === 'active') {
                        title = 'Activate Project?';
                        text = 'This project will be reactivated and ready for work.';
                        confirmText = 'Yes, Activate!';
                        icon = 'question';
                    } else if (status === 'on_hold') {
                        title = 'Put Project On Hold?';
                        text = 'This project will be temporarily paused.';
                        confirmText = 'Yes, Put On Hold!';
                        icon = 'warning';
                    }
                    
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: status === 'active' ? '#16a34a' : '#eab308',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>

    <!-- Additional styles for dropdown -->
    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>
</html>