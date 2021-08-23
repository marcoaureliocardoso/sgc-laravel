<?php

namespace App\Http\Controllers;

use App\Models\Bond;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserTypeAssignment;
use App\Models\Role;
use App\Models\Course;
use App\Models\Pole;
use Illuminate\Http\Request;
use App\CustomClasses\SgcLogger;
use App\Models\Employee;
use App\Http\Requests\StoreBondRequest;
use App\Http\Requests\UpdateBondRequest;
use App\Models\EmployeeDocument;
use App\Models\BondDocument;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewBondNotification;
use App\Http\Requests\ReviewBondRequest;
use App\Notifications\BondImpededNotification;
use App\Notifications\NewRightsNotification;
use App\CustomClasses\ModelFilterHelpers;
use App\Notifications\RequestReviewNotification;

class BondController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bonds_query = Bond::with(['employee', 'course', 'role', 'pole']);

        //filters
        $filters = ModelFilterHelpers::buildFilters($request, Bond::$accepted_filters);
        $bonds_query = $bonds_query->AcceptRequest(Bond::$accepted_filters)->filter();

        //sort
        $bonds_query = $bonds_query->sortable(['updated_at' => 'desc']);

        //get paginate and add querystring on paginate links
        $bonds = $bonds_query->paginate(10);
        $bonds->appends($request->all());

        //write on log
        SgcLogger::writeLog('Bond');

        return view('bond.index', compact('bonds', 'filters'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        $poles = Pole::orderBy('name')->get();
        $bond = new Bond;

        SgcLogger::writeLog('Bond');

        return view('bond.create', compact('employees', 'roles', 'courses', 'poles', 'bond'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBondRequest $request)
    {
        $bond = new Bond;

        $bond->employee_id = $request->employees;
        $bond->role_id = $request->roles;
        $bond->course_id = $request->courses;
        $bond->pole_id = $request->poles;
        $bond->begin = $request->begin;
        $bond->end = $request->end;
        $bond->terminated_at = null;
        $bond->volunteer = $request->has('volunteer');
        $bond->impediment = true;
        $bond->impediment_description = 'Vínculo ainda não revisado';
        $bond->uaba_checked_at = null;

        $bond->save();

        $documents = EmployeeDocument::where('employee_id', $bond->employee_id)->get();
        foreach ($documents as $doc) {
            $bondDocument = new BondDocument();
            $bondDocument->original_name = $doc->original_name;
            $bondDocument->file_data = $doc->file_data;
            $bondDocument->document_type_id = $doc->documentType->id;
            $bondDocument->bond_id = $bond->id;

            $bondDocument->save();
        }

        SgcLogger::writeLog($bond);

        //Notificar assistentes
        //$grantorAssistants = UserType::with('users')->firstWhere('acronym', 'ass')->users;
        $ass_UT = UserType::firstWhere('acronym', 'ass');
        $grantorAssistants = User::where('active', true)->whereActiveUserType($ass_UT->id)->get();
        Notification::send($grantorAssistants, new NewBondNotification($bond));

        return redirect()->route('bonds.index')->with('success', 'Vínculo criado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bond  $bond
     * @return \Illuminate\Http\Response
     */
    public function show(Bond $bond)
    {
        $documents = $bond->bondDocuments;
        return view('bond.show', compact('bond', 'documents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bond  $bond
     * @return \Illuminate\Http\Response
     */
    public function edit(Bond $bond)
    {
        $employees = Employee::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        $poles = Pole::orderBy('name')->get();

        SgcLogger::writeLog($bond);

        return view('bond.edit', compact('employees', 'roles', 'courses', 'poles', 'bond'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bond  $bond
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBondRequest $request, Bond $bond)
    {
        $bond->employee_id = $request->employees;
        $bond->role_id = $request->roles;
        $bond->course_id = $request->courses;
        $bond->pole_id = $request->poles;
        $bond->begin = $request->begin;
        $bond->end = $request->end;
        $bond->volunteer = $request->has('volunteer');

        try {
            $bond->save();
        } catch (\Exception $e) {
            return back()->withErrors(['noStore' => 'Não foi possível salvar o vínculo: ' . $e->getMessage()]);
        }

        SgcLogger::writeLog($bond);

        return redirect()->route('bonds.index')->with('success', 'Vínculo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bond  $bond
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bond $bond)
    {
        SgcLogger::writeLog($bond);

        try {
            $bond->delete();
        } catch (\Exception $e) {
            return back()->withErrors(['noDestroy' => 'Não foi possível excluir o vínculo: ' . $e->getMessage()]);
        }

        return redirect()->route('bonds.index')->with('success', 'Vínculo excluído com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bond  $bond
     * @return \Illuminate\Http\Response
     */
    public function review(ReviewBondRequest $request, Bond $bond)
    {
        $bond->impediment = ($request->impediment == '1') ? true : false;
        $bond->impediment_description = $request->impedimentDescription;
        $bond->uaba_checked_at = now();

        try {
            $bond->save();
        } catch (\Exception $e) {
            return redirect()->route('bonds.show', $bond)->withErrors(['noStore' => 'Não foi possível salvar o vínculo: ' . $e->getMessage()]);
        }

        SgcLogger::writeLog($bond, 'edit');


        if ($bond->impediment == true) {

            //$academicSecretaries = UserType::with('users')->Where('acronym', 'sec')->first()->users;
            $sec_UT = UserType::firstWhere('acronym', 'sec');
            $sec_users = User::where('active', true)->whereActiveUserType($sec_UT->id)->get();

            // $courseBonds = Bond::with(['employee'])->where('course_id', $bond->course->id)->get();
            // $courseCoordinators = collect();
            // foreach ($courseBonds as $courseBond)
            //     if ($courseBond->employee->isCourseCoordinator() && !is_null($courseBond->employee->user))
            //         $courseCoordinators->push($courseBond->employee->user);
            $gra_UT = UserType::firstWhere('acronym', 'gra');
            $course_id = $bond->course->id;
            $gra_users = User::where('active', true)->whereActiveUserType($gra_UT->id)->whereUtaCourseId($course_id)->get();

            //$users = $academicSecretaries->merge($courseCoordinators);
            $users = $sec_users->merge($gra_users);

            Notification::send($users, new BondImpededNotification($bond));
        } else {
            //$users = UserType::with('users')->Where('acronym', 'ldi')->first()->users;
            $ldi_UT = UserType::firstWhere('acronym', 'ldi');
            $ldi_users = User::where('active', true)->whereActiveUserType($ldi_UT->id);

            Notification::send($ldi_users, new NewRightsNotification($bond));
        }

        return redirect()->route('bonds.show', $bond)->with('success', 'Vínculo atualizado com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bond  $bond
     * @return \Illuminate\Http\Response
     */
    public function requestReview(Request $request, Bond $bond)
    {
        //$academicSecretaries = UserType::with('users')->Where('acronym', 'sec')->first()->users;
        $sec_UT = UserType::firstWhere('acronym', 'sec');
        $sec_users = User::where('active', true)->whereActiveUserType($sec_UT->id)->get();

        // $courseBonds = Bond::with(['employee'])->where('course_id', $bond->course->id)->get();
        // $courseCoordinators = collect();
        // foreach ($courseBonds as $courseBond)
        //     if ($courseBond->employee->isCourseCoordinator() && !is_null($courseBond->employee->user))
        //         $courseCoordinators->push($courseBond->employee->user);
        $gra_UT = UserType::firstWhere('acronym', 'gra');
        $course_id = $bond->course->id;
        $gra_users = User::where('active', true)->whereActiveUserType($gra_UT->id)->whereUtaCourseId($course_id)->get();

        //$assistants = UserType::with('users')->Where('acronym', 'ass')->first()->users;
        $ass_UT = UserType::firstWhere('acronym', 'ass');
        $ass_users = User::where('active', true)->whereActiveUserType($ass_UT->id)->get();

        //$users = $academicSecretaries->merge($courseCoordinators)->merge($assistants);
        $users = $sec_users->merge($gra_users)->merge($ass_users);

        Notification::send($users, new RequestReviewNotification($bond));

        return redirect()->route('bonds.show', $bond->id)->with('success', 'Revisão de vínculo solicitada.');
    }
}
