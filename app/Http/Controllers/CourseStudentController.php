<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Models\StudentAnswer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $students = $course->students()->orderBy('id', 'DESC')->get();
        $questionsCount = $course->questions()->count();

        foreach ($students as $student) {
            $studentAnswers = StudentAnswer::where('user_id', $student->id)
                ->whereHas('question', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->get();

            $answersCount = $studentAnswers->count();
            $correctAnswersCount = $studentAnswers->where('answer', 'Correct')->count();

            if ($answersCount == 0) {
                $student->status = 'Not started';
            } elseif ($answersCount < $questionsCount) {
                $student->status = 'Not started';
            } else {
                if ($questionsCount > 0 && ($correctAnswersCount / $questionsCount) >= 0.8) {
                    $student->status = 'Passed';
                } else {
                    $student->status = 'Not Passed';
                }
            }
        }

        return view('admin.students.index', [
            'course' => $course,
            'students' => $students
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        $students = $course->students()->orderBy('id', 'DESC')->get();
        return view('admin.students.add_student', [
            'course' => $course,
            'students' => $students
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $error = ValidationException::withMessages([
                'system error' => ['Email Student tidak tersedia']
            ]);
            throw $error;
        }

        $isEnrolled = $course->students()->where('users.id', $user->id)->exists();

        if ($isEnrolled) {
            $error = ValidationException::withMessages([
                'system_error' => ['Student sudah memiliki hak akses kelas!']
            ]);
            throw $error;
        }

        DB::beginTransaction();

        try {
            $course->students()->attach($user->id);
            DB::commit();
            return redirect()->route('dashboard.course.course_students.show', $course);
        } catch (\Throwable $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()]
            ]);
            throw $error;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseStudent $courseStudent)
    {
        //
    }
}
