<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Projects - Leader Dashboard</title>
    
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
                        <div>
                            <h1 class="text-xl lg:text-3xl font-bold text-white mb-1 lg:mb-2">My Projects</h1>
                            <p class="text-sm lg:text-base text-purple-100">View and manage your assigned projects</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl lg:rounded-2xl px-3 lg:px-4 py-1.5 lg:py-2">
                                <span class="text-white font-semibold text-xs lg:text-base">Total Projects: 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Project State -->
            <div class="bg-white rounded-2xl lg:rounded-3xl shadow-lg border border-gray-100 p-6 lg:p-12 text-center">
                <div class="max-w-md mx-auto">
                    <!-- Icon -->
                    <div class="w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6">
                        <i class="fas fa-briefcase text-2xl lg:text-4xl text-indigo-500"></i>
                    </div>
                    
                    <!-- Message -->
                    <h3 class="text-lg lg:text-2xl font-bold text-gray-800 mb-3 lg:mb-4">No Project Assigned Yet</h3>
                    <p class="text-sm lg:text-base text-gray-600 mb-6">
                        You haven't been assigned to any project yet. Contact your administrator to get assigned to a project.
                    </p>
                    
                    <!-- Action -->
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('dashboard') }}" 
                           class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-5 py-2.5 lg:px-6 lg:py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2 text-sm lg:text-base">
                            <i class="fas fa-arrow-left text-sm"></i>
                            <span>Go to Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
