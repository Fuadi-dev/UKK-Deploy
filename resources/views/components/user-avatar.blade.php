@props(['user', 'size' => 'md', 'class' => ''])

@php
    $sizes = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
        '2xl' => 'w-20 h-20 text-xl',
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $avatarUrl = $user->getAvatarUrl();
    $initials = $user->getInitials();
@endphp

<div class="{{ $sizeClass }} {{ $class }} bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" 
             alt="{{ $user->name }}" 
             class="w-full h-full object-cover"
             onerror="this.style.display='none'; this.parentElement.querySelector('.fallback-initials').style.display='flex';">
        <span class="fallback-initials text-white font-semibold w-full h-full items-center justify-center" style="display: none;">{{ $initials }}</span>
    @else
        <span class="text-white font-semibold">{{ $initials }}</span>
    @endif
</div>
