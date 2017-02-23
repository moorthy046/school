<?php

namespace App\Repositories;

use App\Models\Dormitory;
use Sentinel;

class DormitoryRepositoryEloquent implements DormitoryRepository
{
    /**
     * @var Dormitory
     */
    private $model;
    private $user;


    /**
     * DormitoryRepositoryEloquent constructor.
     * @param Dormitory $model
     */
    public function __construct(Dormitory $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}