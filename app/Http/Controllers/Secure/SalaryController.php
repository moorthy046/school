<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Http\Requests\Secure\SalaryRequest;
use App\Models\Salary;
use App\Repositories\SalaryRepository;
use App\Repositories\UserRepository;
use Datatables;
use Session;
use DB;

class SalaryController extends SecureController
{
    /**
     * @var SalaryRepository
     */
    private $salaryRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * SalaryController constructor.
     * @param UserRepository $userRepository
     * @param SalaryRepository $salaryRepository
     */
    public function __construct(SalaryRepository $salaryRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();

        $this->salaryRepository = $salaryRepository;
        $this->userRepository = $userRepository;

        view()->share('type', 'salary');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('salary.salary');
        return view('salary.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('salary.new');
        $users = $this->list_of_users();
        return view('salary.create', compact('title','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(SalaryRequest $request)
    {
        $salary = new Salary($request->all());
        $salary->school_id = Session::get('current_school');
        $salary->school_year_id = Session::get('current_school_year');
        $salary->save();
        return redirect('/salary');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Salary $salary)
    {
        $title = trans('salary.details');
        $action = 'show';
        return view('salary.show', compact('salary', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Salary $salary
     * @return Response
     */
    public function edit(Salary $salary)
    {
        $title = trans('salary.edit');
        $users = $this->list_of_users();
        return view('salary.edit', compact('title', 'salary','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(SalaryRequest $request, Salary $salary)
    {
        $salary->update($request->all());
        return redirect('/salary');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Salary $salary)
    {
        $title = trans('salary.delete');
        return view('/salary/delete', compact('salary', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect('/salary');
    }

    public function data()
    {
        $salaries = $this->salaryRepository->getAllForSchoolYearSchool(Session::get('current_school_year'),Session::get('current_school'))
            ->with('user')
            ->get()
            ->map(function ($salary) {
                return [
                    'id' => $salary->id,
                    'salary' => $salary->salary,
                    'date' => $salary->date,
                    'full_name' => isset($salary->user)?$salary->user->full_name:"",
                ];
            });

        return Datatables::of($salaries)
            ->add_column('actions', '<a href="{{ url(\'/salary/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/salary/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/salary/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    /**
     * @return mixed
     */
    public function list_of_users()
    {
        $teachers = $this->userRepository->getUsersForRole('teacher')
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        $human_resources = $this->userRepository->getUsersForRole('human_resources')
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        $admins = $this->userRepository->getUsersForRole('admin')
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ];
            })
            ->lists('name', 'id')
            ->toArray();
        return $teachers+$human_resources+$admins;
    }
}
