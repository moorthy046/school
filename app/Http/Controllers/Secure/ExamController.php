<?php

namespace App\Http\Controllers\Secure;

use App\Models\Exam;
use App\Http\Requests\Secure\ExamRequest;
use App\Repositories\ExamRepository;
use App\Repositories\TeacherSubjectRepository;
use Datatables;
use Session;

class ExamController extends SecureController
{
    /**
     * @var TeacherSubjectRepository
     */
    private $teacherSubjectRepository;
    /**
     * @var ExamRepository
     */
    private $examRepository;

    /**
     * ExamController constructor.
     * @param TeacherSubjectRepository $teacherSubjectRepository
     * @param ExamRepository $examRepository
     */
    public function __construct(TeacherSubjectRepository $teacherSubjectRepository,
                                ExamRepository $examRepository)
    {
        parent::__construct();

        $this->teacherSubjectRepository = $teacherSubjectRepository;
        $this->examRepository = $examRepository;

        view()->share('type', 'exam');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('exam.exam');
        return view('exam.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('exam.new');
        $subjects = $this->generateParams();
        return view('exam.create', compact('title','subjects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ExamRequest $request)
    {
        $exam = new Exam($request->all());
        $exam->user_id = $this->user->id;
        $exam->student_group_id = Session::get('current_student_group');
        $exam->save();

        return redirect('/exam');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Exam $exam)
    {
        $title = trans('exam.details');
        $action = 'show';
        return view('exam.show', compact('exam', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Exam $exam)
    {
        $title = trans('exam.edit');
        $subjects = $this->generateParams();
        return view('exam.edit', compact('title', 'exam','subjects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(ExamRequest $request, Exam $exam)
    {
        $exam->update($request->all());
        return redirect('/exam');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Exam $exam)
    {
        // Title
        $title = trans('exam.delete');
        return view('/exam/delete', compact('exam', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect('/exam');
    }

    public function data()
    {
        $exams = $this->examRepository->getAllForGroup(Session::get('current_student_group'))
            ->with('subject')
            ->get()
            ->map(function($exam){
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'subject' => isset($exam->subject->title)?$exam->subject->title:"",
                    'date' => $exam->date,
                ];
            });
        return Datatables::of($exams)
            ->add_column('actions', '<a href="{{ url(\'/exam/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/exam/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/exam/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
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
        return $subjects;
    }
}
