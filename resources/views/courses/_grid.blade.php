<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse($courses as $course)
        <a href="{{ route('courses.show', $course) }}" wire:navigate class="block rounded-lg bg-white dark:bg-gray-800 p-4 shadow ring-1 ring-gray-200 dark:ring-gray-700 hover:ring-gray-300 dark:hover:ring-gray-600 transition">
            @if($course->image)
                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="mb-3 h-40 w-full rounded object-cover">
            @else
                <div class="mb-3 h-40 w-full rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-300">No image</div>
            @endif
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $course->title }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">{{ ucfirst($course->level) }}</p>
        </a>
    @empty
        <p class="col-span-full text-gray-500 dark:text-gray-300">{{ $emptyMessage }}</p>
    @endforelse
</div>
