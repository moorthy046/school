<?php

namespace App\Http\Controllers\Secure;

use App\Events\Message\MessageCreated;
use App\Http\Requests\Secure\MailboxRequest;
use App\Models\Message;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Sentinel;
use App\Http\Requests;

class MailboxController extends SecureController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     * @internal param CompanyRepository $
     */
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;

        view()->share('type', 'mailbox');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('mailbox.mailbox');


        return view('mailbox.index', compact('title'));
    }

    public function getAllData()
    {
        $email_list = Message::where('user_id_receiver', $this->user->id)->whereNull('deleted_at_receiver')->orderBy('id','desc')->get();
        $sent_email_list = Message::where('user_id_sender', $this->user->id)->whereNull('deleted_at_sender')->orderBy('id','desc')->get();

        $users = $this->userRepository->getAll()
            ->where('id','<>',$this->user->id)->get()
            ->filter(function ($user) {
                return ($user->inRole('student') || $user->inRole('parent') || $user->inRole('teacher') || $user->inRole('librarian'));
            })
            ->map(function ($user) {
                return [
                    'id'   => $user->id,
                    'text' => $user->full_name,
                ];
            })->values();

        $users_list = $this->userRepository->getAll()
            ->where('id', '<>', $this->user->id)->get()
            ->filter(function ($user) {
                return ($user->inRole('student') || $user->inRole('parent') || $user->inRole('teacher') || $user->inRole('librarian'));
            })
            ->map(function ($user) {
                return [
                    'full_name'   => $user->full_name,
                    'user_avatar' => $user->user_avatar,
                    'active'      => (isset($user->last_login) && $user->last_login >= Carbon::now()->subMinutes('15')->toDateTimeString()) ? '1' : '0',
                ];
            });
        return response()->json(compact('email_list', 'sent_email_list', 'users','users_list'), 200);
    }


    public function getMail($id)
    {
        $email = Message::with('sender')->find($id);
        $email->read = 1;
        $email->save();

        return response()->json(compact('email'), 200);
    }

    function sendEmail(MailboxRequest $request)
    {
        $message_return = '<div class="alert alert-danger">' . trans('mailbox.danger') . '</div>';
        if (!empty($request->recipients)) {
            foreach ($request->recipients as $item) {
                if ($item != "0" && $item != "") {
                    $email = new Message($request->except('recipients'));
                    $email->user_id_receiver = $item;
                    $email->user_id_sender = $this->user->id;
                    $email->save();
    
                    event(new MessageCreated($email));

                    $user = User::find($item);

                    if (!filter_var(Settings::get('site_email'), FILTER_VALIDATE_EMAIL) === false) {
                        Mail::send('emails.contact', array('user' => $user->first_name . ' ' . $user->last_name, 'bodyMessage' => $request->message),
                            function ($m)
                            use ($user, $request) {
                                $m->from(Settings::get('site_email'), Settings::get('site_name'));
                                $m->to($user->email)->subject($request->subject);
                            });
                    }

                    $message_return = '<div class="alert alert-success">' . trans('mailbox.success') . '</div>';
                }

            }
        }
        echo $message_return;

    }

    function deleteMail(Email $mail)
    {
        if($mail->to == $this->user->id){
            $mail->deleted_at_receiver= Carbon::now();
        }
        else{
            $mail->deleted_at_sender= Carbon::now();
        }
        $mail->save();
    }


    public function postRead(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $model = Message::find($request->get('id'));
        $model->read = true;
        $model->save();

        return response()->json(['message' => trans('mailbox.update_status')], 200);
    }


    public function getData()
    {
        $emails_list = Message::where('user_id_receiver', $this->user->id)
            ->whereNull('deleted_at_receiver')
            ->where('read', 0)
            ->with('sender')
            ->orderBy('id', 'desc');

        $total = $emails_list->count();
        $emails = $emails_list->latest()->take(5)->get();

        return response()->json(compact('total', 'emails'), 200);
    }

    public function postMarkAsRead(Request $request)
    {
        if ($ids = $request->get('ids')) {
            if (is_array($ids)) {
                $messages = Message::whereIn('id', $ids)->get();
                foreach ($messages as $message) {
                    $message->read = true;
                    $message->save();
                }
            } else {
                $message = Message::find($ids);
                $message->read = true;
                $message->save();
            }
        }
    }

    public function getSent()
    {
        $sent = Message::where('user_id_sender', $this->user->id)
            ->whereNull('deleted_at_sender')
            ->with('receiver')
            ->orderBy('id', 'desc')->get();

        return response()->json(compact('sent'), 200);
    }

    public function getReceived(Request $request)
    {
        $received_list = Message::where('user_id_receiver', $this->user->id)
            ->whereNull('deleted_at_receiver')
            ->where('title', 'like', '%' . $request->get('query', '') . '%')
            ->with('sender');
        $received = $received_list->orderBy('id', 'desc')->get();
        $received_count = $received_list->count();
        return response()->json(compact('received','received_count'), 200);
    }


    public function postSend(Request $request)
    {
        foreach ($request->recipients as $item) {
            if ($item != "0" && $item != "") {
                $email = new Message($request->except('recipients', 'emailTemplate'));
                $email->user_id_receiver = $item;
                $email->user_id_sender = \Sentinel::getUser()->id;
                $email->save();

                event(new MessageCreated($email));
                $user = User::find($item);

                if (!filter_var(Settings::get('site_email'), FILTER_VALIDATE_EMAIL) === false) {
                    Mail::send('emails.contact', array('user' => $user->full_name, 'bodyMessage' => $request->message),
                        function ($m)
                        use ($user, $request) {
                            $m->from(Settings::get('site_email'), Settings::get('site_name'));
                            $m->to($user->email)->subject($request->subject);
                        });
                }
            }
        }

        return response()->json(['message' => 'Message sent successfully!'], 200);

    }

    public function postDelete(Request $request)
    {
        if ($ids = $request->get('ids')) {
            if (is_array($ids)) {
                $messages = Message::whereIn('id', $ids)->get();
                foreach ($messages as $message) {
                    $message->deleted_at_receiver = date('Y-m-d H:i:s');
                    $message->save();
                }
            } else {
                $message = Message::find($ids);
                $message->deleted_at_receiver = date('Y-m-d H:i:s');
                $message->save();
            }
        }
    }

    public function postReply($id, Request $request)
    {
        $orgMail = Message::find($id);

        $request->merge([
            'title' => 'Re: ' . $orgMail->title,
        ]);

        $email = new Message($request->all());
        $email->user_id_receiver = $orgMail->user_id_sender;
        $email->user_id_sender = \Sentinel::getUser()->id;
        $email->save();

        event(new MessageCreated($email));

        $user = User::find($orgMail->user_id_receiver);


        if (!filter_var(Settings::get('site_email'), FILTER_VALIDATE_EMAIL) === false) {
            Mail::send('emails.contact', array('user' => $user->full_name, 'bodyMessage' => $request->message),
                function ($m)
                use ($user, $request) {
                    $m->from(Settings::get('site_email'), Settings::get('site_name'));
                    $m->to($user->email)->subject($request->subject);
                });
        }

    }

}
