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
            <form method="POST" action="{{ route('batches.stop') }}">
                @csrf
                <button type="submit" class="{{ $btn }} bg-red-500 hover:bg-red-600 text-white">Stop</button>
            </form>
            <form method="POST" action="{{ route('batches.start') }}">
                @csrf
                <button type="submit" class="{{ $btn }} bg-green-400 hover:bg-green-500 text-white">Start</button>
            </form>
            <button onclick="document.getElementById('queue-modal').classList.remove('hidden')"
                    class="{{ $btn }} border border-gray-300 hover:bg-gray-50 text-gray-700">Add to queue</button>
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
        <div class="col-span-1 space-y-4 self-start">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-5">Production Queue</h2>

                @forelse($activeBatches as $batch)
                    @php $active = $batch->status === 'In Progress'; @endphp
                    <div class="mb-5 last:mb-0" data-batch="{{ $batch->id }}">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $batch->recipe->name }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($batch->quantity) }} units</p>
                            </div>
                            <span data-status-badge
                                  class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium {{ $active ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $batch->status }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                            <div data-progress-bar class="bg-blue-500 h-2 rounded-full" style="width: {{ $batch->progress }}%"></div>
                        </div>
                        <p data-progress-text class="text-right text-xs text-gray-400">{{ $batch->progress }}% complete</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-10">No items in queue</p>
                @endforelse
            </div>

            {{-- Completed Batches --}}
            @if($completedBatches->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Completed</h2>
                @foreach($completedBatches as $batch)
                    @php
                        $badgeClass = match($batch->status) {
                            'Done'      => 'bg-green-100 text-green-700',
                            'Error'     => 'bg-red-100 text-red-700',
                            'Cancelled' => 'bg-yellow-100 text-yellow-700',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $batch->recipe->name }}</p>
                            <p class="text-xs text-gray-400">{{ $batch->end_time ? \Carbon\Carbon::parse($batch->end_time)->diffForHumans() : '—' }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $badgeClass }}">{{ $batch->status }}</span>
                    </div>
                @endforeach
            </div>
            @endif
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

{{-- Queue Modal --}}
<div id="queue-modal" class="hidden fixed inset-0 z-20 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/30" onclick="document.getElementById('queue-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-xl shadow-xl p-6 w-80 z-30">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Start Production</h2>
        <form method="POST" action="{{ route('batches.store') }}">
            @csrf
            <label class="block text-xs font-medium text-gray-600 mb-1">Product Preset</label>
            <select name="recipe_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Select a recipe</option>
                @foreach($recipes as $recipe)
                    <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                @endforeach
            </select>
            <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
            <input type="number" name="quantity" value="1" min="1" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="submit"
                    class="w-full bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                Queue Production
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const activeBatchIds = @json($activeBatches->pluck('id'));

    function updateClock() {
        document.getElementById('last-updated').textContent = 'Last updated: ' + new Date().toLocaleTimeString();
    }
    updateClock();

    async function pollProgress() {
        try {
            const res = await fetch('{{ route('batches.progress') }}');
            const batches = await res.json();

            const currentIds = batches.map(b => b.id).sort().join(',');
            const knownIds   = [...activeBatchIds].sort().join(',');

            if (currentIds !== knownIds) {
                window.location.reload();
                return;
            }

            batches.forEach(batch => {
                const container = document.querySelector(`[data-batch="${batch.id}"]`);
                if (!container) return;
                container.querySelector('[data-progress-bar]').style.width  = batch.progress + '%';
                container.querySelector('[data-progress-text]').textContent = batch.progress + '% complete';
                container.querySelector('[data-status-badge]').textContent  = batch.status;
            });

            updateClock();
        } catch (_) {}
    }

    setInterval(pollProgress, 2000);
</script>
@endpush
