<?php

namespace App\Repositories;

use App\Models\Direction;
use Sentinel;

class DirectionRepositoryEloquent implements DirectionRepository
{
    /**
     * @var Direction
     */
    private $model;
    private $user;


    /**
     * DirectionRepositoryEloquent constructor.
     * @param Direction $model
     */
    public function __construct(Direction $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}