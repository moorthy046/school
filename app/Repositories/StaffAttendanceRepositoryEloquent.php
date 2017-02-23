<?php

namespace App\Repositories;

use App\Models\StaffAttendance;
use Sentinel;

class StaffAttendanceRepositoryEloquent implements StaffAttendanceRepository
{
    /**
     * @var StaffAttendance
     */
    private $model;
    private $user;


    /**
     * StaffAttendanceRepository constructor.
     * @param StaffAttendance $model
     */
    public function __construct(StaffAttendance $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAllForSchool($school_id)
    {
        return $this->model->where('school_id',$school_id);
    }

    public function getAllForSchoolYear($school_year_id)
    {
        return $this->model->where('school_year_id',$school_year_id);
    }

    public function getAllForSchoolSchoolYear($school_id, $school_year_id)
    {
        return $this->model->where('school_year_id',$school_year_id)
            ->where('school_id',$school_id);
    }

    public function getAllForSchoolSchoolYearStaff($school_id, $school_year_id, $staff_id)
    {
        return $this->model->where('school_year_id',$school_year_id)
            ->where('school_id',$school_id)
            ->where('user_id',$staff_id);
    }
}