<aside class="w-64 flex-shrink-0 bg-gray-800 text-gray-200 flex flex-col">
    <div class="h-16 flex items-center justify-center bg-gray-900 shadow-md">
        <h1 class="text-xl font-bold text-white tracking-wider">Laravel CRM</h1>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-1">
        {{-- TOMBOL ISTIRAHAT (DIPINDAHKAN KE ATAS) --}}
        @if(auth()->user()->role === 'agent')
        <div class="px-2 border-b border-gray-700 pb-4 mb-4">
            <form action="{{ route('status.toggle_break') }}" method="POST">
                @csrf
                @if(auth()->user()->status === 'on_break')
                    <button type="submit" class="btn btn-success btn-block">
                        Selesai Istirahat
                    </button>
                @else
                    <button type="submit" class="btn btn-warning btn-block">
                        Mulai Istirahat
                    </button>
                @endif
            </form>
        </div>
        @endif

        {{-- Menu Utama --}}
        <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('contacts.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('contacts.*') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm-9 5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            Kontak
        </a>
        <a href="{{ route('broadcast.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('broadcast.*') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.006 3 11.55c0 4.556 4.03 8.25 9 8.25z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25a9 9 0 006.364-2.636M12 20.25a9 9 0 01-6.364-2.636m12.728 0A9 9 0 0012 11.55a9 9 0 00-6.364 6.064" /></svg>
            Broadcast WhatsApp
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-1.78-4.125M15 15v-1a3 3 0 00-3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Manajemen User
        </a>
        <a href="{{ route('data.create') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('data.*') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
            Manajemen Data
        </a>
        <a href="{{ route('campaigns.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('campaigns.*') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            Manajemen Campaign
        </a>
        <a href="{{ route('reports.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-700 {{ request()->routeIs('reports.index') ? 'bg-gray-900' : '' }}">
            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
            Laporan
        </a>
        @endif
    </nav>
    
    <div class="px-2 py-4 border-t border-gray-700">
        <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();"
                    class="mt-2 flex items-center w-full px-2 py-2 text-sm font-medium rounded-md text-gray-400 hover:bg-gray-700 hover:text-white">
                <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Log Out
            </a>
        </form>
    </div>
</aside>