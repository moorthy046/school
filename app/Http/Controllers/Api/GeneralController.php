<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Behavior;
use App\Models\Book;
use App\Models\BookUser;
use App\Models\Direction;
use App\Models\Dormitory;
use App\Models\Feedback;
use App\Models\MarkType;
use App\Models\MarkValue;
use App\Models\Message;
use App\Models\NoticeType;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\School;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Transportation;
use App\Models\TransportationLocation;
use App\Models\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use DB;

/**
 * General endpoints which can be accessed by any user group
 *
 * @Resource("General", uri="/")
 */

class GeneralController extends Controller
{
    use Helpers;

    /**
     * Behaviors
     *
     * Get all behaviors
     * This list use teacher to put behavior to student
     *
     * @Get("/behaviors")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
            "behaviors":{
                         {"id": "1",
                        "title": "Good"},
                        {"id": "2",
                        "title": "Worse"}
                     }
            })
     */
    public function behaviors()
    {
        $behaviors = Behavior::select(array('id','title'))->get()->toArray();
        return response()->json(['behaviors'=>$behaviors], 200);
    }

    /**
     * Directions
     *
     * Get all directions
     * This is list of all directions of school mostly is for admin who create groups of students
     *
     * @Get("/directions")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "directions":{{"id": "1",
                        "title": "Sociology",
                        "duration": "3"},
                    {"id": "2",
                        "title": "History",
                        "duration": "4"}
                }
        })
     */
    public function directions()
    {
        $directions = Direction::select(array('id','title', 'duration'))->get()->toArray();
        return response()->json(['directions'=>$directions], 200);
    }

    /**
     * Dormitories
     *
     * Get all dormitories
     * Method for admin to list all dormitories in school
     *
     * @Get("/dormitories")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        {"id": "1",
            "title": "Student hotel 1"},
        {"id": "2",
            "title": "Student hotel 2"},
        })
     */
    public function dormitories()
    {
        $dormitories = Dormitory::select(array('id','title'))->get()->toArray();
        return response()->json(['dormitories'=>$dormitories], 200);
    }


    /**
     * Dormitory rooms
     *
     * Get all dormitory rooms
     * Method for admin to list all dormitory rooms in school
     *
     * @Get("/dormitory_rooms")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "dormitories":{{"id": "1",
                    "dormitory": "Student hotel 1",
                    "title": "Room 1"},
                    {"id": "2",
                    "dormitory": "Student hotel 2",
                    "title": "Room 1"}
                }
    })
     */
    public function dormitoryRooms()
    {
        $dormitories = Dormitory::join('dormitory_rooms', 'dormitories.id', '=', 'dormitory_rooms.dormitory_id')
            ->select(array('dormitory_rooms.id','dormitories.title as dormitory', 'dormitory_rooms.title'))->get()->toArray();
        return response()->json(['dormitories'=>$dormitories], 200);
    }

    /**
     * Dormitory beds
     *
     * Get all dormitory beds
     * Method for admin to list all dormitory beds and know which student is in which room in school
     *
     * @Get("/dormitory_beds")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "dormitories":{{"id": "1",
            "dormitory": "Student hotel 1",
            "dormitory_room": "Room 1",
            "bed": "Bed 1"},
        {"id": "2",
            "dormitory": "Student hotel 1",
            "dormitory_room": "Room 2",
            "bed": "Bed 2"}}
    })
     */
    public function dormitoryBeds()
    {
        $dormitories = Dormitory::join('dormitory_rooms', 'dormitories.id', '=', 'dormitory_rooms.dormitory_id')
            ->join('dormitory_beds', 'dormitory_rooms.id', '=', 'dormitory_beds.dormitory_room_id')
            ->select(array('dormitory_rooms.id','dormitories.title as dormitory', 'dormitory_rooms.title as dormitory_room',
                'dormitory_beds.title as bed'))->get()->toArray();
        return response()->json(['dormitories'=>$dormitories], 200);
    }

    /**
     * Mark types
     *
     * Get all mark types
     * Teachers need this to choose which is mark type
     *
     * @Get("/mark_types")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "mark_type":{{"id": "1",
                        "title": "Oral"},
                    {"id": "2",
                        "title": "Writing"}}
    })
     */
    public function markTypes()
    {
        $mark_type = MarkType::select(array('id','title'))->get()->toArray();
        return response()->json(['mark_type'=>$mark_type], 200);
    }

    /**
     * Mark values
     *
     * Get all mark values
     * Teachers need this to choose which is mark value
     *
     * @Get("/mark_values")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "mark_values":{{"id": "1",
            "title": "A"},
        {"id": "2",
            "title": "B"}}
        })
     */
    public function markValues()
    {
        $mark_values = MarkValue::select(array('id','title'))->get()->toArray();
        return response()->json(['mark_values'=>$mark_values], 200);
    }

    /**
     * Notice types
     *
     * Get all notice types
     * Teachers need this to choose which is notice type
     *
     * @Get("/notice_types")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "notice_type": {
                        {"id": "1",
                        "title": "Notice of oral"},
                        {"id": "2",
                        "title": "Notice of writing test"}
                    }
        })
     */
    public function noticeTypes()
    {
        $notice_type = NoticeType::select(array('id','title'))->get()->toArray();
        return response()->json(['notice_type'=>$notice_type], 200);
    }

    /**
     * Notifications
     *
     * Get all notifications
     * All users get all they notification
     *
     * @Get("/notifications")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        "notifications" : {{"id": "1",
                    "read": "0",
                    "title": "Notification 1",
                    "content": "This is content of notification",
                    "date" : "2015-10-12"}
                 }
        })
     */
    public function notifications()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notifications = Notification::where('user_id', $user->id)
            ->select(array('id','read','title','content','date'))->get()->toArray();
        return response()->json(['notifications'=>$notifications], 200);
    }
    /**
     * Get all schools
     * Admin role to get all schools
     *
     * @Get("/schools")
     * @Versions({"v1"})
        @Transaction({
            @Request({"token": "foo"}),
            @Response(200, body={
                "school": {{"id": "1",
                "title": "School 1"},
                {"id": "2",
                "title": "School 1"}
                }
            }),
            @Response(500, body={"error":"not_valid_data"})
            })
        })
     */
    public function schools(Request $request)
    {
        $schools = School::select(array('id', 'title'))->get()->toArray();
        return response()->json(['school'=>$schools], 200);
    }
    /**
     * Get all sections
     * Admin role to get all sections
     *
     * @Get("/sections")
     * @Versions({"v1"})
      @Transaction({
         @Request({"token": "foo", "school_year_id": "1", "school_id": "1"}),
         @Response(200, body={
            "sections": {{"id": "1",
                            "title": "1 - 1",
                            "section_teacher": "Teacher Name 1"},
                        {"id": "2",
                            "title": "1 - 2",
                            "section_teacher": "Teacher Name 2"}
                     }
            }),
       @Response(500, body={"error":"not_valid_data"})
        })
      })
     */
    public function sections(Request $request)
    {
        $data = array(
            'school_year_id' => $request->input('school_year_id'),
            'school_id' => $request->input('school_id'),
        );
        $rules = array(
            'school_year_id' => 'integer',
            'school_id' => 'integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $school_year_id = $request->input('school_year_id');
            $school_id = $request->input('school_id');
            $sections = Section::join('users', 'users.id', '=', 'sections.section_teacher_id')
                ->where('school_year_id', $school_year_id)
                ->where('school_id', $school_id)
                ->select(array('sections.id', 'sections.title',
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as section_teacher')))->get()->toArray();
            return response()->json(['sections'=>$sections], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get all semesters
     * Admin list of all semesters
     *
     * @Get("/semesters")
     * @Versions({"v1"})
     @Transaction({
        @Request({"token": "foo", "school_year_id": "1"}),
        @Response(200, body={
        "semesters":{{"id": "1",
            "title": "First semester",
            "start": "2015-06-12",
            "end": "2015-12-31"}}
        }),
        @Response(500, body={"error":"not_valid_data"})
        })
     })
     */
    public function semesters(Request $request)
    {
        $data = array(
            'school_year_id' => $request->input('school_year_id'),
        );
        $rules = array(
            'school_year_id' => 'integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $school_year_id = $request->input('school_year_id');
            $semesters = Semester::where('school_year_id', $school_year_id)
                ->select(array('id', 'title', 'start', 'end'))->get()->toArray();
            return response()->json(['semesters'=>$semesters], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Subjects
     *
     * Get all subjects for selected direction
     * This used admin for select all subject for selected direction to can add teacher to subject on selected school year
     *
     * @Get("/subjects")
     * @Versions({"v1"})
      @Transaction({
         @Request({"token": "foo", "direction_id": "1"}),
         @Response(200, body={
            "subjects":{{"id": "1",
                "title": "English",
                "class": "3",
                "order": "2"}}
            }),
        @Response(500, body={"error":"not_valid_data"})
        })
    })
     */
    public function subjects(Request $request)
    {
        $data = array(
            'direction_id' => $request->input('direction_id'),
        );
        $rules = array(
            'direction_id' => 'integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $subjects = Subject::where('direction_id', $request->input('direction_id'))->orderBy('order')
                ->select(array('id', 'title', 'class', 'order'))->get()->toArray();
            return response()->json(['subjects'=>$subjects], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Transportations
     *
     * List all transportations used for all roles
     *
     * @Get("/transportations")
     * @Versions({"v1"})
     * @Request({"token": "foo"}),
     * @Response(200, body={
        "transportation":{
                    {"id": "1",
                    "title": "Go to school",
                    "start": "7:00",
                    "end": "7:30"}}
        })
     */
    public function transportations()
    {
        $transportation = Transportation::select(array('id','title','start','end'))->get()->toArray();
        return response()->json(['transportation'=>$transportation], 200);
    }

    /**
     * Transportation directions
     *
     * List all transportation directions for selected transportation used for all roles
     *
     * @Get("/transportation_directions")
     * @Versions({"v1"})
       @Transaction({
         @Request({"token": "foo", "transportation": "1"}),
         @Response(200, body={
            "transportation_directions": {
                        {"id": "1",
                         "name": "Direction name"}
                    }
            }),
         @Response(500, body={"error":"not_valid_data"})
        })
    })
     */
    public function transportationDirections(Request $request)
    {
        $data = array(
            'transportation' => $request->input('transportation'),
        );
        $rules = array(
            'transportation' => 'integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $transportation_directions = TransportationLocation::where('transportation_id', $request->input('transportation'))
                ->select(array('id', 'name'))->get()->toArray();
            return response()->json(['transportation_directions'=>$transportation_directions], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Messages
     *
     * Get all messages used for all roles to get user messages
     *
     * @Get("/messages")
     * @Versions({"v1"})
     * @Request({"token": "foo"})
     * @Response(200, body={
        {"received":{
           {"id":1,
            "parent_message_id":null,
            "read":0,
            "title":"This is title",
            "content":"This is message",
            "created_at":"2015-11-30 15:30:12"}},
        "send":{
           {"id":2,
            "parent_message_id":1,
            "read":0,
            "title":"This is replay title",
            "content":"This is replay message",
            "created_at":"2015-11-30 15:33:05"}}}
        })
     */
    public function messages()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $received = Message::join('users', 'users.id', '=', 'messages.user_id_sender')
            ->where('user_id_receiver', $user->id)->whereNull('deleted_at_receiver')
            ->select(array('messages.id','messages.message_id as parent_message_id','messages.read',
                'messages.title','messages.content','messages.created_at'))->get()->toArray();

        $send = Message::join('users', 'users.id', '=', 'messages.user_id_receiver')
            ->where('user_id_sender', $user->id)->whereNull('deleted_at_sender')
            ->select(array('messages.id','messages.message_id as parent_message_id','messages.read',
                'messages.title','messages.content','messages.created_at'))->get()->toArray();

        return response()->json(array('received'=>$received, 'send'=>$send), 200);
    }
    /**
     * Send message to user, used for all roles to send user messages
     *
     * Send message to user or replay to message. If send parent_message_id greater then 0 than it's replay to message, if 0 then is new message
     *
     * @Post("/send_message")
     * @Versions({"v1"})
        @Transaction({
            @Request({"token": "foo", "user_id_receiver":"1","parent_message_id": "1", "title": "This is title of message", "content": "This is content of message"}),
            @Response(200, body={"success":"success"}),
            @Response(500, body={"error":"not_valid_data"})
            })
        })
     */
    public function sendMssage(Request $request)
    {
        $data = array(
            'user_id_receiver' => $request->input('user_id_receiver'),
            "title" =>  $request->input('title'),
            "content" =>  $request->input('content'),
            'parent_message_id' => $request->input('parent_message_id'),
        );
        $rules = array(
            'user_id_receiver' => 'required|integer',
            'title' => 'required',
            'content' => 'required',
            'parent_message_id' => 'integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $message = new Message();
            if($request->input('parent_message_id')>0) {
                $message->message_id = $request->input('parent_message_id');
                $message_old = Message::find($request->input('parent_message_id'));
                $message->title = "RE ".$message_old->title;
            }
            else {
                $message->title = $request->input('title');
            }
            $message->user_id_receiver = $request->input('user_id_receiver');
            $message->user_id_sender = $user->id;
            $message->save();
            return response()->json(["success" => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Users
     *
     * Get all users, all role can use it to send message
     *
     * @Get("/users")
     * @Versions({"v1"})
     * @Request({"token": "foo"}),
     * @Response(200, body={
        "users":{{"id": "1",
        "first_name": "First name",
        "last_name": "Last name"}}
        })
     */
    public function users()
    {
        $users = User::select(array('id','first_name','last_name'))->get()->toArray();
        return response()->json(['users'=>$users], 200);
    }


    /**
     * Reserve book
     *
     * Reserve book from user, all role can reseve book
     *
     * @Post("/reserve_book")
     * @Versions({"v1"})
        @Transaction({
            @Request({"token": "foo", "book_id": "1"}),
            @Response(200, body={"success":"success"}),
            @Response(500, body={"error":"not_valid_data"})
            })
        })
     */
    public function reserveBook(Request $request)
    {
        $data = array(
            'book_id' => $request->input('book_id'),
        );
        $rules = array(
            'book_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $book_user = new BookUser();
            $book_user->book_id = $request->input('book_id');
            $book_user->user_id = $user->id;
            $book_user->reserved = date('Y-m-d');
            $book_user->save();
            return response()->json(["success" => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
    /**
     * Payments for user
     *
     * Get all payments for user, student select there payment and parent select for there students sending user_id of that student
     *
     * @Get("/payments")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "user_id":"1"}),
     *      @Response(200, body={
                "payments": {{
                    "id": 1,
                    "amount": "10.5",
                    "title": "This is title of payment",
                    "description": "This is description of payment",
                    "created_at": "2015-06-05 10:55:11"
                }}
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function payments(Request $request)
    {
        $data = array(
            'user_id' => $request->input('user_id'),
        );
        $rules = array(
            'user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $payments = Payment::where('user_id', $request->input('user_id'))
                ->oderBy('id', 'DESC')
                ->select(array('id', 'amount','title', 'description','created_at'));
            return response()->json(['payments'=>$payments], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get student for user and school year, parent use it for there students
     *
     * @Get("/student")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "user_id":"1", "school_year_id":"1"}),
     *      @Response(200, body={
                "student":{{
                    "student_id": "1",
                    "section_id": "1",
                    "section_name": "1-2",
                    "order": "2"
                }}
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function student(Request $request)
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
            $student = Student::join('sections', 'sections.id', '=', 'students.section_id')
                ->where('user_id', $request->input('user_id'))
                ->where('school_year_id', $request->input('school_year_id'))
                ->select(array('students.id as student_id', 'students.section_id','students.title as section_name','students.order'));
            return response()->json(['student'=>$student], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get search books
     *
     * @Get("/book_search")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "search":"Test book"}),
     *      @Response(200, body={
                "books": {
                            {"id": "1",
                            "subject": "History",
                            "subject_id":3,
                            "title": "History of world 1",
                            "author": "Group of authors",
                            "year": "2015",
                            "internal": "2015/15",
                            "publisher": "Book publisher",
                            "version": "0.2",
                            "issued": 2,
                            "quantity": 2},
                        {"id": "2",
                            "subject": "English",
                            "subject_id":1,
                            "title": "English 2",
                            "author": "Group of authors",
                            "year": "2015",
                            "internal": "2015/15",
                            "publisher": "Book publisher",
                            "version": "0.2",
                            "issued": 1,
                            "quantity": 2}
                    }
                })
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function bookSearch(Request $request)
    {
        $data = array(
            'search' => $request->input('search'),
        );
        $rules = array(
            'search' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $books = Book::leftJoin('subjects', 'subjects.id', '=', 'books.subject_id')
                ->where(function($query) use($request) {
                    return $query ->where('books.title', 'LIKE', "%".$request->input('search')."%")
                        ->orWhere('books.author', 'LIKE', "%".$request->input('search')."%")
                        ->orWhere('books.year', 'LIKE', "%".$request->input('search')."%")
                        ->orWhere('books.internal', 'LIKE', "%".$request->input('search')."%")
                        ->orWhere('books.publisher', 'LIKE', "%".$request->input('search')."%");
                })
                ->select(array('books.id','subjects.id as subject_id', 'subjects.title as subject', 'books.title', 'books.author', 'books.year'
                , 'books.internal', 'books.publisher', 'books.version','books.quantity',
                    DB::raw('(SELECT count(id) FROM book_users as bu
                                WHERE bu.id= books.id AND bu.back IS NULL AND bu.get IS NOT NULL) as issued')
                ))
                ->get()->toArray();
            return response()->json(['books'=>$books], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get search users
     *
     * @Get("/user_search")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "search":"Test user"}),
     *      @Response(200, body={
                "users":{{"id": "1",
                    "name": "Name Surname"}}
                })
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function userSearch(Request $request)
    {
        $data = array(
            'search' => $request->input('search'),
        );
        $rules = array(
            'search' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $search = explode(' ', $request->input('search'));
            $users = User::where('first_name', 'LIKE', "%$search[0]%")
                ->orWhere('first_name', 'LIKE', "%$search[1]%")
                ->orWhere('last_name', 'LIKE', "%$search[0]%")
                ->orWhere('last_name', 'LIKE', "%$search[1]%")
                ->select(array('users.id', DB::raw('CONCAT(users.first_name, " ", users.last_name) as name')))->get()->toArray();
            return response()->json(['users'=>$users], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Reserved books for user
     *
     * Get all reserved books for user
     *
     * @Get("/reserved_user_books")
     * @Versions({"v1"})
     * @Request({"token": "foo", "user_id":5})
     * @Response(200, body={
        "books":{
                 {"id": "1",
                "title": "Book for mathematics",
                "author": "Group of authors",
                "subject": "Mathematics",
                "reserved": "2015-08-10"}
                 }
            })
     */
    public function reservedUserBooks(Request $request)
    {
        $data = array(
            'user_id' => $request->input('user_id'),
        );
        $rules = array(
            'user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $books = BookUser::join('books', 'books.id', '=', 'book_users.book_id')
                ->whereNotNull('reserved')
                ->whereNull('get')
                ->whereNull('books.deleted_at')
                ->where('book_users.user_id', $request->input('user_id'))
                ->select(array('book_users.id', 'books.internal','books.title', 'books.author',
                    DB::raw('(SELECT title from subjects where subjects.id=books.subject_id) as subject'),
                    'book_users.reserved'))->get()->toArray();
            return response()->json(['books'=>$books], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Borrowed books for user
     *
     * Get all borrowed books for user
     *
     * @Get("/borrowed_user_books")
     * @Versions({"v1"})
     * @Request({"token": "foo", "user_id":5})
     * @Response(200, body={
            "books": {
                {
                    "id": 12,
                    "title": "EngLib",
                    "author": "Ruth D. Brown",
                    "subject": "English",
                    "internal" : "12-15",
                    "get": "2015-09-11"
                },
                {
                    "id": 13,
                    "title": "SciLib",
                    "author": "Matthew D. Stewart",
                    "subject": "Science",
                    "internal" : "158/59",
                    "get": "2015-09-11"
                },
            },
            "user": {
                "id": 15,
                "name": "Full Name",
                "email": "address@sms.com",
                "address": "Kincheloe Road Portland",
                "mobile": "345376587657",
                "phone": "",
                "gender": 1
            }
            })
     */
    public function borrowedUserBooks(Request $request)
    {
        $data = array(
            'user_id' => $request->input('user_id'),
        );
        $rules = array(
            'user_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $books = BookUser::join('books', 'books.id', '=', 'book_users.book_id')
                ->whereNull('back')
                ->whereNotNull('get')
                ->whereNull('books.deleted_at')
                ->where('book_users.user_id', $request->input('user_id'))
                ->select(array('book_users.id', 'books.internal', 'books.title', 'books.author',
                    DB::raw('(SELECT title from subjects where subjects.id=books.subject_id) as subject'),
                    'book_users.get'))->get()->toArray();
            $user = User::where('id',$request->input('user_id'))
                ->select(array('id', DB::raw('CONCAT(first_name, " ", last_name) as name'),
                    'email', 'address', 'mobile', 'phone', 'gender'))->first();
            return response()->json(['books'=> $books, 'user' =>$user], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Books for subject
     *
     * Get all books for subject
     *
     * @Get("/subject_books")
     * @Versions({"v1"})
     * @Transaction({
           @Request({"token": "foo","subject_id":"5"}),
           @Response(200, body={
                "books": {
                    {"id": "1",
                    "title": "Book for mathematics",
                    "author": "Group of authors",
                    "year": "2015",
                    "internal" : "15-14",
                    "publisher": "Book publisher",
                    "version": "0.2",
                    "quantity": 2,
                    "issued": 1}
                    }
            }),
           @Response(500, body={"error":"not_valid_data"})
            })
     * })
     */
    public function subjectBooks(Request $request)
    {
        $data = array(
            'subject_id' => $request->input('subject_id'),
        );
        $rules = array(
            'subject_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $books = Book::where('subject_id', $request->input('subject_id'))
                ->select(array('books.id','books.title', 'books.author', 'books.year'
                , 'books.internal', 'books.publisher', 'books.version','books.quantity',
                    DB::raw('(SELECT count(id) FROM book_users as bu
                                WHERE bu.id= books.id AND bu.back IS NULL AND bu.get IS NOT NULL) as issued')
                ))->get()->toArray();
            return response()->json(['books'=>$books], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Feedback
     *
     * Get all Feedback for user
     *
     * @Get("/feedback")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
                "feedback": {
                    "id": 1,
                    "title": "This is title of notice",
                    "type": "request",
                    "description": "This is description of notice",
                    "date": "2015-02-02 15:32:11"
                    }
            }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function feedback(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (isset($user)) {
            $feedback = Feedback::where('user_id', $user->id)
                ->select(array('id', 'title','feedback_type as type','description','date'))
                ->get()->toArray();

            return response()->json(['feedback' => $feedback], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post feedback from user
     *
     * @Post("/post_feedback")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "feedback_type":"request|praise|contact|error|proposal", "role":"student\librarian|teacher|parent","title":"This is title", "description":"This is description"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postFeedback(Request $request)
    {
        $data = array(
            'feedback_type' => $request->input('feedback_type'),
            'role' => $request->input('role'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'feedback_type' => 'required',
            'title' => 'required',
            'role' => 'required',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = JWTAuth::parseToken()->authenticate();
            $feedback = new Feedback($request->except('token'));
            $feedback->user_id = $user->id;
            $feedback->save();
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Transportation
     *
     * Get all transportation routes
     *
     * @Get("/transportations_directions")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
                "transport": {
                    "id": 1,
                    "title": "This is title of notice",
                    "start": "2015-02-02",
                    "end": "",
                    "locations": {
                                "id": 1,
                                "name": "San Jose",
                                "lat": "",
                                "lang": ""
                            }
                    }
                }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function transportationsDirections(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (isset($user)) {
            $transport = Transportation::select(array('id', 'title','start','end'))->get()->toArray();
            foreach($transport as $key=>$item)
            {
                $locations = TransportationLocation::where('transportation_id', $item['id'])
                    ->select(array('id', 'name','lat','lang'))->get()->toArray();
                $transport[$key]['locations'] = (isset($locations))?$locations:array();
            }
            return response()->json(['transport' => $transport], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}