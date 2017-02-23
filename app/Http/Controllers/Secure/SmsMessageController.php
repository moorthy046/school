<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\SmsMessageRequest;
use App\Models\SmsMessage;
use App\Models\User;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherSubjectRepository;
use App\Repositories\SmsMessageRepository;
use Twilio;
use Datatables;
use Session;

class SmsMessageController extends SecureController
{
    /**
     * @var SmsMessageRepository
     */
    private $smsMessageRepository;
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * @param SmsMessageRepository $smsMessageRepository
     * @param TeacherSubjectRepository $teacherSubjectRepository
     * @param StudentRepository $studentRepository
     */
    public function __construct(SmsMessageRepository $smsMessageRepository,
                                TeacherSubjectRepository $teacherSubjectRepository,
                                StudentRepository $studentRepository)
    {
        parent::__construct();

        $this->smsMessageRepository = $smsMessageRepository;
        $this->teacherSubjectRepository = $teacherSubjectRepository;
        $this->studentRepository = $studentRepository;

        view()->share('type', 'sms_message');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('sms_message.sms_messages');
        return view('sms_message.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('sms_message.new');
        $teachers = $this->teacherSubjectRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('teacher')
            ->get()
            ->filter(function($teacher){
                return (isset($teacher->teacher) && isset($teacher->teacher->mobile) &&
                    $teacher->teacher->mobile!="");
            })
            ->map(function ($teacher) {
                return [
                    'user_id' => $teacher->user_id,
                    'full_name' => $teacher->teacher->full_name,
                ];
            })->lists('full_name','user_id')->toArray();

        $students = $this->studentRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('user')
            ->get()
            ->filter(function($student){
                return (isset($student->user) && isset($student->user->mobile) &&
                    $student->user->mobile!="");
            })
            ->map(function ($student) {
                return [
                    'user_id' => $student->user_id,
                    'full_name' => $student->user->full_name,
                ];
            })->lists('full_name','user_id')->toArray();
        $users = array();
        foreach($teachers as $key=>$item)
        {
            $users[$key] = $item;
        }
        foreach($students as $key=>$item)
        {
            $users[$key] = $item;
        }

        return view('sms_message.create', compact('title','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(SmsMessageRequest $request)
    {
        if(count($request->users_select)>0)
        {
            foreach ($request->users_select as $item)
            {
                $user = User::find($item);
                $smsMessage = new SmsMessage();
                $smsMessage->text = $request->text;
                $smsMessage->number = $user->mobile;
                $smsMessage->user_id = $item;
                $smsMessage->user_id_sender = $this->user->id;
                $smsMessage->save();

                Twilio::message($user->mobile, $request->text);
            }
        }
        return redirect('/sms_message');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(SmsMessage $sms_message)
    {
        $title = trans('sms_message.details');
        $action = 'show';
        return view('sms_message.show', compact('sms_message', 'title', 'action','receivers'));
    }

    public function data()
    {
        $messages = $this->smsMessageRepository->getAllForSender($this->user->id)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'text' => $message->text,
                ];
            });

        return Datatables::of($messages)
            ->add_column('actions', '<a href="{{ url(\'/sms_message/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
            ->remove_column('id')
            ->make();
    }
}
