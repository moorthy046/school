<?php

namespace App\Repositories;

use App\Models\Mark;
use Sentinel;

class MarkRepositoryEloquent implements MarkRepository
{
    /**
     * @var Mark
     */
    private $model;
    private $user;


    /**
     * MarkRepositoryEloquent constructor.
     * @param Mark $model
     */
    public function __construct(Mark $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}