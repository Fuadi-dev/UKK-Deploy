<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - Admin Panel</title>
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
                    <span class="font-medium text-indigo-600">User Management</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <button id="addUserBtn" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add User</span>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Users Content -->
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

            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                            User Management
                        </h1>
                        <p class="text-gray-600 text-lg">
                            Manage all system users, roles, and permissions
                        </p>
                    </div>
                </div>

                <!-- User Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-4 rounded-2xl border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-600 text-sm font-medium">Total Users</p>
                                <p class="text-2xl font-bold text-emerald-800">{{ $users->total() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-4 rounded-2xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-sm font-medium">Admins</p>
                                <p class="text-2xl font-bold text-blue-800">{{ $users->where('role', 'admin')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-4 rounded-2xl border border-amber-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-amber-600 text-sm font-medium">Leaders</p>
                                <p class="text-2xl font-bold text-amber-800">{{ $users->where('role', 'leader')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-rose-50 to-pink-100 p-4 rounded-2xl border border-rose-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-rose-600 text-sm font-medium">Users</p>
                                <p class="text-2xl font-bold text-rose-800">{{ $users->where('role', 'user')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-rose-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6 mb-8">
                <form method="GET" action="{{ route('users') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search users..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center space-x-4">
                        <select name="role" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="all" {{ $role === 'all' ? 'selected' : '' }}>All Roles</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="leader" {{ $role === 'leader' ? 'selected' : '' }}>Leader</option>
                            <option value="user" {{ $role === 'user' ? 'selected' : '' }}>User</option>
                        </select>

                        <select name="sort_by" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Sort by Name</option>
                            <option value="email" {{ $sortBy === 'email' ? 'selected' : '' }}>Sort by Email</option>
                            <option value="role" {{ $sortBy === 'role' ? 'selected' : '' }}>Sort by Role</option>
                            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
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

            <!-- Users Table -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <x-user-avatar :user="$user" size="md" />
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full capitalize
                                            @if($user->role === 'admin') bg-blue-100 text-blue-800
                                            @elseif($user->role === 'leader') bg-amber-100 text-amber-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            @if($user->status == 'free')
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Free</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Working</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button class="edit-user-btn p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors" 
                                                    data-user="{{ json_encode($user) }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @if($user->id !== Auth::id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="delete-btn p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Users Found</h3>
                                        <p class="text-sm text-gray-500">No users match your search criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-8">
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 p-6">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden" style="display: none;">
        <div class="flex items-center justify-center min-h-screen w-full p-4 overflow-y-auto">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content my-8 mx-auto relative">
                <!-- Modal Header -->
                <div class="relative bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-t-3xl p-6">
                    <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 id="modalTitle" class="text-xl font-bold text-white">Add New User</h3>
                                <p class="text-white/80 text-sm">Create or edit user account</p>
                            </div>
                        </div>
                        <button id="closeModalBtn" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <svg class="w-5 h-5 text-white group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="p-8">
                    <form id="userForm" method="POST">
                        @csrf
                        <div id="methodField"></div>
                        
                        <!-- Name Field -->
                        <div class="mb-6">
                            <label for="userName" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Full Name</span>
                            </label>
                            <input type="text" id="userName" name="name" required
                                   class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300"
                                   placeholder="Enter full name">
                        </div>

                        <!-- Email Field -->
                        <div class="mb-6">
                            <label for="userEmail" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                <span>Email Address</span>
                            </label>
                            <input type="email" id="userEmail" name="email" required
                                   class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300"
                                   placeholder="Enter email address">
                        </div>

                        <!-- Role Field -->
                        <div class="mb-6">
                            <label for="userRole" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span>Role</span>
                            </label>
                            <select id="userRole" name="role" required
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300 appearance-none cursor-pointer">
                                <option value="user">👤 User</option>
                                <option value="leader">⭐ Leader</option>
                                <option value="admin">🔧 Admin</option>
                            </select>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-6">
                            <label for="userStatus" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Status</span>
                            </label>
                            <select id="userStatus" name="status" required
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300 appearance-none cursor-pointer">
                                <option value="free">🟢 Free</option>
                                <option value="working">🔴 Working</option>
                            </select>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-6">
                            <label for="userPassword" class="text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span>Password</span>
                                <span id="passwordOptional" class="text-xs text-gray-500 hidden">(Leave blank to keep current)</span>
                            </label>
                            <input type="password" id="userPassword" name="password"
                                   class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 bg-gray-50/50 hover:bg-white hover:border-gray-300"
                                   placeholder="Enter password (min. 8 characters)">
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                            <button type="button" id="cancelBtn" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl transition-all duration-200 font-semibold">
                                Cancel
                            </button>
                            <button type="submit" id="submitBtn" class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Save User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('userModal');
            const addUserBtn = document.getElementById('addUserBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const userForm = document.getElementById('userForm');
            const modalTitle = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitBtn');
            const passwordOptional = document.getElementById('passwordOptional');
            const userPassword = document.getElementById('userPassword');
            const methodField = document.getElementById('methodField');

            let isEditMode = false;
            let currentUserId = null;

            // Open modal for adding user
            addUserBtn.addEventListener('click', function() {
                openModal('add');
            });

            // Open modal for editing user
            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userData = JSON.parse(this.dataset.user);
                    openModal('edit', userData);
                });
            });

            // Close modal
            [closeModalBtn, cancelBtn].forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });

            function openModal(mode, userData = null) {
                isEditMode = mode === 'edit';
                
                if (isEditMode) {
                    modalTitle.textContent = 'Edit User';
                    submitBtn.querySelector('span').textContent = 'Update User';
                    passwordOptional.classList.remove('hidden');
                    userPassword.required = false;
                    methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    userForm.action = `/users/${userData.id}`;
                    currentUserId = userData.id;
                    
                    // Fill form with user data
                    document.getElementById('userName').value = userData.name;
                    document.getElementById('userEmail').value = userData.email;
                    document.getElementById('userRole').value = userData.role;
                    document.getElementById('userStatus').value = userData.status;
                } else {
                    modalTitle.textContent = 'Add New User';
                    submitBtn.querySelector('span').textContent = 'Create User';
                    passwordOptional.classList.add('hidden');
                    userPassword.required = true;
                    methodField.innerHTML = '';
                    userForm.action = '/users';
                    currentUserId = null;
                    
                    // Reset form
                    userForm.reset();
                }

                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                setTimeout(() => {
                    const modalContent = modal.querySelector('.modal-content');
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeModal() {
                const modalContent = modal.querySelector('.modal-content');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    userForm.reset();
                }, 200);
            }

            // Handle delete confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Delete User?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Delete!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting User...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading()
                                }
                            });
                            
                            form.submit();
                        }
                    });
                });
            });

            // Handle escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>
