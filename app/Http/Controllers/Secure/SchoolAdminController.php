<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\School;
use App\Models\SchoolAdmin;
use App\Models\User;
use App\Repositories\UserRepository;
use Datatables;
use Sentinel;
use App\Http\Requests\Secure\SchoolAdminRequest;

class SchoolAdminController extends SecureController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TeacherController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;

        view()->share('type', 'school_admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('school_admin.school_admin');
        return view('school_admin.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('school_admin.new');
        $school_ids = School::lists('title', 'id')->toArray();
        return view('school_admin.create', compact('title','school_ids'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(SchoolAdminRequest $request)
    {
        $user = Sentinel::registerAndActivate($request->all());

        $role = Sentinel::findRoleBySlug('admin');
        $role->users()->attach($user);

        $user = User::find($user->id);
        $user->update($request->except('password'));

        $school_admin = new SchoolAdmin();
        $school_admin->school_id = $request->school_id;
        $school_admin->user_id = $user->id;
        $school_admin->save();

        return redirect('/school_admin');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(User $school_admin)
    {
        // Title
        $title = trans('school_admin.details');
        $action = 'show';
        $school = SchoolAdmin::join('schools', 'schools.id', '=', 'school_admins.school_id')
            ->where('user_id', $school_admin->id)->select('schools.title')->first();
        return view('school_admin.show', compact('school_admin', 'title', 'action','school'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(User $school_admin)
    {
        $title = trans('school_admin.edit');
        $school_ids = School::lists('title', 'id')->toArray();
        $school = SchoolAdmin::where('user_id', $school_admin->id)->first();
        $school_id = isset($school)?$school->id:0;
        return view('school_admin.edit', compact('title', 'school_admin','school_ids','school_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(SchoolAdminRequest $request, $school_admin)
    {
        if($request->password!="")
        {
            $school_admin->password = bcrypt($request->password);
        }
        $school_admin->update($request->except('password'));

        SchoolAdmin::where('user_id','=', $school_admin->id)->delete();

        $schoolAdmin = new SchoolAdmin();
        $schoolAdmin->school_id = $request->school_id;
        $schoolAdmin->user_id = $school_admin->id;
        $schoolAdmin->save();

        return redirect('/school_admin');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(User $school_admin)
    {
        $title = trans('school_admin.delete');
        $school = SchoolAdmin::join('schools', 'schools.id', '=', 'school_admins.school_id')
            ->where('user_id', $school_admin->id)->select('schools.title')->first();
        return view('/school_admin/delete', compact('school_admin', 'title','school'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(User $school_admin)
    {
        SchoolAdmin::where('user_id','=', $school_admin->id)->delete();
        $school_admin->delete();
        return redirect('/school_admin');
    }

    public function data()
    {
        $school_admins = $this->userRepository->getUsersForRole('admin')
            ->map(function ($school_admin) {
                return [
                    'id' => $school_admin->id,
                    'full_name' => $school_admin->full_name,
                ];
            });
        return Datatables::of($school_admins)
            ->add_column('actions', '<a href="{{ url(\'/school_admin/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/school_admin/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/school_admin/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

}
