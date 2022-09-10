<?php

namespace App\Http\Controllers;

use App\Helpers\ModelFilterHelper;
use App\Http\Requests\Course\CreateCourseRequest;
use App\Http\Requests\Course\DestroyCourseRequest;
use App\Http\Requests\Course\EditCourseRequest;
use App\Http\Requests\Course\IndexCourseRequest;
use App\Http\Requests\Course\ShowCourseRequest;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexCourseRequest $request
     *
     * @return View
     */
    public function index(IndexCourseRequest $request): View
    {
        //filters
        $filters = ModelFilterHelper::buildFilters($request, Course::$accepted_filters);

        $courses = $this->service->list();

        return view('course.index', compact('courses', 'filters'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CreateCourseRequest $request
     *
     * @return View
     */
    public function create(CreateCourseRequest $request): View
    {
        return view('course.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCourseRequest $request
     *
     * @return RedirectResponse
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param Course $course
     *
     * @param ShowCourseRequest $request
     *
     * @return View
     */
    /* public function show(ShowCourseRequest $request, Course $course): View
    {
        $this->service->read($course);

        return view('course.show', compact('course'));
    } */

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     *
     * @param EditCourseRequest $request
     *
     * @return View
     */
    public function edit(EditCourseRequest $request, Course $course): View
    {
        return view('course.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCourseRequest $request
     * @param Course $course
     *
     * @return RedirectResponse
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        try {
            $this->service->update($request->validated(), $course);
        } catch (\Exception $e) {
            return back()->withErrors(['noStore' => 'Não foi possível salvar o curso: ' . $e->getMessage()]);
        }

        return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Course $course
     *
     * @param DestroyCourseRequest $request
     *
     * @return RedirectResponse
     */
    public function destroy(DestroyCourseRequest $request, Course $course): RedirectResponse
    {
        try {
            $this->service->delete($course);
        } catch (\Exception $e) {
            return back()->withErrors(['noDestroy' => 'Não foi possível excluir o curso: ' . $e->getMessage()]);
        }

        return redirect()->route('courses.index')->with('success', 'Curso excluído com sucesso.');
    }
}
