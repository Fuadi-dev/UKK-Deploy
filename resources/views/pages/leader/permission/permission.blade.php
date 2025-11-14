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
        
        <div class="container mx-auto px-4 py-8 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-6 py-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Permission Management</h1>
                            <p class="text-purple-100">Manage team member permission requests</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                                <span class="text-white font-semibold text-sm lg:text-base">Total Requests: {{ $permissions->count() }}</span>
                            </div>
                            <button onclick="permissionManager.openCreateModal()" 
                                    class="bg-white text-purple-600 px-4 py-2 lg:px-6 lg:py-3 rounded-2xl font-semibold hover:bg-gray-50 transition-all duration-200 shadow-lg text-sm lg:text-base">
                                <i class="fas fa-plus mr-1 lg:mr-2"></i>Create Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">Pending</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">Approved</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'approved')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-times text-red-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">Rejected</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'rejected')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar text-blue-600 text-lg lg:text-xl"></i>
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <p class="text-gray-600 text-xs lg:text-sm">Total Hours</p>
                            <p class="text-xl lg:text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'approved')->sum('duration_hours') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                        <select id="statusFilter" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type Filter</label>
                        <select id="typeFilter" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="all">All Types</option>
                            <option value="sick_leave">Sick Leave</option>
                            <option value="personal_leave">Personal Leave</option>
                            <option value="vacation">Vacation</option>
                            <option value="emergency">Emergency</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="lg:flex-shrink-0">
                        <button onclick="permissionManager.applyFilters()" 
                                class="w-full lg:w-auto bg-purple-600 text-white px-6 py-2 rounded-xl hover:bg-purple-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Permissions Table -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Permission Requests</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="permissionsTableBody">
                            @forelse($permissions as $permission)
                            <tr class="permission-row hover:bg-gray-50" 
                                data-status="{{ $permission->status }}" 
                                data-type="{{ $permission->permission_type }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold">{{ substr($permission->user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->user->name }}</div>
                                            @if($permission->project)
                                            <div class="text-sm text-gray-500">{{ $permission->project->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                        {{ str_replace('_', ' ', ucfirst($permission->permission_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $permission->start_date->format('M d, Y') }} - {{ $permission->end_date->format('M d, Y') }}
                                    @if($permission->start_time)
                                    <div class="text-xs text-gray-500">
                                        {{ date('H:i', strtotime($permission->start_time)) }} - {{ date('H:i', strtotime($permission->end_time)) }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $permission->duration_hours }} hours
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($permission->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                    @elseif($permission->status === 'approved')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Approved</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="permissionManager.viewPermission({{ $permission->id }})" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($permission->status === 'pending')
                                        <button onclick="permissionManager.approvePermission({{ $permission->id }})" 
                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg transition-colors">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="permissionManager.rejectPermission({{ $permission->id }})" 
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                        <button onclick="permissionManager.deletePermission({{ $permission->id }})" 
                                                class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12">
                                    <div class="text-center">
                                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Permission Requests Found</h3>
                                        <p class="text-gray-600 mb-8">There are no permission requests to display for the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div id="createPermissionModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">Create Permission Request</h3>
                        <button onclick="permissionManager.closeCreateModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <i class="fas fa-times text-white group-hover:text-white/80 transition-colors"></i>
                        </button>
                    </div>
                </div>
                <form id="createPermissionForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                        <select name="user_id" required class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Employee</option>
                            @foreach($projects as $project)
                                @foreach($project->members as $member)
                                    <option value="{{ $member->user->id }}">{{ $member->user->name }} ({{ $project->project_name }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project (Optional)</label>
                        <select name="project_id" class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permission Type</label>
                        <select name="permission_type" required class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Type</option>
                            <option value="sick_leave">Sick Leave</option>
                            <option value="personal_leave">Personal Leave</option>
                            <option value="vacation">Vacation</option>
                            <option value="emergency">Emergency</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <textarea name="reason" required rows="3" placeholder="Explain the reason for this permission request..." 
                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" required 
                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" required 
                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time (Optional)</label>
                            <input type="time" name="start_time" 
                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time (Optional)</label>
                            <input type="time" name="end_time" 
                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="permissionManager.closeCreateModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">
                            Create Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Approve/Reject Modal -->
    <div id="actionModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl max-w-md w-full border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content">
                <div id="actionModalHeader" class="px-6 py-4 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 id="actionModalTitle" class="text-xl font-semibold text-white"></h3>
                        <button onclick="permissionManager.closeActionModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                            <i class="fas fa-times text-white group-hover:text-white/80 transition-colors"></i>
                        </button>
                    </div>
                </div>
                <form id="actionForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="admin_notes" rows="3" placeholder="Add notes about this decision..." 
                                class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"></textarea>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="permissionManager.closeActionModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="actionSubmitBtn"
                                class="flex-1 px-4 py-2 rounded-xl text-white transition-colors">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    class PermissionManager {
        constructor() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            this.currentPermissionId = null;
            this.currentAction = null;
            this.init();
        }

        init() {
            // Initialize form handlers
            document.getElementById('createPermissionForm').addEventListener('submit', (e) => this.handleCreatePermission(e));
            document.getElementById('actionForm').addEventListener('submit', (e) => this.handleActionPermission(e));
        }

        openCreateModal() {
            const modal = document.getElementById('createPermissionModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        closeCreateModal() {
            const modal = document.getElementById('createPermissionModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('createPermissionForm').reset();
            }, 300);
        }

        async handleCreatePermission(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('/leader/permissions', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    this.showSuccess('Permission request created successfully!');
                    this.closeCreateModal();
                    window.location.reload();
                } else {
                    this.showError(result.message || 'Failed to create permission request');
                }
            } catch (error) {
                this.showError('Failed to create permission request. Please try again.');
            }
        }

        approvePermission(id) {
            this.currentPermissionId = id;
            this.currentAction = 'approve';
            
            document.getElementById('actionModalHeader').className = 'bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 rounded-t-3xl';
            document.getElementById('actionModalTitle').textContent = 'Approve Permission Request';
            document.getElementById('actionSubmitBtn').className = 'flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 rounded-xl text-white transition-colors';
            document.getElementById('actionSubmitBtn').textContent = 'Approve';
            
            const modal = document.getElementById('actionModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        rejectPermission(id) {
            this.currentPermissionId = id;
            this.currentAction = 'reject';
            
            document.getElementById('actionModalHeader').className = 'bg-gradient-to-r from-red-600 to-rose-600 px-6 py-4 rounded-t-3xl';
            document.getElementById('actionModalTitle').textContent = 'Reject Permission Request';
            document.getElementById('actionSubmitBtn').className = 'flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-xl text-white transition-colors';
            document.getElementById('actionSubmitBtn').textContent = 'Reject';
            
            const modal = document.getElementById('actionModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        closeActionModal() {
            const modal = document.getElementById('actionModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('actionForm').reset();
                this.currentPermissionId = null;
                this.currentAction = null;
            }, 300);
        }

        async handleActionPermission(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const url = `/leader/permissions/${this.currentPermissionId}/${this.currentAction}`;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    const action = this.currentAction === 'approve' ? 'approved' : 'rejected';
                    this.showSuccess(`Permission request ${action} successfully!`);
                    this.closeActionModal();
                    window.location.reload();
                } else {
                    this.showError(result.message || `Failed to ${this.currentAction} permission request`);
                }
            } catch (error) {
                this.showError(`Failed to ${this.currentAction} permission request. Please try again.`);
            }
        }

        async deletePermission(id) {
            if (!confirm('Are you sure you want to delete this permission request?')) {
                return;
            }

            try {
                const response = await fetch(`/leader/permissions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    this.showSuccess('Permission request deleted successfully!');
                    window.location.reload();
                } else {
                    this.showError(result.message || 'Failed to delete permission request');
                }
            } catch (error) {
                this.showError('Failed to delete permission request. Please try again.');
            }
        }

        applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            const rows = document.querySelectorAll('.permission-row');

            rows.forEach(row => {
                const status = row.dataset.status;
                const type = row.dataset.type;
                
                let showRow = true;
                
                if (statusFilter !== 'all' && status !== statusFilter) {
                    showRow = false;
                }
                
                if (typeFilter !== 'all' && type !== typeFilter) {
                    showRow = false;
                }
                
                // Apply display style for both mobile cards and desktop table rows
                if (row.tagName.toLowerCase() === 'tr') {
                    row.style.display = showRow ? 'table-row' : 'none';
                } else {
                    row.style.display = showRow ? 'block' : 'none';
                }
            });
        }

        showSuccess(message) {
            // You can implement a toast notification system here
            alert(message);
        }

        showError(message) {
            // You can implement a toast notification system here
            alert(message);
        }
    }

    // Initialize permission manager
    const permissionManager = new PermissionManager();
    </script>
</body>
</html>