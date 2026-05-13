@extends('layouts.app')

@section('main-class', 'flex-1 overflow-hidden flex flex-col p-0')

@section('content')
    {{-- Notifications --}}
    @if(session('success'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200 shadow-md">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->has('recipe_name'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200 shadow-md">
            {{ $errors->first('recipe_name') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="px-6 py-4 border-b bg-white flex items-center gap-3 shrink-0">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors p-1 -ml-1 rounded-md">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="text-blue-500">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Configuration</h1>
            <p class="text-sm text-gray-500">Manage machine components and production recipes</p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="ml-auto">
            @csrf
            <button type="submit"
                class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors cursor-pointer">
                Logout
            </button>
        </form>
    </div>

    <div class="flex flex-1 overflow-hidden">
        {{-- Sidebar --}}
        <div class="w-72 border-r bg-white flex flex-col overflow-y-auto p-4 gap-3 shrink-0">
            <form method="POST" action="{{ route('recipe.store') }}" class="flex flex-col gap-3">
                @csrf
                <input type="text" name="recipe_name" placeholder="Recipe name" class="input" />
                <button class="btn-dark">+ Add Recipe</button>
            </form>

            @if($recipes->isNotEmpty())
                <div class="flex flex-col gap-2 mt-1">
                    @foreach($recipes as $recipe)
                        <a href="{{ route('config') }}?recipe={{ $recipe->id }}">
                            <div class="px-3 py-2.5 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 hover:shadow-sm transition-all">
                                <div class="text-sm font-medium text-gray-800">{{ $recipe->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $recipe->recipeSteps->count() }} steps</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Content area --}}
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            @if(!$selected)
                <div class="h-full flex items-center justify-center select-none">
                    <div class="text-center">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-gray-400 text-sm">Select a recipe to configure</p>
                    </div>
                </div>
            @else
                {{-- Recipe title bar --}}
                <div class="bg-white border-b border-gray-200 px-8 py-5 shrink-0">
                    <h1 class="text-xl font-bold text-gray-900">{{ $selected->name }}</h1>
                    <p class="text-sm text-gray-400 mt-0.5">{{ $selected->recipeSteps->count() }} steps</p>
                </div>

                {{-- Scrollable steps area --}}
                <div class="flex-1 overflow-y-auto p-8">
                    <div class="max-w-2xl flex flex-col gap-3">

                        {{-- Existing steps --}}
                        @foreach($selected->recipeSteps as $step)
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 text-xs font-bold flex items-center justify-center shrink-0">
                                            {{ $step->step_order }}
                                        </span>
                                        <span class="text-sm font-semibold text-gray-700">Step {{ $step->step_order }}</span>
                                    </div>
                                    <form method="POST" action="{{ route('recipesteps.destroy', $step->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="px-4 py-4 grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Component</p>
                                        <p class="font-medium text-gray-800">{{ $step->asset->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Command</p>
                                        <p class="font-medium text-gray-800">{{ $step->command }}</p>
                                    </div>
                                    @if($step->parameters)
                                        <div class="col-span-2">
                                            <p class="text-xs text-gray-400 mb-0.5">Parameters</p>
                                            <p class="font-mono text-xs text-gray-700 bg-gray-50 px-2 py-1.5 rounded-md mt-0.5">{{ $step->parameters }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- Add step card --}}
                        <div class="bg-white rounded-xl border-2 border-dashed border-gray-200 overflow-hidden">
                            <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-500">New step</span>
                            </div>
                            <div class="px-4 py-4 flex flex-col gap-3">
                                <form method="GET" action="{{ route('config') }}">
                                    <input type="hidden" name="recipe" value="{{ $selected->id }}">
                                    <p class="text-xs text-gray-400 mb-1">Component</p>
                                    <select name="component" onchange="this.form.submit()" class="input">
                                        <option value="">Select component...</option>
                                        @foreach($components as $component)
                                            <option value="{{ $component['componentType'] }}"
                                                {{ $selectedComponentType === $component['componentType'] ? 'selected' : '' }}>
                                                {{ $component['componentType'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                @if($selectedComponentType)
                                    <form method="POST" action="{{ route('recipesteps.store') }}" class="flex flex-col gap-3">
                                        @csrf
                                        <input type="hidden" name="recipe_id" value="{{ $selected->id }}">
                                        <input type="hidden" name="component" value="{{ $selectedComponentType }}">
                                        <div>
                                            <p class="text-xs text-gray-400 mb-1">Command</p>
                                            <select name="command" class="input">
                                                @foreach($commands as $command)
                                                    <option value="{{ $command['name'] }}">{{ $command['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 mb-1">Parameters</p>
                                            <input type="text" name="parameters" placeholder="Optional" class="input" />
                                        </div>
                                        <button class="btn-dark">+ Add Step</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
