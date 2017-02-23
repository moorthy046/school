<?php

namespace App\Repositories;

use App\Models\Semester;
use Sentinel;

class SemesterRepositoryEloquent implements SemesterRepository
{
    /**
     * @var Semester
     */
    private $model;
    private $user;


    /**
     * SemesterRepositoryEloquent constructor.
     * @param Semester $model
     */
    public function __construct(Semester $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}