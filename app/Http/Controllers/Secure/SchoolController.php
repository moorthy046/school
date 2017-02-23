<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Http\Requests\Secure\SchoolRequest;
use App\Models\School;
use App\Repositories\SchoolRepository;
use Datatables;
use Efriandika\LaravelSettings\Facades\Settings;

class SchoolController extends SecureController
{
    /**
     * @var SchoolRepository
     */
    private $schoolRepository;

    /**
     * SchoolController constructor.
     * @param SchoolRepository $schoolRepository
     */
    public function __construct(SchoolRepository $schoolRepository)
    {
        parent::__construct();

        $this->schoolRepository = $schoolRepository;

        view()->share('type', 'schools');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('schools.school');
        return view('schools.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('schools.new');
        return view('schools.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(SchoolRequest $request)
    {
        $school = new School($request->except('student_card_background_file'));
        if ($request->hasFile('student_card_background_file') != "") {
            $file = $request->file('student_card_background_file');
            $extension = $file->getClientOriginalExtension();
            $picture = str_random(10) . '.' . $extension;

            $destinationPath = public_path() . '/uploads/student_card/';
            $file->move($destinationPath, $picture);
            $school->student_card_background = $picture;
        }
        $school->save();

        return redirect('/schools');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(School $school)
    {
        $title = trans('schools.details');
        $action = 'show';
        return view('schools.show', compact('school', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(School $school)
    {
        $title = trans('schools.edit');
        return view('schools.edit', compact('title', 'school'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(SchoolRequest $request, School $school)
    {
       if ($request->hasFile('student_card_background_file') != "") {
            $file = $request->file('student_card_background_file');
            $extension = $file->getClientOriginalExtension();
            $picture = str_random(10) . '.' . $extension;

            $destinationPath = public_path() . '/uploads/student_card/';
            $file->move($destinationPath, $picture);
            $school->student_card_background = $picture;
        }
        $school->update($request->except('student_card_background_file'));
        return redirect('/schools');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(School $school)
    {
        // Title
        $title = trans('schools.delete');
        return view('/schools/delete', compact('school', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param School $school
     * @return Response
     */
    public function destroy(School $school)
    {
        $school->delete();
        return redirect('/schools');
    }

    public function data()
    {
        if (Settings::get('multi_school') == 'yes') {
            if ($this->user->inRole('super_admin')) {
                $schools = $this->schoolRepository->getAll();
            } elseif ($this->user->inRole('admin') || $this->user->inRole('human_resources') || $this->user->inRole('librarian')) {
                $schools = $this->schoolRepository->getAllAdmin();
            } elseif ($this->user->inRole('teacher')) {
                $schools = $this->schoolRepository->getAllTeacher();
            } else {
                $schools = $this->schoolRepository->getAllStudent();
            }
        } else {
            if ($this->user->inRole('admin') || $this->user->inRole('human_resources') || $this->user->inRole('librarian')) {
                $schools = $this->schoolRepository->getAll();
            } elseif ($this->user->inRole('teacher')) {
                $schools = $this->schoolRepository->getAllTeacher();
            } else {
                $schools = $this->schoolRepository->getAllStudent();
            }
        }
        $schools = $schools->get()
            ->map(function ($school) {
                return [
                    'id' => $school->id,
                    'title' => $school->title,
                    'address' => $school->address,
                    'phone' => $school->phone,
                    'email' => $school->email,
                ];
            });
        if (Settings::get('multi_school') == 'yes') {
            if ($this->user->inRole('super_admin')) {
                return Datatables::of($schools)
                    ->add_column('actions', '<a href="{{ url(\'/schools/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                     <a href="{{ url(\'/schools/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                    <a href="{{ url(\'/schools/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
                    ->remove_column('id')
                    ->make();
            } elseif ($this->user->inRole('admin')) {
                return Datatables::of($schools)
                    ->add_column('actions', '<a href="{{ url(\'/schools/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                     <a href="{{ url(\'/schools/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
                    ->remove_column('id')
                    ->make();
            } else {
                return Datatables::of($schools)
                    ->add_column('actions', '<a href="{{ url(\'/schools/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
                    ->remove_column('id')
                    ->make();
            }
        } else {
            if ($this->user->inRole('admin')) {
                return Datatables::of($schools)
                    ->add_column('actions', '<a href="{{ url(\'/schools/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                     <a href="{{ url(\'/schools/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
                    ->remove_column('id')
                    ->make();
            } else {
                return Datatables::of($schools)
                    ->add_column('actions', '<a href="{{ url(\'/schools/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>')
                    ->remove_column('id')
                    ->make();
            }
        }
    }
}
