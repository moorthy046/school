<?php

namespace App\Repositories;

use App\Models\ParentStudent;
use Sentinel;

class ParentStudentRepositoryEloquent implements ParentStudentRepository
{
    /**
     * @var ParentStudent
     */
    private $model;
    private $user;


    /**
     * ParentStudentRepositoryEloquent constructor.
     * @param ParentStudent $model
     */
    public function __construct(ParentStudent $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}