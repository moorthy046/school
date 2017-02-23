<?php

namespace App\Repositories;

use App\Models\Behavior;
use Sentinel;

class BehaviorRepositoryEloquent implements BehaviorRepository
{
    /**
     * @var Behavior
     */
    private $model;
    private $user;


    /**
     * BehaviorRepositoryEloquent constructor.
     * @param Behavior $model
     */
    public function __construct(Behavior $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}