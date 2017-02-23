<?php

namespace App\Repositories;

use App\Models\Attendance;
use Sentinel;

class AttendanceRepositoryEloquent implements AttendanceRepository
{
    /**
     * @var Attendance
     */
    private $model;
    private $user;


    /**
     * AttendanceRepositoryEloquent constructor.
     * @param Attendance $model
     */
    public function __construct(Attendance $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForStudentsAndSchoolYear($students,$school_year_id)
    {
        return $this->model->whereIn('student_id', $students)
            ->where('school_year_id', $school_year_id);
    }
}