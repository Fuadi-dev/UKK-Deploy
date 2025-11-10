<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Project - {{ $project->project_name }}</title>
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
                    <a href="{{ route('projects') }}" class="hover:text-indigo-600 transition-colors">Projects</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="{{ route('projects.show', $project->slug) }}" class="hover:text-indigo-600 transition-colors">{{ $project->project_name }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Edit</span>
                </div>
                
                <!-- User Profile -->
                <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                    <x-user-avatar :user="Auth::user()" size="sm" />
                </div>
            </div>
        </header>

        <!-- Main Edit Content -->
        <main class="p-6">
            <!-- SweetAlert Success/Error Messages -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
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

            <!-- Edit Form -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                        Edit Project
                    </h1>
                    <p class="text-gray-600">Update project information and settings</p>
                </div>

                <form action="{{ route('projects.update', $project->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Project Name -->
                    <div>
                        <label for="project_name" class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
                        <input type="text" 
                               id="project_name" 
                               name="project_name" 
                               value="{{ old('project_name', $project->project_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter project name"
                               required>
                        @error('project_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Enter project description (optional)">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <input type="date" 
                               id="deadline" 
                               name="deadline" 
                               value="{{ old('deadline', $project->deadline ? $project->deadline->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        @error('deadline')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Project Status</label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-2">Update the project status. You can also use quick actions from the project list page.</p>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('projects.show', $project->slug) }}" 
                           class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 font-medium">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium shadow-lg">
                            Update Project
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
