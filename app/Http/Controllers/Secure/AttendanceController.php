<?php

namespace App\Http\Controllers\Secure;

use App\Events\Attendance\AttendanceCreated;
use App\Http\Requests\Secure\AddAttendanceRequest;
use App\Http\Requests\Secure\AttendanceJustifiedRequest;
use App\Http\Requests\Secure\DeleteRequest;
use App\Http\Requests\Secure\AttendanceGetRequest;
use App\Models\Attendance;
use App\Models\Option;
use App\Models\Semester;
use App\Models\StudentGroup;
use App\Repositories\AttendanceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TimetableRepository;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Session;
use Datatables;
use Illuminate\Support\Collection;

class AttendanceController extends SecureController
{
    /**
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * @var TimetableRepository
     */
    private $timetableRepository;
    /**
     * @var AttendanceRepository
     */
    private $attendanceRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * AttendanceController constructor.
     * @param StudentRepository $studentRepository
     * @param TimetableRepository $timetableRepository
     * @param AttendanceRepository $attendanceRepository
     * @param OptionRepository $optionRepository
     */
    public function __construct(StudentRepository $studentRepository,
                                TimetableRepository $timetableRepository,
                                AttendanceRepository $attendanceRepository, 
                                OptionRepository $optionRepository)
    {
        parent::__construct();

        $this->studentRepository = $studentRepository;
        $this->timetableRepository = $timetableRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->optionRepository = $optionRepository;

        view()->share('type', 'attendance');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('attendance.attendances');
        $students = $this->studentRepository->getAllForStudentGroup(Session::get('current_student_group'))
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                ];
            })->lists('name', 'id')->toArray();

        $current_student_group = StudentGroup::find(Session::get('current_student_group'));
        $section_teacher = isset($current_student_group->section->section_teacher_id) ? $current_student_group->section->section_teacher_id : "";
        $justified_show = (!is_null($section_teacher) && $section_teacher == $this->user->id) ? 1 : 0;

        $justified_list = $this->optionRepository->getAllForSchool(Session::get('current_school'))
            ->where('category', 'justified')->get()
            ->map(function($option){
                return [
                    "title" => $option->title,
                    "value" => $option->value,
                ];
            })->lists('title','value')->toArray();

        $hour_list = $this->timetableRepository->getAll()
            ->with('teacher_subject')
            ->get()
            ->filter(function ($timetable) {
                return ($timetable->teacher_subject->teacher_id == $this->user->id &&
                    $timetable->week_day == date('N', strtotime("- 1 day",strtotime('now'))) &&
                    $timetable->teacher_subject->student_group_id == Session::get('current_student_group'));
            })
            ->map(function ($timetable) {
                return [
                    'id' => $timetable->id,
                    'hour' => $timetable->hour,
                ];
            })->lists('hour', 'id')->toArray();

        return view('attendance.index', compact('title', 'students', 'justified_list', 'justified_show', 'hour_list'));
    }

    public function hoursForDate(AttendanceGetRequest $request)
    {
        $request->date = date_format(date_create_from_format(Settings::get('date_format'), $request->date), 'd-m-Y');

        return $hour_list = $this->timetableRepository->getAll()
            ->with('teacher_subject')
            ->get()
            ->filter(function ($timetable) use ($request) {
                return ($timetable->teacher_subject->teacher_id == $this->user->id &&
                    $timetable->week_day == date('N', strtotime($request->date)) &&
                    $timetable->teacher_subject->student_group_id == Session::get('current_student_group'));
            })
            ->map(function ($timetable) {
                return [
                    'id' => $timetable->id,
                    'hour' => $timetable->hour,
                ];
            })->lists('hour', 'id')->toArray();
    }

    public function addAttendance(AddAttendanceRequest $request)
    {
        $date = date_format(date_create_from_format(Settings::get('date_format'), $request->date), 'd-m-Y');
        $semestar = Semester::where(function ($query) use ($date) {
            $query->where('start', '>=', $date)
                ->where('school_year_id', '=', Session::get('current_school_year'));
        })->orWhere(function ($query) use ($date) {
            $query->where('end', '<=', $date)
                ->where('school_year_id', '=', Session::get('current_school_year'));
        })->first();

        $subject = $hour_list = $this->timetableRepository->getAll()
            ->with('teacher_subject')
            ->get()
            ->filter(function ($timetable) use ($date) {
                return ($timetable->teacher_subject->teacher_id == $this->user->id &&
                    $timetable->week_day == date('N', strtotime($date)) &&
                    $timetable->teacher_subject->student_group_id == Session::get('current_student_group'));
            })
            ->map(function ($timetable) {
                return [
                    'id' => $timetable->teacher_subject->subject_id,
                ];
            })->first();
        if(isset($subject['id'])) {
            foreach ($request['students'] as $student_id) {
                foreach ($request['hour'] as $hour) {
                    $attendance = new Attendance($request->except('students', 'hour'));
                    $attendance->teacher_id = $this->user->id;
                    $attendance->student_id = $student_id;
                    $attendance->semester_id = isset($semestar->id) ? $semestar->id : 1;
                    $attendance->subject_id = $subject['id'];
                    $attendance->hour = $hour;
                    $attendance->school_year_id = Session::get('current_school_year');
                    $attendance->save();

                    event(new AttendanceCreated($attendance));
                }
            }
        }
    }

    public function attendanceForDate(AttendanceGetRequest $request)
    {
        $students = new Collection([]);
        $this->studentRepository->getAllForStudentGroup(Session::get('current_student_group'))
            ->each(function ($student) use ($students) {
                $students->push($student->id);
            });
        $attendances = $this->attendanceRepository->getAllForStudentsAndSchoolYear($students,Session::get('current_school_year'))
            ->with('student', 'student.user')
            ->orderBy('hour')
            ->get()
            ->filter(function ($attendance) use ($request){
                return (Carbon::createFromFormat(Settings::get('date_format'),$attendance->date) ==
                    Carbon::createFromFormat(Settings::get('date_format'),$request->date) &&
                    isset($attendance->student->user->full_name));
            })
            ->map(function($attendance){
                return [
                    'id' => $attendance->id,
                    'name' => $attendance->student->user->full_name,
                    'hour' => $attendance->hour,
                    'justified' => $attendance->justified,
                ];
            })->toArray();
        return json_encode($attendances);
    }

    public function deleteattendance(DeleteRequest $request)
    {
        $attendance = Attendance::find($request['id']);
        $attendance->delete();
    }

    public function justified(AttendanceJustifiedRequest $request)
    {
        $attendance = Attendance::find($request['id']);
        $attendance->justified = $request['justified'];
        $attendance->save();
    }

}
