<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\DormitoryRoomRequest;
use App\Models\DormitoryRoom;
use App\Repositories\DormitoryRepository;
use App\Repositories\DormitoryRoomRepository;
use Datatables;
use Session;

class DormitoryRoomController extends SecureController
{
    /**
     * @var DormitoryRoomRepository
     */
    private $dormitoryRoomRepository;
    /**
     * @var DormitoryRepository
     */
    private $dormitoryRepository;

    /**
     * DormitoryRoomController constructor.
     * @param DormitoryRoomRepository $dormitoryRoomRepository
     * @param DormitoryRepository $dormitoryRepository
     */
    public function __construct(DormitoryRoomRepository $dormitoryRoomRepository,
                                DormitoryRepository $dormitoryRepository)
    {
        parent::__construct();

        $this->dormitoryRoomRepository = $dormitoryRoomRepository;
        $this->dormitoryRepository = $dormitoryRepository;

        view()->share('type', 'dormitoryroom');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('dormitoryroom.dormitoryrooms');
        return view('dormitoryroom.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('dormitoryroom.new');
        $dormitories = $this->dormitoryRepository->getAll()->get()
            ->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title
            ];
            })->lists('title','id')->toArray();
        return view('dormitoryroom.create', compact('title', 'dormitories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(DormitoryRoomRequest $request)
    {
        $room = new DormitoryRoom($request->all());
        $room->save();
        return redirect('/dormitoryroom');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(DormitoryRoom $room)
    {
        $title = trans('dormitoryroom.details');
        $action = 'show';
        return view('dormitoryroom.show', compact('room', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(DormitoryRoom $room)
    {
        $title = trans('dormitoryroom.edit');
        $dormitories = $this->dormitoryRepository->getAll()->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title
                ];
            })->lists('title','id')->toArray();
        return view('dormitoryroom.edit', compact('title', 'room', 'dormitories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(DormitoryRoomRequest $request, DormitoryRoom $room)
    {
        $room->update($request->all());
        return redirect('/dormitoryroom');
    }

    public function delete(DormitoryRoom $room)
    {
        $title = trans('dormitoryroom.delete');
        return view('/dormitoryroom/delete', compact('room', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(DormitoryRoom $room)
    {
        $room->delete();
        return redirect('/dormitoryroom');
    }

    public function data()
    {
        $rooms =  $this->dormitoryRoomRepository->getAll()
            ->with('dormitory')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'title' => $room->title,
                    'dormitory' => isset($room->dormitory)?$room->dormitory->title:"",
                ];
            });

        return Datatables::of($rooms)
            ->filterColumn('dormitory', 'whereRaw', "dormitories.title like ?", ["$1"])
            ->add_column('actions', '<a href="{{ url(\'/dormitoryroom/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/dormitoryroom/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/dormitoryroom/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }
}
