<?php

namespace App\Http\Controllers;

use App\Models\StudentAnswer;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();
        $my_courses = $user->courses()->orderBy('id', 'DESC')->get();

        foreach ($my_courses as $course) {
            $totalQuestionCount = $course->questions()->count();

            $answeredQuestionIds = StudentAnswer::where('user_id', $user->id)
                ->whereHas('question', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->pluck('course_question_id')->toArray();

            $allQuestions = $course->questions()->orderBy('id', 'asc')->get();

            $firstUnansweredQuestion = $allQuestions->whereNotIn('id', $answeredQuestionIds)->first();

            if ($firstUnansweredQuestion) {
                $course->nextQuestionId = $firstUnansweredQuestion->id;
            } else {
                $course->nextQuestionId = null;
            }
        }
        return view('student.courses.index', [
            'my_courses' => $my_courses
        ]);
    }

    public function show($course, $question = null)
    {
        $course = \App\Models\Course::with('questions')->find($course);

        if ($question) {
            $currentQuestion = \App\Models\CourseQuestion::where('course_id', $course->id)->where('id', $question)->first();
        } else {
            $currentQuestion = $course->questions()->orderBy('id', 'asc')->first();
        }

        return view('student.courses.learning', [
            'course' => $course,
            'question' => $currentQuestion,
        ]);
    }

    public function learning_finished($course)
    {
        $course = \App\Models\Course::find($course);
        return view('student.courses.learning_finished', [
            'course' => $course
        ]);
    }

    public function learning_rapport($course)
    {
        $course = \App\Models\Course::with('questions')->find($course);

        $userId = auth()->id();
        $studentAnswers = \App\Models\StudentAnswer::where('user_id', $userId)
            ->whereHas('question', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->get();

        $totalQuestions = $course->questions->count();
        $correctAnswers = $studentAnswers->where('answer', 'Correct')->count();
        $passed = $correctAnswers == $totalQuestions;

        return view('student.courses.learning_rapport', [
            'course' => $course,
            'studentAnswers' => $studentAnswers,
            'totalQuestions' => $totalQuestions,
            'correctAnswers' => $correctAnswers,
            'passed' => $passed
        ]);
    }
}
