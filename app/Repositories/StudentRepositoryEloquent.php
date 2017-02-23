<?php

namespace App\Repositories;

use App\Models\Student;
use Sentinel;
use Illuminate\Support\Collection;

class StudentRepositoryEloquent implements StudentRepository
{
    /**
     * @var Student
     */
    private $model;
    private $user;


    /**
     * StudentRepositoryEloquent constructor.
     * @param Student $model
     */
    public function __construct(Student $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForSchoolYearAndSection($school_year_id, $section_id)
    {
        return $this->model->where('school_year_id', $school_year_id)
            ->where('section_id', $section_id);
    }

    public function getAllForSchoolYear($school_year_id)
    {
        return $this->model->where('school_year_id', $school_year_id);
    }

    public function getAllStudentGroupsForStudentUserAndSchoolYear($student_user_id, $school_year_id)
    {
        $studentgroups = new Collection([]);
        $this->model->with('user', 'studentsgroups')
            ->get()
            ->filter(function ($studentItem) use ($student_user_id, $school_year_id) {
                return (isset($studentItem->user) &&
                    $studentItem->user->id == $student_user_id &&
                    $studentItem->school_year_id == $school_year_id);
            })
            ->each(function ($studentItem) use ($studentgroups) {
                foreach ($studentItem->studentsgroups as $studentsgroup) {
                    $studentgroups->push($studentsgroup->id);
                }
            });
        return $studentgroups;
    }

    public function getAllForStudentGroup($student_group_id)
    {
        $studentitems = new Collection([]);
        $this->model->with('user', 'studentsgroups')
            ->orderBy('order')
            ->get()
            ->each(function ($studentItem) use ($studentitems, $student_group_id) {
                foreach ($studentItem->studentsgroups as $studentsgroup) {
                    if ($studentsgroup->id == $student_group_id) {
                        $studentitems->push($studentItem);
                    }
                }
            });
        return $studentitems;
    }

    public function getAllForSchoolYearAndSchool($school_year_id, $school_id)
    {
        return $this->model->where('school_year_id', $school_year_id)
            ->where('school_id', $school_id);
    }

    public function getAllForSchoolYearSchoolAndSection($school_year_id, $school_id, $section_id)
    {
        return $this->model->where('school_year_id', $school_year_id)
            ->where('school_id', $school_id)
            ->where('section_id', $section_id);
    }
}