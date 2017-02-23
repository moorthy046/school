<?php

namespace App\Http\Controllers\Secure;

use App\Models\Diary;
use App\Http\Requests\Secure\DairyRequest;
use App\Repositories\DiaryRepository;
use App\Repositories\TeacherSubjectRepository;
use Datatables;
use Session;

class DairyController extends SecureController
{
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;
    /**
     * @var DiaryRepository
     */
    private $diaryRepository;

    /**
     * DairyController constructor.
     * @param TeacherSubjectRepository $teacherSubjectRepository
     * @param DiaryRepository $diaryRepository
     */
    public function __construct(TeacherSubjectRepository $teacherSubjectRepository,
                                DiaryRepository $diaryRepository)
    {
        parent::__construct();

        $this->teacherSubjectRepository = $teacherSubjectRepository;
        $this->diaryRepository = $diaryRepository;

        view()->share('type', 'diary');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('diary.diary');
        return view('diary.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('diary.new');
        $this->generateParams();
        return view('diary.create', compact('title', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(DairyRequest $request)
    {
        $diary = new Diary($request->all());
        $diary->user_id = $this->user->id;
        $diary->school_year_id = Session::get('current_school_year');
        $diary->school_id = Session::get('current_school');
        $diary->save();

        return redirect('/diary');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Diary $diary)
    {
        $title = trans('diary.details');
        $action = 'show';
        return view('diary.show', compact('diary', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Diary $diary)
    {
        $title = trans('diary.edit');
        $this->generateParams();
        return view('diary.edit', compact('title', 'diary', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(DairyRequest $request, Diary $diary)
    {
        $diary->update($request->all());
        return redirect('/diary');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Diary $diary)
    {
        // Title
        $title = trans('diary.delete');
        return view('/diary/delete', compact('diary', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Diary $diary)
    {
        $diary->delete();
        return redirect('/diary');
    }

    public function data()
    {
        if ($this->user->inRole('teacher')) {
            $diaries = $this->diaryRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
                ->with('subject')->get()
                ->filter(function ($diary) {
                    return ($diary->user_id == $this->user->id);
                })
                ->map(function ($diary) {
                    return [
                        'id' => $diary->id,
                        'title' => $diary->title,
                        'subject' => isset($diary->subject) ? $diary->subject->title : "",
                        'hour' => $diary->hour,
                        'date' => $diary->date,
                    ];
                });
            return Datatables::of($diaries)
                ->add_column('actions', '<a href="{{ url(\'/diary/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/diary/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/diary/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
                ->remove_column('id')
                ->make();
        } else if ($this->user->inRole('student')) {
            $diaries = $this->diaryRepository->getAllForSchoolYearAndStudentUserId(Session::get('current_school_year'), $this->user->id)
                ->with('subject')
                ->get()
                    ->map(function ($diary) {
                        return [
                            'id' => $diary->id,
                            'title' => $diary->title,
                            'subject' => isset($diary->subject) ? $diary->subject->title : "",
                            'hour' => $diary->hour,
                            'date' => $diary->date,
                        ];
                    });
        } else if ($this->user->inRole('parent')) {
            $user = Session::get('current_student_user_id');

            $diaries = $this->diaryRepository->getAllForSchoolYearAndStudentUserId(Session::get('current_school_year'), $user)
                ->with('subject')
                ->get()
                ->map(function ($diary) {
                    return [
                        'id' => $diary->id,
                        'title' => $diary->title,
                        'subject' => isset($diary->subject) ? $diary->subject->title : "",
                        'hour' => $diary->hour,
                        'date' => $diary->date,
                    ];
                });
        } else {
            $diaries = $this->diaryRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'),Session::get('current_school'))
                ->with('subject')->get()
                ->map(function ($diary) {
                    return [
                        'id' => $diary->id,
                        'title' => $diary->title,
                        'subject' => isset($diary->subject) ? $diary->subject->title : "",
                        'hour' => $diary->hour,
                        'date' => $diary->date,
                    ];
                });
        }
        return Datatables::of($diaries->toBase()->unique())
            ->add_column('actions', '<a href="{{ url(\'/diary/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
            ->remove_column('id')
            ->make();
    }

    /**
     * @return mixed
     */
    private function generateParams()
    {
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

        view()->share('subjects', $subjects);
    }
}
