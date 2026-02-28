@extends('layouts.app')

@section('content')
<div>
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/30">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">My certificates</h1>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if($certificates->isEmpty())
            <p class="text-gray-500 dark:text-gray-300">You don't have any certificates yet. Complete a course to earn one.</p>
        @else
            <ul class="space-y-4">
                @foreach($certificates as $certificate)
                    <li class="rounded-lg bg-white dark:bg-gray-800 p-4 shadow ring-1 ring-gray-200 dark:ring-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h2 class="font-semibold text-gray-900 dark:text-white">{{ $certificate->course->title }}</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                                    Issued {{ $certificate->issued_at->format('F j, Y') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-400 font-mono">ID: {{ $certificate->uuid }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </main>
</div>
@endsection
