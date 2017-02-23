<?php

namespace App\Http\Controllers\Secure;

use App\Events\Mark\MarkCreated;
use App\Http\Requests\Secure\AddMarkRequest;
use App\Http\Requests\Secure\DeleteRequest;
use App\Http\Requests\Secure\ExamGetRequest;
use App\Http\Requests\Secure\MarkGetRequest;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\MarkType;
use App\Models\MarkValue;
use App\Models\Semester;
use App\Repositories\ExamRepository;
use App\Repositories\MarkTypeRepository;
use App\Repositories\MarkValueRepository;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Support\Collection;
use App\Repositories\MarkRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherSubjectRepository;
use Datatables;
use Session;

class MarkController extends SecureController
{
    /**
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * @var MarkRepository
     */
    private $markRepository;
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;
    /**
     * @var ExamRepository
     */
    private $examRepository;
    /**
     * @var MarkValueRepository
     */
    private $markValueRepository;
    /**
     * @var MarkTypeRepository
     */
    private $markTypeRepository;

    /**
     * MarkController constructor.
     * @param StudentRepository $studentRepository
     * @param MarkRepository $markRepository
     * @param TeacherSubjectRepository $teacherSubjectRepository
     * @param ExamRepository $examRepository
     * @param MarkValueRepository $markValueRepository
     * @param MarkTypeRepository $markTypeRepository
     */
    public function __construct(StudentRepository $studentRepository,
                                MarkRepository $markRepository,
                                TeacherSubjectRepository $teacherSubjectRepository,
                                ExamRepository $examRepository,
                                MarkValueRepository $markValueRepository,
                                MarkTypeRepository $markTypeRepository)
    {
        parent::__construct();

        $this->studentRepository = $studentRepository;
        $this->markRepository = $markRepository;
        $this->teacherSubjectRepository = $teacherSubjectRepository;
        $this->examRepository = $examRepository;
        $this->markValueRepository = $markValueRepository;
        $this->markTypeRepository = $markTypeRepository;

        view()->share('type', 'mark');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('mark.marks');
        $students = $this->studentRepository->getAllForStudentGroup(Session::get('current_student_group'))
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                ];
            })->lists('name', 'id')->toArray();
        $subjects = ['' => trans('mark.select_subject')] +
            $this->teacherSubjectRepository
                ->getAllForSchoolYearAndGroupAndTeacher(Session::get('current_school_year'), Session::get('current_student_group'), $this->user->id)
                ->with('subject')
                ->get()
                ->filter(function ($subject) {
                    return (isset($subject->subject->title));
                })
                ->map(function ($subject) {
                    return [
                        'id' => $subject->subject_id,
                        'title' => $subject->subject->title
                    ];
                })->lists('title', 'id')->toArray();
        $marktype = $this->markTypeRepository->getAll()->get()->lists('title', 'id')->toArray();
        $markvalue = $this->markValueRepository->getAll()->get()->lists('title', 'id')->toArray();
        return view('mark.index', compact('title', 'students', 'subjects', 'marktype', 'markvalue'));
    }

    public function marksForSubjectAndDate(MarkGetRequest $request)
    {
        $marks = $this->markRepository->getAll()
            ->with('student', 'student.user', 'mark_type', 'mark_value', 'subject')
            ->get()
            ->filter(function ($marksItem) use ($request) {
                return ($marksItem->school_year_id == Session::get('current_school_year') &&
                    $marksItem->subject_id == $request->subject_id &&
                    Carbon::createFromFormat(Settings::get('date_format'),$marksItem->date) == Carbon::createFromFormat(Settings::get('date_format'),$request->date));
            })
            ->map(function ($mark) {
                return [
                    'id' => $mark->id,
                    'name' => isset($mark->student->user->full_name) ? $mark->student->user->full_name : "",
                    'mark_type' => isset($mark->mark_type) ? $mark->mark_type->title : '',
                    'mark_value' => isset($mark->mark_value) ? $mark->mark_value->title : '',
                ];
            });

        return json_encode($marks);
    }

    public function examsForSubject(ExamGetRequest $request)
    {
        return $this->examRepository->getAllForGroupAndSubject(Session::get('current_student_group'), $request['subject_id'])
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                ];
            })->lists('title', 'id')->toArray();
    }

    public function deletemark(DeleteRequest $request)
    {
        $mark = Mark::find($request['id']);
        $mark->delete();
    }

    public function addmark(AddMarkRequest $request)
    {
        $date = $request['date'];
        $semestar = Semester::where(function ($query) use ($date) {
            $query->where('start', '>=', $date)
                ->where('school_year_id', '=', Session::get('current_school_year'));
        })->orWhere(function ($query) use ($date) {
            $query->where('end', '<=', $date)
                ->where('school_year_id', '=', Session::get('current_school_year'));
        })->first();
        foreach ($request['students'] as $student_id) {
            $mark = new Mark($request->except('students', '_token'));
            $mark->teacher_id = $this->user->id;
            $mark->student_id = $student_id;
            $mark->school_year_id = Session::get('current_school_year');
            $mark->semester_id = isset($semestar->id) ? $semestar->id : 1;
            $mark->save();

            event(new MarkCreated($mark));
        }
    }

}
