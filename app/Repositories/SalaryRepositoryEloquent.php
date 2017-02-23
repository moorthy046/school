<?php

namespace App\Repositories;

use App\Models\Salary;
use Sentinel;

class SalaryRepositoryEloquent implements SalaryRepository
{
    /**
     * @var Salary
     */
    private $model;
    private $user;


    /**
     * SalaryRepositoryEloquent constructor.
     * @param Salary $model
     */
    public function __construct(Salary $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForSchoolYearSchool($school_year_id, $school_id)
    {
        return $this->model->where('school_year_id',$school_year_id)->where('school_id',$school_id);
    }
}