<?php

namespace App\Http\Controllers;

use App\CustomClasses\ModelFilterHelpers;
use App\Http\Requests\ImportApprovedsFileRequest;
use App\Http\Requests\StoreApprovedsRequest;
use App\Models\Approved;
use App\Models\ApprovedState;
use App\Models\Course;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Gender;
use App\Models\MaritalStatus;
use App\Models\Pole;
use App\Models\Role;
use App\Models\State;
use App\Services\ApprovedService;
use App\Services\ApprovedsSourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovedController extends Controller
{
    public function __construct(ApprovedService $approvedService, ApprovedsSourceService $fileService)
    {
        $this->service = $approvedService;
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //check access permission
        if (! Gate::allows('approved-list')) {
            abort(403);
        }

        //filters
        $filters = ModelFilterHelpers::buildFilters($request, Approved::$accepted_filters);

        //get approved states
        $approvedStates = ApprovedState::all();

        $approveds = $this->service->list();

        return view('approved.index', compact('approveds', 'approvedStates', 'filters'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createStep1() // import spreadsheet file view
    {
        //check access permission
        if (! Gate::allows('approved-store')) {
            abort(403);
        }

        return view('approved.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function storeStep1(ImportApprovedsFileRequest $request)
    {
        //check access permission
        if (! Gate::allows('approved-store')) {
            abort(403);
        }

        try {
            $importedApproveds = $this->fileService->importApprovedsFromFile($request->file('file'));

            session(['approveds' => $importedApproveds]);
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route('approveds.create.step2')->with('success', 'Arquivo importado com sucesso.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createStep2() // import spreadsheet file view
    {
        //check access permission
        if (! Gate::allows('approved-store')) {
            abort(403);
        }

        $roles = Role::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        $poles = Pole::orderBy('name')->get();

        $importedApproveds = session('approveds');

        return view('approved.review', compact('importedApproveds', 'roles', 'courses', 'poles'));
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     *
     * @return void
     */
    public function storeStep2(StoreApprovedsRequest $request)
    {
        //check access permission
        if (! Gate::allows('approved-store')) {
            abort(403);
        }

        try {
            $this->service->batchStore($request->validated());
        } catch (\Exception $e) {
            return back()->withErrors(['noStore' => 'Não foi possível salvar o(s) aprovado(s): ' . $e->getMessage()]);
        }

        session()->forget('approveds');

        return redirect()->route('approveds.index')->with('success', 'Aprovados importados com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Approved  $approved
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Approved $approved)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Approved  $approved
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Approved $approved)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Approved  $approved
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Approved $approved)
    {
        //check access permission
        if (! Gate::allows('approved-update-state')) {
            abort(403);
        }

        try {
            $this->service->changeState($request->all(), $approved);
        } catch (\Exception $e) {
            return back()->withErrors(['noStore' => 'Não foi possível salvar o Aprovado: ' . $e->getMessage()]);
        }

        return redirect()->route('approveds.index')->with('success', 'Aprovado alterado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Approved  $approved
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Approved $approved)
    {
        //check access permission
        if (! Gate::allows('approved-destroy')) {
            abort(403);
        }

        try {
            $this->service->delete($approved);
        } catch (\Exception $e) {
            return back()->withErrors(['noDestroy' => 'Não foi possível excluir o Aprovado: ' . $e->getMessage()]);
        }

        return redirect()->route('approveds.index')->with('success', 'Aprovado retirado da lista.');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Approved $approved
     *
     * @return void
     */
    public function designate(Request $request, Approved $approved)
    {
        //check access permission
        if (! Gate::allows('approved-designate')) {
            abort(403);
        }

        $existantEmployee = Employee::where('email', $approved->email)->first();
        if ($existantEmployee) {
            return redirect()->route('approveds.index')->withErrors(['employeeAlreadyExists' => 'Já existe Colaborador no sistema com o mesmo email do Aprovado.']);
        }

        $genders = Gender::orderBy('name')->get();
        $birthStates = State::orderBy('name')->get();
        $documentTypes = DocumentType::orderBy('name')->get();
        $maritalStatuses = MaritalStatus::orderBy('name')->get();
        $addressStates = State::orderBy('name')->get();

        // Create a temporary object Employee to fill with the approved current data
        $employee = new Employee(
            [
                'name' => $approved->name,
                'email' => $approved->email,
                'area_code' => $approved->area_code,
                'phone' => $approved->phone,
                'mobile' => $approved->mobile,
            ]
        );

        $fromApproved = true;

        return view('approved.designate', compact('genders', 'birthStates', 'documentTypes', 'maritalStatuses', 'addressStates', 'employee', 'fromApproved'));
    }
}
