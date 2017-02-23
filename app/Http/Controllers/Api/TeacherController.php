<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SchoolYearTrait;
use App\Models\ApplyingLeave;
use App\Models\Attendance;
use App\Models\BookUser;
use App\Models\Diary;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Timetable;
use App\Models\User;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Traits\TimeTableTrait;
use App\Http\Controllers\Traits\TeacherTrait;
use App\Http\Controllers\Traits\AttendanceTrait;
use App\Http\Controllers\Traits\MarksTrait;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use DB;

/**
 * Teacher endpoints, can be accessed only with role "teacher"
 *
 * @Resource("Teacher", uri="/teacher")
 */

class TeacherController extends Controller
{
    use Helpers;
    use TimeTableTrait;
    use TeacherTrait;
    use MarksTrait;
    use AttendanceTrait;
    use SchoolYearTrait;

    /**
     * Schools for teacher
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

        return response()->json($this->currentSchoolTeacher($current_school,$user->id), 200);
    }
    /**
     * School year for user
     *
     * Get all school years with current school year name and id and other school years that he/she can select.
     * This method use all roles because all data(students, sections, marks, behaviors,semesters,attendances) are depend on school year
     *
     * @Get("/school_years")
     * @Versions({"v1"})
     * @Request({"token": "foo", "school_year_id":1})
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
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json($this->currentSchoolYearTeacher($current_school_year,$user->id), 200);
    }

    /**
     * Timetable for teacher
     *
     * Get timetable for teacher with getting his token and role teacher
     * This method return array of array: first array has number of hour, first subarray is array for number of day and
     * in that array have objects that represent subject and teacher that teaches.
     * After first array goes second array that represents subjects and group that teacher teach in
     * selected school year.
     *
     * @Get("/timetable")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "school_year_id":"1"}),
     *      @Response(200, body={
           "timetable": {"1":
               {
                "1": {"id": 10, "subject": "english", "group": "1 - 2"},
                "2": {"id": 11, "subject": "serbian", "group": "2 - 2"},
                },
          "2":
               {
                "1": {"id": 12, "subject": "history", "group": "1 - 3"},
                "2": {"id": 13, "subject": "english", "group": "1 - 2"},
                }
            },
          "subject_group": {
            {
                "id": 4,
                "subject": "history",
                "group": "1 - 3"
            },
            {
                "id": 1,
                "subject": "english",
                "group": "1 - 3"
            },
           }
          })
         }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * */
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
            return response()->json($this->teacherTimetableAPI($user->id, $request->input('school_year_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Timetable for teacher for selected group
     *
     * Get timetable for teacher with getting selected group id
     * This method return array of array: first array has number of hour, first subarray is array for number of day and
     * in that array have objects that represent subject and teacher that teaches.
     * After first array goes second array that represents subjects and group that teacher teach in
     * selected group.
     *
     * @Get("/timetable_group")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1"}),
     *      @Response(200, body={
                "timetable":{"1":
                    {
                    "1": {"id": 10, "subject": "english", "group": "1 - 2"},
                    "2": {"id": 11, "subject": "serbian", "group": "2 - 2"},
                    },
                "2":
                    {
                    "1": {"id": 12, "subject": "history", "group": "1 - 3"},
                    "2": {"id": 13, "subject": "english", "group": "1 - 2"},
                    }
                },
            "subject_group": {
                        {
                            "id": 4,
                            "subject": "history",
                            "group": "1 - 3"
                        },
                        {
                            "id": 1,
                            "subject": "english",
                            "group": "1 - 3"
                        },
                }
            })
        }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * */
    public function timetableGroup(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($this->teacherGroupTimetableAPI($request->input('student_group_id'),$user->id), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Timetable classes for teacher for selected day and school year
     *
     * Get timetable classes for teacher for selected day and school year.(1-Monday,... 7-Sunday)
     *
     * @Get("/timetable_day")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "school_year_id":"1", "day_id":"1"}),
     *      @Response(200, body={
            "timetable":
                    {
                    "1": {"id": 10, "subject": "english", "group": "1 - 2"},
                    "2": {"id": 11, "subject": "serbian", "group": "2 - 2"},
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
            return response()->json($this->teacherTimetableDayAPI($user->id, $request->input('school_year_id'), $request->input('day_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Timetable for teacher for selected group, day and school year
     *
     * Get timetable classes for teacher for selected group, day and school year.(day: 1-Monday,... 7-Sunday)
     *
     * @Get("/timetable_group_day")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "day_id":"1"}),
     *      @Response(200, body={
                        "timetable":
                            {
                            "1": {"id": 10, "subject": "english", "group": "1 - 2"},
                            "2": {"id": 11, "subject": "serbian", "group": "2 - 2"},
                            }
                    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * */
    public function timetableGroupDay(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'day_id' => $request->input('day_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'day_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($this->teacherGroupTimetableDayAPI($request->input('student_group_id'),$user->id,$request->input('day_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Subject list for teacher for school year
     *
     * Get subject list for teacher for school year
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
                            "group": "1 - 3"
                        },
                        {
                            "id": 1,
                            "subject": "english",
                            "group": "1 - 3"
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
            return response()->json($this->teacherSubjectListAPI($user->id, $request->input('school_year_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Subject list for teacher for student group
     *
     * Get subject list for teacher for student group
     *
     * @Get("/subject_list_group")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1"}),
     *      @Response(200, body={
                "subject_list":
                    {
                        "id": 4,
                        "subject": "history",
                        "group": "1 - 3"
                    },
                    {
                        "id": 1,
                        "subject": "english",
                        "group": "1 - 3"
                    },
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * */
    public function subjectListGroup(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($this->teacherSubjectListGroupAPI($user->id, $request->input('student_group_id')), 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Groups for teacher
     *
     * Get all groups for teacher for selected school year
     *
     * @Get("/groups")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "school_year":"1"}),
     *      @Response(200, body={
        "student_groups": {
            {
            "id": 1,
            "title": "Group 1",
            "direction": "English",
            "class": 2
            }
        }
        }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function groups(Request $request)
    {
        $data = array(
            'school_year_id' => $request->input('school_year'),
        );
        $rules = array(
            'school_year_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json(['student_groups' => $this->teacherGroups($data['school_year_id'],$user->id)], 200);
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
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-10"}),
     *      @Response(200, body={
                "notice": {
                {
                    "id": 1,
                    "title": "This is title of notice",
                    "notice_type_id": "1",
                    "subject_id": "1",
                    "subject": "English",
                    "notice_type": "Exam",
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
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $notices = Notice::join('subjects', 'subjects.id', '=', 'notices.subject_id')
                ->join('notice_types', 'notice_types.id', '=', 'notices.notice_type_id')
                ->where('notices.user_id', $user->id)
                ->where('student_group_id',$request->input('student_group_id'))
                ->where('date',$request->input('date'))
                ->select(array('notices.id', 'notices.title','notices.notice_type_id','notice_types.title as notice_type',
                    'subjects.id as subject_id','subjects.title as subject','notices.description','notices.date'))
                ->get()->toArray();

            return response()->json(['notice' => $notices], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post notice for teacher group
     *
     * @Post("/post_notice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "notice_type_id":"1", "subject_id":"1", "title":"This is title", "date":"2015-06-08", "description":"This is description"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postNotice(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'notice_type_id' => $request->input('notice_type_id'),
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'notice_type_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'title' => 'required',
            'date' => 'required|date',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $notices = new Notice($request->except('token'));
            $notices->user_id = $user->id;
            $notices->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Edit notice
     *
     * @Post("/edit_notice")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "notice_id":"1","subject_id":"1","title":"This is title", "date":"2015-06-08","notice_type_id":"1","description":"This is description"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editNotice(Request $request)
    {
        $data = array(
            'notice_id' => $request->input('notice_id'),
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'notice_type_id' => $request->input('notice_type_id'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'notice_id' => 'required|integer',
            'subject_id' => 'integer|integer',
            'title' => 'required',
            'date' => 'required|date',
            'notice_type_id' => 'required|integer',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $notice = Notice::find($request->input('notice_id'));
            $notice->update($request->except('token','notice_id'));
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Delete notice
     *
     * @Post("/delete_notice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "notice_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteNotice(Request $request)
    {
        $data = array(
            'notice_id' => $request->input('notice_id'),
        );
        $rules = array(
            'notice_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $notice = Notice::find($request->input('notice_id'));
            $notice->delete();
            return response()->json(['success' => "success"], 200);
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
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-10"}),
     *      @Response(200, body={
                        "exams": {
                        {
                            "id": 1,
                            "title": "This is title of exam",
                            "subject": "English",
                            "subject_id": "1",
                            "date": "2015-10-10",
                            "description": "This is a description"
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
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $exams = Exam::join('subjects', 'subjects.id', '=', 'exams.subject_id')
                ->where('exams.user_id', $user->id)
                ->where('student_group_id',$request->input('student_group_id'))
                ->where('date',$request->input('date'))
                ->select(array('exams.id', 'exams.title','exams.subject_id','subjects.title as subject','exams.description','exams.date'))
                ->get()->toArray();

            return response()->json(['exams' => $exams], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Post exam for teacher group
     *
     * @Post("/post_exam")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "subject_id":"1", "title":"This is title", "date":"2015-06-08"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postExam(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date')
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'title' => 'required',
            'date' => 'required|date'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $notices = new Exam($request->except('token'));
            $notices->user_id = $user->id;
            $notices->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Edit exam
     *
     * @Post("/edit_exam")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "exam_id":"1","subject_id":"1","title":"This is title", "date":"2015-06-08","description":"This is description"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editExam(Request $request)
    {
        $data = array(
            'exam_id' => $request->input('exam_id'),
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'exam_id' => 'required|integer',
            'subject_id' => 'integer|integer',
            'title' => 'required',
            'date' => 'required|date',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $exam = Exam::find($request->input('exam_id'));
            $exam->update($request->except('token','exam_id'));
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Delete exam
     *
     * @Post("/delete_exam")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "exam_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteExam(Request $request)
    {
        $data = array(
            'exam_id' => $request->input('exam_id'),
        );
        $rules = array(
            'exam_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $exam = Exam::find($request->input('exam_id'));
            $exam->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Attendances for teacher group between two date
     *
     * Get all attendances for teacher group between two date
     *
     * @Get("/attendances")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "start_date":"2015-10-12", "end_date":"2015-10-12"}),
     *      @Response(200, body={
            "attendance": {
                {
                "id": 1,
                "student": "Student Name",
                "hour": "2",
                "justified": "yes",
                "date": "2015-06-05"
                }
            }
        }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function attendances(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'start_date' => $request->input('start_date'),
            'end_date'=>$request->input('end_date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $attendance = $this->listAttendanceGroup($request->input('student_group_id'),$request->input('start_date'),$request->input('end_date'));

            return response()->json(['attendance' => $attendance], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Attendances for teacher group by date
     *
     * Get all attendances for teacher group by date
     *
     * @Get("/attendances_date")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-12"}),
     *      @Response(200, body={
                "attendance": {
                        {
                            "id": 1,
                            "student": "Student Name",
                            "student_id":"1",
                            "hour": "2",
                            "justified": "yes",
                            "date": "2015-06-05"
                        }
                    }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function attendancesDate(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $attendance = $this->listAttendanceGroupDate($request->input('student_group_id'),$request->input('date'));

            return response()->json(['attendance' => $attendance], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Attendances list of hours from timetable
     *
     * Get all attendances list of hours from timetable for selected date and group
     *
     * @Get("/attendance_hour_list")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-12"}),
     *      @Response(200, body={
                "hour_list": {
                    {
                        "id": 1,
                        "hour": "1",
                        "subject_id":"1",
                        "subject": "english"
                    },
                    {
                        "id": 2,
                        "hour": "2",
                        "subject_id":"1",
                        "subject": "history"
                    }
                }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function attendanceHourList(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $hour_list = Timetable::join('teacher_subjects', 'teacher_subjects.id','=', 'timetables.teacher_subject_id')
                ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                ->where('week_day',date('N', strtotime("- 1 day",strtotime($request->input('date')))))
                ->where('teacher_id', $user->id)
                ->where('student_group_id', $request->input('student_group_id'))
                ->select('timetables.id','timetables.hour','teacher_subjects.subject_id','subjects.title as subject')->get()->toArray();

            return response()->json(['hour_list' => $hour_list], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Post attendance for student,subject and date
     *
     * @Post("/post_attendance")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({
                   "token": "foo",
                    "student_id":"1",
                    "date":"2015-06-08",
                    "hour":"2",
                    "student_group_id":"1",
                    "justified": "yes"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postAttendance(Request $request)
    {
        $data = array(
            'student_id' => $request->input('student_id'),
            'date' => $request->input('date'),
            'hour' => $request->input('hour'),
            'justified' => $request->input('justified'),
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_id' => 'required|integer',
            'date' => 'required|date',
            'hour' => 'required|integer',
            'justified' => 'required',
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $user = JWTAuth::parseToken()->authenticate();

            $student_group = StudentGroup::join('sections', 'sections.id', '=','student_groups.section_id')
                ->select('student_groups.id', 'sections.school_year_id')
                ->where('student_groups.id',$request->input('student_group_id'))->first();
            $date = $request->input('date');
            $semestar = Semester::where(function ($query) use ($date,$student_group) {
                $query->where('start', '>=', $date)
                    ->where('school_year_id', '=', $student_group->school_year_id);
            })->orWhere(function ($query) use ($date,$student_group) {
                $query->where('end', '<=', $date)
                    ->where('school_year_id', '=', $student_group->school_year_id);
            })->first();

            $subject = Subject::join('teacher_subjects', 'teacher_subjects.subject_id','=', 'subjects.id')
                ->join('timetables', 'timetables.teacher_subject_id','=', 'teacher_subjects.id')
                ->where('week_day', date('N', strtotime("- 1 day",strtotime($date))))
                ->where('teacher_subjects.teacher_id', $user->id)
                ->where('student_group_id', $student_group->id)
                ->select('subjects.id')->first();

            $attendance = new Attendance($request->except('token','student_group_id'));
            $attendance->teacher_id = $user->id;
            $attendance->school_year_id = $student_group->school_year_id;
            $attendance->semester_id = isset($semestar->id)?$semestar->id:1;
            $attendance->subject_id = $subject->id;
            $attendance->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Edit attendance
     *
     * @Post("/edit_attendance")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "attendance_id":"1","date":"2015-06-08","hour":"1","student_id":"1","justified":"yes"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editAttendance(Request $request)
    {
        $data = array(
            'attendance_id' => $request->input('attendance_id'),
            'student_id' => $request->input('student_id'),
            'date' => $request->input('date'),
            'hour' => $request->input('hour'),
            'justified' => $request->input('justified'),
        );
        $rules = array(
            'attendance_id' => 'required|integer',
            'student_id' => 'integer|integer',
            'justified' => 'required',
            'date' => 'required|date',
            'hour' => 'required|integer'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $attendance = Attendance::find($request->input('attendance_id'));
            $attendance->update($request->except('token','attendance_id'));
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Delete attendance
     *
     * @Post("/delete_attendance")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "attendance_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteAttendance(Request $request)
    {
        $data = array(
            'attendance_id' => $request->input('attendance_id'),
        );
        $rules = array(
            'attendance_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $attendance = Attendance::find($request->input('attendance_id'));
            $attendance->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Justify attendance
     *
     * @Post("/justify_attendance")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "attendance_id":"1", "justify": "no/yes/unknown"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function justifyAttendance(Request $request)
    {
        $data = array(
            'attendance_id' => $request->input('attendance_id'),
            'justify' =>  $request->input('justify'),
        );
        $rules = array(
            'attendance_id' => 'required|integer',
            'justify' => 'required|min:2',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $attendance = Attendance::find($request->input('attendance_id'));
            $attendance->justify = $request->input('justify');
            $attendance->save();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Marks for teacher group between two date
     *
     * Get all marks for teacher group between two date
     *
     * @Get("/marks")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "subject_id":"1", "start_date": "2015-10-12","end_date": "2015-10-13"}),
     *      @Response(200, body={
            "marks": {
                {
                    "id": 1,
                    "student_name": "Student Name",
                    "student_id":"1",
                    "mark_type": "Oral",
                    "mark_type_id":"1",
                    "mark_value": "A+",
                    "mark_value_id":"1",
                    "exam": "Exam 1",
                    "exam_id":"1",
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
            'student_group_id' => $request->input('student_group_id'),
            'subject_id' => $request->input('subject_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $marks = $this->listMarksGroup($request->input('subject_id'),$request->input('start_date'),$request->input('end_date'),$request->input('student_group_id'));
            return response()->json(['marks' => $marks], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Marks for teacher group for selected date
     *
     * Get all marks for teacher group for selected date
     *
     * @Get("/marks_date")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "date": "2015-10-12"}),
     *      @Response(200, body={
                    "marks": {
                        {
                            "id": 1,
                            "student_name": "Student Name",
                            "student_id":"1",
                            "subject": "Subject",
                            "subject_id":"1",
                            "mark_type": "Oral",
                            "mark_type_id":"1",
                            "mark_value": "A+",
                            "mark_value_id":"1",
                            "exam": "Exam 1",
                            "exam_id":"1",
                            "date":"2015-10-12",
                        }
                    }
                    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *      })
     * })
     */
    public function marksDate(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $marks = $this->listMarksGroupDate($user->id,$request->input('date'),$request->input('student_group_id'));
            return response()->json(['marks' => $marks], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Post mark for student,subject and date
     *
     * @Post("/post_mark")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({
                "token": "foo",
                "exam_id":"1",
                "mark_type_id":"1",
                "student_id":1,
                "subject_id":"1",
                "mark_value_id":"1",
                "date":"2015-06-08",
                "student_group_id":"1"
                }),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function postMark(Request $request)
    {
        $data = array(
            'exam_id' => $request->input('exam_id'),
            'mark_type_id' => $request->input('mark_type_id'),
            'student_id' => $request->input('student_id'),
            'subject_id' => $request->input('subject_id'),
            'mark_value_id' => $request->input('mark_value_id'),
            'date' => $request->input('date'),
            'student_group_id' => $request->input('student_group_id')
        );
        $rules = array(
            'exam_id' => 'integer',
            'mark_type_id' => 'required|integer',
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'mark_value_id' => 'required|integer',
            'date' => 'required|date',
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $date = $request['date'];

            $student_group = StudentGroup::join('sections', 'sections.id', '=','student_groups.section_id')
                ->find($request->input('student_group_id'));

            $semestar = Semester::where(function ($query) use ($date,$student_group) {
                $query->where('start', '>=', $date)
                    ->where('school_year_id', '=', $student_group->school_year_id);
            })->orWhere(function ($query) use ($date,$student_group) {
                $query->where('end', '<=', $date)
                    ->where('school_year_id', '=', $student_group->school_year_id);
            })->first();

            $user = JWTAuth::parseToken()->authenticate();
            $mark = new Mark($request->except('token','student_group_id'));
            $mark->teacher_id = $user->id;
            $mark->school_year_id = $student_group->school_year_id;
            $mark->semester_id = isset($semestar->id)?$semestar->id:1;
            $mark->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Edit mark
     *
     * @Post("/edit_mark")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "mark_id":"1","date":"2015-06-08","exam_id":"1","student_id":"1","mark_type_id":"1","subject_id":"1","mark_value_id":"1"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editMark(Request $request)
    {
        $data = array(
            'mark_id' => $request->input('mark_id'),
            'exam_id' => $request->input('exam_id'),
            'mark_type_id' => $request->input('mark_type_id'),
            'student_id' => $request->input('student_id'),
            'subject_id' => $request->input('subject_id'),
            'mark_value_id' => $request->input('mark_value_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'mark_id' => 'integer|integer',
            'exam_id' => 'integer',
            'mark_type_id' => 'integer|integer',
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'mark_value_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $mark = Mark::find($request->input('mark_id'));
            $mark->update($request->except('token','mark_id'));
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Delete mark
     *
     * @Post("/delete_mark")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "mark_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteMark(Request $request)
    {
        $data = array(
            'mark_id' => $request->input('mark_id'),
        );
        $rules = array(
            'mark_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $mark = Mark::find($request->input('mark_id'));
            $mark->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Students for teacher group
     *
     * Get all students for teacher group
     *
     * @Get("/students")
     * @Versions({"v1"})
      @Transaction({
            @Request({"token": "foo", "student_group_id":"1"}),
            @Response(200, body={
            "students": {
                {
                "id": 1,
                "student_name": "Student Name",
                }
                }
            }),
           @Response(500, body={"error":"not_valid_data"})
           })
      })
     */
    public function students(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $students =  User::join('students', 'users.id', '=','students.user_id')
                ->join('student_student_group', 'student_student_group.student_id', '=','students.id')
                ->where('student_student_group.student_group_id', $request->input('student_group_id'))
                ->select('students.id', DB::raw('CONCAT(users.first_name, " ", users.last_name) as student_name'))
                ->distinct()->get()->toArray();

            return response()->json(['students' => $students], 200);
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
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "books": {
            {"user_book_id":"1",
            "title": "Book for mathematics",
            "author": "Group of authors",
            "get": "2015-08-10"}
            }
        })
     */
    public function borrowedBooks()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $books = BookUser::join('books', 'books.id', '=', 'book_users.book_id')
            ->where('book_users.user_id', $user->id)
            ->whereNotNull('get')
            ->whereNull('back')
            ->select(array('book_users.id as user_book_id','books.title', 'books.author', 'book_users.get'))
            ->get()->toArray();
        return response()->json(['books'=>$books], 200);
    }

    /**
     * Dairies for student
     *
     * Get all diaries for student
     *
     * @Get("/diary")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1"}),
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
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $dairies = Diary::join('subjects', 'subjects.id', '=', 'diaries.subject_id')
                ->where('diaries.user_id',$user->id)
                ->join('teacher_subjects', 'teacher_subjects.subject_id','=', 'diaries.subject_id')
                ->join('student_student_group', 'student_student_group.student_group_id', '=', 'teacher_subjects.student_group_id')
                ->where('teacher_subjects.student_group_id',$request->input('student_group_id'))
                ->orderBy('diaries.date', 'DESC')
                ->orderBy('diaries.hour', 'DESC')
                ->select(array('diaries.id', 'diaries.title','subjects.title as subject','diaries.description','diaries.hour','diaries.date'))
                ->distinct()
                ->get()->toArray();

            return response()->json(['diaries' => $dairies], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Dairies for student group and date
     *
     * Get all diaries for student group and selected date
     *
     * @Get("/diary_date")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-10"}),
     *      @Response(200, body={
                    "diaries": {
                        {
                            "id": 1,
                            "title": "This is title of notice",
                            "subject_id":12,
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
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $dairies = Diary::join('subjects', 'subjects.id', '=', 'diaries.subject_id')
                ->where('diaries.user_id',$user->id)
                ->join('teacher_subjects', 'teacher_subjects.subject_id','=', 'diaries.subject_id')
                ->where('teacher_subjects.student_group_id',$request->input('student_group_id'))
                ->where('diaries.date',$request->input('date'))
                ->orderBy('diaries.date', 'DESC')
                ->orderBy('diaries.hour', 'DESC')
                ->distinct()
                ->select(array('diaries.id', 'diaries.title','subjects.id as subject_id','subjects.title as subject','diaries.hour','diaries.date','diaries.description'))
                ->get()->toArray();

            return response()->json(['diaries' => $dairies], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post diary for subject, hour and date
     *
     * @Post("/post_diary")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "group_id":"1","subject_id":"1","title":"This is title", "date":"2015-06-08","hour":"1","description":"This is description"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function postDairy(Request $request)
    {
        $data = array(
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'hour' => $request->input('hour'),
            'description' => $request->input('description'),
            'group_id' => $request->input('group_id')
        );
        $rules = array(
            'subject_id' => 'integer',
            'title' => 'required',
            'date' => 'required|date',
            'hour' => 'required|integer',
            'group_id' => 'required|integer',
            'description' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $student_group = StudentGroup::join('sections', 'sections.id', '=','student_groups.section_id')
                ->find($request->input('student_group_id'));
            $diary = new Diary($request->except('token'));
            $diary->user_id = $user->id;
            $diary->school_year_id = $student_group->school_year_id;
            $diary->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Edit diary
     *
     * @Post("/edit_diary")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "diary_id":"1","subject_id":"1","title":"This is title", "date":"2015-06-08","hour":"1","description":"This is description"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editDairy(Request $request)
    {
        $data = array(
            'diary_id' => $request->input('diary_id'),
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'hour' => $request->input('hour'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'diary_id' => 'required|integer',
            'subject_id' => 'integer|integer',
            'title' => 'required',
            'date' => 'required|date',
            'hour' => 'required|integer',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $diary = Diary::find($request->input('diary_id'));
            $diary->update($request->except('token','diary_id'));
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete diary
     *
     * @Post("/delete_diary")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "diary_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteDairy(Request $request)
    {
        $data = array(
            'diary_id' => $request->input('diary_id'),
        );
        $rules = array(
            'diary_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $diary = Diary::find($request->input('diary_id'));
            $diary->delete();
            return response()->json(['success' => "success"], 200);
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
     *      @Request({"token": "foo", "student_group_id":"1"}),
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
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $exams = Exam::join('subjects', 'subjects.id','=', 'exams.subject_id')
                        ->join('teacher_subjects', 'teacher_subjects.subject_id','=', 'subjects.id')
                        ->where('teacher_subjects.teacher_id',$user->id)
                        ->where('exams.student_group_id',$request->input('student_group_id'))
                        ->orderBy('exams.date')
                        ->select('exams.id', 'exams.title', 'subjects.title as subject',
                           DB::raw('(SELECT COUNT(id) FROM marks WHERE exam_id =exams.id) as total_marks'))->get()->toArray();

            return response()->json(['exams' => $exams], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get exams for subject
     *
     * @Get("/subject_exams")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1","subject_id":"1"}),
     *      @Response(200, body={
                    "exams": {
                        {
                            "id": 1,
                            "title": "English",
                            "date": "2015-10-10"
                        }
                    }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function subjectExams(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'subject_id' => $request->input('subject_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'subject_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $exams = Exam::join('teacher_subjects', 'teacher_subjects.subject_id','=', 'exams.subject_id')
                ->where('teacher_subjects.teacher_id',$user->id)
                ->where('exams.student_group_id',$request->input('student_group_id'))
                ->where('exams.subject_id',$request->input('subject_id'))
                ->orderBy('exams.date')
                ->select('exams.id', 'exams.title','exams.date')->get()->toArray();

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
     *      @Request({"token": "foo", "exam_id":"1"}),
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
        );
        $rules = array(
            'exam_id' => 'required|integer',
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
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-10"}),
     *      @Response(200, body={
                "applying_leave": {
                    {
                    "id": 1,
                    "title": "This is title of exam",
                    "description": "This is description of exam",
                    "date": "2015-10-10",
                    "student_name": "Student Name"
                    }
                }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function applyingLeave(Request $request){
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'date' => $request->input('date'),
            );
        $rules = array(
            'student_group_id' => 'required|integer',
            'date' => 'required|date',
            );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $applying_leave = ApplyingLeave::join('students', 'students.id', '=', 'applying_leaves.student_id')
                ->join('users', 'users.id', '=', 'students.user_id')
                ->join('student_student_group', 'student_student_group.student_id', '=', 'applying_leaves.student_id')
                ->join('teacher_subjects', 'teacher_subjects.student_group_id', '=', 'student_student_group.student_group_id')
                ->where('teacher_subjects.teacher_id', '=', $user->id)
                ->where('teacher_subjects.student_group_id', '=', $request->input('student_group_id'))
                ->where('applying_leaves.date', '=', $request->input('date'))
                ->distinct()
                ->select(array('applying_leaves.id', 'applying_leaves.title', 'applying_leaves.date', 'applying_leaves.description',
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as student_name')))
                ->get()->toArray();
            return response()->json(['applying_leave' => $applying_leave], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Get last school year and group
     * Get last school year and group where teacher teach some subject
     *
     * @Get("/school_year_group")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
                            "school_year_id": 1,
                            "school_year": "2015-2016",
                            "group_id": "1",
                            "group": "1-2"
                    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    function selectSchoolYearGroup(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $school_year_group = SchoolYear::join('teacher_subjects', 'teacher_subjects.school_year_id', '=', 'school_years.id')
            ->join('student_groups', 'student_groups.id', '=', 'teacher_subjects.student_group_id')
            ->whereNull('teacher_subjects.deleted_at')
            ->whereNull('student_groups.deleted_at')
            ->where('teacher_subjects.teacher_id', $user->id)
            ->orderBy('school_years.id', 'DESC')
            ->orderBy('teacher_subjects.student_group_id', 'DESC')
            ->select('school_years.id as school_year_id', 'student_groups.id as group_id',
                'school_years.title as school_year', 'student_groups.title as group')
            ->distinct()->first();

        return response()->json($school_year_group, 200);
    }

    /**
     * Subject for group
     *
     * Get all subject for teacher for selected student group
     *
     * @Get("/subjects")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1"}),
     *      @Response(200, body={
                "subjects": {
                        {
                            "id": 1,
                            "title": "English",
                        }
                    }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function subjects(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $subjects = TeacherSubject::join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                ->where('teacher_subjects.teacher_id', '=', $user->id)
                ->where('teacher_subjects.student_group_id', '=', $request->input('student_group_id'))
                ->whereNull('subjects.deleted_at')
                ->distinct()
                ->select(array('subjects.id', 'subjects.title'))
                ->get()->toArray();
            return response()->json(['subjects' => $subjects], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Hours for group and date
     *
     * Get all hours for selected student group and date
     *
     * @Get("/hours")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "student_group_id":"1", "date":"2015-10-10", "subject_id":"1"}),
     *      @Response(200, body={
                    "hours": {
                        {
                            "id": "1",
                            "hour": "2"
                        }
                    }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function hours(Request $request)
    {
        $data = array(
            'student_group_id' => $request->input('student_group_id'),
            'subject_id' => $request->input('subject_id'),
            'date' => $request->input('date'),
        );
        $rules = array(
            'student_group_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $hours = Timetable::join('teacher_subjects', 'teacher_subjects.id','=', 'timetables.teacher_subject_id')
                ->where('week_day', date('N', strtotime("- 1 day",strtotime($request->input('date')))))
                ->where('teacher_id', $user->id)
                ->where('student_group_id', $request->input('student_group_id'))
                ->where('subject_id', $request->input('subject_id'))
                ->select('timetables.id','timetables.hour')->get()->toArray();
            return response()->json(['hours' => $hours], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
