@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">My Permission Requests</h1>
                        <p class="text-purple-100">Submit and track your permission requests</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                            <span class="text-white font-semibold">Total Requests: {{ $permissions->count() }}</span>
                        </div>
                        @if($userCard)
                            <button onclick="permissionManager.openCreateModal()" 
                                    class="bg-white text-purple-600 px-6 py-3 rounded-2xl font-semibold hover:bg-gray-50 transition-all duration-200 shadow-lg">
                                <i class="fas fa-plus mr-2"></i>New Request
                            </button>
                        @else
                            <div class="bg-white/30 text-white/60 px-6 py-3 rounded-2xl font-semibold cursor-not-allowed" 
                                 title="You need an assigned card to create permission requests">
                                <i class="fas fa-lock mr-2"></i>New Request
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Pending</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Approved</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Rejected</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Hours</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $permissions->where('status', 'approved')->sum('duration_hours') }}</p>
                    </div>
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
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="permissionsTableBody">
                        @forelse($permissions as $permission)
                        <tr class="permission-row hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar text-white text-sm"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ str_replace('_', ' ', ucfirst($permission->permission_type)) }}</div>
                                        @if($permission->project)
                                        <div class="text-sm text-gray-500">{{ $permission->project->project_name }}</div>
                                        @endif
                                    </div>
                                </div>
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
                                @if($permission->approved_at)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $permission->approved_at->format('M d, Y') }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="permissionManager.viewPermission({{ $permission->id }})" 
                                            class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($permission->status === 'pending')
                                    <button onclick="permissionManager.editPermission({{ $permission->id }})" 
                                            class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="permissionManager.deletePermission({{ $permission->id }})" 
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12">
                                @if(!$userCard)
                                    <!-- No Card Assigned State -->
                                    <div class="text-center">
                                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Card Assigned</h3>
                                        <p class="text-gray-600 mb-8">You need to have an assigned card before you can submit permission requests. Please contact your project manager to get a card assigned.</p>
                                        <div class="bg-white/30 text-gray-400 px-6 py-3 rounded-2xl font-semibold cursor-not-allowed inline-flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Request Locked
                                        </div>
                                    </div>
                                @else
                                    <!-- No Permission Requests State -->
                                    <div class="text-center">
                                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Permission Requests Yet</h3>
                                        <p class="text-gray-600 mb-8">You haven't submitted any permission requests yet. Create your first request to get started.</p>
                                        <button onclick="permissionManager.openCreateModal()" 
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create First Request
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Permission Modal -->
<div id="permissionModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 id="modalTitle" class="text-xl font-semibold text-white">Create Permission Request</h3>
                    <button onclick="permissionManager.closeModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                        <i class="fas fa-times text-white group-hover:text-white/80 transition-colors"></i>
                    </button>
                </div>
            </div>
            <form id="permissionForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="permissionId" name="permission_id">
                <input type="hidden" name="_method" id="methodField">
                
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
                    <button type="button" onclick="permissionManager.closeModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Permission Modal -->
<div id="viewPermissionModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-white/20 transform transition-all duration-300 scale-95 opacity-0 modal-content">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Permission Details</h3>
                    <button onclick="permissionManager.closeViewModal()" class="p-2 rounded-xl hover:bg-white/20 transition-all duration-200 group">
                        <i class="fas fa-times text-white group-hover:text-white/80 transition-colors"></i>
                    </button>
                </div>
            </div>
            <div id="viewPermissionContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
class PermissionManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        this.editMode = false;
        this.init();
    }

    init() {
        // Initialize form handlers
        document.getElementById('permissionForm').addEventListener('submit', (e) => this.handleSubmitPermission(e));
    }

    openCreateModal() {
        // Check if user has a card assigned
        const hasCard = {{ $userCard ? 'true' : 'false' }};
        
        if (!hasCard) {
            this.showError('You need to have an assigned card to submit permission requests.');
            return;
        }
        
        this.editMode = false;
        document.getElementById('modalTitle').textContent = 'Create Permission Request';
        document.getElementById('methodField').value = '';
        document.getElementById('permissionId').value = '';
        document.getElementById('permissionForm').reset();
        
        const modal = document.getElementById('permissionModal');
        const modalContent = modal.querySelector('.modal-content');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    closeModal() {
        const modal = document.getElementById('permissionModal');
        const modalContent = modal.querySelector('.modal-content');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('permissionForm').reset();
        }, 300);
    }

    async editPermission(id) {
        this.editMode = true;
        
        try {
            const response = await fetch(`/user/permissions/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const result = await response.json();
                const permission = result.data;
                
                document.getElementById('modalTitle').textContent = 'Edit Permission Request';
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('permissionId').value = permission.id;
                
                // Fill form with existing data
                document.querySelector('[name="project_id"]').value = permission.project_id || '';
                document.querySelector('[name="permission_type"]').value = permission.permission_type;
                document.querySelector('[name="reason"]').value = permission.reason;
                document.querySelector('[name="start_date"]').value = permission.start_date;
                document.querySelector('[name="end_date"]').value = permission.end_date;
                document.querySelector('[name="start_time"]').value = permission.start_time || '';
                document.querySelector('[name="end_time"]').value = permission.end_time || '';
                
                const modal = document.getElementById('permissionModal');
                const modalContent = modal.querySelector('.modal-content');
                
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                this.showError('Failed to load permission data');
            }
        } catch (error) {
            this.showError('Failed to load permission data');
        }
    }

    async handleSubmitPermission(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const method = this.editMode ? 'PUT' : 'POST';
        const url = this.editMode ? `/user/permissions/${formData.get('permission_id')}` : '/user/permissions';
        
        if (this.editMode) {
            formData.append('_method', 'PUT');
        }
        
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
                const action = this.editMode ? 'updated' : 'created';
                this.showSuccess(`Permission request ${action} successfully!`);
                this.closeModal();
                window.location.reload();
            } else {
                this.showError(result.message || 'Failed to submit permission request');
            }
        } catch (error) {
            this.showError('Failed to submit permission request. Please try again.');
        }
    }

    async viewPermission(id) {
        try {
            const response = await fetch(`/user/permissions/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const result = await response.json();
                const permission = result.data;
                
                const content = `
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Permission Type</h4>
                            <p class="text-gray-600">${permission.permission_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                        </div>
                        
                        ${permission.project ? `
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Project</h4>
                            <p class="text-gray-600">${permission.project.name}</p>
                        </div>
                        ` : ''}
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Reason</h4>
                            <p class="text-gray-600">${permission.reason}</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Date Range</h4>
                            <p class="text-gray-600">${new Date(permission.start_date).toLocaleDateString()} - ${new Date(permission.end_date).toLocaleDateString()}</p>
                            ${permission.start_time ? `<p class="text-sm text-gray-500 mt-1">Time: ${permission.start_time} - ${permission.end_time}</p>` : ''}
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Duration</h4>
                            <p class="text-gray-600">${permission.duration_hours} hours</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Status</h4>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                                permission.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                permission.status === 'approved' ? 'bg-green-100 text-green-800' :
                                'bg-red-100 text-red-800'
                            }">${permission.status.charAt(0).toUpperCase() + permission.status.slice(1)}</span>
                        </div>
                        
                        ${permission.admin_notes ? `
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Admin Notes</h4>
                            <p class="text-gray-600">${permission.admin_notes}</p>
                        </div>
                        ` : ''}
                        
                        <div class="pt-4">
                            <button onclick="permissionManager.closeViewModal()" 
                                    class="w-full px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                `;
                
                document.getElementById('viewPermissionContent').innerHTML = content;
                
                const modal = document.getElementById('viewPermissionModal');
                const modalContent = modal.querySelector('.modal-content');
                
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                this.showError('Failed to load permission details');
            }
        } catch (error) {
            this.showError('Failed to load permission details');
        }
    }

    closeViewModal() {
        const modal = document.getElementById('viewPermissionModal');
        const modalContent = modal.querySelector('.modal-content');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    async deletePermission(id) {
        if (!confirm('Are you sure you want to delete this permission request?')) {
            return;
        }

        try {
            const response = await fetch(`/user/permissions/${id}`, {
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
@endsection
