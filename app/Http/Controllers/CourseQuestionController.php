<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class CourseQuestionController extends Controller
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
    public function create(Course $course)
    {
        $students = $course->students()->orderBy('id', 'DESC')->get();
        return view('admin.questions.create', [
            'course' => $course,
            'students' => $students
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answers' => 'required|array',
            'answers.*' => 'required|string',
            'correct_answer' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            $question = $course->question()->create([
                'question' => $request->question,
            ]);

            foreach ($request->answers as $index => $answerText) {
                $isCorrect = ($request->correct_answer == $index);
                $question->answer()->create([
                    'answer' => $answerText,
                    'is_correct' => $isCorrect,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.courses.show', $course->id);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error!' . $e->getMessage()]
            ]);
            throw $error;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseQuestion $courseQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseQuestion $coursesQuestion)
    {
        $course = $coursesQuestion->course;

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found for this question.');
        }

        $students = $course->students()->orderBy('id', 'DESC')->get();
        $answers = $coursesQuestion->answer()->get();

        return view('admin.questions.edit', [
            'question' => $coursesQuestion,
            'course' => $course,
            'students' => $students,
            'answers' => $answers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseQuestion $coursesQuestion)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answers' => 'required|array',
            'answers.*' => 'required|string',
            'correct_answer' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            // Update question text
            $coursesQuestion->update([
                'question' => $request->question,
            ]);

            // Delete old answers
            $coursesQuestion->answer()->delete();

            // Create new answers
            foreach ($request->answers as $index => $answerText) {
                $isCorrect = ($request->correct_answer == $index);
                $coursesQuestion->answer()->create([
                    'answer' => $answerText,
                    'is_correct' => $isCorrect,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.courses.show', $coursesQuestion->course_id);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()]
            ]);
            throw $error;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseQuestion $coursesQuestion)
    {
        try {
            $coursesQuestion->delete();
            return redirect()->route('dashboard.courses.show', $coursesQuestion->course_id);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error!' . $e->getMessage()]
            ]);
            throw $error;
        }
    }
}
