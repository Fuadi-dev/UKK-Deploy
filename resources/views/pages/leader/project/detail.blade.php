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
        
        <div class="container mx-auto px-3 lg:px-8 py-4 lg:py-8">
            <!-- Header Section -->
            <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-gray-100 mb-4 lg:mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-4 lg:px-8 py-4 lg:py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 lg:gap-4">
                        <div class="flex-1">
                            <h1 class="text-lg lg:text-3xl font-bold text-white mb-1 lg:mb-2 truncate">{{ $project->project_name }}</h1>
                            <p class="text-xs lg:text-base text-purple-100">Project details and team management</p>
                        </div>
                        <div class="flex items-center gap-2 lg:gap-4 flex-wrap">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl lg:rounded-2xl px-3 lg:px-4 py-1.5 lg:py-2">
                                <span class="text-white font-semibold text-xs lg:text-base">Status: {{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                            </div>
                            <a href="{{ route('dashboard') }}" 
                               class="bg-white text-purple-600 px-3 py-2 lg:px-6 lg:py-3 rounded-xl lg:rounded-2xl font-semibold hover:bg-gray-50 transition-all duration-200 shadow-lg text-xs lg:text-base whitespace-nowrap">
                                <i class="fas fa-arrow-left mr-1 lg:mr-2 text-xs lg:text-sm"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Project Header -->
            <div class="bg-white rounded-2xl lg:rounded-3xl shadow-lg border border-gray-100 p-4 lg:p-8 mb-4 lg:mb-8">
                <div class="flex items-start justify-between mb-4 lg:mb-6">
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-3 lg:mb-4">
                            <span class="px-3 py-1.5 lg:px-4 lg:py-2 text-xs lg:text-sm font-medium rounded-full inline-block
                                @if($project->status === 'active') bg-green-100 text-green-800
                                @elseif($project->status === 'completed') bg-blue-100 text-blue-800
                                @elseif($project->status === 'review') bg-purple-100 text-purple-800
                                @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-800
                                @elseif($project->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                            
                            @if($project->status === 'active')
                            <!-- Submit for Review Button -->
                            <form action="{{ route('leader.projects.updateStatus', $project) }}" method="POST" class="complete-project-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="review">
                                <button type="button" 
                                        class="complete-project-btn px-3 py-1.5 lg:px-4 lg:py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs lg:text-sm font-medium rounded-full hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-1.5 lg:gap-2 whitespace-nowrap">
                                    <i class="fas fa-paper-plane text-xs lg:text-sm"></i>
                                    <span class="hidden sm:inline">Submit for Review</span>
                                    <span class="sm:hidden">Submit</span>
                                </button>
                            </form>
                            @elseif($project->status === 'review')
                            <div class="flex items-center gap-2 px-3 py-1.5 lg:px-4 lg:py-2 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 text-xs lg:text-sm font-medium rounded-full">
                                <i class="fas fa-hourglass-half text-xs lg:text-sm"></i>
                                <span>Waiting for Admin Review</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Rejection Note Alert -->
                        @if($project->rejection_note && $project->status === 'active')
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-semibold text-red-800 mb-1">Project Rejected</h3>
                                    <p class="text-sm text-red-700 mb-2">{{ $project->rejection_note }}</p>
                                    <p class="text-xs text-red-600">
                                        Rejected {{ $project->rejected_at ? $project->rejected_at->diffForHumans() : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <p class="text-gray-600 text-sm lg:text-lg mb-4 lg:mb-6 line-clamp-3 lg:line-clamp-none">{{ $project->description }}</p>
                        
                        <!-- Project Creator -->
                        <div class="flex items-center gap-2 lg:gap-3 mb-3 lg:mb-4">
                            <x-user-avatar :user="$project->user" size="md" />
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-800 text-sm lg:text-base truncate">{{ $project->user->name }}</p>
                                <p class="text-xs lg:text-sm text-gray-500">Project Creator • {{ $project->user->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Timeline -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-3 lg:p-4 rounded-xl">
                        <div class="flex items-center gap-2 lg:gap-3">
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-plus text-blue-600 text-xs lg:text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-xs lg:text-sm font-medium text-gray-600 mb-0.5 lg:mb-1">Start Date</h3>
                                <p class="text-sm lg:text-lg font-bold text-indigo-600 truncate">
                                    {{ $project->created_at ? $project->created_at->format('M j, Y') : 'Not set' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-3 lg:p-4 rounded-xl">
                        <div class="flex items-center gap-2 lg:gap-3">
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-purple-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-times text-purple-600 text-xs lg:text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-xs lg:text-sm font-medium text-gray-600 mb-0.5 lg:mb-1">Deadline</h3>
                                <p class="text-sm lg:text-lg font-bold {{ $project->deadline && $project->deadline->isPast() && $project->status === 'active' ? 'text-red-600' : 'text-purple-600' }} truncate">
                                    {{ $project->deadline ? $project->deadline->format('M j, Y') : 'Not set' }}
                                </p>
                                @if($project->deadline && $project->status === 'active')
                                    <p class="text-xs {{ $project->deadline->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $project->deadline->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-3 lg:p-4 rounded-xl">
                        <div class="flex items-center gap-2 lg:gap-3">
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-green-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users text-green-600 text-xs lg:text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-xs lg:text-sm font-medium text-gray-600 mb-0.5 lg:mb-1">Team Size</h3>
                                <p class="text-sm lg:text-lg font-bold text-green-600">{{ $project->members->count() }} Members</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members Section -->
            <div class="bg-white rounded-2xl lg:rounded-3xl shadow-lg border border-gray-100 p-4 lg:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 lg:gap-0 mb-4 lg:mb-6">
                    <h2 class="text-lg lg:text-2xl font-bold text-gray-800 flex items-center gap-2 lg:gap-3">
                        <i class="fas fa-users text-indigo-600 text-base lg:text-xl"></i>
                        <span>Team Members</span>
                    </h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs lg:text-sm text-gray-600">Total:</span>
                        <span class="px-2 py-1 lg:px-3 lg:py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs lg:text-sm font-medium">
                            {{ $project->members->count() }} members
                        </span>
                    </div>
                </div>

                @if($project->members->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6">
                        @foreach($project->members as $member)
                            <div class="bg-gradient-to-r from-gray-50 to-white p-4 lg:p-6 rounded-xl lg:rounded-2xl border border-gray-100 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-center gap-2 lg:gap-4 mb-3 lg:mb-4">
                                    <x-user-avatar :user="$member->user" size="lg" />
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-800 text-sm lg:text-base truncate">{{ $member->user->name }}</h4>
                                        <p class="text-xs lg:text-sm text-gray-600 capitalize">{{ $member->user->role }}</p>
                                    </div>
                                </div>

                                <!-- Member Status -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <span class="px-2 py-1 lg:px-3 lg:py-1 text-xs font-medium rounded-full inline-block
                                        @if($member->user->status === 'free') bg-green-100 text-green-700
                                        @elseif($member->user->status === 'working') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($member->user->status) }}
                                    </span>
                                    
                                    <div class="text-xs text-gray-500 truncate">
                                        @if($member->user->email)
                                            {{ $member->user->email }}
                                        @endif
                                    </div>
                                </div>

                                <!-- Member Details -->
                                <div class="mt-3 lg:mt-4 pt-3 lg:pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-4 text-xs">
                                        <div>
                                            <p class="text-gray-500">Joined</p>
                                            <p class="font-medium">{{ $member->created_at->format('M j, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Account Type</p>
                                            <p class="font-medium">
                                                @if($member->user->google_id)
                                                    <span class="text-green-600">Google</span>
                                                @else
                                                    <span class="text-blue-600">Local</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Members State -->
                    <div class="text-center py-6 lg:py-12">
                        <div class="w-12 h-12 lg:w-20 lg:h-20 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-3 lg:mb-4">
                            <i class="fas fa-user-friends text-lg lg:text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-base lg:text-xl font-bold text-gray-600 mb-2">No Team Members</h3>
                        <p class="text-sm lg:text-base text-gray-500">This project doesn't have any team members assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript for Complete Project Confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert Success/Error Messages
            @if(session('success'))
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
            @endif

            @if(session('error'))
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
            @endif

            // Handle Complete Project Button
            const completeBtn = document.querySelector('.complete-project-btn');
            if (completeBtn) {
                completeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    
                    Swal.fire({
                        title: 'Submit Project for Review?',
                        html: `
                            <div class="text-left space-y-3">
                                <p class="text-gray-600">Are you sure you want to submit this project for admin review?</p>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-2">
                                    <p class="text-sm text-blue-800 font-medium">
                                        <i class="fas fa-info-circle mr-1"></i> What will happen:
                                    </p>
                                    <ul class="text-sm text-blue-700 space-y-1 ml-6 list-disc">
                                        <li>Project status will change to <strong>"Review"</strong></li>
                                        <li>Admin will review and either approve or reject</li>
                                        <li>If approved, project will be marked as completed</li>
                                        <li>If rejected, project will return to active status</li>
                                    </ul>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="fas fa-paper-plane mr-2"></i>Yes, Submit for Review!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'font-medium px-6',
                            cancelButton: 'font-medium px-6'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Submitting Project...',
                                text: 'Please wait a moment',
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
            }
        });
    </script>
</body>
</html>
