<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests\Secure\MarkValueRequest;
use App\Models\MarkValue;
use App\Repositories\MarkValueRepository;
use Datatables;
use Session;
use Auth;

class MarkValueController extends SecureController
{
    /**
     * @var MarkValueRepository
     */
    private $markValueRepository;

    /**
     * MarkValueController constructor.
     * @param MarkValueRepository $markValueRepository
     */
    public function __construct(MarkValueRepository $markValueRepository)
    {
        parent::__construct();

        $this->markValueRepository = $markValueRepository;

        view()->share('type', 'markvalue');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('markvalue.markvalues');
        return view('markvalue.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('markvalue.new');
        return view('markvalue.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(MarkValueRequest $request)
    {
        $markValue = new MarkValue($request->all());
        $markValue->save();

        return redirect('/markvalue');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(MarkValue $markValue)
    {
        $title = trans('markvalue.details');
        $action = 'show';
        return view('markvalue.show', compact('markValue', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(MarkValue $markValue)
    {
        $title = trans('markvalue.edit');
        return view('markvalue.edit', compact('title', 'markValue'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(MarkValueRequest $request, MarkValue $markValue)
    {
        $markValue->update($request->all());
        return redirect('/markvalue');
    }

    public function delete(MarkValue $markValue)
    {
        // Title
        $title = trans('markvalue.delete');
        return view('/markvalue/delete', compact('markValue', 'title'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(MarkValue $markValue)
    {
        $markValue->delete();
        return redirect('/markvalue');
    }

    public function data()
    {
        $markValues = $this->markValueRepository->getAll()
            ->get()
            ->map(function ($markValue) {
                return [
                    'id' => $markValue->id,
                    'title' => $markValue->title,
                ];
            });

        return Datatables::of($markValues)
            ->add_column('actions', '<a href="{{ url(\'/markvalue/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/markvalue/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/markvalue/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }
}
