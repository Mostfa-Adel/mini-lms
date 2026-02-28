<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        $courses = Course::published()->get();
        return view('livewire.home-page', ['courses' => $courses])->layout('layouts.app');
    }
}
