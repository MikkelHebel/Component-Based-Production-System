@extends('layouts.app')

@section('content')
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-7 h-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h2.5l2-7 3.5 14 3.5-14 2 7H21" />
            </svg>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manufacturing Monitoring System</h1>
                <p class="text-sm text-gray-500">Real-time tracking of warehouse, AGV, and assembly operations</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php $btn = 'px-4 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer'; @endphp
            <button class="{{ $btn }} bg-red-500 hover:bg-red-600 text-white">Stop</button>
            <button class="{{ $btn }} bg-green-400 hover:bg-green-500 text-white">Start</button>
            <button class="{{ $btn }} border border-gray-300 hover:bg-gray-50 text-gray-700">Add to queue</button>
            <a href="{{ route('config') }}" class="{{ $btn }} flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Configuration
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="{{ $btn }} border border-gray-300 hover:bg-gray-50 text-gray-700">Logout</button>
            </form>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="mt-6 grid grid-cols-4 gap-5 pb-20">

        {{-- Production Queue --}}
        <div class="col-span-1 bg-white border border-gray-200 rounded-xl p-5 self-start">
            <h2 class="font-semibold text-gray-800 mb-5">Production Queue</h2>

            @forelse($activeBatches as $batch)
                @php $active = $batch->status === 'In Progress'; @endphp
                <div class="mb-5 last:mb-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $batch->recipe->name }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($batch->quantity) }} units</p>
                        </div>
                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium {{ $active ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $batch->status }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $batch->progress }}%"></div>
                    </div>
                    <p class="text-right text-xs text-gray-400">{{ $batch->progress }}% complete</p>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-10">No items in queue</p>
            @endforelse
        </div>

        {{-- Warehouse --}}
        <div class="col-span-3 bg-white border border-gray-200 rounded-xl p-5 self-start">
            <h2 class="font-semibold text-gray-800 mb-5">Warehouse</h2>

            @forelse($inventory as $items)
                @php
                    $asset = $items->first()->asset;
                    $dot = match($asset->connection_status) {
                        'connected'    => 'bg-green-500',
                        'disconnected' => 'bg-red-500',
                        default        => 'bg-gray-300',
                    };
                @endphp
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $asset->name }}</h3>
                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($items as $entry)
                            @php
                                [$dotClass, $badgeClass, $label] = match(true) {
                                    $entry->quantity === 0  => ['bg-red-500',    'bg-red-50 text-red-700 border-red-200',       'Out of Stock'],
                                    $entry->quantity <= 20  => ['bg-yellow-400', 'bg-yellow-50 text-yellow-700 border-yellow-200', 'Low Stock'],
                                    default                 => ['bg-green-500',  'bg-green-50 text-green-700 border-green-200',  'In Stock'],
                                };
                            @endphp
                            <div class="flex items-center justify-between py-3 px-1">
                                <div class="flex items-center gap-3">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $dotClass }}"></span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $entry->item->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $entry->quantity }} units &middot; Tray {{ $entry->tray_number }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-medium border {{ $badgeClass }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-10">No inventory data available</p>
            @endforelse
        </div>
    </div>
@endsection

@section('footer')
    @php
        [$statusDot, $statusText] = match($systemStatus) {
            'online'  => ['bg-green-500',  'System Online'],
            'partial' => ['bg-yellow-400', 'System Partially Online'],
            'offline' => ['bg-red-500',    'System Offline'],
            default   => ['bg-gray-400',   'Status Unknown'],
        };
    @endphp
    <div class="fixed bottom-4 inset-x-6 bg-white border border-gray-200 rounded-xl shadow-md px-5 py-3 flex items-center justify-between text-sm z-10">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $statusDot }}"></span>
                <span class="font-medium text-gray-600">{{ $statusText }}</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-400">
                @foreach($assets as $asset)
                    @php
                        $dot = match($asset->connection_status) {
                            'connected'    => 'bg-green-500',
                            'disconnected' => 'bg-red-500',
                            default        => 'bg-gray-300',
                        };
                    @endphp
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                        {{ $asset->name }}
                    </span>
                @endforeach
            </div>
        </div>
        <span class="text-gray-400" id="last-updated">Last updated: --:--:--</span>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('last-updated').textContent = 'Last updated: ' + new Date().toLocaleTimeString();
    setInterval(() => window.location.reload(), 15000);
</script>
@endpush
