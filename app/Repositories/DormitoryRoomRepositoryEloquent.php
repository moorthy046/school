<?php

namespace App\Repositories;

use App\Models\DormitoryRoom;
use Sentinel;

class DormitoryRoomRepositoryEloquent implements DormitoryRoomRepository
{
    /**
     * @var DormitoryRoom
     */
    private $model;
    private $user;


    /**
     * DormitoryRoomRepositoryEloquent constructor.
     * @param DormitoryRoom $model
     */
    public function __construct(DormitoryRoom $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}