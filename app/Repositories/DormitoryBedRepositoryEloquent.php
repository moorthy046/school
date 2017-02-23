<?php

namespace App\Repositories;

use App\Models\DormitoryBed;
use Sentinel;

class DormitoryBedRepositoryEloquent implements DormitoryBedRepository
{
    /**
     * @var DormitoryBed
     */
    private $model;
    private $user;


    /**
     * DormitoryBedRepositoryEloquent constructor.
     * @param DormitoryBed $model
     */
    public function __construct(DormitoryBed $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}