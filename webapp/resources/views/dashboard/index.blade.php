@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between">
        {{-- Title --}}
        <div class="flex items-center gap-3">
            <div class="text-blue-500">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h2.5l2-7 3.5 14 3.5-14 2 7H21" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manufacturing Monitoring System</h1>
                <p class="text-sm text-gray-500">Real-time tracking of warehouse, AGV, and assembly operations</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors cursor-pointer">
                Stop
            </button>
            <button class="px-4 py-2 bg-green-400 hover:bg-green-500 text-white text-sm font-semibold rounded-lg transition-colors cursor-pointer">
                Start
            </button>
            <button class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors cursor-pointer">
                Add to queue
            </button>
            <a href="{{ route('config') }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Configuration
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </div>
@endsection
