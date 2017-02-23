<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\ReportRequest;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use App\Repositories\BehaviorRepository;
use App\Repositories\BookRepository;
use App\Repositories\BookUserRepository;
use App\Repositories\ExamRepository;
use App\Repositories\MarkRepository;
use App\Repositories\NoticeRepository;
use App\Repositories\OptionRepository;
use App\Repositories\SemesterRepository;
use App\Repositories\StudentFinalMarkRepository;
use App\Repositories\StudentGroupRepository;
use App\Repositories\StudentRepository;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Session;

class ReportController extends SecureController
{
    /**
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * @var ExamRepository
     */
    private $examRepository;
    /**
     * @var StudentGroupRepository
     */
    private $studentGroupRepository;
    /**
     * @var AttendanceRepository
     */
    private $attendanceRepository;
    /**
     * @var MarkRepository
     */
    private $markRepository;
    /**
     * @var BehaviorRepository
     */
    private $behaviorRepository;
    /**
     * @var BookRepository
     */
    private $bookRepository;
    /**
     * @var BookUserRepository
     */
    private $bookUserRepository;
    /**
     * @var NoticeRepository
     */
    private $noticeRepository;
    /**
     * @var SemesterRepository
     */
    private $semesterRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;
    /**
     * @var StudentFinalMarkRepository
     */
    private $studentFinalMarkRepository;

    /**
     * ReportController constructor.
     * @param StudentRepository $studentRepository
     * @param ExamRepository $examRepository
     * @param StudentGroupRepository $studentGroupRepository
     * @param AttendanceRepository $attendanceRepository
     * @param MarkRepository $markRepository
     * @param BehaviorRepository $behaviorRepository
     * @param BookRepository $bookRepository
     * @param BookUserRepository $bookUserRepository
     * @param NoticeRepository $noticeRepository
     * @param SemesterRepository $semesterRepository
     * @param OptionRepository $optionRepository
     * @param StudentFinalMarkRepository $studentFinalMarkRepository
     */
    public function __construct(StudentRepository $studentRepository,
                                ExamRepository $examRepository,
                                StudentGroupRepository $studentGroupRepository,
                                AttendanceRepository $attendanceRepository,
                                MarkRepository $markRepository,
                                BehaviorRepository $behaviorRepository,
                                BookRepository $bookRepository,
                                BookUserRepository $bookUserRepository,
                                NoticeRepository $noticeRepository,
                                SemesterRepository $semesterRepository,
                                OptionRepository $optionRepository,
                                StudentFinalMarkRepository $studentFinalMarkRepository)
    {
        parent::__construct();

        $this->studentRepository = $studentRepository;
        $this->examRepository = $examRepository;
        $this->studentGroupRepository = $studentGroupRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->markRepository = $markRepository;
        $this->behaviorRepository = $behaviorRepository;
        $this->bookRepository = $bookRepository;
        $this->bookUserRepository = $bookUserRepository;
        $this->noticeRepository = $noticeRepository;
        $this->semesterRepository = $semesterRepository;
        $this->optionRepository = $optionRepository;
        $this->studentFinalMarkRepository = $studentFinalMarkRepository;

        view()->share('type', 'report');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('report.report');
        $students = $this->studentRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->user_id,
                    'name' => $student->user->full_name
                ];
            })
            ->lists('name', 'id')->toArray();
        $exams = array();
        if ($this->user->inRole('teacher')) {
            $exams = $this->examRepository->getAllForGroup(Session::get('current_student_group'))
                ->with('subject')
                ->get()
                ->filter(function ($exam) {
                    return isset($exam->subject);
                })
                ->map(function ($exam) {
                    return [
                        'id' => $exam->id,
                        'name' => $exam->title . ' ' . $exam->subject->title,
                    ];
                })->lists('name', 'id')->toArray();
        }
        if ($this->user->inRole('admin')) {
            $exams = $this->examRepository->getAll()
                ->with('subject', 'student_group')
                ->get()
                ->filter(function ($exam) {
                    return (isset($exam->subject) &&
                        isset($exam->student_group) &&
                        $exam->student_group->school_year_id == Session::get('current_school_id'));
                })
                ->map(function ($exam) {
                    return [
                        'id' => $exam->id,
                        'name' => $exam->title . ' ' . $exam->subject->title,
                    ];
                })->lists('name', 'id')->toArray();
        }
        $start_date = $end_date = date('d.m.Y.');
        $report_type = $this->optionRepository->getAllForSchool(Session::get('current_school'))
            ->where('category', 'report_type')->get()
            ->map(function($option){
                return [
                    "title" => $option->title,
                    "value" => $option->value,
                ];
            })->lists('title','value')->toArray();
        return view('report.index', compact('title', 'students', 'start_date', 'end_date', 'exams','report_type'));
    }


    public function student(User $user)
    {
        $title = trans('report.report');
        $students = array();

        $students[$user->id] = $user->full_name;
        $student = Student::where('user_id', $user->id)
            ->where('school_year_id', Session::get('current_school_year'))
            ->first();

        $student_groups = new Collection([]);
        $stGroups = array();
        $this->studentGroupRepository->getAllForSchoolYearSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('students')
            ->get()
            ->each(function ($group) use ($student, $student_groups) {
                foreach ($group->students as $student_item) {
                    if ($student_item->id == $student->id) {
                        $student_groups->push($group);
                    }
                }
            });
        foreach ($student_groups as $group) {
            $stGroups[] = $group->id;
        }

        $exams = $this->examRepository->getAll()
            ->with('subject', 'student_group')
            ->whereIn('student_group_id', $stGroups)
            ->get()
            ->filter(function ($exam) use ($student) {
                return isset($exam->subject);
            })
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->title . ' - ' . $exam->subject->title,
                ];
            })->lists('name', 'id')->toArray();

        $start_date = $end_date = date('d.m.Y.');
        $report_type = $this->optionRepository->getAllForSchool(Session::get('current_school'))
            ->where('category', 'report_type')->get()
            ->map(function($option){
                return [
                    "title" => $option->title,
                    "value" => $option->value,
                ];
            })->lists('title','value')->toArray();
        return view('report.index', compact('title', 'students', 'start_date', 'end_date', 'exams','report_type'));
    }

    public function create(ReportRequest $request)
    {
        $data = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <html>
                    <head>
                        <style>
                        table {
                            border-collapse: collapse;
                             width:100%;
                        }

                        table, td, th {
                            border: 1px solid black;
                            text-align:center;
                            vertical-align:middle;
                        }
                        </style>
                    </head>
                <body><img src="'. url('uploads/site/thumb_'.Settings::get('logo')). '">'.Settings::get('name').'<hr>';
        switch ($request->report_type) {
            case 'list_attendances':
                $data .= $this->getListOfAttendances($request);
                break;
            case 'list_marks':
                $data .= $this->getListOfMarks($request);
                break;
            case 'list_exam_marks':
                $data .= $this->getListOfExamMarks($request);
                break;
            case 'list_behaviors':
                $data .= $this->getListOfBehaviors($request);
                break;
        }
        $data .= '</body></html>';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($data);
        return $pdf->stream();
    }

    private function getListOfAttendances($request)
    {
        $attendaces = new Collection([]);
        $this->attendanceRepository->getAll()
            ->with('student', 'student.user')
            ->orderBy('date')
            ->orderBy('hour')
            ->get()
            ->each(function ($attendaceItem) use ($request, $attendaces) {
                foreach ($request->student_id as $student_user_id) {
                    if ($student_user_id == $attendaceItem->student->user_id)
                        $attendaces->push($attendaceItem);
                }
            });
        $attendaces = $attendaces
            ->filter(function ($attendace) use ($request) {
                return ($attendace->school_year_id == Session::get('current_school_year') &&
                    $attendace->date >= date('Y-m-d', (strtotime($request->start_date))) &&
                    $attendace->date <= date('Y-m-d', (strtotime($request->end_date))));
            })
            ->map(function ($attendace) {
                return [
                    'date' => $attendace->date,
                    'justified' => $attendace->justified,
                    'hour' => $attendace->hour,
                    'name' => isset($attendace->student->user->full_name) ? $attendace->student->user->full_name : "",
                ];
            });
        $result = ' <h1>' . trans('report.list_attendances') . '</h1>
                    ' . $request['start_date'] . ' - ' . $request['end_date'] . '<br>
                    <table>
                        <thead>
                            <tr>
                            <th>' . trans('report.student') . '</th>
                            <th>' . trans('report.date') . '</th>
                            <th>' . trans('report.hour') . '</th>
                            <th>' . trans('report.justified') . '</th>
                            </tr>
                        </thead><tbody>';
        foreach ($attendaces as $item) {
            $result .= '<tr>
                                        <td>' . $item['name'] . '</td>
                                        <td>' . date('d.m.Y.', (strtotime($item['date']))) . '</td>
                                        <td>' . $item['hour'] . '</td>
                                        <td>' . $item['justified'] . '</td>
                                     </tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    private function getListOfMarks($request)
    {
        $marks = new Collection([]);
        $this->markRepository->getAll()
            ->with('student', 'student.user', 'mark_type', 'mark_value', 'subject')
            ->orderBy('date')
            ->get()
            ->each(function ($markItem) use ($request, $marks) {
                foreach ($request->student_id as $student_user_id) {
                    if ($student_user_id == $markItem->student->user_id)
                        $marks->push($markItem);
                }
            });
        $marks = $marks
            ->filter(function ($marksItem) use ($request) {
                return ($marksItem->school_year_id == Session::get('current_school_year') &&
                    $marksItem->date >= date('Y-m-d', (strtotime($request->start_date))) &&
                    $marksItem->date <= date('Y-m-d', (strtotime($request->end_date))));
            })
            ->map(function ($mark) {
                return [
                    'date' => $mark->date,
                    'mark_type' => isset($mark->mark_type) ? $mark->mark_type->title : '',
                    'mark_value' => isset($mark->mark_value) ? $mark->mark_value->title : '',
                    'subject' => isset($mark->subject) ? $mark->subject->title : '',
                    'name' => isset($mark->student->user->full_name) ? $mark->student->user->full_name : "",
                ];
            });
        $result = '<h1>' . trans('report.list_marks') . '</h1>
                    ' . $request['start_date'] . ' - ' . $request['end_date'] . '<br>
                    <table>
                        <thead>
                            <tr>
                            <th>' . trans('report.student') . '</th>
                            <th>' . trans('report.date') . '</th>
                            <th>' . trans('report.mark_value') . '</th>
                            <th>' . trans('report.mark_type') . '</th>
                            <th>' . trans('report.subject') . '</th>
                            </tr>
                        </thead><tbody>';
        foreach ($marks as $item) {
            $result .= '<tr>
                            <td>' . $item['name'] . '</td>
                            <td>' . date('d.m.Y.', (strtotime($item['date']))) . '</td>
                            <td>' . $item['mark_value'] . '</td>
                            <td>' . $item['mark_type'] . '</td>
                            <td>' . $item['subject'] . '</td>
                         </tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    private function getListOfExamMarks($request)
    {
        $marks = new Collection([]);
        $this->markRepository->getAll()
            ->with('student', 'student.user', 'mark_type', 'mark_value', 'subject')
            ->orderBy('date')
            ->get()
            ->each(function ($markItem) use ($request, $marks) {
                foreach ($request->student_id as $student_user_id) {
                    if ($student_user_id == $markItem->student->user_id)
                        $marks->push($markItem);
                }
            });
        $marks = $marks
            ->filter(function ($marksItem) use ($request) {
                return ($marksItem->school_year_id == Session::get('current_school_year') &&
                    $marksItem->exam_id == $request->exam_id);
            })
            ->map(function ($mark) {
                return [
                    'date' => $mark->date,
                    'mark_type' => isset($mark->mark_type) ? $mark->mark_type->title : '',
                    'mark_value' => isset($mark->mark_value) ? $mark->mark_value->title : '',
                    'subject' => isset($mark->subject) ? $mark->subject->title : '',
                    'name' => isset($mark->student->user->full_name) ? $mark->student->user->full_name : "",
                ];
            });
        $exam = Exam::find($request['exam_id'])->first();
        $result = '<h1>' . trans('report.list_marks_exam') . ' - ' . $exam->title . '</h1>
                    <table>
                        <thead>
                            <tr>
                            <th>' . trans('report.student') . '</th>
                            <th>' . trans('report.date') . '</th>
                            <th>' . trans('report.mark_value') . '</th>
                            <th>' . trans('report.mark_type') . '</th>
                            <th>' . trans('report.subject') . '</th>
                            </tr>
                        </thead><tbody>';
        foreach ($marks as $item) {
            $result .= '<tr>
                            <td>' . $item['name'] . '</td>
                            <td>' . date('d.m.Y.', (strtotime($item['date']))) . '</td>
                            <td>' . $item['mark_value'] . '</td>
                            <td>' . $item['mark_type'] . '</td>
                            <td>' . $item['subject'] . '</td>
                         </tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    private function getListOfBehaviors($request)
    {
        $behaviours = new Collection([]);
        $this->behaviorRepository->getAll()
            ->with('students', 'students.user')
            ->get()
            ->each(function ($behaviourItem) use ($request, $behaviours) {
                foreach ($request->student_id as $student_user_id) {
                    if (isset($behaviourItem->students)) {
                        foreach ($behaviourItem->students as $studentItem) {
                            if ($student_user_id == $studentItem->user_id &&
                                $studentItem->school_year_id == Session::get('current_school_year')
                            ) {
                                $behaviours->push($behaviourItem);
                            }
                        }
                    }
                }
            });
        $behaviours = $behaviours
            ->map(function ($behaviour) {
                return [
                    'behaviour' => $behaviour->title,
                    'name' => isset($behaviour->students->first()->user->full_name) ? $behaviour->students->first()->user->full_name : "",
                ];
            });

        $result = '<h1>' . trans('report.behaviours') . '</h1>
                    <table>
                        <thead>
                            <tr>
                            <th>' . trans('report.student') . '</th>
                            <th>' . trans('report.behaviour') . '</th>
                            </tr>
                        </thead><tbody>';
        foreach ($behaviours as $item) {
            $result .= '<tr>
                            <td>' . $item['name'] . '</td>
                            <td>' . $item['behaviour'] . '</td>
                         </tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    public function attendances(User $student_user)
    {
        $title = trans('report.attendances');
        $method = 'getAttendances';
        if (!$this->user->inRole('student')) {
            $student_user = User::find(Session::get('current_student_user_id'));
        }
        return view('report.list', compact('title', 'student_user', 'method'));
    }

    public function marks(User $student_user)
    {
        $title = trans('report.marks');
        $method = 'getMarks';
        if (!$this->user->inRole('student')) {
            $student_user = User::find(Session::get('current_student_user_id'));
        }
        return view('report.list', compact('title', 'student_user', 'method'));
    }

    public function notice(User $student_user)
    {
        $title = trans('report.notices');
        $method = 'getNotices';
        if (!$this->user->inRole('student')) {
            $student_user = User::find(Session::get('current_student_user_id'));
        }
        return view('report.list', compact('title', 'student_user', 'method'));
    }

    public function subjectbook(User $student_user)
    {
        $title = trans('report.subjectbook');
        $method = 'getSubjectBook';
        if ($this->user->inRole('parent')) {
            $student_user = User::find(Session::get('current_student_user_id'));
        }
        return view('report.list', compact('title', 'method', 'student_user'));
    }

    public function exams(User $student_user)
    {
        $title = trans('report.exams');
        $method = 'getSubjectExams';
        if (!$this->user->inRole('student')) {
            $student_user = User::find(Session::get('current_student_user_id'));
        }
        return view('report.list', compact('title', 'method', 'student_user'));
    }

    /**
     * @param User $user
     */
    public function getStudentSubjects(User $user)
    {
        return Subject::join('directions', 'subjects.direction_id', '=', 'directions.id')
            ->join('student_groups', function ($join) {
                $join->on('student_groups.direction_id', '=', 'directions.id');
                $join->on('student_groups.class', '=', 'subjects.class');
            })
            ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
            ->join('students', 'students.id', '=', 'student_student_group.student_id')
            ->where('students.user_id', $user->id)
            ->where('students.school_year_id', Session::get('current_school_year'))
            ->orderBy('subjects.class')
            ->orderBy('subjects.order')
            ->select('subjects.id', 'subjects.title')->lists('subjects.title', 'subjects.id')->toArray();
    }

    public function semesters(User $user)
    {
        return $this->semesterRepository->getAll()
            ->with('students')
            ->orderBy('start')
            ->get()
            ->filter(function ($semester) use ($user) {
                return ($semester->students->user_id = $user->id);
            })
            ->map(function ($semester) {
                return [
                    'id' => $semester->id,
                    'title' => $semester->title,
                ];
            })->lists('title', 'id')->toArray();
    }

    public function marksForSubject(User $user, Request $request)
    {
        $marks = $this->markRepository->getAll()
            ->with('student', 'student.user', 'mark_type', 'mark_value', 'subject')
            ->orderBy('date')
            ->get()
            ->filter(function ($mark_list) use ($request, $user) {
                return ($mark_list->school_year_id == Session::get('current_school_year') &&
                    $mark_list->subject_id == $request->subject_id &&
                    isset($mark_list->student->user) && $mark_list->student->user_id == $user->id &&
                    ((isset($request->semester_id)) ? $mark_list->semester_id == $request->semester_id : true));
            })
            ->map(function ($mark) {
                return [
                    'date' => $mark->date,
                    'mark_type' => isset($mark->mark_type) ? $mark->mark_type->title : '',
                    'mark_value' => isset($mark->mark_value) ? $mark->mark_value->title : '',
                ];
            })->toArray();

        $student = Student::where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))
            ->where('user_id', $user->id)->first();

        $final_marks = $this->studentFinalMarkRepository
            ->getAllForStudentSubjectSchoolYearSchool($student->id, $request->subject_id,
                Session::get('current_school_year'),Session::get('current_school'))
            ->with('mark_value')
            ->get()
            ->map(function ($final_mark) {
                return [
                    'date' => $final_mark->created_at->format(Settings::get('date_format')),
                    'mark_type' => trans('student_final_mark.final_mark'),
                    'mark_value' => isset($final_mark->mark_value) ? $final_mark->mark_value->title : '',
                ];
            })->toArray();
            return $marks + $final_marks;
    }

    public function attendancesForSubject(User $user, Request $request)
    {
        return $attendance = $this->attendanceRepository->getAll()
            ->with('student', 'student.user')
            ->orderBy('date')
            ->orderBy('hour')
            ->get()
            ->filter(function ($attendace) use ($request, $user) {
                return ($attendace->school_year_id == Session::get('current_school_year') &&
                    $attendace->subject_id == $request->subject_id &&
                    isset($attendace->student->user) && $attendace->student->user_id == $user->id &&
                    (isset($request->semester_id) ? $attendace->semester_id == $request->semester_id : ""));
            })
            ->map(function ($attendace) {
                return [
                    'date' => $attendace->date,
                    'justified' => $attendace->justified,
                    'hour' => $attendace->hour
                ];
            });
    }

    public function noticesForSubject(User $user, Request $request)
    {
        $notices = $this->noticeRepository->getAllForSchoolYear(Session::get('current_school_year'))
            ->with('student_group', 'student_group.students')
            ->orderBy('date')
            ->get()
            ->filter(function ($notice) use ($request) {
                return ($notice->subject_id == $request->subject_id && isset($notice->student_group->students));
            })
            ->map(function ($notice) {
                return [
                    'date' => $notice->date,
                    'title' => $notice->title,
                    'description' => $notice->description
                ];
            })
            ->toBase()->unique();
        return $notices;
    }

    public function getSubjectBook(Request $request)
    {
        return $this->bookRepository->getAll()
            ->get()
            ->filter(function ($book) use ($request) {
                return ($book->subject_id == $request->subject_id);
            })
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'publisher' => $book->publisher,
                    'version' => $book->version,
                    'quantity' => $book->quantity,
                    'author' => $book->author,
                    'title' => $book->title,
                    'issued' => $this->bookUserRepository->getAll()
                        ->get()
                        ->filter(function ($item) use ($book) {
                            return ($item->book_id == $book->id &&
                                (!is_null($item->get)) && is_null($item->back));
                        })->count()
                ];
            });
    }

    public function examForSubject(User $user, Request $request)
    {
        $student_groups = new Collection([]);
        $stGroups = array();
        $this->studentGroupRepository->getAllForSchoolYearSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('students', 'students.user')
            ->get()
            ->each(function ($group) use ($user, $student_groups) {
                foreach ($group->students as $student_item) {
                    if ($student_item->user->id == $user->id) {
                        $student_groups->push($group);
                    }
                }
            });
        foreach ($student_groups as $group) {
            $stGroups[] = $group->id;
        }

        return $this->examRepository->getAll()
            ->with('subject', 'student_group')
            ->whereIn('student_group_id', $stGroups)
            ->get()
            ->filter(function ($exam) use ($request) {
                return (isset($exam->subject) && $exam->subject_id == $request->subject_id);
            })
            ->map(function ($exam) {
                return [
                    'title' => $exam->title,
                    'subject' => $exam->subject->title,
                    'date' => $exam->date,
                    'description' => $exam->description,
                ];
            });
    }
}
