<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\User;
use App\Repositories\UserRepository;
use Datatables;
use Session;
use DB;
use Sentinel;
use App\Http\Requests\Secure\TeacherRequest;

class TeacherController extends SecureController
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

        view()->share('type', 'teacher');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('teacher.teacher');
        return view('teacher.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('teacher.new');
        return view('teacher.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(TeacherRequest $request)
    {
        $user = Sentinel::registerAndActivate($request->all());

        $role = Sentinel::findRoleBySlug('teacher');
        $role->users()->attach($user);

        $user = User::find($user->id);
        $user->update($request->except('password'));

        return redirect('/teacher');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(User $teacher)
    {
        // Title
        $title = trans('teacher.details');
        $action = 'show';
        return view('teacher.show', compact('teacher', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(User $teacher)
    {
        $title = trans('teacher.edit');
        return view('teacher.edit', compact('title', 'teacher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(TeacherRequest $request, $user_id)
    {
        $teacher = User::find($user_id);
        if($request->password!="")
        {
            $teacher->password = bcrypt($request->password);
        }
        $teacher->update($request->except('password'));

        return redirect('/teacher');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(User $teacher)
    {
        $title = trans('teacher.delete');
        return view('/teacher/delete', compact('teacher', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect('/teacher');
    }

    public function data()
    {
        $teachers = $this->userRepository->getUsersForRole('teacher')
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'full_name' => $teacher->full_name,
                ];
            });
        return Datatables::of($teachers)
            ->add_column('actions', '<a href="{{ url(\'/teacher/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/teacher/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/teacher/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

}
