<?php

namespace App\Repositories;

use App\Models\School;
use Sentinel;

class SchoolRepositoryEloquent implements SchoolRepository
{
    /**
     * @var School
     */
    private $model;
    private $user;


    /**
     * SchoolRepositoryEloquent constructor.
     * @param School $model
     */
    public function __construct(School $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model
            ->select('schools.*');
    }

    public function getAllAdmin()
    {
        return $this->model->join('school_admins', 'schools.id', '=', 'school_admins.school_id')
            ->where('school_admins.user_id', $this->user->id)
            ->select('schools.*');
    }

    public function getAllTeacher()
    {
        return $this->model->join('teacher_subjects', 'schools.id', '=', 'teacher_subjects.school_id')
            ->where('teacher_subjects.teacher_id', $this->user->id)
            ->select('schools.*');
    }

    public function getAllStudent()
    {
        return $this->model->join('sections', 'schools.id', '=', 'sections.school_id')
            ->join('students','students.section_id','=','sections.id')
            ->where('students.user_id', $this->user->id)
            ->select('schools.*');
    }
}