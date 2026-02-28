<div>
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/30">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('courses.show', $course) }}" wire:navigate class="text-sm text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">&larr; Back to {{ $course->title }}</a>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white mt-1">{{ $lesson->title }}</h1>
        </div>
    </header>
    <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Only this block re-renders when marking completed (progress bar + button) --}}
        @auth
            @livewire('lesson-progress-block', ['course' => $course, 'lesson' => $lesson])
        @endauth

        <div
            x-data="{
                player: null,
                autoMarked: false,
                init() {
                    this.$nextTick(() => {
                        const el = document.getElementById('lesson-video');
                        if (!el) return;
                        this.player = new Plyr(el, { hideControls: false });
                        this.player.on('ended', () => {
                            if (!this.autoMarked) {
                                this.autoMarked = true;
                                Livewire.dispatch('mark-lesson-completed');
                            }
                        });
                    });
                }
            }"
        >
            <div class="rounded-lg overflow-hidden bg-black mb-6">
                <video id="lesson-video" playsinline controls>
                    <source src="{{ $lesson->video_url }}" type="video/mp4">
                </video>
            </div>

            {{-- Prev/Next lesson navigation --}}
            @php
                $prev = $this->previousLesson;
                $next = $this->nextLesson;
            @endphp
            @if($prev || $next)
                <div class="mt-8 flex items-center justify-between gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                    @if($prev)
                        <a href="{{ route('courses.lessons.show', [$course, $prev]) }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            <span class="truncate">{{ $prev->title }}</span>
                        </a>
                    @else
                        <span></span>
                    @endif
                    @if($next)
                        <a href="{{ route('courses.lessons.show', [$course, $next]) }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition ml-auto">
                            <span class="truncate">{{ $next->title }}</span>
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>
