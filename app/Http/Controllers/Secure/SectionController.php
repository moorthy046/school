<?php

namespace App\Http\Controllers\Secure;

use App\Models\Student;
use App\Http\Requests;
use App\Models\Section;
use App\Models\StudentGroup;
use App\Repositories\SectionRepository;
use App\Repositories\StudentGroupRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use Sentinel;
use Datatables;
use Session;
use DB;
use App\Http\Requests\Secure\SectionRequest;

class SectionController extends SecureController
{
    /**
     * @var SectionRepository
     */
    private $sectionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * @var StudentGroupRepository
     */
    private $studentGroupRepository;

    /**
     * SectionController constructor.
     * @param SectionRepository $sectionRepository
     * @param UserRepository $userRepository
     * @param StudentRepository $studentRepository
     * @param StudentGroupRepository $studentGroupRepository
     */
    public function __construct(SectionRepository $sectionRepository,
                                UserRepository $userRepository,
                                StudentRepository $studentRepository,
                                StudentGroupRepository $studentGroupRepository)
    {
        parent::__construct();

        $this->sectionRepository = $sectionRepository;
        $this->userRepository = $userRepository;
        $this->studentRepository = $studentRepository;
        $this->studentGroupRepository = $studentGroupRepository;

        view()->share('type', 'section');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('section.section');
        return view('section.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('section.new');
        $teachers = $this->userRepository->getUsersForRole('teacher')
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        return view('section.create', compact('title','teachers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(SectionRequest $request)
    {
        $section = new Section($request->all());
        $section->school_id = Session::get('current_school');
        $section->save();
        return redirect('/section');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Section $section)
    {
        $title = trans('section.details');
        $action = 'show';
        return view('section.show', compact('section', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Section $section)
    {
        $title = trans('section.edit');
        $teachers = $this->userRepository->getUsersForRole('teacher')
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        return view('section.edit', compact('title', 'section','teachers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(SectionRequest $request, Section $section)
    {
        $section->update($request->all());
        return redirect('/section');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Section $section)
    {
        $title = trans('section.delete');
        return view('/section/delete', compact('section', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Section $section)
    {
        $section->delete();
        return redirect('/section');
    }

    public function data()
    {
        $sections = $this->sectionRepository->getAllForSchoolYearSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('teacher')
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'quantity' => $section->quantity,
                    'total' => $section->total->count(),
                    'full_name' => isset($section->teacher)?$section->teacher->full_name:"",
                ];
            });

        return Datatables::of($sections)
            ->add_column('actions', '<a href="{{ url(\'/section/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/section/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/section/\' . $id . \'/groups\' ) }}" class="btn btn-success btn-sm">
                                            <i class="fa fa-list-ul"></i> {{ trans("section.groups") }}</a>
                                     <a href="{{ url(\'/section/\' . $id . \'/students\' ) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-users"></i> {{ trans("section.students") }}</a>
                                     <a href="{{ url(\'/section/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function groups(Section $section)
    {
        $title = trans('section.groups');
        $id = $section->id;
        return view('section.groups', compact('title','id'));
    }

    public function groups_data(Section $section)
    {
        $studentGroups = $this->studentGroupRepository->getAllForSection($section->id)
            ->with('direction')
            ->get()
            ->map(function($studentGroup)
            {
                return [
                    'id'=> $studentGroup->id,
                    'title'=> $studentGroup->title,
                    'direction'=> isset($studentGroup->direction)?$studentGroup->direction->title:"",
                    'class'=> $studentGroup->class
                ];
            });

        return Datatables::of($studentGroups)
            ->add_column('actions', '<a href="{{ url(\'/studentgroup/'.$section->id.'/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/studentgroup/'.$section->id.'/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                    <a href="{{ url(\'/studentgroup/'.$section->id.'/\' . $id . \'/students\' ) }}" class="btn btn-success btn-sm">
                                            <i class="fa fa-users"></i> {{ trans("section.students") }}</a>
                                     <a href="{{ url(\'/studentgroup/'.$section->id.'/\' . $id . \'/subjects\' ) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-list-ol"></i> {{ trans("section.subjects") }}</a>
                                     <a href="{{ url(\'/studentgroup/'.$section->id.'/\' . $id . \'/timetable\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-calendar"></i>  {{ trans("studentgroup.timetable") }}</a>
                                    <a href="{{ url(\'/studentgroup/'.$section->id.'/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function students(Section $section)
    {
        $title = trans('section.students');
        $id = $section->id;
        return view('section.students', compact('title','id'));
    }

    public function students_data(Section $section)
    {
        $students = $this->studentRepository
            ->getAllForSchoolYearAndSection(Session::get('current_school_year'),$section->id)
            ->with('user')
            ->orderBy('students.order')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'full_name' => isset($student->user)?$student->user->full_name:"",
                    'order' => $student->order,
                ];
            });

        return Datatables::of($students)
            ->add_column('actions', '<a href="{{ url(\'/student/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/student/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                    <a href="{{ url(\'/student/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }
}
