<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LearningController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();
        $my_courses = $user->courses()->orderBy('id', 'DESC')->get();
        return view('student.courses.index', [
            'my_courses' => $my_courses
        ]);
    }

    public function show($course)
    {
        $course = \App\Models\Course::with('questions')->find($course); // Eager load questions

        return view('student.courses.learning', [
            'course' => $course,
            'questions' => $course->questions()->orderBy('id', 'DESC')->get()
        ]);
    }
}
