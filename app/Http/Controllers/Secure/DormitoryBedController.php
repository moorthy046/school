<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\DormitoryBedRequest;
use App\Models\DormitoryBed;
use App\Repositories\DormitoryBedRepository;
use App\Repositories\DormitoryRoomRepository;
use App\Repositories\StudentRepository;
use Datatables;
use Session;
use DB;

class DormitoryBedController extends SecureController
{
    /**
     * @var DormitoryRoomRepository
     */
    private $dormitoryRoomRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * @var DormitoryBedRepository
     */
    private $dormitoryBedRepository;

    /**
     * DormitoryBedController constructor.
     * @param DormitoryRoomRepository $dormitoryRoomRepository
     * @param StudentRepository $studentRepository
     * @param DormitoryBedRepository $dormitoryBedRepository
     */
    public function __construct(DormitoryRoomRepository $dormitoryRoomRepository,
                                StudentRepository $studentRepository,
                                DormitoryBedRepository $dormitoryBedRepository)
    {
        parent::__construct();

        $this->dormitoryRoomRepository = $dormitoryRoomRepository;
        $this->studentRepository = $studentRepository;
        $this->dormitoryBedRepository = $dormitoryBedRepository;

        view()->share('type', 'dormitorybed');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('dormitorybed.dormitorybeds');
        return view('dormitorybed.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('dormitorybed.new');
        $this->generateParams();

        return view('dormitorybed.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request|DormitoryBedRequest $request
     * @return Response
     */
    public function store(DormitoryBedRequest $request)
    {
        $bed = new DormitoryBed($request->all());
        $bed->save();
        return redirect('/dormitorybed');
    }

    /**
     * Display the specified resource.
     *
     * @param DormitoryBed $bed
     * @return Response
     * @internal param int $id
     */
    public function show(DormitoryBed $bed)
    {
        $title = trans('dormitorybed.details');
        $action = 'show';
        return view('dormitorybed.show', compact('bed', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DormitoryBed $bed
     * @return Response
     * @internal param int $id
     */
    public function edit(DormitoryBed $bed)
    {
        $title = trans('dormitorybed.edit');

        $this->generateParams();

        return view('dormitorybed.edit', compact('title', 'bed'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request|DormitoryBedRequest $request
     * @param DormitoryBed $bed
     * @return Response
     * @internal param int $id
     */
    public function update(DormitoryBedRequest $request, DormitoryBed $bed)
    {
        $bed->update($request->all());
        return redirect('/dormitorybed');

    }

    public function delete(DormitoryBed $bed)
    {
        $title = trans('dormitorybed.delete');
        return view('/dormitorybed/delete', compact('bed', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DormitoryBed $bed
     * @return Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy(DormitoryBed $bed)
    {
        $bed->delete();
        return redirect('/dormitorybed');
    }

    public function data()
    {
        $dormitoryBeds = $this->dormitoryBedRepository->getAll()
            ->with('dormitory_room','student')
            ->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "title" => $item->title,
                    "room" => isset($item->dormitory_room)?$item->dormitory_room->title:"",
                ];
            });
        return Datatables::of($dormitoryBeds)
            ->add_column('actions', '<a href="{{ url(\'/dormitorybed/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/dormitorybed/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/dormitorybed/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    /**
     * @return array
     */
    private function generateParams()
    {
        $dormitory_rooms = $this->dormitoryRoomRepository->getAll()
            ->with('dormitory')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => (isset($item->dormitory) ? $item->dormitory->title : "") . ' ' . $item->title,
                ];
            })->lists('title', 'id')
            ->toArray();
        view()->share('dormitory_rooms', $dormitory_rooms);

        $students = $this->studentRepository->getAllForSchoolYear(Session::get('current_school_year'))
            ->with('user', 'section')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => (isset($student->section) ? $student->section->title : "") . " " .
                    isset($student->user) ? $student->user->full_name : "",
                ];
            })->lists('name', 'id')->toArray();
        view()->share('students', $students);
    }
}
