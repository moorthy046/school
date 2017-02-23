<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Http\Requests\Secure\DeleteRequest;
use App\Http\Requests\Secure\TimetableRequest;
use App\Models\Direction;
use App\Models\Section;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Timetable;
use App\Repositories\StudentRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\TeacherSubjectRepository;
use App\Repositories\TimetableRepository;
use App\Repositories\UserRepository;
use Datatables;
use Illuminate\Http\Request;
use Session;
use Sentinel;
use DB;
use App\Http\Requests\Secure\StudentGroupRequest;
use App\Http\Controllers\Traits\TimeTableTrait;

class StudentGroupController extends SecureController
{
    use TimeTableTrait;
    /**
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * @var SubjectRepository
     */
    private $subjectRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;
    /**
     * @var TimetableRepository
     */
    private $timetableRepository;

    /**
     * StudentGroupController constructor.
     * @param StudentRepository $studentRepository
     * @param SubjectRepository $subjectRepository
     * @param UserRepository $userRepository
     * @param TeacherSubjectRepository $teacherSubjectRepository
     * @param TimetableRepository $timetableRepository
     */
    public function __construct(StudentRepository $studentRepository,
                                SubjectRepository $subjectRepository,
                                UserRepository $userRepository,
                                TeacherSubjectRepository $teacherSubjectRepository,
                                TimetableRepository $timetableRepository)
    {
        parent::__construct();

        $this->studentRepository = $studentRepository;
        $this->subjectRepository = $subjectRepository;
        $this->userRepository = $userRepository;
        $this->teacherSubjectRepository = $teacherSubjectRepository;
        $this->timetableRepository = $timetableRepository;

        view()->share('type', 'studentgroup');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Section $section)
    {
        $title = trans('studentgroup.new');
        $directions = [''=>trans('studentgroup.select_direction')] + Direction::lists('title', 'id')->toArray();
        return view('studentgroup.create', compact('title', 'directions', 'section'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StudentGroupRequest $request)
    {
        $studentgroup = new StudentGroup($request->all());
        $studentgroup->save();
        return redirect('/section/' . $request->section_id . '/groups');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Section $section, StudentGroup $studentgroup)
    {
        $title = trans('studentgroup.details');
        $action = 'show';
        return view('studentgroup.show', compact('studentgroup', 'title', 'action', 'section'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Section $section, StudentGroup $studentgroup)
    {
        $title = trans('studentgroup.edit');
        return view('studentgroup.edit', compact('title', 'studentgroup', 'section'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(StudentGroupRequest $request, StudentGroup $studentgroup)
    {
        $studentgroup->update($request->all());
        return redirect('/section/' . $request->section_id . '/groups');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(StudentGroup $studentgroup)
    {
        // Title
        $title = trans('studentgroup.delete');
        return view('/studentgroup/delete', compact('studentgroup', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(StudentGroup $studentgroup)
    {
        $studentgroup->delete();
        return redirect('/section/' . $studentgroup->section_id . '/groups');
    }

    public function students(Section $section, StudentGroup $studentgroup)
    {
        $title = trans('studentgroup.students');
        $students = $this->studentRepository
            ->getAllForSchoolYearAndSection(Session::get('current_school_year'),$studentgroup->section_id)
            ->get()
            ->map(function($student){
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                ];
            })->lists('name','id')->toArray();

        return view('studentgroup.students', compact('studentgroup', 'title', 'section', 'students'));
    }

    public function addstudents(Section $section, StudentGroup $studentgroup, Request $request)
    {
        if(isset($request['students_select']) && $request['students_select']!=null) {
            $studentgroup->students()->sync($request['students_select']);
        }
        return redirect('/section/' . $section->id . '/groups');
    }

    public function subjects(Section $section, StudentGroup $studentgroup)
    {
        $title = trans('studentgroup.subjects');
        $subjects = $this->subjectRepository
            ->getAllForDirectionAndClass($studentgroup->direction_id,$studentgroup->class)
            ->orderBy('order')
            ->get();

        $teachers = $this->userRepository->getUsersForRole('teacher')
            ->map(function($teacher)
            {
                return [
                    'id'=> $teacher->id,
                    'name'=> $teacher->full_name,
                ];
            })->lists('name','id')->toArray();

        $teacher_subject = array();
        foreach ($subjects as $item) {
            $teacher_subject[$item->id] =
                $this->teacherSubjectRepository->getAllForSubjectAndGroup($item->id,$studentgroup->id)
                    ->get()
                    ->lists('teacher_id')->toArray();
        }

        return view('studentgroup.subjects', compact('studentgroup', 'title', 'subjects', 'section', 'teachers', 'teacher_subject'));
    }

    public function addeditsubject(Subject $subject, StudentGroup $studentgroup, Request $request)
    {
        $this->teacherSubjectRepository->getAllForSubjectAndGroup($subject->id,$studentgroup->id)
            ->delete();

        if (!empty($request['teachers_select'])) {
            foreach ($request['teachers_select'] as $teacher) {
                $teacherSubject = new TeacherSubject;
                $teacherSubject->subject_id = $subject->id;
                $teacherSubject->school_year_id = Session::get('current_school_year');
                $teacherSubject->school_id = Session::get('current_school');
                $teacherSubject->student_group_id = $studentgroup->id;
                $teacherSubject->teacher_id = $teacher;
                $teacherSubject->save();
            }
        }
    }

    public function timetable(Section $section, StudentGroup $studentgroup)
    {
        $title = trans('studentgroup.timetable');
        $subject_list = $this->teacherSubjectRepository
            ->getAllForSchoolYearAndGroup(Session::get('current_school_year'),$studentgroup->id)
            ->with('teacher','subject')
            ->get()
            ->filter(function($teacherSubject) {
                return (isset($teacherSubject->subject) && isset($teacherSubject->teacher));
            })
            ->map(function($teacherSubject){
                return [
                    'id' => $teacherSubject->id,
                    'title' => isset($teacherSubject->subject)?$teacherSubject->subject->title:"",
                    'name' => isset($teacherSubject->teacher)?$teacherSubject->teacher->full_name:"",
                ];
            });
        $timetable = $this->timetableRepository
            ->getAllForTeacherSubject($subject_list);
        return view('studentgroup.timetable', compact('studentgroup', 'title', 'action', 'section', 'subject_list', 'timetable'));
    }

    public function addtimetable(Section $section, StudentGroup $studentgroup, TimetableRequest $request)
    {
        $timetable = new Timetable($request->all());
        $timetable->save();

        return $timetable->id;
    }

    public function deletetimetable(Section $section, StudentGroup $studentgroup, DeleteRequest $request)
    {
        $timetable = Timetable::find($request['id']);
        $timetable->delete();
    }

    public function getDuration(Request $request)
    {
        $direction = Direction::find($request['direction']);
        return isset($direction->duration)?$direction->duration:1;
    }
}
