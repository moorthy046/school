<?php
namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Models\ParentStudent;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Repositories\StudentRepository;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Datatables;
use Session;
use DB;
use Sentinel;
use App\Http\Requests\Secure\StudentRequest;

class StudentController extends SecureController
{
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * StudentController constructor.
     * @param StudentRepository $studentRepository
     */
    public function __construct(StudentRepository $studentRepository)
    {
        parent::__construct();
        $this->studentRepository = $studentRepository;

        view()->share('type', 'student');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('student.student');
        return view('student.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('student.new');
        $sections = Section::where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))
            ->lists('title', 'id')->toArray();
        return view('student.create', compact('title', 'sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StudentRequest $request)
    {
        $user = Sentinel::registerAndActivate($request->except('section_id', 'order'));

        $role = Sentinel::findRoleBySlug('student');
        $role->users()->attach($user);

        $user = User::find($user->id);
        $user->update($request->except('section_id', 'order','password'));

        $student = new Student($request->only('section_id', 'order'));
        $student->school_year_id = Session::get('current_school_year');
        $student->school_id = Session::get('current_school');
        $student->user_id = $user->id;
        $student->save();

        $student->student_no = $this->generateStudentNo($student->id,Session::get('current_school'));
        $student->save();

        return redirect('/student');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Student $student)
    {
        $title = trans('student.details');
        $action = 'show';
        return view('student.show', compact('student', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(Student $student)
    {
        $title = trans('student.edit');
        $sections = Section::where('school_year_id', Session::get('current_school_year'))
            ->where('school_id', Session::get('current_school'))->lists('title', 'id')->toArray();
        return view('student.edit', compact('title', 'student', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->only('section_id', 'order'));
        if ($request->password != "") {
            $student->user->password = bcrypt($request->password);
        }
        $student->user->update($request->except('section_id', 'order','password'));
        return redirect('/student');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(Student $student)
    {
        // Title
        $title = trans('student.delete');
        return view('/student/delete', compact('student', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect('/student');
    }

    public function data()
    {
        $students = $this->studentRepository->getAllForSchoolYearAndSchool(Session::get('current_school_year'), Session::get('current_school'))
            ->with('user', 'section')
            ->orderBy('students.order')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'title' => isset($student->section)?$student->section->title:"",
                    'full_name' => isset($student->user)?$student->user->full_name:"",
                    'order' => $student->order,
                    'user_id' => $student->user_id,
                ];
            });
        return Datatables::of($students)
            ->add_column('actions', '<a href="{{ url(\'/student/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/report/\' . $user_id . \'/forstudent\' ) }}" class="btn btn-warning btn-sm" >
                                            <i class="fa fa-bar-chart"></i>  {{ trans("table.report") }}</a>
                                    <a href="{{ url(\'/student/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                    <a href="{{ url(\'/student_card/\' . $user_id ) }}" target="_blank" class="btn btn-success btn-sm" >
                                            <i class="fa fa-credit-card"></i>  {{ trans("student.student_card") }}</a>
                                     <a href="{{ url(\'/student/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->remove_column('user_id')
            ->make();
    }

}
