<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Http\Requests\Secure\DeleteRequest;
use App\Http\Requests\Secure\TimetableRequest;
use App\Models\StudentGroup;
use App\Models\Timetable;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherSubjectRepository;
use App\Repositories\TimetableRepository;
use Illuminate\Support\Collection;
use Datatables;
use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\Traits\TimeTableTrait;

class TeacherGroupController extends SecureController
{
    use TimeTableTrait;
    /**
     * @var TimetableRepository
     */
    private $timetableRepository;
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * TeacherGroupController constructor.
     * @param TimetableRepository $timetableRepository
     * @param TeacherSubjectRepository $teacherSubjectRepository
     * @param StudentRepository $studentRepository
     */
    public function __construct(TimetableRepository $timetableRepository,
                                TeacherSubjectRepository $teacherSubjectRepository,
                                StudentRepository $studentRepository)
    {
        parent::__construct();

        $this->timetableRepository = $timetableRepository;
        $this->teacherSubjectRepository = $teacherSubjectRepository;
        $this->studentRepository = $studentRepository;

        view()->share('type', 'teachergroup');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(StudentGroup $teachergroup)
    {
        $title = trans('teachergroup.details');
        $action = 'show';
        return view('teachergroup.show', compact('teachergroup', 'title', 'action'));
    }

    public function index()
    {
        $title = trans('teachergroup.mygroups');
        return view('teachergroup.mygroup', compact('title'));
    }

    public function data()
    {
        $studentGroups = $this->teacherSubjectRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('student_group', 'student_group.direction')
            ->get()
            ->each(function ($teacherSubject) {
                if ($teacherSubject->teacher_id == $this->user->id && $teacherSubject->student_group->direction) {
                    return true;
                }
            })
            ->map(function($studentGroup){
                return [
                    'id' => $studentGroup->student_group->id,
                    'title' => $studentGroup->student_group->title,
                    'direction' => isset($studentGroup->student_group->direction->title)?$studentGroup->student_group->direction->title:"",
                    "class" => $studentGroup->student_group->class,
                ];
            });
        return Datatables::of($studentGroups->toBase()->unique())
            ->add_column('actions', '<a href="{{ url(\'/teachergroup/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                    <a href="{{ url(\'/teachergroup/\' . $id . \'/students\' ) }}" class="btn btn-success btn-sm">
                                            <i class="fa fa-users"></i> {{ trans("section.students") }}</a>
                                     <a href="{{ url(\'/teachergroup/\' . $id . \'/grouptimetable\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-calendar"></i>  {{ trans("teachergroup.timetable") }}</a>')
            ->remove_column('id')
            ->make();
    }

    public function students(StudentGroup $teachergroup)
    {
        $title = trans('teachergroup.students');
        $students_added =  $this->studentRepository->getAllForStudentGroup($teachergroup->id)->lists('user_id')->all();
        $students = $this->studentRepository->getAllForSchoolYear(Session::get('current_school_year'))
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->user_id,
                    'name' => $student->user->full_name
                ];
            })
            ->lists('name', 'id')->toArray();
        return view('teachergroup.students', compact('teachergroup', 'title', 'section', 'students','students_added'));
    }

    public function addstudents(StudentGroup $teachergroup, Request $request)
    {
        $teachergroup->students()->sync($request['students_select']);
        return redirect('/teachergroup');
    }

    public function grouptimetable(StudentGroup $teachergroup)
    {
        $title = trans('teachergroup.timetable');

        $school_year_id = Session::get('current_school_year');

        $subject_list = $this->teacherSubjectRepository
            ->getAllForSchoolYearAndGroup($school_year_id,$teachergroup->id)
            ->with('teacher','subject')
            ->get()
            ->filter(function($teacherSubject){
                return (isset($teacherSubject->subject) &&
                    $teacherSubject->teacher_id == $this->user->id &&
                    isset($teacherSubject->teacher));
            })
            ->map(function($teacherSubject){
                return [
                    'id' => $teacherSubject->id,
                    'title' => $teacherSubject->subject->title,
                    'name' => $teacherSubject->teacher->full_name,
                ];
            });
        $timetable = $this->timetableRepository
            ->getAllForTeacherSubject($subject_list);

        return view('teachergroup.timetable', compact('teachergroup', 'title', 'section', 'subject_list', 'timetable'));
    }

    public function addtimetable(TimetableRequest $request)
    {
        $timetable = new Timetable($request->all());
        $timetable->save();

        return $timetable->id;
    }

    public function deletetimetable(DeleteRequest $request)
    {
        $timetable = Timetable::find($request['id']);
        if (!is_null($timetable)) {
            $timetable->delete();
        }
    }

    public function timetable()
    {
        $title = trans('teachergroup.timetable');

        $school_year_id = Session::get('current_school_year');

        $studentgroups = new Collection([]);
        $studentGroupsList = $this->teacherSubjectRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('student_group', 'student_group.direction')
            ->get()
            ->each(function ($teacherSubject) {
                if ($teacherSubject->teacher_id == $this->user->id && $teacherSubject->student_group->direction) {
                    return true;
                }
            })
            ->map(function($studentGroup){
                return [
                    'id' => $studentGroup->student_group->id,
                    'title' => $studentGroup->student_group->title,
                    'direction' => isset($studentGroup->student_group->direction->title)?$studentGroup->student_group->direction->title:"",
                    "class" => $studentGroup->student_group->class,
                ];
            })->toBase()->unique();
        foreach($studentGroupsList as $items)
        {
            $studentgroups->push($items['id']);
        }
        $subject_list = $this->teacherSubjectRepository
            ->getAllForSchoolYearAndGroups($school_year_id,$studentgroups)
            ->with('teacher','subject')
            ->get()
            ->filter(function($teacherSubject){
                return (isset($teacherSubject->subject) &&
                    $teacherSubject->teacher_id == $this->user->id &&
                    isset($teacherSubject->teacher));
            })
            ->map(function($teacherSubject){
                return [
                    'id' => $teacherSubject->id,
                    'title' => $teacherSubject->subject->title,
                    'name' => $teacherSubject->teacher->full_name,
                ];
            });
        $timetable = $this->timetableRepository
            ->getAllForTeacherSubject($subject_list);

        return view('teachergroup.timetable', compact('title', 'action', 'subject_list', 'timetable'));
    }

}
