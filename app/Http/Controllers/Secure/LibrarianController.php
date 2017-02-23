<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\User;
use App\Repositories\UserRepository;
use Datatables;
use Session;
use DB;
use Sentinel;
use App\Http\Requests\Secure\LibrarianRequest;

class LibrarianController extends SecureController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * LibrarianController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;

        view()->share('type', 'librarian');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('librarian.librarian');
        return view('librarian.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('librarian.new');
        return view('librarian.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(LibrarianRequest $request)
    {
        $user = Sentinel::registerAndActivate($request->all());

        $role = Sentinel::findRoleBySlug('librarian');
        $role->users()->attach($user);

        $user = User::find($user->id);
        $user->update($request->except('password'));

        return redirect('/librarian');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(User $librarian)
    {
        $title = trans('librarian.details');
        $action = 'show';
        return view('librarian.show', compact('librarian', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(User $librarian)
    {
        $title = trans('librarian.edit');
        return view('librarian.edit', compact('title', 'librarian'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(LibrarianRequest $request, $user_id)
    {
        $librarian = User::find($user_id);
        if($request->password!="")
        {
            $librarian->password = bcrypt($request->password);
        }
        $librarian->update($request->except('password'));
        return redirect('/librarian');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(User $librarian)
    {
        $title = trans('librarian.delete');
        return view('/librarian/delete', compact('librarian', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(User $librarian)
    {
        $librarian->delete();
        return redirect('/librarian');
    }

    public function data()
    {
        $librarians = $this->userRepository->getUsersForRole('librarian')
            ->map(function ($librarian) {
                return [
                    'id' => $librarian->id,
                    'full_name' => $librarian->full_name,
                ];
            });
        return Datatables::of($librarians)
            ->add_column('actions', '<a href="{{ url(\'/librarian/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/librarian/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/librarian/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

}
