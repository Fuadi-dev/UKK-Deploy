<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Project Review - Admin Panel</title>
    
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
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-white/20 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <!-- Mobile: Back Button, Desktop: Breadcrumb -->
                <a href="{{ route('dashboard') }}" class="lg:hidden p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="hidden lg:flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Home</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Project Review</span>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-2">
                    <!-- User Profile - Hidden on mobile -->
                    <div class="hidden md:flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                        <x-user-avatar :user="Auth::user()" size="sm" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Review Content -->
        <main class="p-3 lg:p-6">
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

            <!-- Page Header -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-8 mb-4 lg:mb-8">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <div>
                        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1 lg:mb-2">
                            Project Review
                        </h1>
                        <p class="hidden lg:block text-gray-600 text-lg">
                            Review and approve or reject projects pending completion
                        </p>
                    </div>
                </div>

                <!-- Review Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4">
                    <div class="bg-gradient-to-br from-purple-50 to-pink-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-purple-600 font-medium mb-1">In Review</p>
                                <p class="text-lg lg:text-2xl font-bold text-purple-700">{{ $projects->count() }}</p>
                            </div>
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-green-600 font-medium mb-1">Pending Action</p>
                                <p class="text-lg lg:text-2xl font-bold text-green-700">{{ $projects->count() }}</p>
                            </div>
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-3 lg:p-4 rounded-xl lg:rounded-2xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-blue-600 font-medium mb-1">Total Reviews</p>
                                <p class="text-lg lg:text-2xl font-bold text-blue-700">{{ $projects->count() }}</p>
                            </div>
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

            <!-- Projects Grid -->
            @if($projects->isEmpty())
                <div class="bg-white/40 backdrop-blur-lg rounded-3xl shadow-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Projects in Review</h3>
                    <p class="text-gray-500">There are currently no projects waiting for review.</p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    @foreach($projects as $project)
                        <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 hover:shadow-3xl transition-all duration-300 overflow-hidden">
                            <!-- Project Header -->
                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-4 lg:p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg lg:text-xl font-bold text-white mb-2">{{ $project->project_name }}</h3>
                                        <div class="flex items-center gap-2 text-purple-100 text-xs lg:text-sm">
                                            <x-user-avatar :user="$project->user" size="xs" />
                                            <span>by {{ $project->user->name }}</span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full">
                                        Review
                                    </span>
                                </div>
                            </div>

                            <!-- Project Details -->
                            <div class="p-4 lg:p-6">
                                <div class="mb-4">
                                    <p class="text-gray-700 text-sm lg:text-base leading-relaxed line-clamp-3">
                                        {{ $project->description ?? 'No description provided' }}
                                    </p>
                                </div>

                                <!-- Project Info Grid -->
                                <div class="grid grid-cols-2 gap-3 lg:gap-4 mb-4 lg:mb-6">
                                    <div class="bg-gray-50 rounded-lg lg:rounded-xl p-3">
                                        <p class="text-xs text-gray-500 mb-1">Deadline</p>
                                        <p class="text-xs lg:text-sm font-semibold text-gray-800">
                                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'No deadline' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg lg:rounded-xl p-3">
                                        <p class="text-xs text-gray-500 mb-1">Team Members</p>
                                        <p class="text-xs lg:text-sm font-semibold text-gray-800">
                                            {{ $project->members->count() }} {{ Str::plural('member', $project->members->count()) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Team Members -->
                                @if($project->members->isNotEmpty())
                                    <div class="mb-4 lg:mb-6">
                                        <p class="text-xs text-gray-500 mb-2">Team Members:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($project->members as $member)
                                                <span class="px-2 lg:px-3 py-1 bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 text-xs font-medium rounded-full border border-purple-200">
                                                    {{ $member->user->name }} 
                                                    <span class="text-purple-400">•</span>
                                                    <span class="text-purple-500">{{ $member->role }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex gap-2 lg:gap-3">
                                    <!-- Approve Button -->
                                    <form action="{{ route('review.approve', $project->id) }}" method="POST" class="flex-1 approve-form">
                                        @csrf
                                        <button type="button" 
                                                class="approve-btn w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs lg:text-sm font-semibold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Approve</span>
                                        </button>
                                    </form>

                                    <!-- Reject Button -->
                                    <button onclick="openRejectModal({{ $project->id }}, '{{ addslashes($project->project_name) }}')"
                                            class="flex-1 px-3 lg:px-4 py-2.5 lg:py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs lg:text-sm font-semibold rounded-xl hover:from-red-600 hover:to-rose-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Reject</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden" style="display: none;">
    <div class="flex items-center justify-center min-h-screen w-full p-4 overflow-y-auto">
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content my-8 mx-auto relative">
            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-red-500 via-rose-500 to-pink-500 rounded-t-3xl p-6">
                <div class="absolute inset-0 opacity-10 rounded-t-3xl" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">Reject Project</h3>
                    </div>
                    <button id="closeModalBtn" type="button" onclick="closeRejectModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-white group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="rejectForm" method="POST" class="p-8">
                @csrf
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">
                        You are about to reject: <strong id="projectName" class="text-gray-900"></strong>
                    </p>
                    
                    <label for="rejection_note" class="block text-sm font-semibold text-gray-700 mb-2">
                        Rejection Note <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="rejection_note" 
                        name="rejection_note" 
                        rows="4" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none transition-all duration-200"
                        placeholder="Please provide a reason for rejection..."></textarea>
                    <p class="text-xs text-gray-500 mt-2">This note will be visible to the project leader.</p>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            id="cancelBtn"
                            class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            id="submitBtn"
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white font-semibold rounded-xl hover:from-red-600 hover:to-rose-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Reject Project</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('rejectModal');

    // Handle approve button confirmation
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Approve Project?',
                html: `
                    <div class="text-left space-y-3">
                        <p class="text-gray-600">Are you sure you want to approve this project?</p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-2">
                            <p class="text-sm text-green-800 font-medium">
                                <i class="fas fa-info-circle mr-1"></i> What will happen:
                            </p>
                            <ul class="text-sm text-green-700 space-y-1 ml-6 list-disc">
                                <li>Project will be marked as <strong>Completed</strong></li>
                                <li>Team members will be freed (if not in other active projects)</li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'font-medium px-6',
                    cancelButton: 'font-medium px-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Approving Project...',
                        text: 'Please wait a moment',
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
});

function openRejectModal(projectId, projectName) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const projectNameEl = document.getElementById('projectName');
    
    form.action = `/review/${projectId}/reject`;
    projectNameEl.textContent = projectName;
    
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal-content');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const textarea = document.getElementById('rejection_note');
    const modalContent = modal.querySelector('.modal-content');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        textarea.value = '';
        document.body.style.overflow = '';
    }, 200);
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('rejectModal').classList.contains('hidden')) {
        closeRejectModal();
    }
});
</script>

<!-- Additional styles for line-clamp -->
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
