<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseType;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\CustomClasses\SgcLogger;
use Illuminate\Http\Request;
use App\CustomClasses\ModelFilterHelpers;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //check access permission
        if (!Gate::allows('course-list')) return response()->view('access.denied')->setStatusCode(401);

        $courses_query = new Course();

        //filters
        $filters = ModelFilterHelpers::buildFilters($request, Course::$accepted_filters);
        $courses_query = $courses_query->AcceptRequest(Course::$accepted_filters)->filter();

        //sort
        $courses_query = $courses_query->sortable(['name' => 'asc'])->with('courseType');

        //get paginate and add querystring on paginate links
        $courses = $courses_query->paginate(10);
        $courses->appends($request->all());

        //write on log
        SgcLogger::writeLog(target: 'Course');

        return view('course.index', compact('courses', 'filters'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //check access permission
        if (!Gate::allows('course-store')) return response()->view('access.denied')->setStatusCode(401);

        $courseTypes = CourseType::orderBy('name')->get();
        $course = new Course;

        SgcLogger::writeLog(target: 'Course');

        return view('course.create', compact('courseTypes', 'course'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseRequest $request)
    {
        //check access permission
        if (!Gate::allows('course-store')) return response()->view('access.denied')->setStatusCode(401);

        $course = new Course;

        $course->name = $request->name;
        $course->description = $request->description;
        $course->course_type_id = $request->courseTypes;
        $course->begin = $request->begin;
        $course->end = $request->end;

        $course->save();

        SgcLogger::writeLog(target: $course);

        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        //check access permission
        if (!Gate::allows('course-show')) return response()->view('access.denied')->setStatusCode(401);

        SgcLogger::writeLog(target: $course);

        return view('course.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        //check access permission
        if (!Gate::allows('course-update')) return response()->view('access.denied')->setStatusCode(401);

        $courseTypes = CourseType::orderBy('name')->get();

        SgcLogger::writeLog(target: $course);

        return view('course.edit', compact('course', 'courseTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        //check access permission
        if (!Gate::allows('course-update')) return response()->view('access.denied')->setStatusCode(401);

        $course->name = $request->name;
        $course->description =  $request->description;
        $course->course_type_id = $request->courseTypes;
        $course->begin = $request->begin;
        $course->end = $request->end;

        try {
            $course->save();
        } catch (\Exception $e) {
            return back()->withErrors(['noStore' => 'Não foi possível salvar o curso: ' . $e->getMessage()]);
        }

        SgcLogger::writeLog(target: $course);

        return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        //check access permission
        if (!Gate::allows('course-destroy')) return response()->view('access.denied')->setStatusCode(401);

        SgcLogger::writeLog(target: $course);
        try {
            $course->delete();
        } catch (\Exception $e) {
            return back()->withErrors(['noDestroy' => 'Não foi possível excluir o curso: ' . $e->getMessage()]);
        }

        return redirect()->route('courses.index')->with('success', 'Curso excluído com sucesso.');
    }
}
