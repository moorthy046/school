<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AttendanceTrait;
use App\Http\Controllers\Traits\MarksTrait;
use App\Http\Controllers\Traits\SchoolYearTrait;
use App\Http\Controllers\Traits\TimeTableTrait;
use App\Models\ApplyingLeave;
use App\Models\BookUser;
use App\Models\Diary;
use App\Models\Exam;
use App\Models\Invoice;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\ParentStudent;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Dingo\Api\Routing\Helpers;
use Sentinel;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use DB;

/**
 * Parent endpoints, can be accessed only with role "parent"
 *
 * @Resource("Parent", uri="/parent")
 */
class ParentController extends Controller
{
    use Helpers;
    use TimeTableTrait;
    use MarksTrait;
    use AttendanceTrait;
    use SchoolYearTrait;

    /**
     * Schools for parent
     *
     * Get all schools for selected user
     *
     * @Get("/schools")
     * @Versions({"v1"})
     * @Request({"token": "foo", "school_id":1,"student_user_id":1,})
     * @Response(200, body={
        "current_school_item": "Primary school",
        "current_school": 1,
            "other_schools":
            {
                "id": 1,
                "title": "Primary school"
            }
        })
     */
    public function schools(Request $request)
    {
        $current_school = $request->input('school_id');
        $student_user_id = $request->input('student_user_id');

        return response()->json($this->currentSchoolStudent($current_school,$student_user_id), 200);
    }
    
    /**
     * School year for student
     *
     * Get all school years with current school year name and id and other school years that he/she can select.
     * This method use all roles because all data(students, sections, marks, behaviors,semesters,attendances) are depend on school year
     *
     * @Get("/school_years")
     * @Versions({"v1"})
     * @Request({"token": "foo", "school_year_id":1, "student_id":1,"school_id":1})
     * @Response(200, body={
            "current_school_value": "2014/2015",
            "current_school_id": 1,
            "other_school_years":
                {
                    "id": 1,
                    "title": "2014/2015"
                }
            })
     */
    public function schoolYears(Request $request)
    {
        $current_school_year = $request->input('school_year_id');
        $student_id = $request->input('student_id');
        $current_school = $request->input('school_id');
        return response()->json($this->currentSchoolYearParent($current_school_year,$student_id,$current_school), 200);
    }

    /**
     * Timetable for student
     *
     * Get timetable for student with getting his token and role student
     * This method return array of array: first array has number of hour, first subarray is array for number of day and
     * in that array have objects that represent subject and teacher that teaches
     *
     * @Get("/timetable")
     * @Versions({"v1"})
     * @Request({"token": "foo", "student_user_id":"5","school_year":"1"})
     * @Response (200, body={
                "timetable":
                    {"1":
                        {
                        "1": {"id": 10, "subject": "English", "teacher": "Test teacher 1"},
                        "2": {"id": 11, "subject": "Serbian", "teacher": "Test teacher 2"},
                        },
                    "2":
                        {
                        "1": {"id": 12, "subject": "History", "teacher": "Test teacher 2"},
                        "2": {"id": 13, "subject": "English", "teacher": "Test teacher 1"},
                        }
                }
        })
     */
    public function timetable(Request $request)
    {
        $data = array(
            'student_user_id' => $request->input('student_user_id'),
            'school_year' => $request->input('school_year'),
        );
        $rules = array(
            'student_user_id' => 'required|integer',
            'school_year' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
                return $this->studentsTimetable($request->input('student_user_id'),$request->input('school_year'));
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Timetable for parent for selected student, school year and day
     *
     * Get timetable classes for parent for selected student, school year and day (day: 1-Monday,... 7-Sunday)
     *
     * @Get("/timetable_day")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "school_year_id":"1","student_user_id":"1","day_id":"1"}),
     *      @Response(200, body={
                "timetable":
                    {
                        "1": {"id": 10, "subject": "English", "teacher": "Test teacher 1"},
                        "2": {"id": 11, "subject": "Serbian", "teacher": "Test teacher 2"},
                    }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * */
    public function timetableDay(Request $request)
    {
        $data = array(
            'school_year_id' => $request->input('school_year_id'),
            'student_user_id' => $request->input('student_user_id'),
            'day_id' => $request->input('day_id'),
        );
        $rules = array(
            'school_year_id' => 'required|integer',
            'student_user_id' => 'required|integer',
            'day_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return response()->json($this->studentsTimetableDayAPI($request->input('student_user_id'),$request->input('school_year_id'),$request->input('day_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * List of subjects and teachers for student
     *
     * Get list of subjects and teachers for student
     *
     * @Get("/subject_list")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "school_year_id":"1", "student_user_id":"1"}),
     *      @Response(200, body={
                "subject_list":
                    {
                        "id": 4,
                        "subject": "history",
                        "teacher": "Teacher 1"
                    },
                    {
                        "id": 1,
                        "subject": "english",
                        "teacher": "Teacher 2"
                    },
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * */
    public function subjectList(Request $request)
    {
        $data = array(
            'school_year_id' => $request->input('school_year_id'),
            'student_user_id' => $request->input('student_user_id'),
        );
        $rules = array(
            'school_year_id' => 'required|integer',
            'student_user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return response()->json($this->studentsTimetableSubjectsDayAPI($request->input('student_user_id'),$request->input('school_year_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Borrowed books
     *
     * Get all borrowed books
     *
     * @Get("/borrowed_books")
     * @Versions({"v1"})
     * @Transaction({
            @Request({"token": "foo", "student_user_id":"5"}),
            @Response(200, body={
                "borrowed_books":
                    {"title": "Book for mathematics",
                        "author": "Group of authors",
                        "id":"12",
                        "internal":"12-45",
                        "get": "2015-08-10"},
           @Response(500, body={"error":"not_valid_data"})
            })
    })
     */
    public function borrowedBooks(Request $request)
    {
        $data = array(
            'student_user_id' => $request->input('student_user_id'),
        );
        $rules = array(
            'student_user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $borrowed_books = BookUser::join('books', 'books.id', '=', 'book_users.book_id')
                ->where('book_users.user_id', $request['student_user_id'])
                ->whereNotNull('get')
                ->whereNull('back')
                ->select(array('books.title','books.id', 'books.internal','books.author', 'book_users.get'))->get()->toArray();
            return response()->json(['borrowed_books'=>$borrowed_books], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Attendances for student and dates
     *
     * Get all attendances for student between two dates
     *
     * @Get("/attendances")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1", "start_date":"2015-10-12", "end_date":"2015-10-12"}),
     *      @Response(200, body={
                "attendance": {
                    "2015-10-01": {
                        "2": "yes",
                        "3": "no"
                        },
                    }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function attendances(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
            'start_date' => $request->input('start_date'),
            'end_date'=>$request->input('end_date'),
        );
        $rules = array(
            'student_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $attendance = $this->listAttendanceStudentAPI($request->input('student_id'),$request->input('start_date'),$request->input('end_date'));

            return response()->json(['attendance' => $attendance], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Marks for student between two dates and subject
     *
     * Get all marks for student between two dates and subject
     *
     * @Get("/marks")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1", "subject_id":"12", "start_date": "2015-10-12","end_date": "2015-10-13"}),
            @Response(200, body={
                "marks": {
                    {
                        "id": 1,
                        "subject": "Subject Name",
                        "mark_type": "Oral",
                        "mark_value": "A+",
                        "exam": "Exam 1",
                        "date": "2015-06-10"
                    }
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *      })
     * })
     */
    public function marks(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
            'subject_id' => $request->input('subject_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        );
        $rules = array(
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $marks = $this->listMarksStudent($request->input('subject_id'),$request->input('start_date'),$request->input('end_date'),$request->input('student_id'));
            return response()->json(['marks' => $marks], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Invoices for user
     *
     * Get all invoices for user
     *
     * @Get("/invoices")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_user_id":"1"}),
     *      @Response(200, body={
            "invoices": {
                    "id": 1,
                    "amount": "10.5",
                    "title": "This is title of invoices",
                    "description": "This is description of invoices"
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function invoices(Request $request)
    {
        $data = array(
            'student_user_id' => $request->input('student_user_id'),
        );
        $rules = array(
            'student_user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoices = Invoice::where('user_id', $request->input('student_user_id'))
                ->orderBy('id', 'DESC')
                ->where('paid', '=', 0)
                ->select(array('id', 'amount','title', 'description','created_at'))->get();
            return response()->json(['invoices'=>$invoices], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Children for parent
     *
     * Get all children for parent
     *
     * @Get("/children")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "user_id":"1"}),
     *      @Response(200, body={
            "children": {
                    "student_name": "Student Name",
                    "student_user_id": "1"
                    }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function children(Request $request)
    {
        $data = array(
            'user_id' => $request->input('user_id'),
        );
        $rules = array(
            'user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $children = ParentStudent::join('users', 'users.id', '=', 'parent_students.user_id_student')
                ->where('user_id_parent', $request->input('user_id'))
                ->select(array( DB::raw('CONCAT(users.first_name, " ", users.last_name) as student_name'),
                    'user_id_student as student_user_id'))->get()->toArray();
            return response()->json(['children'=>$children], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Exams for student
     *
     * Get all exams for student
     *
     * @Get("/exams")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1"}),
     *      @Response(200, body={
            "exams": {
                {
                    "id": 1,
                    "title": "This is title of exam",
                    "subject": "English",
                    "date": "2015-06-08"
                }
            }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function exams(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $exams = Exam::join('subjects', 'subjects.id', '=', 'exams.subject_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'exams.student_group_id')
                ->where('student_student_group.student_id',$request->input('student_id'))
                ->select(array('exams.id', 'exams.title','subjects.title as subject','exams.date'))
                ->orderBy('exams.date', 'DESC')
                ->get()->toArray();
            return response()->json(['exams' => $exams], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Notices for student
     *
     * Get all notices for student
     *
     * @Get("/notices")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1"}),
     *      @Response(200, body={
                "notice": {
                    {
                        "id": 1,
                        "title": "This is title of notice",
                        "subject": "English",
                        "description": "This is description of notice",
                        "date": "2015-02-02"
                    }
                }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function notices(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $notices = Notice::join('subjects', 'subjects.id', '=', 'notices.subject_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'notices.student_group_id')
                ->where('student_student_group.student_id',$request->input('student_id'))
                ->select(array('notices.id', 'notices.title','subjects.title as subject','notices.description','notices.date'))
                ->orderBy('notices.date', 'DESC')
                ->get()->toArray();

            return response()->json(['notice' => $notices], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Dairies for student
     *
     * Get all diaries for student
     *
     * @Get("/diary")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1"}),
     *      @Response(200, body={
            "diaries": {
                {
                    "id": 1,
                    "title": "This is title of notice",
                    "subject": "English",
                    "description": "This is description of notice",
                    "hour": "2",
                    "date": "2015-02-02"
                }
            }
        }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function diary(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $dairies = Diary::join('subjects', 'subjects.id', '=', 'diaries.subject_id')
                ->join('teacher_subjects', 'teacher_subjects.subject_id','=','subjects.id')
                ->join('student_student_group', 'student_student_group.student_group_id','=','teacher_subjects.student_group_id')
                ->where('student_student_group.student_id',$request->input('student_id'))
                ->orderBy('diaries.date', 'DESC')
                ->orderBy('diaries.hour', 'DESC')
                ->select(array('diaries.id', 'diaries.title','subjects.title as subject','diaries.hour','diaries.date'))
                ->get()->toArray();

            return response()->json(['diaries' => $dairies], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Dairies for student and date
     *
     * Get all diaries for student and selected date
     *
     * @Get("/diary_date")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1", "date":"2015-10-10"}),
     *      @Response(200, body={
                "diaries": {
                    {
                    "id": 1,
                    "title": "This is title of notice",
                    "subject": "English",
                    "description": "This is description of notice",
                    "hour": "2",
                    "date": "2015-10-10"
                    }
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function diaryForDate(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $dairies = Diary::join('subjects', 'subjects.id', '=', 'diaries.subject_id')
                ->join('teacher_subjects', 'teacher_subjects.subject_id','=','subjects.id')
                ->join('student_student_group', 'student_student_group.student_group_id','=','teacher_subjects.student_group_id')
                ->where('student_student_group.student_id',$request->input('student_id'))
                ->where('diaries.date',$request->input('date'))
                ->orderBy('diaries.date', 'DESC')
                ->orderBy('diaries.hour', 'DESC')
                ->select(array('diaries.id', 'diaries.title','subjects.title as subject','diaries.hour','diaries.date'))
                ->get()->toArray();

            return response()->json(['diaries' => $dairies], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Exams for student and student
     *
     * Get exams for subject and student
     *
     * @Get("/exam_subject")
     * @Versions({"v1"})
     * @Transaction({
            @Request({"token": "foo", "student_id":"1", "subject_id":"1"}),
            @Response(200, body={
                "exams": {
                    {
                    "id": 1,
                    "title": "This is title of exam",
                    "subject": "English",
                    "description": "This is description of exam",
                    "date": "2015-02-02"
                    }
                }
            }),
            @Response(500, body={"error":"not_valid_data"})
            })
     * })
     */
    public function examForSubject(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
            'subject_id' => $request->input('subject_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $exams = Exam::join('subjects', 'subjects.id', '=', 'exams.subject_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'exams.student_group_id')
                ->join('students', 'students.id', '=', 'student_student_group.student_id')
                ->where('student_student_group.student_id', $request->input('student_id'))
                ->where('exams.subject_id', $request->input('subject_id'))
                ->orderBy('date')
                ->select('exams.id','exams.title', 'subjects.title as subject', 'exams.date', 'exams.description')
                ->get()->toArray();

            return response()->json(['exams' => $exams], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Exams for student group
     *
     * Get exams for student group
     *
     * @Get("/exam_group")
     * @Versions({"v1"})
     * @Transaction({
        @Request({"token": "foo", "student_group_id":"1", "student_id":"1"}),
        @Response(200, body={
            "exams": {
                    {
                    "id": 1,
                    "title": "This is title of exam",
                    "subject": "English",
                    "description": "This is description of exam",
                    "date": "2015-02-02"
                    }
                }
            }),
        @Response(500, body={"error":"not_valid_data"})
        })
     * })
     */
    public function examForGroup(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $exams = Exam::join('subjects', 'subjects.id', '=', 'exams.subject_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'exams.student_group_id')
                ->join('students', 'students.id', '=', 'student_student_group.student_id')
                ->where('student_student_group.student_id', $request->input('student_id'))
                ->where('exams.student_group_id', $request->input('student_group_id'))
                ->orderBy('date')
                ->select('exams.id','exams.title', 'subjects.title as subject', 'exams.date', 'exams.description')
                ->get()->toArray();

            return response()->json(['exams' => $exams], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get student id
     *
     * Get student id for user and school year
     *
     * @Get("/student_for_user")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "user_id":"1", "school_year_id":"1"}),
     *      @Response(200, body={
                "student_id":  "1"
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function studentForUser(Request $request)
    {
        $data = array(
            'user_id' => $request->input('user_id'),
            'school_year_id' => $request->input('school_year_id'),
        );
        $rules = array(
            'user_id' => 'required|integer',
            'school_year_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $student = Student::where('user_id', $request->input('user_id'))
                    ->where('school_year_id', $request->input('school_year_id'))->first();
            return response()->json(['student_id'=>isset($student)?$student->id:''], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get exams with totals marks
     *
     * @Get("/exam_marks")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1"}),
     *      @Response(200, body={
                "exams": {
                    {
                        "id": 1,
                        "title": "This is title of exam",
                        "subject": "English",
                        "date": "2015-02-02",
                        "total_marks": "5"
                    }
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function examMarks(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $exams = Exam::join('subjects', 'subjects.id','=', 'exams.subject_id')
                ->join('student_student_group', 'student_student_group.student_group_id','=', 'exams.student_group_id')
                ->where('student_student_group.student_id',$request->input('student_id'))
                ->orderBy('exams.date')
                ->select('exams.id', 'exams.title', 'subjects.title as subject',
                    DB::raw('(SELECT COUNT(id) FROM marks WHERE exam_id =exams.id) as total_marks'))->get()->toArray();

            return response()->json(['exams' => $exams], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Get marks for selected exam
     *
     * @Get("/exam_marks_details")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "exam_id":"1", "student_id":"1"}),
     *      @Response(200, body={
                "marks": {
                    {
                        "id": 1,
                        "title": "This is title of exam",
                        "description": "This is description of exam",
                        "subject": "English",
                        "date": "2015-02-02",
                        "marks": {
                            {
                                "id": "105",
                                "mark_value": "F",
                                "mark_type": "gk"
                            }
                        }
                    }
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function examMarksDetails(Request $request)
    {
        $data = array(
            'exam_id' => $request->input('exam_id'),
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'exam_id' => 'required|integer',
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $marks = Exam::join('subjects', 'subjects.id','=', 'exams.subject_id')
                ->where('exams.id',$request->input('exam_id'))
                ->orderBy('exams.date')
                ->select('exams.id', 'exams.title','exams.title','exams.description', 'subjects.title as subject')
                ->first()->toArray();
            $marks['marks'] = Mark::join('mark_values', 'mark_values.id','=', 'marks.mark_value_id')
                ->join('mark_types', 'mark_types.id','=', 'marks.mark_type_id')
                ->where('marks.exam_id', $request->input('exam_id'))
                ->where('marks.student_id',$request->input('student_id'))
                ->select('marks.id', 'mark_values.title as mark_value', 'mark_types.title as mark_type')
                ->get()->toArray();
            return response()->json(['marks' => $marks], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get all applying leave for selected student
     *
     * @Get("/applying_leave")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1"}),
     *      @Response(200, body={
                "applying_leave": {
                        {
                            "id": 1,
                            "title": "This is title of exam",
                            "description": "This is description of exam",
                            "date": "2015-02-02"
                        }
                }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function applyingLeave(Request $request){
        $data = array(
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $applying_leave = ApplyingLeave::join('parent_students', 'parent_students.user_id_parent', '=', 'applying_leaves.parent_id')
                ->where('applying_leaves.parent_id', '=', $user->id)
                ->where('parent_students.activate', '=', 1)
                ->where('applying_leaves.student_id', '=', $request->input('student_id'))
                ->whereNull('parent_students.deleted_at')
                ->orderBy('applying_leaves.date', 'DESC')
                ->select(array('applying_leaves.id', 'applying_leaves.title','applying_leaves.date','applying_leaves.description'))
                ->get()->toArray();
            return response()->json(['applying_leave' => $applying_leave], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Post applying leave for student
     *
     * @Post("/post_applying_leave")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1","title":"This is title", "date":"2015-06-08","description":"This is description"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function postApplyingLeave(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'description' => $request->input('description')
        );
        $rules = array(
            'student_id' => 'integer',
            'title' => 'required',
            'date' => 'required|date',
            'description' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $student = Student::find($request->input('student_id'));
            $user = JWTAuth::parseToken()->authenticate();
            $applying_leave = new ApplyingLeave($request->except('token'));
            $applying_leave->parent_id = $user->id;
            $applying_leave->school_year_id = $student->school_year_id;
            $applying_leave->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Delete diary
     *
     * @Post("/delete_applying_leave")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "applying_leave_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteApplyingLeave(Request $request)
    {
        $data = array(
            'applying_leave_id' => $request->input('applying_leave_id'),
        );
        $rules = array(
            'applying_leave_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $applying_leave = ApplyingLeave::find($request->input('applying_leave_id'));
            $applying_leave->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Get all fee details leave for selected student
     * (paid=1-payed , 0-not payed)
     *
     * @Get("/fee_details")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_user_id":"1"}),
     *      @Response(200, body={
                    "fee_details": {
                        "student_name": "Student  Name",
                        "terms": {
                            {
                                "id": "5",
                                "title": "fee",
                                "paid": "1",
                                "amount": "200.00",
                                "date": "2015-09-11 06:25:49"
                            },
                            {
                                "id": "6",
                                "title": "John Mid-Term",
                                "paid": "0",
                                "amount": "200.00",
                                "date": "2015-09-16 10:03:20"
                            }
                        },
                        "total_fee": "400.00",
                        "paid_fee": "300.00",
                        "balance_fee": "100.00"
                    }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function feeDetails(Request $request)
    {
        $data = array(
            'student_user_id' => $request->input('student_user_id'),
        );
        $rules = array(
            'student_user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $fee_details = array();
            $student_user_id = $request->input('student_user_id');
            $user = User::find($student_user_id);
            $fee_details['student_name'] = $user->first_name.' '.$user->last_name;
            $fee_details['terms'] = Invoice::where('user_id', '=',$student_user_id)->count();
            $total_fee = Invoice::where('user_id', '=',$student_user_id)->sum('amount');
            $paid_fee = Invoice::where('user_id', '=',$student_user_id)->where('paid', '1')->sum('amount');
            $fee_details['total_fee'] = $total_fee;
            $fee_details['paid_fee'] = $paid_fee;
            $fee_details['terms'] = Invoice::leftJoin("payments", "payments.invoice_id","=", 'invoices.id')
                ->where('invoices.user_id', '=',$student_user_id)
                ->select('invoices.id', 'invoices.title', 'invoices.paid', 'invoices.amount', 'payments.created_at as date')->get()->toArray();
            $fee_details['balance_fee'] =  $total_fee - $paid_fee;
            return response()->json(['fee_details' => $fee_details], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get last school year and child
     * Get last school year and last child which added
     *
     * @Get("/school_year_child")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
                "school_year_id": 1,
                "school_year": "2015-2016",
                "student_id": "1",
                "student_user_id": "1",
                "student_name": "Student Name"
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    function selectSchoolYearChild(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $school_year_group = SchoolYear::join('students', 'students.school_year_id', '=', 'school_years.id')
            ->join('parent_students', 'parent_students.user_id_student', '=', 'students.user_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->whereNull('students.deleted_at')
            ->where('parent_students.user_id_parent', $user->id)
            ->orderBy('school_years.id', 'DESC')
            ->orderBy('students.id', 'DESC')
            ->select('school_years.id as school_year_id', 'students.id as student_id', 'students.user_id as student_user_id',
                'school_years.title as school_year',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as student_name'))
            ->distinct()->first();

        return response()->json($school_year_group, 200);
    }
}
