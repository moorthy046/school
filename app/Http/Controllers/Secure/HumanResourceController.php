<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\User;
use App\Repositories\UserRepository;
use Datatables;
use Session;
use DB;
use Sentinel;
use App\Http\Requests\Secure\TeacherRequest;

class HumanResourceController extends SecureController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TeacherController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;

        view()->share('type', 'human_resource');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('human_resource.human_resource');
        return view('human_resource.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('human_resource.new');
        return view('human_resource.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(TeacherRequest $request)
    {
        $user = Sentinel::registerAndActivate($request->all());

        $role = Sentinel::findRoleBySlug('human_resources');
        $role->users()->attach($user);

        $user = User::find($user->id);
        $user->update($request->except('password'));

        return redirect('/human_resource');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(User $human_resource)
    {
        // Title
        $title = trans('human_resource.details');
        $action = 'show';
        return view('human_resource.show', compact('human_resource', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(User $human_resource)
    {
        $title = trans('human_resource.edit');
        return view('human_resource.edit', compact('title', 'human_resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(TeacherRequest $request, $user_id)
    {
        $human_resource = User::find($user_id);
        if($request->password!="")
        {
            $human_resource->password = bcrypt($request->password);
        }
        $human_resource->update($request->except('password'));

        return redirect('/human_resource');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(User $human_resource)
    {
        $title = trans('human_resource.delete');
        return view('/human_resource/delete', compact('human_resource', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(User $human_resource)
    {
        $human_resource->delete();
        return redirect('/human_resource');
    }

    public function data()
    {
        $human_resources = $this->userRepository->getUsersForRole('human_resources')
            ->map(function ($human_resource) {
                return [
                    'id' => $human_resource->id,
                    'full_name' => $human_resource->full_name,
                ];
            });
        return Datatables::of($human_resources)
            ->add_column('actions', '<a href="{{ url(\'/human_resource/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/human_resource/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/human_resource/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

}
