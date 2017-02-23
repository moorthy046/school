<?php

namespace App\Repositories;

use App\Models\Scholarship;
use Sentinel;

class ScholarshipRepositoryEloquent implements ScholarshipRepository
{
    /**
     * @var Scholarship
     */
    private $model;
    private $user;


    /**
     * ScholarshipRepositoryEloquent constructor.
     * @param Scholarship $model
     */
    public function __construct(Scholarship $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}