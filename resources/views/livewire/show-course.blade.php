<div>
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/30">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>
                <a href="{{ route('home') }}" wire:navigate class="text-sm text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">&larr; Back to courses</a>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mt-1">{{ $course->title }}</h1>
                <p class="text-gray-500 dark:text-gray-300">{{ ucfirst($course->level) }}</p>
            </div>
            @auth
                @if($this->isEnrolled)
                    <span class="rounded bg-green-100 dark:bg-green-900/40 px-3 py-1 text-sm text-green-800 dark:text-green-200">Enrolled</span>
                @else
                    <button wire:click="enroll" type="button" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Enroll
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" wire:navigate class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Log in to enroll</a>
            @endauth
        </div>
    </header>
    <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if($course->description)
            <p class="text-gray-600 dark:text-gray-300 mb-8">{{ $course->description }}</p>
        @endif
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Lessons</h2>
        <div x-data="{ openId: null }" class="space-y-2">
            @foreach($course->lessons as $lesson)
                <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                    <button
                        type="button"
                        @click="openId = openId === {{ $lesson->id }} ? null : {{ $lesson->id }}"
                        class="flex w-full items-center justify-between px-4 py-3 text-left"
                    >
                        <span class="font-medium text-gray-900 dark:text-white">{{ $lesson->title }}</span>
                        @if($lesson->is_free_preview)
                            <span class="rounded bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-600 dark:text-gray-300">Free preview</span>
                        @endif
                        <span x-show="openId === {{ $lesson->id }}" class="text-gray-400 dark:text-gray-400">&#9650;</span>
                        <span x-show="openId !== {{ $lesson->id }}" class="text-gray-400 dark:text-gray-400">&#9660;</span>
                    </button>
                    <div x-show="openId === {{ $lesson->id }}" x-transition class="border-t border-gray-100 dark:border-gray-700 px-4 py-3">
                        @if($lesson->is_free_preview || (Auth::check() && $this->isEnrolled))
                            <a href="{{ route('courses.lessons.show', [$course, $lesson]) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">Watch lesson &rarr;</a>
                        @else
                            <span class="text-gray-500 dark:text-gray-300">Enroll to access this lesson.</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</div>
