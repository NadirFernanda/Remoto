@php
    // Lucide-inspired inline icon system for Blade.
    // Usage: @include('components.icon', ['name' => 'save', 'class' => 'mr-2'])
    $icon = $name ?? null;
    $c = $class ?? '';
    if (! $icon) {
        echo '';
        return;
    }
@endphp

@switch($icon)
    @case('save')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h11l5 5v9a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <path d="M7 3v4"></path>
        </svg>
        @break
    @case('edit')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        @break
    @case('eye')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        @break
    @case('chat')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-12.3 8.38 8.38 0 0 1 3.8.9"></path>
            <path d="M21 11.5v4a2 2 0 0 1-2 2H7l-4 4V7a2 2 0 0 1 2-2h4"/>
            <path d="M8 11h8M8 15h6" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        @break
    @case('check')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 13l4 4L19 7"></path>
        </svg>
        @break
    @case('close')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        @break
    @case('menu')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        @break
    @case('dots')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 10h.01M12 10h.01M16 10h.01"></path>
        </svg>
        @break
    @case('arrow-left')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 19l-7-7 7-7"></path>
        </svg>
        @break
    @case('clock')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 8v4l3 3"></path>
            <circle cx="12" cy="12" r="10"></circle>
        </svg>
        @break
    @case('wallet')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h14"></path>
            <rect x="7" y="12" width="10" height="6" rx="1"></rect>
        </svg>
        @break
    @case('user')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        @break
    @case('bell')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"></path>
            <path d="M13.73 21a2 2 0 01-3.46 0"></path>
        </svg>
        @break
    @case('search')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="M21 21l-4.35-4.35"></path>
        </svg>
        @break
    @case('flag')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
            <line x1="4" y1="22" x2="4" y2="15"></line>
        </svg>
        @break
    @case('send')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"></line>
            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
        </svg>
        @break
    @case('star')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2.75l2.8 5.67 6.25.91-4.53 4.42 1.07 6.24L12 0.35l-5.59 2.94 1.07-6.24L2.95 9.33l6.25-.91L12 2.75z"></path>
        </svg>
        @break
    @case('sparkles')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l1.8 4.7L18.5 9l-4.7 1.3L12 15l-1.8-4.7L5.5 9l4.7-1.3L12 3z"></path>
            <path d="M19 15l.9 2.1L22 18l-2.1.9L19 21l-.9-2.1L16 18l2.1-.9L19 15z"></path>
            <path d="M5 15l.9 2.1L8 18l-2.1.9L5 21l-.9-2.1L2 18l2.1-.9L5 15z"></path>
        </svg>
        @break
    @case('shield-check')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l7 3v6c0 4.5-2.8 8.3-7 10-4.2-1.7-7-5.5-7-10V6l7-3z"></path>
            <path d="M9.5 12.5l1.5 1.5 3.5-4"></path>
        </svg>
        @break
    @case('briefcase')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="7" width="18" height="12" rx="2"></rect>
            <path d="M8 7V5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
            <path d="M3 12h18"></path>
        </svg>
        @break
    @case('globe')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path>
        </svg>
        @break
    @case('chart')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 18h18"></path>
            <path d="M7 14l3-3 3 2 5-7"></path>
            <path d="M17 6h3v3"></path>
        </svg>
        @break
    @case('store')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 9h16l-1 10H5L4 9z"></path>
            <path d="M8 9V7a4 4 0 1 1 8 0v2"></path>
        </svg>
        @break
    @case('badge-check')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7 12l3 3 7-7"></path>
            <path d="M12 3.5l7 3v5c0 4-2.7 7.5-7 9.5-4.3-2-7-5.5-7-9.5v-5l7-3z"></path>
        </svg>
        @break
    @case('cookie')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3a9 9 0 1 0 9 9 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4 4 4 0 0 1 4 4"></path>
            <circle cx="8.5" cy="8.5" r="1"></circle>
            <circle cx="9.5" cy="15.5" r="1"></circle>
            <circle cx="15.5" cy="10.5" r="1"></circle>
            <circle cx="16.5" cy="16.5" r="1"></circle>
        </svg>
        @break
    @case('file')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"></path>
            <path d="M14 2v6h6"></path>
            <path d="M9 13h6M9 17h6"></path>
        </svg>
        @break
    @case('file-text')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"></path>
            <path d="M14 2v6h6"></path>
            <path d="M9 13h6M9 17h6"></path>
        </svg>
        @break
    @case('paperclip')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.5 18.5l-4.2-4.2a4.5 4.5 0 0 1 6.4-6.4l6.2 6.2a6.5 6.5 0 0 1-9.2 9.2l-7-7"></path>
        </svg>
        @break
    @case('pencil')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        @break
    @case('trash')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 6h18"></path>
            <path d="M8 6V4h8v2"></path>
            <path d="M19 6l-1 14H6L5 6"></path>
            <path d="M10 11v5M14 11v5"></path>
        </svg>
        @break
    @case('folder')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H9l2 2h7.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9z"></path>
        </svg>
        @break
    @case('settings')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.86l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .91 1.7 1.7 0 0 0-.2 1.03V22a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-.2-1.03 1.7 1.7 0 0 0-1-.91 1.7 1.7 0 0 0-1.86.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.91-1 1.7 1.7 0 0 0-1.03-.2H2.5a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.03-.2 1.7 1.7 0 0 0 .91-1A1.7 1.7 0 0 0 4.6 8.9l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.91 1.7 1.7 0 0 0 .2-1.03V2.5a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 .2 1.03 1.7 1.7 0 0 0 1 .91A1.7 1.7 0 0 0 15.1 4.6l.06.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.27.29.63.46 1.02.46H20.9a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.02.46A1.7 1.7 0 0 0 19.4 15z"></path>
        </svg>
        @break
    @case('credit-card')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
            <path d="M2 10h20"></path>
        </svg>
        @break
    @case('mail')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"></path>
            <path d="M22 7L12 13 2 7"></path>
        </svg>
        @break
    @case('user-round')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M4 20a8 8 0 0 1 16 0"></path>
        </svg>
        @break
    @case('wrench')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a6 6 0 0 0-8.4 8.4l-1.8 1.8a2 2 0 1 0 2.8 2.8l1.8-1.8a6 6 0 0 0 8.4-8.4l-3.4 3.4a2.5 2.5 0 0 1-3.5-3.5l3.5-3.5z"></path>
        </svg>
        @break
    @case('help-circle')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M9.1 9a3 3 0 1 1 5.8 1c-.7 1.4-2.1 2-2.9 2.8-.5.5-.8 1-.8 1.7"></path>
            <circle cx="12" cy="17.5" r="0.8" fill="currentColor" stroke="none"></circle>
        </svg>
        @break
    @case('image')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
            <circle cx="8.5" cy="10.5" r="1.8"></circle>
            <path d="M21 15l-5-5L6 20"></path>
        </svg>
        @break
    @default
        {{-- unknown icon --}}
@endswitch
