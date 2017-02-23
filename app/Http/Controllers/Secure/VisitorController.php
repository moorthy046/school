<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\School;
use App\Models\SchoolAdmin;
use App\Models\User;
use App\Models\Visitor;
use App\Repositories\UserRepository;
use Datatables;
use Sentinel;
use App\Http\Requests\Secure\SchoolAdminRequest;

class VisitorController extends SecureController
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

        view()->share('type', 'visitor');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('visitor.visitor');
        return view('visitor.index', compact('title'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(User $visitor)
    {
        // Title
        $title = trans('visitor.details');
        $action = 'show';
        return view('visitor.show', compact('visitor', 'title', 'action'));
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(User $visitor)
    {
        $title = trans('visitor.delete');
        return view('/visitor/delete', compact('visitor', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(User $visitor)
    {
        Visitor::where('user_id','=', $visitor->id)->delete();

        $visitor->delete();
        return redirect('/visitor');
    }

    public function data()
    {
        $visitors = $this->userRepository->getUsersForRole('visitor')
            ->map(function ($visitor) {
                return [
                    'id' => $visitor->id,
                    'full_name' => $visitor->full_name,
                ];
            });
        return Datatables::of($visitors)
            ->add_column('actions', '<a href="{{ url(\'/visitor/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/visitor_card/\' . $id ) }}"  target="_blank" class="btn btn-success btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("visitor.visitor_card") }}</a>
                                     <a href="{{ url(\'/visitor/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

}
