@extends('layouts.app')

@section('content')
<div>
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/30">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Career 180 – Courses</h1>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($courses as $course)
                <a href="{{ route('courses.show', $course) }}" wire:navigate class="block rounded-lg bg-white p-4 shadow ring-1 ring-gray-200 hover:ring-gray-300 transition">
                    @if($course->image)
                        <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="mb-3 h-40 w-full rounded object-cover">
                    @else
                        <div class="mb-3 h-40 w-full rounded bg-gray-200 flex items-center justify-center text-gray-500">No image</div>
                    @endif
                    <h2 class="font-semibold text-gray-900">{{ $course->title }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ ucfirst($course->level) }}</p>
                </a>
            @empty
                <p class="col-span-full text-gray-500">No published courses yet.</p>
            @endforelse
        </div>
    </main>
</div>
@endsection
