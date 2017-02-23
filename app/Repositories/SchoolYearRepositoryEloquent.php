<?php

namespace App\Repositories;

use App\Models\SchoolYear;
use Sentinel;

class SchoolYearRepositoryEloquent implements SchoolYearRepository
{
    /**
     * @var SchoolYear
     */
    private $model;
    private $user;


    /**
     * SchoolYearRepositoryEloquent constructor.
     * @param SchoolYear $model
     */
    public function __construct(SchoolYear $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}