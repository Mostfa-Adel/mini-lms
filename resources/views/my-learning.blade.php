@extends('layouts.app')

@section('content')
<div>
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-900/30">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">My learning</h1>
        </div>
    </header>
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @include('courses._grid', [
            'courses' => $courses,
            'emptyMessage' => "You haven't enrolled in any courses yet.",
        ])
        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    </main>
</div>
@endsection
