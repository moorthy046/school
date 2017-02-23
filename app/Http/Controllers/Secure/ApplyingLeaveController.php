<?php

namespace App\Http\Controllers\Secure;

use App\Models\ApplyingLeave;
use App\Repositories\ApplyingLeaveRepository;
use App\Repositories\TeacherSubjectRepository;
use Datatables;
use Session;
use App\Http\Requests\Secure\ApplyingLeaveRequest;

class ApplyingLeaveController extends SecureController
{
    /**
     * @var ApplyingLeaveRepository
     */
    private $applyingLeaveRepository;
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;

    /**
     * ApplyingLeaveController constructor.
     * @param ApplyingLeaveRepository $applyingLeaveRepository
     * @param TeacherSubjectRepository $teacherSubjectRepository
     */
    public function __construct(ApplyingLeaveRepository $applyingLeaveRepository,
                                TeacherSubjectRepository $teacherSubjectRepository)
    {
        parent::__construct();

        $this->applyingLeaveRepository = $applyingLeaveRepository;
        $this->teacherSubjectRepository = $teacherSubjectRepository;

        view()->share('type', 'applyingleave');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('applyingleave.applyingleaves');
        return view('applyingleave.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('applyingleave.new');

        $this->generateParam();

        return view('applyingleave.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request|ApplyingLeaveRequest $request
     * @return Response
     */
    public function store(ApplyingLeaveRequest $request)
    {
        $applyingLeave = new ApplyingLeave($request->all());
        $applyingLeave->parent_id = $this->user->id;
        $applyingLeave->school_year_id = Session::get('current_school_year');
        $applyingLeave->save();

        return redirect('/applyingleave');
    }

    /**
     * Display the specified resource.
     *
     * @param ApplyingLeave $applyingleave
     * @return Response
     * @internal param int $id
     */
    public function show(ApplyingLeave $applyingleave)
    {
        $title = trans('applyingleave.details');
        $action = 'show';
        return view('applyingleave.show', compact('applyingleave', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ApplyingLeave $applyingleave
     * @return Response
     * @internal param int $id
     */
    public function edit(ApplyingLeave $applyingleave)
    {
        $title = trans('applyingleave.edit');

        $this->generateParam();

        return view('applyingleave.edit', compact('title', 'applyingleave'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request|ApplyingLeaveRequest $request
     * @param ApplyingLeave $applyingleave
     * @return Response
     * @internal param int $id
     */
    public function update(ApplyingLeaveRequest $request, ApplyingLeave $applyingleave)
    {
        $applyingleave->update($request->all());
        return redirect('/applyingleave');
    }

    public function delete(ApplyingLeave $applyingleave)
    {
        $title = trans('applyingleave.delete');
        return view('/applyingleave/delete', compact('applyingleave', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ApplyingLeave $applyingleave
     * @return Response
     * @internal param int $id
     */
    public function destroy(ApplyingLeave $applyingleave)
    {
        $applyingleave->delete();
        return redirect('/applyingleave');
    }

    public function data()
    {
        if ($this->user->inRole('parent')) {
            $applyingleave = $this->applyingLeaveRepository
                ->getAllForStudentAndSchoolYear(Session::get('current_student_id'),Session::get('current_school_year'))
                ->with('student.user')
                ->get()
                ->map(function($item){
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'name' => isset($item->student->user)?$item->student->user->full_name:"",
                        'date' => $item->date,
                    ];
                });
            return Datatables::of($applyingleave)
                ->add_column('actions', '<a href="{{ url(\'/applyingleave/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/applyingleave/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/applyingleave/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
                ->remove_column('id')
                ->make();
        } else {
            $teacherSection = $this->teacherSubjectRepository
                ->getAllForSchoolYearAndGroup(Session::get('current_school_year'),Session::get('current_student_group'))
                ->with('student_group')
                ->get()
                ->filter(function($teacherSubject) {
                    return isset($teacherSubject->student_group);
                })
                ->map(function($teacherSubject){
                    return [
                        'section_id' => $teacherSubject->student_group->section_id,
                    ];
                })->lists('section_id')->toArray();

            $applyingleave = $this->applyingLeaveRepository->getAll()
                ->with('student')
                ->get()
                ->filter(function($item) use ($teacherSection){
                    foreach ($teacherSection as $section)
                    {
                        if(isset($item->student->section_id) &&
                            $item->student->section_id == $section)
                            return $item;
                    }
                })
                ->map(function($item){
                    return [
                       'id' => $item->id,
                        'title' => $item->title,
                        'student' => $item->student->user->full_name,
                        'date' => $item->date
                    ];
                });
            return Datatables::of($applyingleave)
                ->add_column('actions', '<a href="{{ url(\'/applyingleave/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
                ->remove_column('id')
                ->make();
        }

    }

    private function generateParam()
    {
        $student_id = Session::get('current_student_id');
        view()->share('student_id', $student_id);
    }
}
