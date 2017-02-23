<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\Direction;
use App\Models\Subject;
use App\Repositories\DirectionRepository;
use App\Repositories\SectionRepository;
use App\Repositories\SubjectRepository;
use Datatables;
use Guzzle\Http\Message\Response;
use Session;
use App\Http\Requests\Secure\SubjectRequest;


class SubjectController extends SecureController
{
    /**
     * @var SubjectRepository
     */
    private $subjectRepository;
    /**
     * @var DirectionRepository
     */
    private $directionRepository;

    /**
     * SubjectController constructor.
     * @param SubjectRepository $subjectRepository
     * @param DirectionRepository $directionRepository
     */
    public function __construct(SubjectRepository $subjectRepository,
                                DirectionRepository $directionRepository)
    {
        parent::__construct();

        $this->subjectRepository = $subjectRepository;
        $this->directionRepository = $directionRepository;

        view()->share('type', 'subject');
    }

    /**
     *
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $title = trans('subject.subjects');
        return view('subject.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('subject.new');
        $directions = $this->directionRepository->getAll()->lists('title', 'id')->toArray();
        return view('subject.create', compact('title', 'directions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request|SubjectRequest $request
     * @return Response
     */
    public function store(SubjectRequest $request)
    {
        $subject = new Subject($request->all());
        $subject->save();
        return redirect('/subject');
    }

    /**
     * Display the specified resource.
     *
     * @param Subject $subject
     * @return Response
     * @internal param int $id
     */
    public function show(Subject $subject)
    {
        $title = trans('subject.details');
        $action = 'show';
        return view('subject.show', compact('subject', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Subject $subject
     * @return Response
     * @internal param int $id
     */
    public function edit(Subject $subject)
    {
        $title = trans('subject.edit');
        $directions = $this->directionRepository->getAll()->lists('title', 'id')->toArray();
        return view('subject.edit', compact('title', 'subject', 'directions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request|SubjectRequest $request
     * @param Subject $subject
     * @return Response
     * @internal param int $id
     */
    public function update(SubjectRequest $request, Subject $subject)
    {
        $subject->update($request->all());
        return redirect('/subject');
    }

    public function delete(Subject $subject)
    {
        $title = trans('subject.delete');
        return view('/subject/delete', compact('subject', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subject $subject
     * @return Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect('/subject');
    }

    public function data()
    {
        $subjects = $this->subjectRepository->getAll()
            ->with('direction')
            ->orderBy('subjects.class')
            ->orderBy('subjects.order')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'order' => $subject->order,
                    'class' => $subject->class,
                    'title' => $subject->title,
                    'direction' => isset($subject->direction) ? $subject->direction->title : "",
                ];
            });

        return Datatables::of($subjects)
            ->add_column('actions', '<a href="{{ url(\'/subject/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/subject/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/subject/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }
}
