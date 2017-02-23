<?php

namespace App\Repositories;

use App\Models\ApplyingLeave;
use Sentinel;

class ApplyingLeaveRepositoryEloquent implements ApplyingLeaveRepository
{
    /**
     * @var ApplyingLeave
     */
    private $model;
    private $user;


    /**
     * ApplyingLeaveRepositoryEloquent constructor.
     * @param ApplyingLeave $model
     */
    public function __construct(ApplyingLeave $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForStudentAndSchoolYear($student_id, $school_year_id)
    {
        return $this->model->where('school_year_id', $school_year_id)
            ->where('student_id', $student_id);
    }
}