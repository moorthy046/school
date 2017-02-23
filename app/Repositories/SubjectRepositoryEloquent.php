<?php

namespace App\Repositories;

use App\Models\Subject;
use Sentinel;

class SubjectRepositoryEloquent implements SubjectRepository
{
    /**
     * @var Subject
     */
    private $model;
    private $user;


    /**
     * SubjectRepositoryEloquent constructor.
     * @param Subject $model
     */
    public function __construct(Subject $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForDirectionAndClass($direction_id, $class)
    {
        return $this->model->where('direction_id', $direction_id)
            ->where('class', $class);
    }
}