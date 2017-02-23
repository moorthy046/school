<?php

namespace App\Http\Controllers\Secure;

use App\Http\Requests;
use App\Http\Requests\Secure\CreateNewSections;
use App\Http\Requests\Secure\SchoolYearRequest;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Repositories\SchoolYearRepository;
use App\Repositories\SectionRepository;
use App\Repositories\StudentRepository;
use Datatables;
use Session;
use DB;

class SchoolYearController extends SecureController
{
    /**
     * @var SchoolYearRepository
     */
    private $schoolYearRepository;
    /**
     * @var SectionRepository
     */
    private $sectionRepository;
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * SchoolYearController constructor.
     * @param SchoolYearRepository $schoolYearRepository
     * @param SectionRepository $sectionRepository
     * @param StudentRepository $studentRepository
     */
    public function __construct(SchoolYearRepository $schoolYearRepository,
                                SectionRepository $sectionRepository,
                                StudentRepository $studentRepository)
    {
        parent::__construct();

        $this->schoolYearRepository = $schoolYearRepository;
        $this->sectionRepository = $sectionRepository;
        $this->studentRepository = $studentRepository;

        view()->share('type', 'schoolyear');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('schoolyear.schoolyear');
        return view('schoolyear.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('schoolyear.new');
        return view('schoolyear.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(SchoolYearRequest $request)
    {
        $schoolyear = new SchoolYear($request->all());
        $schoolyear->save();

        return redirect('/schoolyear');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(SchoolYear $schoolyear)
    {
        // Title
        $title = trans('schoolyear.details');
        $action = 'show';
        return view('schoolyear.show', compact('schoolyear', 'title', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit(SchoolYear $schoolyear)
    {
        $title = trans('schoolyear.edit');
        return view('schoolyear.edit', compact('title', 'schoolyear'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(SchoolYearRequest $request, SchoolYear $schoolyear)
    {
        $schoolyear->update($request->all());
        return redirect('/schoolyear');
    }

    /**
     *
     *
     * @param $website
     * @return Response
     */
    public function delete(SchoolYear $schoolyear)
    {
        // Title
        $title = trans('schoolyear.delete');
        return view('/schoolyear/delete', compact('schoolyear', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(SchoolYear $schoolyear)
    {
        $schoolyear->delete();
        return redirect('/schoolyear');
    }

    public function data()
    {
        $schoolyears = $this->schoolYearRepository->getAll()
            ->get()
            ->map(function ($schoolyear) {
                return [
                    'id' => $schoolyear->id,
                    'title' => $schoolyear->title,
                ];
            });

        return Datatables::of($schoolyears)
            ->add_column('actions', '<a href="{{ url(\'/schoolyear/\' . $id . \'/edit\' ) }}" class="btn btn-success btn-sm" >
                                            <i class="fa fa-pencil-square-o "></i>  {{ trans("table.edit") }}</a>
                                    <a href="{{ url(\'/schoolyear/\' . $id . \'/copy_data\' ) }}" class="btn btn-default btn-sm" >
                                            <i class="fa fa-files-o"></i>  {{ trans("schoolyear.copy_sections_students") }}</a>
                                    <a href="{{ url(\'/schoolyear/\' . $id . \'/show\' ) }}" class="btn btn-primary btn-sm" >
                                            <i class="fa fa-eye"></i>  {{ trans("table.details") }}</a>
                                     <a href="{{ url(\'/schoolyear/\' . $id . \'/delete\' ) }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> {{ trans("table.delete") }}</a>')
            ->remove_column('id')
            ->make();
    }

    public function copyData(SchoolYear $schoolyear)
    {
        $title = trans('schoolyear.copy_sections_students_to'). $schoolyear->title;
        $school_year_list = [''=> trans('schoolyear.schoolyear_select')] +
            $this->schoolYearRepository->getAll()->where('id', '<>', $schoolyear->id)->lists('title', 'id')->toArray();
        return view('/schoolyear/copy', compact('schoolyear', 'title','school_year_list'));
    }

    public function getSections(SchoolYear $schoolyear)
    {
        return $this->sectionRepository->getAllForSchoolYear($schoolyear->id)->get()
            ->lists('title', 'id')->toArray();
    }

    public function postData(SchoolYear $schoolyear, CreateNewSections $request)
    {
        DB::beginTransaction();
        if(isset($request->section)) {
            foreach ($request->section as $key => $section_temp) {
                if ($section_temp != "") {
                    $section = $this->sectionRepository->getAllForSchoolYear($request->select_school_year_id)
                        ->where('id', $key)->first();

                    if(isset($section->section_teacher_id)) {
                        $section_new = new Section();
                        $section_new->school_year_id = $schoolyear->id;
                        $section_new->section_teacher_id = $section->section_teacher_id;
                        $section_new->school_id = $section->school_id;
                        $section_new->title = $section_temp;
                        $section_new->save();

                        $students = $this->studentRepository->getAllForSchoolYearAndSection($request->select_school_year_id,$key)->get();
                        if(!empty($students)) {
                            foreach ($students as $student) {
                                $student_new = new Student();
                                $student_new->school_year_id = $schoolyear->id;
                                $student_new->user_id = $student->user_id;
                                $student_new->section_id = $section_new->id;
                                $section_new->school_id = $section->school_id;
                                $student_new->order = $student->order;
                                $student_new->school_id = $student->school_id;
                                $student_new->save();
                                
                                $student_new->student_no = $this->generateStudentNo($student_new->id,$student->school_id);
                                $student_new->save();
                            }
                        }
                    }
                }
            }
        }
        DB::commit();

        return redirect('/schoolyear');
    }

}
