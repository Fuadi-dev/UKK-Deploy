<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Project - Project Management</title>
    
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
                <!-- Breadcrumb - Hidden on mobile -->
                <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('projects') }}" class="hover:text-indigo-600 transition-colors">Projects</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="font-medium text-indigo-600">Create New Project</span>
                </div>
                
                <!-- Mobile: Show simple title with back button -->
                <div class="md:hidden flex items-center space-x-2">
                    <a href="{{ route('projects') }}" class="p-1 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h2 class="text-lg font-bold text-gray-800">Create Project</h2>
                </div>
                
                <!-- User Profile -->
                <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-white/50 transition-colors">
                    <x-user-avatar :user="Auth::user()" size="sm" />
                </div>
            </div>
        </header>

        <!-- Main Create Content -->
        <main class="p-3 lg:p-6">
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

            <!-- Create Form -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl lg:rounded-3xl shadow-2xl border border-white/20 p-4 lg:p-8">
                <div class="mb-6 lg:mb-8">
                    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-1 lg:mb-2">
                        Create New Project
                    </h1>
                    <p class="text-gray-600 text-sm lg:text-base hidden md:block">Start a new project and collaborate with your team</p>
                </div>

                <form action="{{ route('projects.store') }}" method="POST" class="space-y-4 lg:space-y-6">
                    @csrf

                    <!-- Project Name -->
                    <div>
                        <label for="project_name" class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
                        <input type="text" 
                               id="project_name" 
                               name="project_name" 
                               value="{{ old('project_name') }}"
                               class="w-full px-3 py-2.5 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
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
                                  rows="3"
                                  class="w-full px-3 py-2.5 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Enter project description (optional)">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Leader -->
                    <div>
                        <label for="leaderSearch" class="block text-sm font-medium text-gray-700 mb-2">Project Leader</label>
                        <div class="relative">
                            <input type="text" 
                                   id="leaderSearch" 
                                   class="w-full px-3 py-2.5 pr-10 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Search for leaders..."
                                   autocomplete="off">
                            
                            <!-- Search icon -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <!-- Loading indicator -->
                            <div id="leaderSearchLoading" class="absolute inset-y-0 right-0 pr-3 items-center pointer-events-none hidden">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-500"></div>
                            </div>
                            
                            <!-- Search results dropdown -->
                            <div id="leaderSearchResults" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 lg:max-h-64 overflow-y-auto z-20 hidden">
                                <!-- No results message -->
                                <div id="leaderNoResults" class="p-3 lg:p-4 text-center text-gray-500 text-sm hidden">
                                    <svg class="w-6 h-6 lg:w-8 lg:h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <p>No leaders found</p>
                                    <p class="text-xs text-gray-400 mt-1">Only users with 'leader' role will appear</p>
                                </div>
                                
                                <!-- Search results will be populated here -->
                                <div id="leaderSearchResultsList"></div>
                            </div>
                        </div>
                        
                        <!-- Selected leader display -->
                        <div id="selectedLeaderDisplay" class="mt-3 lg:mt-4 hidden">
                            <div class="flex items-center p-3 lg:p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-full flex items-center justify-center mr-2 lg:mr-3 overflow-hidden flex-shrink-0" id="selectedLeaderAvatarContainer">
                                    <!-- Avatar image (hidden by default) -->
                                    <img id="selectedLeaderAvatar" class="w-full h-full object-cover rounded-full hidden" alt="Leader Avatar" onerror="this.style.display='none'; document.getElementById('selectedLeaderInitial').parentElement.style.display='flex';">
                                    <!-- Fallback initials -->
                                    <div class="w-full h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center" id="selectedLeaderInitialContainer">
                                        <span id="selectedLeaderInitial" class="text-white font-semibold"></span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div id="selectedLeaderName" class="font-medium text-gray-900 text-sm lg:text-base truncate"></div>
                                    <div id="selectedLeaderEmail" class="text-xs lg:text-sm text-gray-600 truncate hidden md:block"></div>
                                    <div class="text-xs text-blue-600 uppercase font-medium">Leader</div>
                                </div>
                                <button type="button" id="clearLeaderSelection" class="ml-2 p-1 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <input type="hidden" id="selectedLeaderId" name="leader_id" value="">
                        <p class="text-xs lg:text-sm text-gray-500 mt-2 hidden md:block">Select a leader who will be responsible for this project. This person will automatically become Project Manager.</p>
                        @error('leader_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <input type="date" 
                               id="deadline" 
                               name="deadline" 
                               value="{{ old('deadline') }}"
                               class="w-full px-3 py-2.5 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        @error('deadline')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Project Status</label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-3 py-2.5 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                        <p class="text-xs lg:text-sm text-gray-500 mt-2 hidden md:block">Select the initial status for this project. You can change it later from the project management page.</p>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col-reverse md:flex-row items-center justify-between gap-3 pt-4 lg:pt-6 border-t border-gray-200">
                        <a href="{{ route('projects') }}" 
                           class="w-full md:w-auto px-4 py-2.5 lg:px-6 lg:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 font-medium text-sm lg:text-base text-center">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="w-full md:w-auto px-6 py-2.5 lg:px-8 lg:py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium shadow-lg text-sm lg:text-base">
                            Create Project
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- JavaScript for leader search -->
    <script>
        let leaderSearchTimeout;

        document.addEventListener('DOMContentLoaded', function() {
            const leaderSearch = document.getElementById('leaderSearch');
            const leaderSearchResults = document.getElementById('leaderSearchResults');
            const leaderSearchResultsList = document.getElementById('leaderSearchResultsList');
            const leaderNoResults = document.getElementById('leaderNoResults');
            const leaderSearchLoading = document.getElementById('leaderSearchLoading');
            const selectedLeaderDisplay = document.getElementById('selectedLeaderDisplay');
            const clearLeaderSelection = document.getElementById('clearLeaderSelection');
            const selectedLeaderId = document.getElementById('selectedLeaderId');

            // Show/hide loading for leader search
            function showLeaderLoading() {
                leaderSearchLoading.classList.remove('hidden');
                leaderSearchLoading.style.display = 'flex';
                leaderSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'none';
            }

            function hideLeaderLoading() {
                leaderSearchLoading.classList.add('hidden');
                leaderSearchLoading.style.display = 'none';
                leaderSearch.parentElement.querySelector('.pointer-events-none svg').style.display = 'block';
            }

            // Leader search with real-time response
            leaderSearch.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                
                clearTimeout(leaderSearchTimeout);
                
                if (searchTerm.length === 0) {
                    leaderSearchResults.classList.add('hidden');
                    hideLeaderLoading();
                    return;
                }

                if (searchTerm.length < 2) {
                    leaderSearchResults.classList.add('hidden');
                    hideLeaderLoading();
                    return;
                }

                showLeaderLoading();
                
                // Real-time search with debounce
                leaderSearchTimeout = setTimeout(() => {
                    searchLeaders(searchTerm);
                }, 300);
            });

            // Search leaders function
            function searchLeaders(searchTerm) {
                fetch(`/api/users/search?search=${encodeURIComponent(searchTerm)}&role=leader`)
                    .then(response => response.json())
                    .then(users => {
                        displayLeaderSearchResults(users);
                        hideLeaderLoading();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        leaderSearchResults.classList.add('hidden');
                        hideLeaderLoading();
                    });
            }

            // Display leader search results
            function displayLeaderSearchResults(users) {
                leaderSearchResultsList.innerHTML = '';
                leaderNoResults.classList.add('hidden');
                
                if (users.length === 0) {
                    leaderNoResults.classList.remove('hidden');
                } else {
                    leaderSearchResultsList.innerHTML = users.map(user => `
                        <div class="leader-result p-2 lg:p-3 ${user.is_working ? 'bg-gray-50 cursor-not-allowed opacity-60' : 'hover:bg-gray-50 cursor-pointer'} border-b last:border-b-0 transition-colors" 
                             data-user-id="${user.id}" 
                             data-user-name="${user.name}" 
                             data-user-email="${user.email}"
                             data-user-avatar="${user.avatar_url || ''}"
                             data-user-initials="${user.initials}"
                             data-user-working="${user.is_working || false}"
                             data-user-status="${user.status || 'available'}">
                            <div class="flex items-center space-x-2 lg:space-x-3">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-full flex items-center justify-center overflow-hidden relative flex-shrink-0">
                                    ${user.avatar_url ? 
                                        `<img src="${user.avatar_url}" alt="${user.name}" class="w-full h-full object-cover rounded-full" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                         <div class="w-full h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center" style="display: none;">
                                             <span class="text-white font-semibold text-sm">${user.initials}</span>
                                         </div>` :
                                        `<div class="w-full h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                             <span class="text-white font-semibold text-sm">${user.initials}</span>
                                         </div>`
                                    }
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 ${user.is_working ? 'bg-orange-400' : 'bg-green-400'} rounded-full border border-white"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 text-sm lg:text-base truncate">${user.name}</div>
                                    <div class="text-xs lg:text-sm text-gray-600 truncate hidden md:block">${user.email}</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-xs text-blue-600 uppercase font-medium">${user.role || 'User'}</div>
                                        <span class="px-2 py-1 text-xs font-medium ${user.is_working ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'} rounded-full capitalize">${user.status || 'available'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
                
                leaderSearchResults.classList.remove('hidden');

                // Add click events to leader results
                document.querySelectorAll('.leader-result').forEach(result => {
                    result.addEventListener('click', function() {
                        const userId = this.dataset.userId;
                        const userName = this.dataset.userName;
                        const userEmail = this.dataset.userEmail;
                        const userAvatar = this.dataset.userAvatar;
                        const userInitials = this.dataset.userInitials;
                        const userWorking = this.dataset.userWorking === 'true';
                        const userStatus = this.dataset.userStatus;
                        
                        // Check if user is already working on another project
                        if (userWorking) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Leader Sedang Bekerja!',
                                text: 'Leader ini sedang ditugaskan pada project lain dan tidak dapat dipilih sebagai leader project baru.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#f59e0b'
                            });
                            return;
                        }
                        
                        // Set selected leader
                        selectedLeaderId.value = userId;
                        document.getElementById('selectedLeaderName').textContent = userName;
                        document.getElementById('selectedLeaderEmail').textContent = userEmail;
                        
                        // Handle avatar display
                        const selectedLeaderAvatar = document.getElementById('selectedLeaderAvatar');
                        const selectedLeaderInitialContainer = document.getElementById('selectedLeaderInitialContainer');
                        const selectedLeaderInitial = document.getElementById('selectedLeaderInitial');
                        
                        if (userAvatar && userAvatar !== 'null' && userAvatar !== '') {
                            selectedLeaderAvatar.src = userAvatar;
                            selectedLeaderAvatar.classList.remove('hidden');
                            selectedLeaderInitialContainer.style.display = 'none';
                        } else {
                            selectedLeaderAvatar.classList.add('hidden');
                            selectedLeaderInitialContainer.style.display = 'flex';
                            selectedLeaderInitial.textContent = userInitials || userName.charAt(0).toUpperCase();
                        }
                        
                        // Show selected leader display
                        selectedLeaderDisplay.classList.remove('hidden');
                        
                        // Hide search results
                        leaderSearchResults.classList.add('hidden');
                        
                        // Clear search input
                        leaderSearch.value = '';
                    });
                });
            }

            // Clear leader selection
            clearLeaderSelection.addEventListener('click', function() {
                selectedLeaderId.value = '';
                selectedLeaderDisplay.classList.add('hidden');
                leaderSearch.value = '';
                
                // Reset avatar display
                const selectedLeaderAvatar = document.getElementById('selectedLeaderAvatar');
                const selectedLeaderInitialContainer = document.getElementById('selectedLeaderInitialContainer');
                if (selectedLeaderAvatar) {
                    selectedLeaderAvatar.classList.add('hidden');
                    selectedLeaderAvatar.src = '';
                }
                if (selectedLeaderInitialContainer) {
                    selectedLeaderInitialContainer.style.display = 'flex';
                }
                
                leaderSearch.focus();
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!leaderSearch.contains(e.target) && !leaderSearchResults.contains(e.target)) {
                    leaderSearchResults.classList.add('hidden');
                }
            });

            // Escape key to close search results
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    leaderSearchResults.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
