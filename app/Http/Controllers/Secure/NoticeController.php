<?php

namespace App\Http\Controllers\Secure;

use App\Models\Notice;
use App\Http\Requests\Secure\NoticeRequest;
use App\Repositories\NoticeRepository;
use App\Repositories\NoticeTypeRepository;
use App\Repositories\StudentGroupRepository;
use App\Repositories\TeacherSubjectRepository;
use Sentinel;
use Datatables;
use DB;
use Session;

class NoticeController extends SecureController
{
    /**
     * @var NoticeRepository
     */
    private $noticeRepository;
    /**
     * @var StudentGroupRepository
     */
    private $studentGroupRepository;
    /**
     * @var NoticeTypeRepository
     */
    private $noticeTypeRepository;
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;

    /**
     * NoticeController constructor.
     * @param NoticeRepository $noticeRepository
     * @param StudentGroupRepository $studentGroupRepository
     * @param NoticeTypeRepository $noticeTypeRepository
     * @param TeacherSubjectRepository $teacherSubjectRepository
     */
    public function __construct(NoticeRepository $noticeRepository,
                                StudentGroupRepository $studentGroupRepository,
                                NoticeTypeRepository $noticeTypeRepository,
                                TeacherSubjectRepository $teacherSubjectRepository)
    {
        parent::__construct();

        $this->noticeRepository = $noticeRepository;
        $this->studentGroupRepository = $studentGroupRepository;
        $this->noticeTypeRepository = $noticeTypeRepository;
        $this->teacherSubjectRepository = $teacherSubjectRepository;

        view()->share('type', 'notice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('notice.notice');
        return view('notice.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('notice.new');
        $this->generateParams();

        return view('notice.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(NoticeRequest $request)
    {
        if ($this->user->inRole('admin')) {
            if (!is_null($request['group_id'])) {
                foreach ($request['group_id'] as $group_id) {
                    $subjects = $this->teacherSubjectRepository
                        ->getAllForGroup($group_id)->get();
                    if ($subjects->count() > 1) {
                        foreach ($subjects as $subject) {
                            $notice = new Notice($request->except('group_id', 'token'));
                            $notice->user_id = $this->user->id;
                            $notice->student_group_id = $group_id;
                            $notice->school_year_id = Session::get('current_school_year');
                            $notice->school_id = Session::get('current_school');
                            $notice->subject_id = $subject->subject_id;
                            $notice->save();
                        }
                    }
                }
            }
        } else {
            $notice = new Notice($request->except('token'));
            $notice->user_id = Sentinel::getUser()->id;
            $notice->student_group_id = Session::get('current_student_group');
            $notice->school_year_id = Session::get('current_school_year');
            $notice->school_id = Session::get('current_school');
            $notice->save();
        }
        return redirect('/notice');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Notice $notice)
    {
        $title = trans('notice.details');
        $action = 'show';
        return view('notice.show', compact('notice', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Notice $notice)
    {
        $title = trans('notice.edit');

        if ($this->user->inRole('admin')) {
            $subjects = $this->teacherSubjectRepository
                ->getAllForGroup($notice->student_group_id)
                ->with('subject')
                ->get()
                ->map(function($subject){
                    return [
                        'id' => $subject->subject->id,
                        'title' => $subject->subject->title
                    ];
                })
                ->lists('title', 'id')->toArray();
            view()->share('subjects', $subjects);
        }
        $this->generateParams();
        return view('notice.edit', compact('title', 'notice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(NoticeRequest $request, Notice $notice)
    {
        $notice->update($request->all());
        return redirect('/notice');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Notice $notice)
    {
        // Title
        $title = trans('notice.delete');
        return view('/notice/delete', compact('notice', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect('/notice');
    }

    public function data()
    {
        if ($this->user->inRole('admin')) {
            $notices = $this->noticeRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
                ->get()
                ->map(function ($notice) {
                    return [
                        'id' => $notice->id,
                        'title' => $notice->title,
                        'subject' => isset($notice->subject) ? $notice->subject->title : "",
                        'date' => $notice->date,
                    ];
                });
        } else {
            $notices = $this->noticeRepository
                ->getAllForSchoolYearAndGroup(Session::get('current_school_year'),Session::get('current_student_group'))
                ->get()
                ->map(function ($notice) {
                    return [
                        'id' => $notice->id,
                        'title' => $notice->title,
                        'subject' => isset($notice->subject) ? $notice->subject->title : "",
                        'date' => $notice->date,
                    ];
                });
        }
        return Datatables::of($notices)
            ->add_column('actions', '<a href="{{ url(\'/notice/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/notice/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/notice/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    private function generateParams()
    {
        if ($this->user->inRole('teacher')) {
            $subjects = $this->teacherSubjectRepository
                ->getAllForSchoolYearAndGroupAndTeacher(Session::get('current_school_year'),
                    Session::get('current_student_group'),
                    $this->user->id)
                ->with('subject')
                ->get()
                ->map(function ($subject) {
                    return [
                        'id' => $subject->subject->id,
                        'title' => $subject->subject->title,
                    ];
                })->lists('title', 'id')->toArray();
            view()->share('subjects', $subjects);
        }
        if ($this->user->inRole('admin')) {
            $groups = $this->studentGroupRepository->getAllForSchoolYearSchool(Session::get('current_school_year'),Session::get('current_school'))
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'title' => $group->class . ' ' . $group->title,
                    ];
                })->lists('title', 'id')->toArray();
            view()->share('groups', $groups);
        }
        $notice_types = $this->noticeTypeRepository->getAll()->get()
            ->map(function ($notice_type) {
                return [
                    'id' => $notice_type->id,
                    'title' => $notice_type->title,
                ];
            })->lists('title', 'id')->toArray();
        view()->share('notice_type', $notice_types);
    }
}
