<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SchoolYearTrait;
use App\Models\BookUser;
use App\Models\Diary;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\SchoolYear;
use App\Models\Subject;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Traits\TimeTableTrait;
use App\Http\Controllers\Traits\AttendanceTrait;
use App\Http\Controllers\Traits\MarksTrait;
use JWTAuth;
use Illuminate\Http\Request;
use Validator;
use DB;

/**
 * Student endpoints, can be accessed only with role "student"
 *
 * @Resource("Student", uri="/student")
 */

class StudentController extends Controller
{
    use Helpers;
    use TimeTableTrait;
    use MarksTrait;
    use AttendanceTrait;
    use SchoolYearTrait;

    /**
     * Schools for student
     *
     * Get all schools for selected user
     *
     * @Get("/schools")
     * @Versions({"v1"})
     * @Request({"token": "foo", "school_id":1})
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
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json($this->currentSchoolStudent($current_school,$user->id), 200);
    }
    /**
     * School year for student
     *
     * Get all school years with current school year name and id and other school years that he/she can select.
     * This method use all roles because all data(students, sections, marks, behaviors,semesters,attendances) are depend on school year
     *
     * @Get("/school_years")
     * @Versions({"v1"})
     * @Request({"token": "foo", "school_year_id":1, "student_user_id":1})
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
        $student_user_id = $request->input('student_user_id');
        $school_id = $request->input('school_id');
        return response()->json($this->currentSchoolYearSchoolStudent($current_school_year,$student_user_id,$school_id), 200);
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
     * @Request({"token": "foo", "school_year_id":"1"})
        @Response(200, body={
            "timetable": {"1":
                {
                "1": {"id": 10, "subject": "English", "teacher": "Test teacher 1"},
                "2": {"id": 11, "subject": "Serbian", "teacher": "Test teacher 2"},
                },
            "2":
                {
                "1": {"id": 12, "subject": "History", "teacher": "Test teacher 2"},
                "2": {"id": 13, "subject": "English", "teacher": "Test teacher 1"},
                }
            },
     })
     */
    public function timetable(Request $request)
    {
        $data = array(
            'school_year_id' => $request->input('school_year_id'),
        );
        $rules = array(
            'school_year_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $timetable = $this->studentsTimetable($user->id,$request->input('school_year_id'));
            return response()->json(['timetable' => $timetable], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Timetable for student for day and school year and day
     *
     * Get timetable classes for student for selected school year and day (day: 1-Monday,... 7-Sunday)
     *
     * @Get("/timetable_day")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "school_year_id":"1", "day_id":"1"}),
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
            'day_id' => $request->input('day_id'),
        );
        $rules = array(
            'school_year_id' => 'required|integer',
            'day_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($this->studentsTimetableDayAPI($user->id,$request->input('school_year_id'),$request->input('day_id')), 200);
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
     *      @Request({"token": "foo", "school_year_id":"1"}),
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
        );
        $rules = array(
            'school_year_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($this->studentsTimetableSubjectsDayAPI($user->id,$request->input('school_year_id')), 200);
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
     * @Request({"token": "foo"})
     * @Response(200, body={
        "books": {{"title": "Book for mathematics",
            "id":"12",
            "internal":"12-45",
            "author": "Group of authors",
            "get": "2015-08-10"}}
    })
     */
    public function borrowedBooks()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(['books' => BookUser::join('books', 'books.id', '=', 'book_users.book_id')
            ->where('book_users.user_id', $user->id)
            ->whereNotNull('get')
            ->whereNull('back')
            ->whereNull('books.deleted_at')
            ->select(array('books.title', 'books.internal','books.id','books.author', 'book_users.get'))->get()->toArray()], 200);
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
     * Exams for teacher group
     *
     * Get all exams for teacher group
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
     * Notices for teacher group
     *
     * Get all notices for teacher group
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
                        "date": "2015-02-02"
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
     * Subject list for student
     *
     * Get subject list for student
     *
     * @Get("/student_subject")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_id":"1"}),
     *      @Response(200, body={
                "subjects": {
                    "id": 1,
                    "subject": "English",
                    "teacher": "Test Teacher",
                    "image": ""
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function studentSubject(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $subjects = Subject::join('directions', 'subjects.direction_id', '=', 'directions.id')
                ->join('student_groups', function ($join) {
                    $join->on('student_groups.direction_id', '=', 'directions.id');
                    $join->on('student_groups.class', '=', 'subjects.class');
                })
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'student_groups.id')
                ->join('students', 'students.id', '=', 'student_student_group.student_id')
                ->join('teacher_subjects', function ($join) {
                    $join->on('teacher_subjects.subject_id', '=', 'subjects.id');
                    $join->on('teacher_subjects.school_year_id', '=', 'students.school_year_id');
                })
                ->join('users', 'users.id', '=', 'teacher_subjects.teacher_id')
                ->where('student_student_group.student_id', $request->input('student_id'))
                ->orderBy('subjects.class')
                ->orderBy('subjects.order')
                ->select('subjects.id', 'subjects.title',
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as teacher'),
                    DB::raw('CONCAT(" ") as image'))
                ->get()->toArray();

            return response()->json(['subjects' => $subjects], 200);
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
     * Get last school year and student_id
     * Get last school year and student_id for student user
     *
     * @Get("/school_year_student")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
                        "school_year_id": 1,
                        "school_year": "2015-2016",
                        "student_id": "1"
                    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    function selectSchoolYearStudent(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $school_year_group = SchoolYear::join('students', 'students.school_year_id', '=', 'school_years.id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->whereNull('students.deleted_at')
            ->where('users.id', $user->id)
            ->orderBy('school_years.id', 'DESC')
            ->orderBy('students.id', 'DESC')
            ->select('school_years.id as school_year_id', 'students.id as student_id',
                'school_years.title as school_year')
            ->distinct()->first();

        return response()->json($school_year_group, 200);
    }
}
