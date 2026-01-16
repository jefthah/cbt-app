<?php

namespace App\Http\Controllers;

use App\Models\StudentAnswer;
use Illuminate\Http\Request;

class StudentAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $course, $question)
    {
        $request->validate([
            'system_answer_id' => 'required|exists:course_answers,id',
        ]);

        $user = auth()->user();

        $selectedAnswer = \App\Models\CourseAnswer::find($request->system_answer_id);

        if ($selectedAnswer->is_correct == 'correct') {
            $result = "Correct";
        } else {
            $result = "Wrong";
        }

        // Simpan jawaban siswa
        StudentAnswer::create([
            'user_id' => $user->id,
            'course_question_id' => $question,
            'answer' => $result,
        ]);

        // Cari pertanyaan berikutnya di kursus yang sama
        $nextQuestion = \App\Models\CourseQuestion::where('course_id', $course)
            ->where('id', '>', $question)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextQuestion) {
            return redirect()->route('dashboard.learning.course.show', [
                'course' => $course,
                'question' => $nextQuestion->id
            ]);
        } else {
            return redirect()->route('dashboard.learning.finished.course', $course);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAnswer $studentAnswer)
    {
        //
    }
}
