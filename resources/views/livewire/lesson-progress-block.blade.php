<div wire:key="lesson-progress-block">
    {{-- Progress bar --}}
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
            <span>Progress</span>
            <span>{{ $this->progress['completed'] }} / {{ $this->progress['total'] }} lessons</span>
        </div>
        <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
            <div class="h-full rounded-full bg-indigo-600 transition-all duration-500 ease-out" style="width: {{ $this->progress['percentage'] }}%"></div>
        </div>
    </div>

    {{-- Mark as completed button (only when enrolled and not yet completed) --}}
    @if($this->isEnrolled)
        @if($this->isCompleted)
            <p class="text-green-600 dark:text-green-400 font-medium">Completed</p>
        @else
            <div
                x-data="{ showModal: false }"
            >
                <button type="button" @click="showModal = true" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Mark as completed
                </button>
                <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-transition>
                    <div class="rounded-lg bg-white dark:bg-gray-800 p-6 shadow-xl max-w-sm mx-4" @click.outside="showModal = false">
                        <p class="text-gray-700 dark:text-gray-200">Mark this lesson as completed?</p>
                        <div class="mt-4 flex gap-2 justify-end">
                            <button type="button" @click="showModal = false" class="rounded bg-gray-200 dark:bg-gray-600 px-3 py-1.5 text-sm text-gray-800 dark:text-gray-200">Cancel</button>
                            <button type="button" wire:click="markCompleted" @click="showModal = false" class="rounded bg-indigo-600 px-3 py-1.5 text-sm text-white">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
