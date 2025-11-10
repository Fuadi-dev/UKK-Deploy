<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Project Management') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    @include('components.sidebar')
    
    <!-- Main Content with sidebar offset -->
    <div class="lg:ml-64 min-h-screen">
        <!-- Mobile Header Spacer -->
        <div class="lg:hidden h-16"></div>
        
        @yield('content')
    </div>
</body>
</html>
