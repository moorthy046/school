<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\ParentStudent;
use App\Models\User;
use App\Repositories\ParentStudentRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use Datatables;
use Session;
use DB;
use Sentinel;
use App\Http\Requests\Secure\ParentRequest;

class ParentController extends SecureController
{
    /**
     * @var ParentStudentRepository
     */
    private $parentStudentRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * ParentController constructor.
     * @param ParentStudentRepository $parentStudentRepository
     * @param UserRepository $userRepository
     * @param StudentRepository $studentRepository
     */
    public function __construct(ParentStudentRepository $parentStudentRepository,
                                UserRepository $userRepository,
                                StudentRepository $studentRepository)
    {
        parent::__construct();

        $this->parentStudentRepository = $parentStudentRepository;
        $this->userRepository = $userRepository;
        $this->studentRepository = $studentRepository;

        view()->share('type', 'parent');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('parent.parent');
        return view('parent.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('parent.new');
        $students = $this->userRepository->getUsersForRole('student')
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                ];
            })->lists('name', 'id')->toArray();
        return view('parent.create', compact('title', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ParentRequest $request)
    {
        $user = Sentinel::registerAndActivate($request->except('student_id'));

        $role = Sentinel::findRoleBySlug('parent');
        $role->users()->attach($user);

        $user = User::find($user->id);
        $user->update($request->except('student_id','password'));

        foreach ($request['student_id'] as $student) {
            $parent = new ParentStudent();
            $parent->user_id_student = $student;
            $parent->user_id_parent = $user->id;
            $parent->activate = 1;
            $parent->save();
        }

        return redirect('/parent');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(ParentStudent $student_parent)
    {
        $title = trans('parent.details');
        $action = 'show';
        return view('parent.show', compact('student_parent', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(ParentStudent $student_parent)
    {
        $title = trans('parent.edit');
        $students = $this->userRepository->getUsersForRole('student')
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                ];
            })->lists('name', 'id')->toArray();
        $students_ids = $student_parent->student->id;
        return view('parent.edit', compact('title', 'user', 'students', 'student_parent','students_ids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(ParentRequest $request, $id)
    {
        $parentStudent = ParentStudent::find($id);

        $parentStudent->parent->update($request->only('student_id'));

        ParentStudent::where('user_id_parent', $parentStudent->parent->id)->delete();

        if (!empty($request->student_id)) {
            foreach ($request->student_id as $student) {
                $parent = new ParentStudent();
                $parent->user_id_student = $student;
                $parent->user_id_parent = $parentStudent->parent->id;
                $parent->activate = 1;
                $parent->save();
            }
        }

        return redirect('/parent');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(ParentStudent $student_parent)
    {
        $title = trans('parent.delete');
        return view('parent.delete', compact('student_parent', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(ParentStudent $student_parent)
    {
        $student_parent->delete();
        return redirect('/parent');
    }

    public function data()
    {
        $parents = $this->parentStudentRepository->getAll()
            ->with('student', 'parent')
            ->get()
            ->filter(function ($user) {
                return isset($user->parent->id);
            })
            ->map(function ($parent) {
                return [
                    'id' => $parent->id,
                    'parent' => isset($parent->parent) ? $parent->parent->full_name : "",
                    'student' => isset($parent->student->id) ? $parent->student->full_name : "",
                ];
            });
        return Datatables::of($parents)
            ->add_column('actions', '<a href="{{ url(\'/parent/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                   <a href="{{ url(\'/parent/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/parent/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

}
